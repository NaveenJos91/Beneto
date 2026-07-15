<?php

namespace App\Entity;

use App\Repository\AnnonceRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: AnnonceRepository::class)]
class Annonce
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 150)]
    #[Assert\NotBlank(message: 'Le titre est obligatoire.')]
    #[Assert\Length(max: 150, maxMessage: 'Le titre ne peut pas dépasser {{ limit }} caractères.')]
    private ?string $titre = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Assert\NotBlank(message: 'La description est obligatoire.')]
    #[Assert\Length(min: 10, minMessage: 'Décrivez votre annonce en au moins {{ limit }} caractères.')]
    private ?string $description = null;

    // Statut de modération : en_attente, validee, refusee
    #[ORM\Column(length: 20)]
    private string $statut = 'en_attente';

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\ManyToOne(inversedBy: 'annonces')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $auteur = null;

    #[ORM\ManyToOne(inversedBy: 'annonces')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotNull(message: 'Choisissez une thématique.')]
    private ?Theme $theme = null;

    #[ORM\ManyToOne(inversedBy: 'annonces')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotNull(message: 'Choisissez une ville.')]
    private ?Ville $ville = null;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
    }

    public function getId(): ?int { return $this->id; }

    public function getTitre(): ?string { return $this->titre; }
    public function setTitre(string $titre): self { $this->titre = $titre; return $this; }

    public function getDescription(): ?string { return $this->description; }
    public function setDescription(string $description): self { $this->description = $description; return $this; }

    public function getStatut(): string { return $this->statut; }
    public function setStatut(string $statut): self { $this->statut = $statut; return $this; }

    public function getCreatedAt(): ?\DateTimeImmutable { return $this->createdAt; }

    public function getAuteur(): ?User { return $this->auteur; }
    public function setAuteur(?User $auteur): self { $this->auteur = $auteur; return $this; }

    public function getTheme(): ?Theme { return $this->theme; }
    public function setTheme(?Theme $theme): self { $this->theme = $theme; return $this; }

    public function getVille(): ?Ville { return $this->ville; }
    public function setVille(?Ville $ville): self { $this->ville = $ville; return $this; }
}
