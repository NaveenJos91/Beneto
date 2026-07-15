<?php

namespace App\Controller;

use App\Entity\Annonce;
use App\Repository\AnnonceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

/**
 * Ce contrôleur gère l'espace d'administration : la modération des annonces.
 *
 * Le #[IsGranted('ROLE_ADMIN')] placé ici, au-dessus de la CLASSE, protège
 * TOUTES les pages de ce contrôleur d'un coup : il faut être administrateur
 * pour accéder à n'importe laquelle. C'est plus pratique et plus sûr que de
 * le répéter sur chaque méthode.
 */
#[IsGranted('ROLE_ADMIN')]
class AdminController extends AbstractController
{
    // La page de modération : /admin/annonces
    // Elle affiche les annonces en attente (à traiter) et celles déjà validées.
    #[Route('/admin/annonces', name: 'admin_annonces')]
    public function annonces(AnnonceRepository $annonceRepo): Response
    {
        return $this->render('admin/annonces.html.twig', [
            // findBy = on récupère les annonces selon leur statut, triées par date.
            'enAttente' => $annonceRepo->findBy(['statut' => 'en_attente'], ['createdAt' => 'DESC']),
            'validees' => $annonceRepo->findBy(['statut' => 'validee'], ['createdAt' => 'DESC']),
        ]);
    }

    // Valider une annonce : /admin/annonce/{id}/valider
    // methods: ['POST'] = ça ne peut se faire qu'en envoyant le formulaire (bouton "Valider"), pas via un simple lien.
    #[Route('/admin/annonce/{id}/valider', name: 'admin_annonce_valider', methods: ['POST'], requirements: ['id' => '\d+'])]
    public function valider(Annonce $annonce, Request $request, EntityManagerInterface $em): Response
    {
        // Vérification du jeton CSRF (protection contre les fausses requêtes venant d'un autre site).
        if ($this->isCsrfTokenValid('moderate_'.$annonce->getId(), (string) $request->request->get('_token'))) {
            // On passe l'annonce en "validee" : elle devient alors visible dans la recherche publique.
            $annonce->setStatut('validee');
            $em->flush(); // on enregistre le changement
            $this->addFlash('success', 'Annonce validée et publiée.');
        }

        return $this->redirectToRoute('admin_annonces');
    }

    // Refuser une annonce : même principe, mais on passe le statut à "refusee".
    #[Route('/admin/annonce/{id}/refuser', name: 'admin_annonce_refuser', methods: ['POST'], requirements: ['id' => '\d+'])]
    public function refuser(Annonce $annonce, Request $request, EntityManagerInterface $em): Response
    {
        if ($this->isCsrfTokenValid('moderate_'.$annonce->getId(), (string) $request->request->get('_token'))) {
            $annonce->setStatut('refusee');
            $em->flush();
            $this->addFlash('success', 'Annonce refusée.');
        }

        return $this->redirectToRoute('admin_annonces');
    }
}