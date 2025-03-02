<?php

namespace App\Controller;

use App\Repository\ProduitRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/cart')]
class CartController extends AbstractController
{
    #[Route('/add/{id}', name: 'app_cart_add', methods: ['GET'])]
    public function addToCart(int $id, SessionInterface $session, ProduitRepository $produitRepository): Response
    {
        $produit = $produitRepository->find($id);
        if (!$produit) {
            $this->addFlash('error', 'Produit non trouvé.');
            return $this->redirectToRoute('app_cart_show');
        }

        $cart = $session->get('cart', []);
        if (!isset($cart[$id])) {
            $cart[$id] = 1;
        } else {
            $cart[$id]++;
        }
        $session->set('cart', $cart);

        return $this->redirectToRoute('app_cart_show');
    }

    #[Route('/show', name: 'app_cart_show', methods: ['GET'])]
    public function showCart(SessionInterface $session, ProduitRepository $produitRepository): Response
    {
        $cart = $session->get('cart', []);
        $products = [];
        $total = 0;

        foreach ($cart as $id => $quantity) {
            $product = $produitRepository->find($id);
            if ($product) {
                $products[] = ['product' => $product, 'quantity' => $quantity];
                $total += $product->getPrix() * $quantity;
            } else {
                unset($cart[$id]);
            }
        }
        $session->set('cart', $cart);

        return $this->render('cart/show.html.twig', [
            'products' => $products,
            'total' => $total,
        ]);
    }

    #[Route('/remove/{id}', name: 'app_cart_remove', methods: ['GET'])]
    public function removeFromCart(int $id, SessionInterface $session): Response
    {
        $cart = $session->get('cart', []);
        if (isset($cart[$id])) {
            unset($cart[$id]);
            $session->set('cart', $cart);
            $this->addFlash('success', 'Produit retiré du panier.');
        }

        return $this->redirectToRoute('app_cart_show');
    }
}