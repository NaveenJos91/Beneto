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

/**
 * Ce contrôleur gère tout ce qui concerne les annonces :
 * les afficher, les rechercher, en créer, les modifier et les supprimer.
 * Un "contrôleur" reçoit la demande de l'utilisateur, fait le travail, et renvoie une page.
 */
class AnnonceController extends AbstractController
{
    // La route, c'est l'adresse de la page. Ici : /annonces
    // Cette page est publique : elle affiche les annonces et permet de filtrer par thème et ville.
    #[Route('/annonces', name: 'annonce_index')]
    public function index(Request $request, AnnonceRepository $annonceRepo, ThemeRepository $themeRepo, VilleRepository $villeRepo): Response
    {
        // On récupère ce que l'utilisateur a choisi dans les filtres (dans l'adresse : ?theme=...&ville=...)
        $themeId = $request->query->get('theme');
        $villeId = $request->query->get('ville');

        // On demande au "repository" (la classe qui va chercher dans la base) les annonces qui correspondent.
        // Si aucun filtre n'est choisi, on passe null et il renvoie toutes les annonces validées.
        $annonces = $annonceRepo->rechercher(
            $themeId !== null && $themeId !== '' ? (int) $themeId : null,
            $villeId !== null && $villeId !== '' ? (int) $villeId : null
        );

        // On envoie les données à la page (le template Twig) qui va afficher le HTML.
        return $this->render('annonce/index.html.twig', [
            'annonces' => $annonces,
            'themes' => $themeRepo->findAll(),   // pour remplir la liste déroulante des thèmes
            'villes' => $villeRepo->findAll(),   // pour remplir la liste déroulante des villes
            'selectedTheme' => $themeId,         // pour garder le filtre sélectionné à l'écran
            'selectedVille' => $villeId,
        ]);
    }

    // Page "Mes annonces" : la liste des annonces du bénévole connecté.
    // #[IsGranted('ROLE_BENEVOLE')] = il faut être bénévole pour accéder à cette page.
    #[Route('/mes-annonces', name: 'annonce_mine')]
    #[IsGranted('ROLE_BENEVOLE')]
    public function mine(AnnonceRepository $annonceRepo): Response
    {
        // findBy = on cherche les annonces dont l'auteur est l'utilisateur connecté, triées par date décroissante.
        $annonces = $annonceRepo->findBy(['auteur' => $this->getUser()], ['createdAt' => 'DESC']);

        return $this->render('annonce/mine.html.twig', ['annonces' => $annonces]);
    }

    // Créer une nouvelle annonce (réservé aux bénévoles).
    #[Route('/annonce/nouvelle', name: 'annonce_new')]
    #[IsGranted('ROLE_BENEVOLE')]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        // On crée une annonce vide, puis on construit le formulaire lié à cette annonce.
        $annonce = new Annonce();
        $form = $this->createForm(AnnonceType::class, $annonce);
        // handleRequest lit les données envoyées par l'utilisateur et les met dans l'annonce.
        $form->handleRequest($request);

        // Si le formulaire a été envoyé ET que les données sont valides (titre rempli, etc.)...
        if ($form->isSubmitted() && $form->isValid()) {
            $annonce->setAuteur($this->getUser());   // l'auteur = l'utilisateur connecté
            $annonce->setStatut('en_attente');       // par défaut, l'annonce attend la validation d'un admin

            // persist + flush = on enregistre l'annonce dans la base de données (MySQL).
            $em->persist($annonce);
            $em->flush();

            // addFlash = un petit message de confirmation affiché à l'utilisateur.
            $this->addFlash('success', 'Votre annonce a été créée. Elle sera visible après validation par un administrateur.');

            // On renvoie l'utilisateur vers sa liste d'annonces.
            return $this->redirectToRoute('annonce_mine');
        }

        // Si le formulaire n'est pas encore envoyé (ou invalide), on réaffiche la page avec le formulaire.
        return $this->render('annonce/new.html.twig', ['form' => $form->createView()]);
    }

    // Afficher le détail d'une annonce.
    // {id} dans l'adresse = le numéro de l'annonce. requirements \d+ = ce numéro doit être des chiffres.
    // Symfony va automatiquement chercher l'annonce correspondante dans la base.
    #[Route('/annonce/{id}', name: 'annonce_show', requirements: ['id' => '\d+'])]
    public function show(Annonce $annonce): Response
    {
        // Sécurité : une annonce pas encore validée ne doit être visible que par son auteur ou un admin.
        $user = $this->getUser();
        $estProprietaire = $user && $annonce->getAuteur() === $user;
        if ($annonce->getStatut() !== 'validee' && !$estProprietaire && !$this->isGranted('ROLE_ADMIN')) {
            // Sinon, on renvoie une erreur "page introuvable" (404).
            throw $this->createNotFoundException('Annonce introuvable.');
        }

        return $this->render('annonce/show.html.twig', ['annonce' => $annonce]);
    }

    // Modifier une annonce (seul son auteur peut le faire).
    #[Route('/annonce/{id}/modifier', name: 'annonce_edit', requirements: ['id' => '\d+'])]
    #[IsGranted('ROLE_BENEVOLE')]
    public function edit(Annonce $annonce, Request $request, EntityManagerInterface $em): Response
    {
        // Sécurité : on vérifie que l'utilisateur connecté est bien l'auteur de l'annonce.
        if ($annonce->getAuteur() !== $this->getUser()) {
            // Sinon, accès refusé (erreur 403).
            throw $this->createAccessDeniedException('Vous ne pouvez modifier que vos propres annonces.');
        }

        // On réutilise le même formulaire que pour la création, mais pré-rempli avec l'annonce existante.
        $form = $this->createForm(AnnonceType::class, $annonce);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Après modification, l'annonce repasse "en attente" pour être revalidée par un admin.
            $annonce->setStatut('en_attente');
            // Ici pas besoin de persist : l'annonce existe déjà, flush suffit à enregistrer les changements.
            $em->flush();
            $this->addFlash('success', 'Annonce modifiée. Elle repassera en validation.');

            return $this->redirectToRoute('annonce_mine');
        }

        return $this->render('annonce/edit.html.twig', ['form' => $form->createView(), 'annonce' => $annonce]);
    }

    // Supprimer une annonce (son auteur OU un admin).
    // methods: ['POST'] = on ne peut supprimer qu'en envoyant un formulaire, pas juste en visitant un lien (plus sûr).
    #[Route('/annonce/{id}/supprimer', name: 'annonce_delete', methods: ['POST'], requirements: ['id' => '\d+'])]
    #[IsGranted('ROLE_USER')]
    public function delete(Annonce $annonce, Request $request, EntityManagerInterface $em): Response
    {
        // Sécurité : seul l'auteur ou un admin peut supprimer.
        $estProprietaire = $annonce->getAuteur() === $this->getUser();
        if (!$estProprietaire && !$this->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException('Action non autorisée.');
        }

        // Vérification du jeton CSRF : c'est une protection contre les fausses requêtes.
        // Ça garantit que la demande de suppression vient bien de notre formulaire, pas d'un site malveillant.
        if ($this->isCsrfTokenValid('delete_annonce_'.$annonce->getId(), (string) $request->request->get('_token'))) {
            // remove + flush = on supprime l'annonce de la base.
            $em->remove($annonce);
            $em->flush();
            $this->addFlash('success', 'Annonce supprimée.');
        }

        return $this->redirectToRoute('annonce_mine');
    }
}