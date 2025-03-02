<?php
namespace App\Controller\Admin;

use App\Entity\Reclammation;
use App\Enum\Role;
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
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Twilio\Rest\Client;

class ReclammationCrudController extends AbstractCrudController
{
    private $twilio;

    public function __construct(Client $twilio)
    {
        $this->twilio = $twilio;
    }

    public static function getEntityFqcn(): string
    {
        return Reclammation::class;
    }

    public function configureActions(Actions $actions): Actions
    {
        $actions->disable(Action::NEW);
        $actions->disable(Action::EDIT);

        $replyAction = Action::new('reply', 'Reply', 'fa fa-reply')
            ->linkToRoute('admin_reclamation_reply', fn(Reclammation $reclamation): array => ['id' => $reclamation->getId()])
            ->setCssClass('btn btn-primary');

        $resolvedAction = Action::new('resolved', 'Mark as Resolved', 'fa fa-check')
            ->linkToRoute('admin_reclamation_resolved', fn(Reclammation $reclamation): array => ['id' => $reclamation->getId()])
            ->setCssClass('btn btn-success');

        $rejectedAction = Action::new('rejected', 'Mark as Rejected', 'fa fa-times')
            ->linkToRoute('admin_reclamation_rejected', fn(Reclammation $reclamation): array => ['id' => $reclamation->getId()])
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
    public function replyReclamation(Request $request, EntityManagerInterface $entityManager, ReclammationRepository $reclammationRepository, int $id): Response
    {
        $reclamation = $reclammationRepository->find($id);

        if (!$reclamation) {
            $this->addFlash('danger', 'Reclamation not found!');
            return $this->redirectToRoute('admin_dashboard', ['entity' => 'Reclammation', 'action' => 'index']);
        }

        $reply = new ReponseReclamation();
        $reply->setReclamation($reclamation);
        $reply->setDateReponse(new \DateTime());

        $form = $this->createForm(ReponseReclamationType::class, $reply);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($reply);
            $reclamation->setStatut(StatutReclammation::EN_COURS);
            $entityManager->persist($reclamation);
            $entityManager->flush();

            $this->sendSmsNotification($reclamation, "Your reclamation is now 'En cours'. Reply: " . $reply->getMessage());
            $this->addFlash('success', 'Reply sent successfully and status updated to "En cours".');

            return $this->redirectToRoute('admin_dashboard', ['entity' => 'Reclammation', 'action' => 'detail', 'id' => $reclamation->getId()]);
        }

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
            return $this->redirectToRoute('admin_dashboard', ['entity' => 'Reclammation', 'action' => 'index']);
        }

        $reclamation->setStatut(StatutReclammation::RESOLUE);
        $entityManager->persist($reclamation);
        $entityManager->flush();

        $this->sendSmsNotification($reclamation, "Your reclamation has been marked as 'Résolue'.");
        $this->addFlash('success', 'Reclamation marked as resolved.');

        return $this->redirectToRoute('admin_dashboard', ['entity' => 'Reclammation', 'action' => 'detail', 'id' => $reclamation->getId()]);
    }

    #[Route('/admin/reclamation/{id}/rejected', name: 'admin_reclamation_rejected')]
    public function markAsRejected(EntityManagerInterface $entityManager, ReclammationRepository $reclammationRepository, int $id): Response
    {
        $reclamation = $reclammationRepository->find($id);

        if (!$reclamation) {
            $this->addFlash('danger', 'Reclamation not found!');
            return $this->redirectToRoute('admin_dashboard', ['entity' => 'Reclammation', 'action' => 'index']);
        }

        $reclamation->setStatut(StatutReclammation::REJETEE);
        $entityManager->persist($reclamation);
        $entityManager->flush();

        $this->sendSmsNotification($reclamation, "Your reclamation has been marked as 'Rejetée'.");
        $this->addFlash('success', 'Reclamation marked as rejected.');

        return $this->redirectToRoute('admin_dashboard', ['entity' => 'Reclammation', 'action' => 'detail', 'id' => $reclamation->getId()]);
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
                ->formatValue(fn($value) => $value ? $value->getPrenom() . ' ' . $value->getNom() : 'No user'),
            ChoiceField::new('utilisateur.role')
                ->setLabel('Rôle')
                ->setChoices(fn() => array_column(Role::cases(), 'value', 'value'))
                ->formatValue(function ($value) {
                    if ($value instanceof Role) {
                        $colors = [
                            'admin' => '<span>ADMIN</span>',
                            'agriculture' => '<span>AGRICULTURE</span>',
                            'client' => '<span>CLIENT</span>',
                            'association' => '<span>ASSOCIATION</span>',
                        ];
                        return $colors[$value->value] ?? $value->label();
                    }
                    return 'No role specified';
                })
                ->setCustomOption('renderAsHtml', true)
                ->hideOnForm(),
        ];

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

        if ($pageName === Crud::PAGE_DETAIL) {
            $fields[] = AssociationField::new('reponseReclamations', 'Replies')
                ->setTemplatePath('reponse_reclamation/replies.html.twig')
                ->onlyOnDetail();
        }

        return $fields;
    }

    private function sendSmsNotification(Reclammation $reclamation, string $message): void
    {
        $user = $reclamation->getUtilisateur();
        if ($user && method_exists($user, 'getTelephone') && $user->getTelephone()) {
            $this->twilio->messages->create(
                $user->getTelephone(),
                [
                    'from' => $_ENV['TWILIO_PHONE_NUMBER'],
                    'body' => $message,
                ]
            );
        }
    }
}