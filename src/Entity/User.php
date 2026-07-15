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

/**
 * Une "entité", c'est une classe PHP qui représente une table de la base de données.
 * Ici, User représente la table "users" : chaque objet User = une ligne (un utilisateur).
 * Les #[ORM\...] au-dessus des propriétés indiquent à Doctrine comment les enregistrer en base.
 *
 * On précise que la table s'appelle "users", et que l'email doit être UNIQUE
 * (deux personnes ne peuvent pas avoir le même email).
 */
#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: 'users')]
#[UniqueEntity(fields: ['email'], message: 'Un compte existe déjà avec cet email.')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    // L'identifiant unique de chaque utilisateur (clé primaire), généré automatiquement.
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    // L'email : sert d'identifiant de connexion. Il est unique et doit être un email valide.
    #[ORM\Column(length: 180, unique: true)]
    #[Assert\NotBlank(message: 'Veuillez saisir un email.')]
    #[Assert\Email(message: 'Cet email n\'est pas valide.')]
    private ?string $email = null;

    // Les rôles de l'utilisateur, stockés en JSON (une liste : apprenant, bénévole, admin...).
    // Le format liste permet à une même personne d'avoir plusieurs rôles.
    #[ORM\Column(type: Types::JSON)]
    private array $roles = [];

    // Le mot de passe (stocké HACHÉ, jamais en clair).
    #[ORM\Column]
    private ?string $password = null;

    #[ORM\Column(length: 100)]
    #[Assert\NotBlank(message: 'Veuillez saisir votre prénom.')]
    private ?string $prenom = null;

    #[ORM\Column(length: 100)]
    #[Assert\NotBlank(message: 'Veuillez saisir votre nom.')]
    private ?string $nom = null;

    // Lien vers la ville de l'utilisateur. ManyToOne = plusieurs users peuvent habiter la même ville.
    // (Pas de "nullable: false" ici, donc la ville est facultative.)
    #[ORM\ManyToOne]
    private ?Ville $ville = null;

    // La date de création du compte (remplie automatiquement à l'inscription).
    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private ?\DateTimeImmutable $createdAt = null;

    // La liste des annonces publiées par cet utilisateur.
    // OneToMany = un utilisateur peut avoir plusieurs annonces.
    #[ORM\OneToMany(mappedBy: 'auteur', targetEntity: Annonce::class)]
    private Collection $annonces;

    // Le constructeur : appelé quand on crée un nouvel utilisateur.
    public function __construct()
    {
        // On initialise la liste d'annonces (vide au départ) et la date de création (maintenant).
        $this->annonces = new ArrayCollection();
        $this->createdAt = new \DateTimeImmutable();
    }

    public function getId(): ?int { return $this->id; }

    public function getEmail(): ?string { return $this->email; }
    public function setEmail(string $email): self { $this->email = $email; return $this; }

    /**
     * Symfony a besoin de savoir ce qui identifie l'utilisateur pour la connexion.
     * Ici, c'est l'email.
     */
    public function getUserIdentifier(): string { return (string) $this->email; }

    /**
     * Renvoie les rôles de l'utilisateur.
     * On ajoute toujours ROLE_USER : tout utilisateur connecté a au minimum ce rôle de base.
     * array_unique évite les doublons.
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        $roles[] = 'ROLE_USER';
        return array_unique($roles);
    }
    public function setRoles(array $roles): self { $this->roles = $roles; return $this; }

    /**
     * Ajoute un rôle à l'utilisateur (par exemple devenir bénévole en plus d'apprenant),
     * sans le mettre deux fois s'il l'a déjà.
     */
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
     * Sert à effacer des données sensibles temporaires après la connexion.
     * Ici on n'en a pas, donc la méthode est vide (mais Symfony l'exige).
     */
    public function eraseCredentials(): void {}
}