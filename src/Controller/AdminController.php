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

#[IsGranted('ROLE_ADMIN')]
class AdminController extends AbstractController
{
    // Tableau de modération : annonces en attente
    #[Route('/admin/annonces', name: 'admin_annonces')]
    public function annonces(AnnonceRepository $annonceRepo): Response
    {
        return $this->render('admin/annonces.html.twig', [
            'enAttente' => $annonceRepo->findBy(['statut' => 'en_attente'], ['createdAt' => 'DESC']),
            'validees' => $annonceRepo->findBy(['statut' => 'validee'], ['createdAt' => 'DESC']),
        ]);
    }

    #[Route('/admin/annonce/{id}/valider', name: 'admin_annonce_valider', methods: ['POST'], requirements: ['id' => '\d+'])]
    public function valider(Annonce $annonce, Request $request, EntityManagerInterface $em): Response
    {
        if ($this->isCsrfTokenValid('moderate_'.$annonce->getId(), (string) $request->request->get('_token'))) {
            $annonce->setStatut('validee');
            $em->flush();
            $this->addFlash('success', 'Annonce validée et publiée.');
        }

        return $this->redirectToRoute('admin_annonces');
    }

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
