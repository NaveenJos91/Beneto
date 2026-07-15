<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Ce contrôleur gère l'inscription d'un nouvel utilisateur.
 * C'est ici qu'on crée le compte et qu'on protège le mot de passe.
 */
class RegistrationController extends AbstractController
{
    // Adresse de la page d'inscription : /inscription
    #[Route('/inscription', name: 'app_register')]
    public function register(
        Request $request,
        // On demande à Symfony l'outil qui sait hacher les mots de passe (le "password hasher").
        UserPasswordHasherInterface $passwordHasher,
        // Et l'EntityManager, qui sert à enregistrer dans la base de données.
        EntityManagerInterface $em
    ): Response {
        // On crée un utilisateur vide, puis le formulaire d'inscription lié à cet utilisateur.
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        // handleRequest lit ce que l'utilisateur a saisi dans le formulaire.
        $form->handleRequest($request);

        // Si le formulaire est envoyé ET valide (email correct, mot de passe assez fort, etc.)...
        if ($form->isSubmitted() && $form->isValid()) {

            // ÉTAPE IMPORTANTE : on ne stocke JAMAIS le mot de passe en clair.
            // On le transforme en une suite illisible (un "hash") avec l'algorithme bcrypt.
            // getData() récupère le mot de passe tapé ; hashPassword() le transforme.
            $hashed = $passwordHasher->hashPassword($user, $form->get('plainPassword')->getData());
            $user->setPassword($hashed); // on enregistre le hash, pas le vrai mot de passe

            // On récupère les rôles cochés par l'utilisateur (apprenant et/ou bénévole).
            $user->setRoles($form->get('roles')->getData());

            // persist + flush = on enregistre le nouvel utilisateur dans la base (MySQL).
            $em->persist($user);
            $em->flush();

            // Petit message de confirmation.
            $this->addFlash('success', 'Votre compte a été créé. Vous pouvez vous connecter.');

            // On envoie l'utilisateur vers la page de connexion.
            return $this->redirectToRoute('app_login');
        }

        // Si le formulaire n'est pas encore envoyé (ou invalide), on réaffiche la page d'inscription.
        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }
}