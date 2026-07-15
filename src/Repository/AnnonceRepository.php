<?php

namespace App\Repository;

use App\Entity\Annonce;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Annonce>
 *
 * Recherche d'annonces par thématique et par ville (fonctionnalité clé de Bénéto).
 */
class AnnonceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Annonce::class);
    }

    /**
     * @return Annonce[]
     */
    public function rechercher(?int $themeId, ?int $villeId): array
    {
        $qb = $this->createQueryBuilder('a')
            ->andWhere('a.statut = :statut')
            ->setParameter('statut', 'validee')
            ->orderBy('a.createdAt', 'DESC');

        if ($themeId) {
            $qb->andWhere('a.theme = :theme')->setParameter('theme', $themeId);
        }
        if ($villeId) {
            $qb->andWhere('a.ville = :ville')->setParameter('ville', $villeId);
        }

        return $qb->getQuery()->getResult();
    }
}
