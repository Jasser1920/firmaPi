<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use App\Repository\UtilisateurRepository;
use Doctrine\ORM\EntityManagerInterface;

class SecurityController extends AbstractController
{
    #[Route(path: '/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // if ($this->getUser()) {
        //     return $this->redirectToRoute('target_path');
        // }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    #[Route('/reset-password', name: 'app_reset_password')]
    public function resetPassword(
        Request $request,
        UtilisateurRepository $utilisateurRepository,
        MailerInterface $mailer,
        EntityManagerInterface $entityManager
    ): Response {
        $form = $this->createFormBuilder()
            ->add('email', \Symfony\Component\Form\Extension\Core\Type\EmailType::class, [
                'label' => 'Email',
                'attr' => ['class' => 'form-control'],
            ])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $email = $form->get('email')->getData();
            $user = $utilisateurRepository->findOneBy(['email' => $email]);

            if ($user) {
                // Generate a reset code
                $resetCode = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
                $user->setConfirmationCode($resetCode);
                $entityManager->persist($user);
                $entityManager->flush();

                // Send reset email
                $emailMessage = (new TemplatedEmail())
                    ->from('firmaprojectpi@gmail.com')
                    ->to($email)
                    ->subject('Réinitialisation de votre mot de passe')
                    ->htmlTemplate('emails/reset_password.html.twig')
                    ->context(['code' => $resetCode, 'user' => $user]);

                try {
                    $mailer->send($emailMessage);
                    $this->addFlash('success', 'Un code de réinitialisation a été envoyé à votre email.');
                    return $this->redirectToRoute('app_reset_password_confirm', ['email' => $email]);
                } catch (\Exception $e) {
                    $this->addFlash('error', 'Échec de l’envoi de l’email. Veuillez réessayer.');
                }
            } else {
                $this->addFlash('error', 'Aucun compte trouvé avec cet email.');
            }
        }

        return $this->render('security/reset_password.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/reset-password/confirm', name: 'app_reset_password_confirm')]
    public function resetPasswordConfirm(
        Request $request,
        UtilisateurRepository $utilisateurRepository,
        EntityManagerInterface $entityManager
    ): Response {
        $email = $request->query->get('email');
        $user = $utilisateurRepository->findOneBy(['email' => $email]);

        if (!$user) {
            $this->addFlash('error', 'Utilisateur non trouvé. Veuillez recommencer.');
            return $this->redirectToRoute('app_reset_password');
        }

        $form = $this->createFormBuilder()
            ->add('code', \Symfony\Component\Form\Extension\Core\Type\TextType::class, [
                'label' => 'Code de réinitialisation',
                'attr' => ['class' => 'form-control'],
            ])
            ->add('newPassword', \Symfony\Component\Form\Extension\Core\Type\PasswordType::class, [
                'label' => 'Nouveau mot de passe',
                'attr' => ['class' => 'form-control'],
            ])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            if ($data['code'] === $user->getConfirmationCode()) {
                $user->setMotdepasse(password_hash($data['newPassword'], PASSWORD_BCRYPT));
                $user->setConfirmationCode(null);
                $entityManager->persist($user);
                $entityManager->flush();

                $this->addFlash('success', 'Mot de passe réinitialisé avec succès ! Connectez-vous.');
                return $this->redirectToRoute('app_login');
            } else {
                $this->addFlash('error', 'Code de réinitialisation invalide.');
            }
        }

        return $this->render('security/reset_password_confirm.html.twig', [
            'form' => $form->createView(),
            'email' => $email,
        ]);
    }
}