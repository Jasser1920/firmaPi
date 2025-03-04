<?php

namespace App\Controller;

use App\Entity\Produit;
use App\Form\ProduitType;
use App\Repository\ProduitRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Cloudinary\Cloudinary;

#[Route('/produit')]
final class ProduitController extends AbstractController
{
    private Cloudinary $cloudinary;

    public function __construct(Cloudinary $cloudinary)
    {
        $this->cloudinary = $cloudinary;
    }

    #[Route('/{page<\d+>}', name: 'app_produit_index', methods: ['GET'], defaults: ['page' => 1])]
    public function index(Request $request, ProduitRepository $produitRepository, int $page = 1): Response
    {
        // Get search, filter, and sort parameters
        $search = $request->query->get('search', '');
        $maxPrice = $request->query->get('max_price', 500);
        $sort = $request->query->get('sort', 'price_asc');

        // Pagination parameters
        $limit = 9; // Products per page
        $offset = ($page - 1) * $limit;

        // Build query
        $qb = $produitRepository->createQueryBuilder('p');

        // Search by name
        if ($search) {
            $qb->andWhere('p.nom LIKE :search')
               ->setParameter('search', "%$search%");
        }

        // Filter by price
        $qb->andWhere('p.prix <= :max_price')
           ->setParameter('max_price', $maxPrice);

        // Sorting
        [$sortField, $sortDirection] = match ($sort) {
            'price_desc' => ['prix', 'DESC'],
            'name_asc' => ['nom', 'ASC'],
            'name_desc' => ['nom', 'DESC'],
            default => ['prix', 'ASC'], // price_asc
        };
        $qb->orderBy("p.$sortField", $sortDirection);

        // Get total count for pagination
        $totalProducts = (clone $qb)->select('COUNT(p.id)')->getQuery()->getSingleScalarResult();
        $totalPages = max(1, ceil($totalProducts / $limit));

        // Apply pagination
        $qb->setFirstResult($offset)
           ->setMaxResults($limit);

        $produits = $qb->getQuery()->getResult();

        return $this->render('produit/index.html.twig', [
            'produits' => $produits,
            'search' => $search,
            'max_price' => $maxPrice,
            'sort' => $sort,
            'current_page' => $page,
            'total_pages' => $totalPages,
        ]);
    }

    #[Route('/new', name: 'app_produit_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $produit = new Produit();
        $form = $this->createForm(ProduitType::class, $produit);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Handle image upload to Cloudinary
            $imageFile = $form->get('image')->getData();
            if ($imageFile) {
                try {
                    $publicId = 'product_' . uniqid();
                    $uploadResult = $this->cloudinary->uploadApi()->upload($imageFile->getPathname(), [
                        'folder' => 'product_images',
                        'public_id' => $publicId,
                        'overwrite' => true,
                        'resource_type' => 'image',
                    ]);
                    $produit->setImage($uploadResult['secure_url']);
                } catch (\Cloudinary\Api\Exception\ApiError $e) {
                    $this->addFlash('error', 'Erreur lors du téléchargement de l\'image : ' . $e->getMessage());
                    return $this->render('produit/new.html.twig', [
                        'produit' => $produit,
                        'form' => $form->createView(),
                    ]);
                }
            } else {
                // Set a default image if no image is uploaded
                $produit->setImage('https://res.cloudinary.com/your-cloud-name/image/upload/v1234567890/product_images/default-product.jpg');
            }

            $entityManager->persist($produit);
            $entityManager->flush();
            $this->addFlash('success', 'Produit créé avec succès !');
            return $this->redirectToRoute('app_produit_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('produit/new.html.twig', [
            'produit' => $produit,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/produit/{id}', name: 'app_produit_show', methods: ['GET'])]
    public function show(int $id, ProduitRepository $produitRepository): Response
    {
        $produit = $produitRepository->find($id);
        if (!$produit) {
            throw $this->createNotFoundException('Produit non trouvé.');
        }
    
        return $this->render('produit/show.html.twig', [
            'produit' => $produit,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_produit_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, int $id, ProduitRepository $produitRepository, EntityManagerInterface $entityManager): Response
    {
        $produit = $produitRepository->find($id);
        if (!$produit) {
            throw $this->createNotFoundException('Produit non trouvé.');
        }

        $form = $this->createForm(ProduitType::class, $produit);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Handle image upload to Cloudinary
            $imageFile = $form->get('image')->getData();
            if ($imageFile) {
                try {
                    $publicId = 'product_' . $produit->getId() . '_' . uniqid();
                    $uploadResult = $this->cloudinary->uploadApi()->upload($imageFile->getPathname(), [
                        'folder' => 'product_images',
                        'public_id' => $publicId,
                        'overwrite' => true,
                        'resource_type' => 'image',
                    ]);
                    $produit->setImage($uploadResult['secure_url']);
                } catch (\Cloudinary\Api\Exception\ApiError $e) {
                    $this->addFlash('error', 'Erreur lors du téléchargement de l\'image : ' . $e->getMessage());
                    return $this->render('produit/edit.html.twig', [
                        'produit' => $produit,
                        'form' => $form->createView(),
                    ]);
                }
            }

            $entityManager->flush();
            $this->addFlash('success', 'Produit modifié avec succès !');
            return $this->redirectToRoute('app_produit_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('produit/edit.html.twig', [
            'produit' => $produit,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_produit_delete', methods: ['POST'])]
    public function delete(Request $request, int $id, ProduitRepository $produitRepository, EntityManagerInterface $entityManager): Response
    {
        $produit = $produitRepository->find($id);
        if (!$produit) {
            throw $this->createNotFoundException('Produit non trouvé.');
        }

        if ($this->isCsrfTokenValid('delete' . $produit->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($produit);
            $entityManager->flush();
            $this->addFlash('success', 'Produit supprimé avec succès !');
        }

        return $this->redirectToRoute('app_produit_index', [], Response::HTTP_SEE_OTHER);
    }
}