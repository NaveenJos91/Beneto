<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: 'users')]
#[UniqueEntity(fields: ['email'], message: 'Un compte existe déjà avec cet email.')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180, unique: true)]
    #[Assert\NotBlank(message: 'Veuillez saisir un email.')]
    #[Assert\Email(message: 'Cet email n\'est pas valide.')]
    private ?string $email = null;

    #[ORM\Column(type: Types::JSON)]
    private array $roles = [];

    #[ORM\Column]
    private ?string $password = null;

    #[ORM\Column(length: 100)]
    #[Assert\NotBlank(message: 'Veuillez saisir votre prénom.')]
    private ?string $prenom = null;

    #[ORM\Column(length: 100)]
    #[Assert\NotBlank(message: 'Veuillez saisir votre nom.')]
    private ?string $nom = null;

    #[ORM\ManyToOne]
    private ?Ville $ville = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\OneToMany(mappedBy: 'auteur', targetEntity: Annonce::class)]
    private Collection $annonces;

    public function __construct()
    {
        $this->annonces = new ArrayCollection();
        $this->createdAt = new \DateTimeImmutable();
    }

    public function getId(): ?int { return $this->id; }

    public function getEmail(): ?string { return $this->email; }
    public function setEmail(string $email): self { $this->email = $email; return $this; }

    /**
     * Identifiant unique de l'utilisateur pour la sécurité (ici, l'email).
     */
    public function getUserIdentifier(): string { return (string) $this->email; }

    public function getRoles(): array
    {
        $roles = $this->roles;
        $roles[] = 'ROLE_USER'; // tout utilisateur connecté a au moins ce rôle
        return array_unique($roles);
    }
    public function setRoles(array $roles): self { $this->roles = $roles; return $this; }

    public function addRole(string $role): self
    {
        if (!in_array($role, $this->roles, true)) {
            $this->roles[] = $role;
        }
        return $this;
    }

    public function getPassword(): ?string { return $this->password; }
    public function setPassword(string $password): self { $this->password = $password; return $this; }

    public function getPrenom(): ?string { return $this->prenom; }
    public function setPrenom(string $prenom): self { $this->prenom = $prenom; return $this; }

    public function getNom(): ?string { return $this->nom; }
    public function setNom(string $nom): self { $this->nom = $nom; return $this; }

    public function getVille(): ?Ville { return $this->ville; }
    public function setVille(?Ville $ville): self { $this->ville = $ville; return $this; }

    public function getCreatedAt(): ?\DateTimeImmutable { return $this->createdAt; }

    /** @return Collection<int, Annonce> */
    public function getAnnonces(): Collection { return $this->annonces; }

    /**
     * Effacer les données sensibles temporaires (ici rien à effacer).
     */
    public function eraseCredentials(): void {}
}
