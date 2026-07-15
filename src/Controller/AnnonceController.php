<?php

namespace App\Controller;

use App\Entity\Annonce;
use App\Form\AnnonceType;
use App\Repository\AnnonceRepository;
use App\Repository\ThemeRepository;
use App\Repository\VilleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class AnnonceController extends AbstractController
{
    // Recherche publique par thème et par ville (annonces validées uniquement)
    #[Route('/annonces', name: 'annonce_index')]
    public function index(Request $request, AnnonceRepository $annonceRepo, ThemeRepository $themeRepo, VilleRepository $villeRepo): Response
    {
        $themeId = $request->query->get('theme');
        $villeId = $request->query->get('ville');

        $annonces = $annonceRepo->rechercher(
            $themeId !== null && $themeId !== '' ? (int) $themeId : null,
            $villeId !== null && $villeId !== '' ? (int) $villeId : null
        );

        return $this->render('annonce/index.html.twig', [
            'annonces' => $annonces,
            'themes' => $themeRepo->findAll(),
            'villes' => $villeRepo->findAll(),
            'selectedTheme' => $themeId,
            'selectedVille' => $villeId,
        ]);
    }

    // Les annonces du bénévole connecté
    #[Route('/mes-annonces', name: 'annonce_mine')]
    #[IsGranted('ROLE_BENEVOLE')]
    public function mine(AnnonceRepository $annonceRepo): Response
    {
        $annonces = $annonceRepo->findBy(['auteur' => $this->getUser()], ['createdAt' => 'DESC']);

        return $this->render('annonce/mine.html.twig', ['annonces' => $annonces]);
    }

    // Créer une annonce (réservé aux bénévoles)
    #[Route('/annonce/nouvelle', name: 'annonce_new')]
    #[IsGranted('ROLE_BENEVOLE')]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $annonce = new Annonce();
        $form = $this->createForm(AnnonceType::class, $annonce);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $annonce->setAuteur($this->getUser());
            $annonce->setStatut('en_attente'); // en attente de validation par un admin
            $em->persist($annonce);
            $em->flush();
            $this->addFlash('success', 'Votre annonce a été créée. Elle sera visible après validation par un administrateur.');

            return $this->redirectToRoute('annonce_mine');
        }

        return $this->render('annonce/new.html.twig', ['form' => $form->createView()]);
    }

    // Détail d'une annonce
    #[Route('/annonce/{id}', name: 'annonce_show', requirements: ['id' => '\d+'])]
    public function show(Annonce $annonce): Response
    {
        // Une annonce non validée n'est visible que par son auteur ou un admin
        $user = $this->getUser();
        $estProprietaire = $user && $annonce->getAuteur() === $user;
        if ($annonce->getStatut() !== 'validee' && !$estProprietaire && !$this->isGranted('ROLE_ADMIN')) {
            throw $this->createNotFoundException('Annonce introuvable.');
        }

        return $this->render('annonce/show.html.twig', ['annonce' => $annonce]);
    }

    // Modifier une annonce (auteur uniquement)
    #[Route('/annonce/{id}/modifier', name: 'annonce_edit', requirements: ['id' => '\d+'])]
    #[IsGranted('ROLE_BENEVOLE')]
    public function edit(Annonce $annonce, Request $request, EntityManagerInterface $em): Response
    {
        if ($annonce->getAuteur() !== $this->getUser()) {
            throw $this->createAccessDeniedException('Vous ne pouvez modifier que vos propres annonces.');
        }

        $form = $this->createForm(AnnonceType::class, $annonce);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $annonce->setStatut('en_attente'); // repasse en validation après modification
            $em->flush();
            $this->addFlash('success', 'Annonce modifiée. Elle repassera en validation.');

            return $this->redirectToRoute('annonce_mine');
        }

        return $this->render('annonce/edit.html.twig', ['form' => $form->createView(), 'annonce' => $annonce]);
    }

    // Supprimer une annonce (auteur ou admin)
    #[Route('/annonce/{id}/supprimer', name: 'annonce_delete', methods: ['POST'], requirements: ['id' => '\d+'])]
    #[IsGranted('ROLE_USER')]
    public function delete(Annonce $annonce, Request $request, EntityManagerInterface $em): Response
    {
        $estProprietaire = $annonce->getAuteur() === $this->getUser();
        if (!$estProprietaire && !$this->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException('Action non autorisée.');
        }

        if ($this->isCsrfTokenValid('delete_annonce_'.$annonce->getId(), (string) $request->request->get('_token'))) {
            $em->remove($annonce);
            $em->flush();
            $this->addFlash('success', 'Annonce supprimée.');
        }

        return $this->redirectToRoute('annonce_mine');
    }
}
