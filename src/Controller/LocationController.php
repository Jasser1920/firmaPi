<?php

namespace App\Controller;

use App\Entity\Location;
use App\Form\LocationType;
use App\Repository\LocationRepository;
use App\Repository\TerrainRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/location')]
final class LocationController extends AbstractController
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route(name: 'app_location_index', methods: ['GET'])]
    public function index(LocationRepository $locationRepository): Response
    {
        return $this->render('location/index.html.twig', [
            'locations' => $locationRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_location_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $location = new Location();
        $form = $this->createForm(LocationType::class, $location);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($location);
            $this->entityManager->flush();

            return $this->redirectToRoute('app_location_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('location/new.html.twig', [
            'location' => $location,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_location_show', methods: ['GET', 'POST'])]
    public function show(Request $request, Location $location): Response
    {
        $form = $this->createForm(LocationType::class, $location);
        $form->handleRequest($request);

        if ($request->isMethod('POST')) {
            if ($form->isSubmitted() && $form->isValid()) {
                $this->entityManager->flush();
                $this->addFlash('success', 'La location a été mise à jour avec succès !');
                return $this->redirectToRoute('app_location_index');
            }
            $this->addFlash('error', 'Formulaire invalide.');
        }

        return $this->render('location/show.html.twig', [
            'location' => $location,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}/delete', name: 'app_location_delete', methods: ['POST'])]
    public function delete(Request $request, Location $location): Response
    {
        if ($this->isCsrfTokenValid('delete' . $location->getId(), $request->request->get('_token'))) {
            $this->entityManager->remove($location);
            $this->entityManager->flush();
            $this->addFlash('success', 'La location a été supprimée avec succès.');
        } else {
            $this->addFlash('error', 'Échec de la suppression.');
        }

        return $this->redirectToRoute('app_location_index');
    }

    #[Route('/terrain/{id}/louer', name: 'app_location_rent', methods: ['GET', 'POST'])]
public function rent(int $id, TerrainRepository $terrainRepository): Response
{
    $terrain = $terrainRepository->find($id);

    if (!$terrain) {
        throw $this->createNotFoundException('Terrain non trouvé.');
    }

    if (!$terrain->isDisponibilite()) {
        $this->addFlash('error', 'Ce terrain n\'est pas disponible à la location.');
        return $this->redirectToRoute('app_terrain_index');
    }

    $location = new Location();
    $location->setTerrain($terrain);
    $location->setDateDebut(new \DateTime());
    $location->setDateFin((new \DateTime())->modify('+1 month'));
    $location->setPrixTotal($terrain->getPrixLocation());
    $location->setModePaiement('Non spécifié');

    $this->entityManager->persist($location);
    $this->entityManager->flush();

    $terrain->setDisponibilite(false);
    $this->entityManager->flush();

    $this->addFlash('success', 'Le terrain a été loué avec succès !');

    return $this->redirectToRoute('app_location_index', [], Response::HTTP_SEE_OTHER);
}

}