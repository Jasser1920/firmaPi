<?php

namespace App\Controller;

use App\Entity\Utilisateur;
use App\Repository\UtilisateurRepository;
use App\Form\ProfileEditType;
use App\Form\VerifyCodeType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;

class ProfileController extends AbstractController
{
    #[Route('/profile/edit', name: 'app_profile_edit')]
    public function editProfile(
        Request $request,
        EntityManagerInterface $entityManager,
        SluggerInterface $slugger,
        MailerInterface $mailer
    ): Response {
        /** @var Utilisateur|null $user */
        $user = $this->getUser();
        if (!$user) {
            throw $this->createAccessDeniedException('You must be logged in to edit your profile.');
        }

        $originalEmail = $user->getEmail();
        $form = $this->createForm(ProfileEditType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $profilePictureFile = $form->get('profilePicture')->getData();
            if ($profilePictureFile) {
                $originalFilename = pathinfo($profilePictureFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $profilePictureFile->guessExtension();

                try {
                    $profilePictureFile->move(
                        $this->getParameter('profile_picture_directory'),
                        $newFilename
                    );
                    $user->setProfilePicture($newFilename);
                } catch (FileException $e) {
                    $this->addFlash('error', 'There was an error uploading your profile picture.');
                    return $this->redirectToRoute('app_profile_edit');
                }
            }

            $newEmail = $user->getEmail();
            if ($originalEmail !== $newEmail) {
                $confirmationCode = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
                $user->setConfirmationCode($confirmationCode);
                $user->setIsVerified(false);

                $email = (new TemplatedEmail())
                    ->from('firmaprojectpi@gmail.com')
                    ->to($newEmail)
                    ->subject('Your Email Confirmation Code')
                    ->htmlTemplate('emails/confirmation_code.html.twig')
                    ->context(['code' => $confirmationCode, 'user' => $user]);
                try {
                    $mailer->send($email);
                } catch (\Exception $e) {
                    $this->addFlash('error', 'Failed to send confirmation email. Please try again later.');
                    return $this->redirectToRoute('app_profile_edit');
                }

                $entityManager->persist($user);
                $entityManager->flush();

                $this->addFlash('success', 'A confirmation code has been sent to your new email.');
                return $this->redirectToRoute('app_verify_code');
            }

            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash('success', 'Profile updated successfully!');
            return $this->redirectToRoute('app_profiledetails');
        }

        return $this->render('security/profile_edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/verify/code', name: 'app_verify_code')]
    public function verifyCode(
        Request $request,
        EntityManagerInterface $entityManager,
        UtilisateurRepository $utilisateurRepository
    ): Response {
        // If the user is logged in, use their session
        $user = $this->getUser();

        // If the user is not logged in, check for an email in the session
        if (!$user) {
            $email = $request->getSession()->get('verify_email');
            if (!$email) {
                $this->addFlash('error', 'No verification session found. Please register again.');
                return $this->redirectToRoute('app_register');
            }

            // Fetch the user by email
            $user = $utilisateurRepository->findOneBy(['email' => $email]);
            if (!$user) {
                $this->addFlash('error', 'User not found. Please register again.');
                return $this->redirectToRoute('app_register');
            }
        }

        $form = $this->createForm(VerifyCodeType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $submittedCode = $form->get('code')->getData();

            if ($submittedCode === $user->getConfirmationCode()) {
                $user->setIsVerified(true);
                $user->setConfirmationCode(null);
                $entityManager->persist($user);
                $entityManager->flush();

                $this->addFlash('success', 'Your email has been verified!');
                return $this->redirectToRoute('app_login'); // Redirect to login after verification
            } else {
                $this->addFlash('error', 'Invalid confirmation code.');
            }
        }

        return $this->render('profile/verify_code.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}