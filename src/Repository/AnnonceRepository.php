<?php

namespace App\Repository;

use App\Entity\Annonce;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Un "repository", c'est la classe qui va chercher des données dans la base.
 * Celui-ci s'occupe des annonces. C'est ici qu'on écrit la recherche par thème et par ville,
 * qui est la fonctionnalité clé de Bénéto.
 *
 * @extends ServiceEntityRepository<Annonce>
 */
class AnnonceRepository extends ServiceEntityRepository
{
    // Le constructeur relie ce repository à l'entité Annonce (il sait qu'il travaille sur les annonces).
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Annonce::class);
    }

    /**
     * Recherche les annonces selon un thème et/ou une ville.
     * Les deux paramètres sont optionnels (le "?" veut dire qu'ils peuvent être null = non fournis).
     *
     * @return Annonce[]  (renvoie une liste d'annonces)
     */
    public function rechercher(?int $themeId, ?int $villeId): array
    {
        // createQueryBuilder = on construit la requête petit à petit ('a' est un surnom pour "annonce").
        $qb = $this->createQueryBuilder('a')
            // On ne veut QUE les annonces validées (pas celles en attente ou refusées).
            // ":statut" est un PARAMÈTRE : on met "validee" dedans juste après, séparément.
            ->andWhere('a.statut = :statut')
            ->setParameter('statut', 'validee')
            // On trie de la plus récente à la plus ancienne.
            ->orderBy('a.createdAt', 'DESC');

        // Si un thème est demandé, on ajoute un filtre sur le thème.
        if ($themeId) {
            $qb->andWhere('a.theme = :theme')->setParameter('theme', $themeId);
        }
        // Pareil pour la ville.
        if ($villeId) {
            $qb->andWhere('a.ville = :ville')->setParameter('ville', $villeId);
        }

        // getQuery()->getResult() = on lance la requête et on récupère les annonces trouvées.
        return $qb->getQuery()->getResult();
    }
}