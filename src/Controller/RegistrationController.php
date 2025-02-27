<?php

namespace App\Controller;

use App\Entity\Utilisateur;
use App\Form\RegistrationFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;

class RegistrationController extends AbstractController
{
    #[Route('/register', name: 'app_register')]
    public function register(
        Request $request,
        UserPasswordHasherInterface $passwordHasher,
        EntityManagerInterface $entityManager,
        ValidatorInterface $validator,
        SluggerInterface $slugger,
        MailerInterface $mailer // Add MailerInterface for sending emails
    ): Response {
        $user = new Utilisateur();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);
        $request->getSession()->set('verify_email', $user->getEmail());
        if ($form->isSubmitted()) {
            $errors = $validator->validate($user);

            if ($form->isValid() && count($errors) === 0) {
                // Handle profile picture upload
                /** @var UploadedFile $profilePictureFile */
                $profilePictureFile = $form->get('profilePicture')->getData();

                if ($profilePictureFile) {
                    $originalFilename = pathinfo($profilePictureFile->getClientOriginalName(), PATHINFO_FILENAME);
                    $safeFilename = $slugger->slug($originalFilename); // Generate a safe filename
                    $newFilename = $safeFilename . '-' . uniqid() . '.' . $profilePictureFile->guessExtension();

                    try {
                        $profilePictureFile->move(
                            $this->getParameter('profile_picture_directory'), // Ensure this parameter is defined in services.yaml
                            $newFilename
                        );
                    } catch (FileException $e) {
                        $this->addFlash('error', 'There was an error uploading your profile picture.');
                        return $this->redirectToRoute('app_register');
                    }

                    // Save the filename to the user entity
                    $user->setProfilePicture($newFilename);
                }

                // Hash the password
                $user->setMotdepasse(
                    $passwordHasher->hashPassword(
                        $user,
                        $form->get('plainPassword')->getData()
                    )
                );

                // Set the user as not verified
                $user->setIsVerified(false);

                // Generate a confirmation code
                $confirmationCode = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
                $user->setConfirmationCode($confirmationCode);

                // Save the user to the database
                $entityManager->persist($user);
                $entityManager->flush();

                // Send the confirmation email
                $email = (new TemplatedEmail())
                    ->from('firmaprojectpi@gmail.com')
                    ->to($user->getEmail())
                    ->subject('Your Email Confirmation Code')
                    ->htmlTemplate('emails/confirmation_code.html.twig')
                    ->context(['code' => $confirmationCode, 'user' => $user]);

                try {
                    $mailer->send($email);
                } catch (\Exception $e) {
                    $this->addFlash('error', 'Failed to send confirmation email. Please try again later.');
                    return $this->redirectToRoute('app_register');
                }

                // Redirect to the verification page
                $this->addFlash('success', 'A confirmation code has been sent to your email. Please verify your email to complete registration.');
                return $this->redirectToRoute('app_verify_code');
            } else {
                foreach ($errors as $error) {
                    $this->addFlash('error', $error->getMessage());
                }
            }
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }
}