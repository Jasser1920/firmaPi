<?php

namespace App\Controller;

use App\Entity\Produit;
use App\Entity\Categorie;
use App\Repository\ProduitRepository;
use App\Repository\CategorieRepository;
use App\Service\TextRazorService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AnalysisController extends AbstractController
{
    #[Route('/analyze/produit/{id}', name: 'analyze_produit')]
    public function analyzeProduit(
        int $id,
        ProduitRepository $produitRepository,
        CategorieRepository $categorieRepository,
        TextRazorService $textRazorService,
        EntityManagerInterface $entityManager
    ): Response {
        // Fetch Produit
        $produit = $produitRepository->find($id);
        if (!$produit) {
            throw $this->createNotFoundException('Produit not found');
        }

        $description = $produit->getDescription();
        if (!$description) {
            return new Response('No description to analyze', 400);
        }

        try {
            // Analyze with TextRazor
            $response = $textRazorService->analyzeText($description);
            $result = $textRazorService->extractProductsAndCategories($response);

            // Update Produit name with the first product (if any)
            if (!empty($result['products'])) {
                $produit->setNom($result['products'][0]); // First product as name
            }

            // Link to or create a Category, excluding "Autre"
            if (!empty($result['categories'])) {
                // Filter out "Autre" and take the first meaningful category
                $validCategories = array_filter($result['categories'], fn($cat) => $cat !== "Autre");
                $categoryName = !empty($validCategories) ? reset($validCategories) : null;

                if ($categoryName) {
                    $categorie = $categorieRepository->findOneBy(['nom_categorie' => $categoryName]);

                    if (!$categorie) {
                        $categorie = new Categorie();
                        $categorie->setNomCategorie($categoryName);
                        $entityManager->persist($categorie);
                    }

                    $produit->setCategorie($categorie);
                }
                // If only "Autre" or no valid category, leave categorie unset or handle differently
            }

            // Save changes
            $entityManager->flush();

            return $this->render('analysis/index.html.twig', [
                'produit' => $produit,
                'products' => $result['products'],
                'categories' => $result['categories'],
            ]);
        } catch (\Exception $e) {
            return new Response('Error: ' . $e->getMessage(), 500);
        }
    }
}