<?php

namespace App\Tests\Entity;

use App\Entity\Annonce;
use App\Entity\Theme;
use App\Entity\User;
use App\Entity\Ville;
use PHPUnit\Framework\TestCase;

/**
 * Tests unitaires : on teste les entités isolément (sans base de données).
 */
class EntityTest extends TestCase
{
    public function testUnUtilisateurADeBaseLeRoleUser(): void
    {
        $user = new User();
        // Même sans rôle défini, tout utilisateur doit avoir ROLE_USER
        $this->assertContains('ROLE_USER', $user->getRoles());
    }

    public function testAjouterUnRoleFonctionne(): void
    {
        $user = new User();
        $user->addRole('ROLE_BENEVOLE');
        $this->assertContains('ROLE_BENEVOLE', $user->getRoles());
        // ROLE_USER doit toujours être présent en plus
        $this->assertContains('ROLE_USER', $user->getRoles());
    }

    public function testUneNouvelleAnnonceEstEnAttente(): void
    {
        $annonce = new Annonce();
        // Par défaut, une annonce doit être "en_attente" de validation
        $this->assertSame('en_attente', $annonce->getStatut());
    }

    public function testLesRelationsDeLAnnonceFonctionnent(): void
    {
        $user  = (new User())->setPrenom('Naveen')->setNom('Joseph');
        $theme = (new Theme())->setNom('Mathématiques');
        $ville = (new Ville())->setNom('Palaiseau')->setCodePostal('91120');

        $annonce = (new Annonce())
            ->setTitre('Aide en maths')
            ->setDescription('Cours de soutien le soir.')
            ->setAuteur($user)
            ->setTheme($theme)
            ->setVille($ville);

        $this->assertSame('Aide en maths', $annonce->getTitre());
        $this->assertSame($user, $annonce->getAuteur());
        $this->assertSame('Mathématiques', $annonce->getTheme()->getNom());
        $this->assertSame('Palaiseau', $annonce->getVille()->getNom());
    }
}