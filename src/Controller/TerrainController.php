<?php

namespace App\Controller;

use App\Entity\Terrain;
use App\Form\TerrainType;
use App\Repository\TerrainRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/terrain')]
final class TerrainController extends AbstractController
{
    #[Route(name: 'app_terrain_index', methods: ['GET'])]
    public function index(Request $request, TerrainRepository $terrainRepository): Response
    {
        $search = $request->query->get('search', '');
        $price = $request->query->get('price', '');
        $sort = $request->query->get('sort', '');
    
        $queryBuilder = $terrainRepository->createQueryBuilder('t');
    
        // Filter by user role
        if ($this->isGranted('ROLE_AGRICULTURE')) {
            $user = $this->getUser();
            $queryBuilder
                ->andWhere('t.user = :user')
                ->setParameter('user', $user);
        }
    
        if (!empty($search)) {
            $queryBuilder
                ->andWhere('t.description LIKE :search')
                ->setParameter('search', '%' . $search . '%');
        }
    
        if (!empty($price) && is_numeric($price)) {
            $queryBuilder
                ->andWhere('t.prixLocation <= :price')
                ->setParameter('price', $price);
        }
    
        if ($sort === 'price') {
            $queryBuilder->orderBy('t.prixLocation', 'ASC');
        } elseif ($sort === 'size') {
            $queryBuilder->orderBy('t.superficie', 'ASC');
        } elseif ($sort === 'date') {
            $queryBuilder->orderBy('t.dateCreation', 'DESC');
        }
    
        $terrains = $queryBuilder->getQuery()->getResult();
    
        return $this->render('terrain/index.html.twig', [
            'terrains' => $terrains,
            'search' => $search,
            'price' => $price,
            'sort' => $sort,
        ]);
    }

    #[Route('/new', name: 'app_terrain_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $terrain = new Terrain();
        $form = $this->createForm(TerrainType::class, $terrain);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($terrain);
            $entityManager->flush();
            $this->addFlash('success', 'Le terrain a été ajouté avec succès !');
            return $this->redirectToRoute('app_terrain_index');
        }

        return $this->render('terrain/new.html.twig', [
            'terrain' => $terrain,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_terrain_show', methods: ['GET'])]
    public function show(Terrain $terrain): Response
    {$form = $this->createForm(TerrainType::class, $terrain);
        return $this->render('terrain/show.html.twig', [
            'terrain' => $terrain, // Pass the Terrain entity
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}/edit', name: 'app_terrain_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Terrain $terrain, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(TerrainType::class, $terrain);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            $this->addFlash('success', 'Terrain mis à jour avec succès !');
            return $this->redirectToRoute('app_terrain_index');
        }

        return $this->render('terrain/edit.html.twig', [
            'terrain' => $terrain,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/delete', name: 'app_terrain_delete', methods: ['POST'])]
    public function delete(Request $request, Terrain $terrain, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $terrain->getId(), $request->request->get('_token'))) {
            $entityManager->remove($terrain);
            $entityManager->flush();
            $this->addFlash('success', 'Le terrain a été supprimé avec succès !');
        } else {
            $this->addFlash('error', 'Échec de la suppression du terrain.');
        }

        return $this->redirectToRoute('app_terrain_index');
    }
}
