<?php

namespace App\Entity;

use App\Repository\VilleRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: VilleRepository::class)]
class Ville
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    private ?string $nom = null;

    #[ORM\Column(length: 10)]
    private ?string $codePostal = null;

    #[ORM\OneToMany(mappedBy: 'ville', targetEntity: Annonce::class)]
    private Collection $annonces;

    public function __construct()
    {
        $this->annonces = new ArrayCollection();
    }

    public function getId(): ?int { return $this->id; }

    public function getNom(): ?string { return $this->nom; }
    public function setNom(string $nom): self { $this->nom = $nom; return $this; }

    public function getCodePostal(): ?string { return $this->codePostal; }
    public function setCodePostal(string $codePostal): self { $this->codePostal = $codePostal; return $this; }

    /** @return Collection<int, Annonce> */
    public function getAnnonces(): Collection { return $this->annonces; }
}
