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

class RegistrationController extends AbstractController
{
    #[Route('/register', name: 'app_register')]
    public function register(
        Request $request,
        UserPasswordHasherInterface $passwordHasher,
        EntityManagerInterface $entityManager,
        ValidatorInterface $validator,
        SluggerInterface $slugger // Add SluggerInterface for generating safe filenames
    ): Response {
        $user = new Utilisateur();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

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

                // Save the user to the database
                $entityManager->persist($user);
                $entityManager->flush();

                $this->addFlash('success', 'Registration successful! You can now log in.');

                return $this->redirectToRoute('app_login');
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