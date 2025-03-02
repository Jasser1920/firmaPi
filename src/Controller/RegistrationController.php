<?php

namespace App\Controller;

use App\Entity\Utilisateur;
use App\Form\RegistrationFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Cloudinary\Cloudinary;

class RegistrationController extends AbstractController
{
    private $cloudinary;

    public function __construct(Cloudinary $cloudinary)
    {
        $this->cloudinary = $cloudinary;
    }

    #[Route('/register', name: 'app_register')]
    public function register(
        Request $request,
        UserPasswordHasherInterface $passwordHasher,
        EntityManagerInterface $entityManager,
        ValidatorInterface $validator,
        MailerInterface $mailer
    ): Response {
        $user = new Utilisateur();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $errors = $validator->validate($user);

            if (count($errors) === 0) {
                // Handle profile picture upload to Cloudinary
                $profilePictureFile = $form->get('profilePicture')->getData();
                if ($profilePictureFile) {
                    try {
                        $publicId = 'user_new_' . uniqid(); // No ID yet, so use 'new'
                        $uploadResult = $this->cloudinary->uploadApi()->upload($profilePictureFile->getPathname(), [
                            'folder' => 'profile_pictures',
                            'public_id' => $publicId,
                            'overwrite' => true,
                            'resource_type' => 'image',
                        ]);
                        $user->setProfilePicture($uploadResult['secure_url']);
                    } catch (\Cloudinary\Api\Exception\ApiError $e) {
                        $this->addFlash('error', 'Failed to upload profile picture to Cloudinary: ' . $e->getMessage());
                        return $this->redirectToRoute('app_register');
                    }
                }

                // Hash the password
                $user->setMotdepasse( // Assuming standard naming; adjust if it's setMotdepasse
                    $passwordHasher->hashPassword(
                        $user,
                        $form->get('plainPassword')->getData()
                    )
                );

                // Set user as not verified and generate confirmation code
                $user->setIsVerified(false);
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
                    $request->getSession()->set('verify_email', $user->getEmail()); // Moved here after validation
                    $this->addFlash('success', 'A confirmation code has been sent to your email. Please verify your email to complete registration.');
                    return $this->redirectToRoute('app_verify_code');
                } catch (\Symfony\Component\Mailer\Exception\TransportExceptionInterface $e) {
                    $this->addFlash('error', 'Failed to send confirmation email: ' . $e->getMessage());
                    return $this->redirectToRoute('app_register');
                }
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