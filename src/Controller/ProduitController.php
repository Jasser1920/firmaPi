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
    private $cloudinary;

    public function __construct(Cloudinary $cloudinary)
    {
        $this->cloudinary = $cloudinary;
    }

    #[Route(name: 'app_produit_index', methods: ['GET'])]
    public function index(ProduitRepository $produitRepository): Response
    {
        return $this->render('produit/index.html.twig', [
            'produits' => $produitRepository->findAll(),
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
                $this->addFlash('error', 'Failed to upload image to Cloudinary: ' . $e->getMessage());
                return $this->render('produit/new.html.twig', [
                    'produit' => $produit,
                    'form' => $form->createView(),
                ]);
            }
        } else {
            // Set a default image if no image is uploaded
            $produit->setImage('https://example.com/default-image.png');
        }

        $entityManager->persist($produit);
        $entityManager->flush();

        return $this->redirectToRoute('app_produit_index', [], Response::HTTP_SEE_OTHER);
    }

    return $this->render('produit/new.html.twig', [
        'produit' => $produit,
        'form' => $form->createView(),
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
                $this->addFlash('error', 'Failed to upload image to Cloudinary: ' . $e->getMessage());
                return $this->render('produit/edit.html.twig', [
                    'produit' => $produit,
                    'form' => $form->createView(),
                ]);
            }
        } else {
            // Keep the existing image if no new image is uploaded
            $existingImage = $produit->getImage();
            if (!$existingImage) {
                $produit->setImage('https://example.com/default-image.png');
            }
        }

        $entityManager->flush();
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

        if ($this->isCsrfTokenValid('delete'.$produit->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($produit);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_produit_index', [], Response::HTTP_SEE_OTHER);
    }
    
    #[Route('/{id}', name: 'app_produit_show', methods: ['GET'])]
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
}