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

#[AsCommand(name: 'app:seed', description: 'Insère les villes et thèmes de départ')]
class SeedCommand extends Command
{
    public function __construct(private EntityManagerInterface $em)
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $villes = [
            ['Palaiseau', '91120'],
            ['Massy', '91300'],
            ['Évry-Courcouronnes', '91000'],
            ['Versailles', '78000'],
            ['Paris', '75001'],
        ];
        $themes = ['Mathématiques', 'Français', 'Anglais', 'Informatique', 'Physique-Chimie', 'Histoire-Géographie'];

        if ($this->em->getRepository(Ville::class)->count([]) === 0) {
            foreach ($villes as [$nom, $cp]) {
                $v = (new Ville())->setNom($nom)->setCodePostal($cp);
                $this->em->persist($v);
            }
            $io->success(count($villes).' villes ajoutées.');
        } else {
            $io->note('Des villes existent déjà, insertion ignorée.');
        }

        if ($this->em->getRepository(Theme::class)->count([]) === 0) {
            foreach ($themes as $nom) {
                $t = (new Theme())->setNom($nom);
                $this->em->persist($t);
            }
            $io->success(count($themes).' thèmes ajoutés.');
        } else {
            $io->note('Des thèmes existent déjà, insertion ignorée.');
        }

        $this->em->flush();
        $io->success('Jeu de données de départ prêt.');

        return Command::SUCCESS;
    }
}
