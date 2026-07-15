<?php

namespace App\Command;

use App\Entity\Theme;
use App\Entity\Ville;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * Ceci est une COMMANDE, c'est-à-dire un petit programme qu'on lance dans le terminal
 * (et non une page web). On l'exécute avec : php bin/console app:seed
 *
 * Son rôle : remplir la base avec des données de départ (les villes et les thèmes),
 * pour que l'application soit utilisable dès le début (sinon les listes seraient vides).
 * "seed" veut dire "semer / amorcer" les données.
 */
#[AsCommand(name: 'app:seed', description: 'Insère les villes et thèmes de départ')]
class SeedCommand extends Command
{
    // On récupère l'EntityManager (l'outil qui enregistre en base) pour pouvoir l'utiliser ici.
    public function __construct(private EntityManagerInterface $em)
    {
        parent::__construct();
    }

    // La méthode execute() est ce qui se lance quand on tape la commande.
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        // SymfonyStyle sert à afficher de jolis messages dans le terminal (succès, notes...).
        $io = new SymfonyStyle($input, $output);

        // La liste des villes de départ (nom + code postal).
        $villes = [
            ['Palaiseau', '91120'],
            ['Massy', '91300'],
            ['Évry-Courcouronnes', '91000'],
            ['Versailles', '78000'],
            ['Paris', '75001'],
        ];
        // La liste des thèmes de départ.
        $themes = ['Mathématiques', 'Français', 'Anglais', 'Informatique', 'Physique-Chimie', 'Histoire-Géographie'];

        // On n'ajoute les villes QUE si la table est vide (count === 0).
        // Ça évite de créer des doublons si on relance la commande plusieurs fois.
        if ($this->em->getRepository(Ville::class)->count([]) === 0) {
            foreach ($villes as [$nom, $cp]) {
                $v = (new Ville())->setNom($nom)->setCodePostal($cp);
                $this->em->persist($v); // on prépare l'enregistrement
            }
            $io->success(count($villes).' villes ajoutées.');
        } else {
            $io->note('Des villes existent déjà, insertion ignorée.');
        }

        // Même logique pour les thèmes.
        if ($this->em->getRepository(Theme::class)->count([]) === 0) {
            foreach ($themes as $nom) {
                $t = (new Theme())->setNom($nom);
                $this->em->persist($t);
            }
            $io->success(count($themes).' thèmes ajoutés.');
        } else {
            $io->note('Des thèmes existent déjà, insertion ignorée.');
        }

        // flush() applique réellement tous les enregistrements préparés dans la base.
        $this->em->flush();
        $io->success('Jeu de données de départ prêt.');

        // On indique que la commande s'est bien terminée.
        return Command::SUCCESS;
    }
}