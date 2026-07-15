<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class ProfileController extends AbstractController
{
    #[Route('/profil', name: 'app_profile')]
    #[IsGranted('ROLE_USER')]
    public function index(): Response
    {
        return $this->render('profile/index.html.twig');
    }

    #[Route('/profil/ajouter-role/{role}', name: 'app_profile_add_role')]
    #[IsGranted('ROLE_USER')]
    public function addRole(string $role, EntityManagerInterface $em): Response
    {
        $autorises = ['ROLE_APPRENANT', 'ROLE_BENEVOLE'];
        /** @var User $user */
        $user = $this->getUser();

        if (in_array($role, $autorises, true)) {
            $user->addRole($role);
            $em->flush();
            $this->addFlash('success', 'Rôle ajouté. Reconnectez-vous pour l\'activer pleinement.');
        }

        return $this->redirectToRoute('app_profile');
    }

    // Droit à l'effacement (RGPD) : l'utilisateur supprime lui-même son compte
    #[Route('/profil/supprimer', name: 'app_profile_delete', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function delete(Request $request, EntityManagerInterface $em): Response
    {
        if ($this->isCsrfTokenValid('delete_account', (string) $request->request->get('_token'))) {
            /** @var User $user */
            $user = $this->getUser();
            $managed = $em->getRepository(User::class)->find($user->getId());
            if ($managed) {
                $em->remove($managed);
                $em->flush();
            }
            // Invalide la session : l'utilisateur est déconnecté
            $request->getSession()->invalidate();
            $this->addFlash('success', 'Votre compte et vos données ont été supprimés.');
        }

        return $this->redirectToRoute('home');
    }
}
