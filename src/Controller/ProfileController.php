<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

/**
 * Ce contrôleur gère le profil de l'utilisateur connecté :
 * voir son profil, ajouter un rôle, et supprimer son compte (droit RGPD).
 */
class ProfileController extends AbstractController
{
    // Page "Mon profil" : /profil
    // #[IsGranted('ROLE_USER')] = il faut être connecté pour y accéder.
    #[Route('/profil', name: 'app_profile')]
    #[IsGranted('ROLE_USER')]
    public function index(): Response
    {
        // On affiche simplement la page de profil (les infos viennent de l'utilisateur connecté, dans le template).
        return $this->render('profile/index.html.twig');
    }

    // Ajouter un rôle à son compte (ex : devenir aussi bénévole en plus d'apprenant).
    // {role} = le rôle à ajouter, passé dans l'adresse.
    #[Route('/profil/ajouter-role/{role}', name: 'app_profile_add_role')]
    #[IsGranted('ROLE_USER')]
    public function addRole(string $role, EntityManagerInterface $em): Response
    {
        // Sécurité : on n'autorise QUE ces deux rôles. Impossible de s'auto-attribuer ROLE_ADMIN par l'adresse.
        $autorises = ['ROLE_APPRENANT', 'ROLE_BENEVOLE'];
        /** @var User $user */
        $user = $this->getUser(); // l'utilisateur connecté

        // Si le rôle demandé fait bien partie des rôles autorisés...
        if (in_array($role, $autorises, true)) {
            $user->addRole($role);   // on ajoute le rôle
            $em->flush();            // on enregistre en base
            $this->addFlash('success', 'Rôle ajouté. Reconnectez-vous pour l\'activer pleinement.');
        }

        return $this->redirectToRoute('app_profile');
    }

    // Supprimer son propre compte — c'est le "droit à l'effacement" du RGPD.
    // methods: ['POST'] = uniquement via le formulaire de suppression (pas un simple lien).
    #[Route('/profil/supprimer', name: 'app_profile_delete', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function delete(Request $request, EntityManagerInterface $em): Response
    {
        // Vérification du jeton CSRF (protection contre les fausses requêtes).
        if ($this->isCsrfTokenValid('delete_account', (string) $request->request->get('_token'))) {
            /** @var User $user */
            $user = $this->getUser();

            // On récupère l'utilisateur "géré" par Doctrine à partir de son id, puis on le supprime.
            $managed = $em->getRepository(User::class)->find($user->getId());
            if ($managed) {
                $em->remove($managed); // suppression de l'utilisateur
                $em->flush();          // on applique en base
            }

            // On invalide la session : comme le compte n'existe plus, on déconnecte la personne.
            $request->getSession()->invalidate();
            $this->addFlash('success', 'Votre compte et vos données ont été supprimés.');
        }

        // On renvoie vers la page d'accueil.
        return $this->redirectToRoute('home');
    }
}