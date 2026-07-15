<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function index(): Response
    {
        // On envoie quelques données au template, comme le fera plus tard la vraie page d'accueil.
        return $this->render('home/index.html.twig', [
            'nom_projet' => 'Bénéto',
            'slogan' => 'La plateforme de tutorat bénévole local',
        ]);
    }
}
