<?php
namespace App\Controller\Admin;

use App\Entity\Reclammation;
use App\enum\Role;
use App\Form\ReponseReclamationType;
use App\Enum\StatutReclammation;
use App\Entity\ReponseReclamation;
use App\Repository\ReclammationRepository;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ReclammationCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Reclammation::class;
    }

    public function configureActions(Actions $actions): Actions
    {
        $actions->disable(Action::NEW);
        $actions->disable(Action::EDIT);
        // Reply Action
        $replyAction = Action::new('reply', 'Reply', 'fa fa-reply')
            ->linkToRoute('admin_reclamation_reply', function (Reclammation $reclamation): array {
                return ['id' => $reclamation->getId()];
            })
            ->setCssClass('btn btn-primary');

        // Resolved Action
        $resolvedAction = Action::new('resolved', 'Mark as Resolved', 'fa fa-check')
            ->linkToRoute('admin_reclamation_resolved', function (Reclammation $reclamation): array {
                return ['id' => $reclamation->getId()];
            })
            ->setCssClass('btn btn-success');

        // Rejected Action
        $rejectedAction = Action::new('rejected', 'Mark as Rejected', 'fa fa-times')
            ->linkToRoute('admin_reclamation_rejected', function (Reclammation $reclamation): array {
                return ['id' => $reclamation->getId()];
            })
            ->setCssClass('btn btn-danger');

        return $actions
            ->add(Crud::PAGE_INDEX, $replyAction)
            ->add(Crud::PAGE_INDEX, $resolvedAction)
            ->add(Crud::PAGE_INDEX, $rejectedAction)
            ->add(Crud::PAGE_DETAIL, $replyAction)
            ->add(Crud::PAGE_DETAIL, $resolvedAction)
            ->add(Crud::PAGE_DETAIL, $rejectedAction);
    }

    #[Route('/admin/reclamation/{id}/reply', name: 'admin_reclamation_reply')]
    public function replyReclamation(Request $request, AdminContext $context, EntityManagerInterface $entityManager, ReclammationRepository $reclammationRepository, int $id): Response
    {
        // Fetch the reclamation manually if AdminContext fails
        $reclamation = $reclammationRepository->find($id);

        if (!$reclamation) {
            $this->addFlash('danger', 'Reclamation not found!');
            return $this->redirectToRoute('admin_dashboard', [
                'entity' => 'Reclammation',
                'action' => 'index',
            ]);
        }

        // Create the response entity
        $reply = new ReponseReclamation();
        $reply->setReclamation($reclamation);
        $reply->setDateReponse(new \DateTime());

        // Create the form
        $form = $this->createForm(ReponseReclamationType::class, $reply);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Persist and save the reply
            $entityManager->persist($reply);

            // Update the status of the reclamation to "En cours"
            $reclamation->setStatut(StatutReclammation::EN_COURS);

            // Save the changes to the reclamation
            $entityManager->persist($reclamation);

            // Flush both changes (reply and status update)
            $entityManager->flush();

            $this->addFlash('success', 'Reply sent successfully and status updated to "En cours".');

            return $this->redirectToRoute('admin_dashboard', [
                'entity' => 'Reclammation',
                'action' => 'detail',
                'id' => $reclamation->getId(),
            ]);
        }

        // Render the form in the Twig template
        return $this->render('reponse_reclamation/reply_reclamation.html.twig', [
            'form' => $form->createView(),
            'reclamation' => $reclamation,
        ]);
    }

    #[Route('/admin/reclamation/{id}/resolved', name: 'admin_reclamation_resolved')]
    public function markAsResolved(EntityManagerInterface $entityManager, ReclammationRepository $reclammationRepository, int $id): Response
    {
        $reclamation = $reclammationRepository->find($id);

        if (!$reclamation) {
            $this->addFlash('danger', 'Reclamation not found!');
            return $this->redirectToRoute('admin_dashboard', [
                'entity' => 'Reclammation',
                'action' => 'index',
            ]);
        }

        // Update the status to "Résolue"
        $reclamation->setStatut(StatutReclammation::RESOLUE);
        $entityManager->persist($reclamation);
        $entityManager->flush();

        $this->addFlash('success', 'Reclamation marked as resolved.');

        return $this->redirectToRoute('admin_dashboard', [
            'entity' => 'Reclammation',
            'action' => 'detail',
            'id' => $reclamation->getId(),
        ]);
    }

    #[Route('/admin/reclamation/{id}/rejected', name: 'admin_reclamation_rejected')]
    public function markAsRejected(EntityManagerInterface $entityManager, ReclammationRepository $reclammationRepository, int $id): Response
    {
        $reclamation = $reclammationRepository->find($id);

        if (!$reclamation) {
            $this->addFlash('danger', 'Reclamation not found!');
            return $this->redirectToRoute('admin_dashboard', [
                'entity' => 'Reclammation',
                'action' => 'index',
            ]);
        }

        // Update the status to "Rejetée"
        $reclamation->setStatut(StatutReclammation::REJETEE);
        $entityManager->persist($reclamation);
        $entityManager->flush();

        $this->addFlash('success', 'Reclamation marked as rejected.');

        return $this->redirectToRoute('admin_dashboard', [
            'entity' => 'Reclammation',
            'action' => 'detail',
            'id' => $reclamation->getId(),
        ]);
    }

    public function configureFields(string $pageName): iterable
{
    $fields = [
        IdField::new('id')->hideOnForm(),
        TextEditorField::new('description')->onlyOnIndex(),
        ChoiceField::new('statut')
            ->setChoices([
                'En attente' => StatutReclammation::EN_ATTENTE,
                'En cours' => StatutReclammation::EN_COURS,
                'Résolue' => StatutReclammation::RESOLUE,
                'Rejetée' => StatutReclammation::REJETEE,
            ])
            ->formatValue(function ($value) {
                $colors = [
                    StatutReclammation::EN_ATTENTE->value => '<span class="badge bg-primary">En attente</span>',
                    StatutReclammation::EN_COURS->value => '<span class="badge bg-secondary">En cours</span>',
                    StatutReclammation::RESOLUE->value => '<span class="badge bg-success">Résolue</span>',
                    StatutReclammation::REJETEE->value => '<span class="badge bg-danger">Rejetée</span>',
                ];
                return $colors[$value->value] ?? $value->value;
            })->setCustomOption('renderAsHtml', true),
        AssociationField::new('utilisateur')
            ->formatValue(function ($value) {
                return $value ? $value->getPrenom() . ' ' . $value->getNom() : 'No user';
            }),
            // Corrected 'role' field for the Role enum, read-only, visible on index/detail
        ChoiceField::new('utilisateur.role')
        ->setLabel('Rôle')
        ->setChoices(function () {
            // Use enum cases and their string values as both keys and values
            return array_column(Role::cases(), 'value', 'value');
        })
        ->formatValue(function ($value) {
            // $value is a Role enum object, so use its properties directly
            if ($value instanceof Role) {
                $colors = [
                    'admin' => '<span >ADMIN</span>',
                    'agriculture' => '<span >AGRICULTURE</span>',
                    'client' => '<span >CLIENT</span>',
                    'association' => '<span >ASSOCIATION</span>',
                ];
                return $colors[$value->value] ?? $value->label();
            }
            return 'No role specified'; 
        })
        ->setCustomOption('renderAsHtml', true)
        ->hideOnForm(),
        
    ];

    // Add a custom field to show replies on the index page
    if ($pageName === Crud::PAGE_INDEX) {
        $fields[] = TextField::new('repliesSummary', 'Replies')
            ->formatValue(function ($value, $entity) {
                $replies = $entity->getReponseReclamations();
                if ($replies->isEmpty()) {
                    return 'No replies yet.';
                }
                $summary = [];
                foreach ($replies as $reply) {
                    $summary[] = $reply->getMessage() . ' (' . $reply->getDateReponse()->format('Y-m-d H:i:s') . ')';
                }
                return implode('<br>', $summary);
            })
            ->setCustomOption('renderAsHtml', true);
    }

    // Add the detailed replies field to the detail page
    if ($pageName === Crud::PAGE_DETAIL) {
        $fields[] = AssociationField::new('reponseReclamations', 'Replies')
            ->setTemplatePath('reponse_reclamation/replies.html.twig')
            ->onlyOnDetail();
    }

    return $fields;
}
}