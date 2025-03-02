<?php

namespace App\Controller;

use App\Entity\Commande;
use App\Form\CheckoutType;
use App\Form\CommandeType;
use App\Repository\CommandeRepository;
use App\Repository\ProduitRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Stripe\Stripe;
use Stripe\Checkout\Session as StripeSession;
use GuzzleHttp\Client;

#[Route('/commande')]
final class CommandeController extends AbstractController
{
    #[Route(name: 'app_commande_index', methods: ['GET'])]
    public function index(CommandeRepository $commandeRepository): Response
    {
        return $this->render('commande/index.html.twig', [
            'commandes' => $commandeRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_commande_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, SessionInterface $session, ProduitRepository $produitRepository): Response
    {
        $cart = $session->get('cart', []);
        if (empty($cart)) {
            $this->addFlash('error', 'Votre panier est vide.');
            return $this->redirectToRoute('app_cart_show');
        }

        $products = [];
        $total = 0;
        foreach ($cart as $id => $quantity) {
            $product = $produitRepository->find($id);
            if ($product) {
                $products[] = ['product' => $product, 'quantity' => $quantity];
                $total += $product->getPrix() * $quantity;
            }
        }

        if (empty($products)) {
            $this->addFlash('error', 'Aucun produit valide dans le panier.');
            return $this->redirectToRoute('app_cart_show');
        }

        $commande = new Commande();
        $commande->setTotal($total);
        foreach ($products as $item) {
            for ($i = 0; $i < $item['quantity']; $i++) {
                $commande->addProduit($item['product']);
            }
        }

        $form = $this->createForm(CheckoutType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $commande->setLivraison($data['livraison']);
            $entityManager->persist($commande);
            $entityManager->flush();

            return $this->redirectToRoute('app_commande_payment', ['id' => $commande->getId()]);
        }

        return $this->render('commande/new.html.twig', [
            'form' => $form->createView(),
            'products' => $products,
            'total' => $total,
        ]);
    }

    #[Route('/payment/{id}', name: 'app_commande_payment', methods: ['GET'])]
    public function payment(int $id, CommandeRepository $commandeRepository, SessionInterface $session): Response
    {
        $commande = $commandeRepository->find($id);
        if (!$commande) {
            throw $this->createNotFoundException('Commande non trouvée.');
        }
    
        Stripe::setApiKey($_ENV['STRIPE_SECRET']);
    
        $lineItems = [];
        $cart = $session->get('cart', []);
        foreach ($cart as $productId => $quantity) {
            $product = $commande->getProduits()->filter(fn($p) => $p->getId() == $productId)->first();
            if ($product) {
                $lineItems[] = [
                    'price_data' => [
                        'currency' => 'eur',
                        'product_data' => [
                            'name' => $product->getNom(),
                        ],
                        'unit_amount' => $product->getPrix() * 100,
                    ],
                    'quantity' => $quantity,
                ];
            }
        }
    
        // Use the PUBLIC_URL environment variable to construct absolute URLs
        $baseUrl = rtrim($_ENV['PUBLIC_URL'], '/');
        $successPath = $this->generateUrl('app_commande_success', ['id' => $commande->getId()]);
        $cancelPath = $this->generateUrl('app_commande_cancel', ['id' => $commande->getId()]);
        $successUrl = $baseUrl . $successPath;
        $cancelUrl = $baseUrl . $cancelPath;
    
        // Debug the URLs
        dump('Base URL:', $baseUrl);
        dump('Success Path:', $successPath);
        dump('Cancel Path:', $cancelPath);
        dump('Success URL:', $successUrl);
        dump('Cancel URL:', $cancelUrl);
    
        $session = StripeSession::create([
            'payment_method_types' => ['card'],
            'line_items' => $lineItems,
            'mode' => 'payment',
            'success_url' => $successUrl,
            'cancel_url' => $cancelUrl,
            'metadata' => [
                'commande_id' => $commande->getId(),
            ],
        ]);
    
        return $this->render('commande/payment.html.twig', [
            'stripe_key' => $_ENV['STRIPE_KEY'],
            'session_id' => $session->id,
            'commande' => $commande,
        ]);
    }

    #[Route('/success/{id}', name: 'app_commande_success', methods: ['GET'])]
    public function success(int $id, CommandeRepository $commandeRepository, SessionInterface $session): Response
    {
        $commande = $commandeRepository->find($id);
        if (!$commande) {
            throw $this->createNotFoundException('Commande non trouvée.');
        }

        $session->remove('cart');
        $this->addFlash('success', 'Paiement réussi ! Votre commande a été confirmée.');

        return $this->render('commande/success.html.twig', [
            'commande' => $commande,
        ]);
    }

    #[Route('/cancel/{id}', name: 'app_commande_cancel', methods: ['GET'])]
    public function cancel(int $id, CommandeRepository $commandeRepository): Response
    {
        $commande = $commandeRepository->find($id);
        if (!$commande) {
            throw $this->createNotFoundException('Commande non trouvée.');
        }

        $this->addFlash('error', 'Paiement annulé. Veuillez réessayer.');
        return $this->redirectToRoute('app_commande_payment', ['id' => $commande->getId()]);
    }

    #[Route('/{id}', name: 'app_commande_show', methods: ['GET'], requirements: ['id' => '\d+'])]
    public function show(?Commande $commande): Response
    {
        if (!$commande) {
            throw new NotFoundHttpException('Commande non trouvée.');
        }

        return $this->render('commande/show.html.twig', [
            'commande' => $commande,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_commande_edit', methods: ['GET', 'POST'], requirements: ['id' => '\d+'])]
    public function edit(Request $request, ?Commande $commande, EntityManagerInterface $entityManager): Response
    {
        if (!$commande) {
            throw new NotFoundHttpException('Commande non trouvée.');
        }

        $form = $this->createForm(CommandeType::class, $commande);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            $this->addFlash('success', 'Commande mise à jour avec succès.');
            return $this->redirectToRoute('app_commande_index');
        }

        return $this->render('commande/edit.html.twig', [
            'commande' => $commande,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_commande_delete', methods: ['POST'], requirements: ['id' => '\d+'])]
    public function delete(Request $request, ?Commande $commande, EntityManagerInterface $entityManager): Response
    {
        if (!$commande) {
            throw new NotFoundHttpException('Commande non trouvée.');
        }

        if ($this->isCsrfTokenValid('delete' . $commande->getId(), $request->request->get('_token'))) {
            $entityManager->remove($commande);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_commande_index');
    }
    #[Route('/{id}/generate-invoice', name: 'app_commande_generate_invoice', methods: ['GET'], requirements: ['id' => '\d+'])]
    public function generateInvoice(int $id, CommandeRepository $commandeRepository): Response
    {
        $commande = $commandeRepository->find($id);
        if (!$commande) {
            throw $this->createNotFoundException('Commande non trouvée.');
        }

        // Générer la facture PDF
        $pdfUrl = $this->generateInvoicePdf($commande);

        if (!$pdfUrl) {
            $this->addFlash('error', 'Erreur lors de la génération de la facture.');
            return $this->redirectToRoute('app_commande_show', ['id' => $commande->getId()]);
        }

        // Rediriger vers l'URL temporaire du PDF hébergé par PDF.co
        return $this->redirect($pdfUrl);
    }

    /**
     * Génère une facture PDF à partir des données de la commande
     */
    private function generateInvoicePdf(Commande $commande): ?string
    {
        // Rendre le template HTML pour la facture
        $html = $this->renderView('commande/invoice_template.html.twig', [
            'commande' => $commande,
            'produits' => $commande->getProduits(),
            'total' => $commande->getTotal(),
        ]);

        // Configurer le client Guzzle pour appeler PDF.co
        $client = new Client();
        $apiKey = $_ENV['PDFCO_API_KEY'];

        try {
            $response = $client->post('https://api.pdf.co/v1/pdf/convert/from/html', [
                'headers' => [
                    'x-api-key' => $apiKey,
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'html' => $html,
                    'name' => 'facture_' . $commande->getId() . '.pdf',
                    'async' => false, // Génération synchrone
                ],
            ]);

            $result = json_decode($response->getBody()->getContents(), true);
            if ($result['error']) {
                throw new \Exception($result['message']);
            }

            return $result['url']; // Retourne l'URL temporaire du PDF
        } catch (\Exception $e) {
            $this->addFlash('error', 'Erreur lors de la génération : ' . $e->getMessage());
            return null;
        }
    }
    
}