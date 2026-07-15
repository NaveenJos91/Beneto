<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

/**
 * Ce contrôleur gère la connexion et la déconnexion.
 * Point important : ce n'est PAS ce code qui vérifie le mot de passe.
 * C'est Symfony (son système de sécurité) qui s'en occupe tout seul en coulisses.
 * Ici, on se contente d'afficher la page de connexion et de récupérer une éventuelle erreur.
 */
class SecurityController extends AbstractController
{
    // Adresse de la page de connexion : /connexion
    #[Route('/connexion', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // Si la dernière tentative de connexion a échoué, on récupère l'erreur pour l'afficher
        // (par exemple "identifiants invalides").
        $error = $authenticationUtils->getLastAuthenticationError();

        // On récupère le dernier email saisi, pour le réafficher dans le champ
        // (comme ça l'utilisateur n'a pas à le retaper après une erreur).
        $lastUsername = $authenticationUtils->getLastUsername();

        // On affiche la page de connexion, en lui passant l'email et l'éventuelle erreur.
        return $this->render('security/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
        ]);
    }

    // Adresse de la déconnexion : /deconnexion
    #[Route('/deconnexion', name: 'app_logout')]
    public function logout(): void
    {
        // Ce code n'est jamais vraiment exécuté : la déconnexion est gérée automatiquement
        // par le "firewall" (le pare-feu de sécurité) de Symfony, configuré dans security.yaml.
        // Cette méthode doit quand même exister pour que la route /deconnexion fonctionne.
        throw new \LogicException('Cette méthode est gérée par le firewall de Symfony.');
    }
}