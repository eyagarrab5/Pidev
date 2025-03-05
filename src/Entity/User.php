<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, unique: true)]
    private ?string $email = null;

    #[ORM\Column(length: 255)]
    private ?string $password_hash = null;

    #[ORM\Column(length: 50)]
    private ?string $role = 'ROLE_USER';
    #[ORM\Column(type: 'boolean')]
private bool $banned = false;

    #[ORM\Column(length: 255)]
    private ?string $auth_method = 'email';

    #[ORM\Column]
    private ?bool $verified = false;
    
    


   

    #[ORM\Column(length: 255, nullable: true)]

    #[Assert\Url(message: "L'URL de l'image doit être une URL valide.")]
    #[Assert\Regex(
        pattern: "/^https:\/\/unsplash\.com\/.*$/",
        message: "L'image doit provenir du site Unsplash (ex: https://unsplash.com/...)."
    )]
    private ?string $image = null;

    #[ORM\Column(type: "string", length: 20, nullable: true)]
    #[Assert\Regex(
        pattern: "/^[A-Z]{2}-\d{3}-[A-Z]{2}$/",
        message: "Le numéro de véhicule doit être au format valide (ex: AB-123-CD)."
    )]
    private ?string $vehicule = null;

    #[ORM\Column(type: "string", length: 20, nullable: true)]
    #[Assert\Regex(
        pattern: "/^\+216\d{8}$/",
        message: "Le numéro de téléphone doit commencer par +216 et contenir 8 chiffres supplémentaires (ex: +21612345678)."
    )]
    private ?string $telephone = '+216'; // Valeur par défaut

   
    #[ORM\Column(type: 'string', length: 255)]
    #[Assert\NotBlank(message: "Le prénom est obligatoire.")]
    #[Assert\Length(
        min: 2,
        max: 255,
        minMessage: "Le prénom doit contenir au moins {{ limit }} caractères.",
        maxMessage: "Le prénom ne doit pas dépasser {{ limit }} caractères."
    )]
    #[Assert\Regex(
        pattern: "/^[a-zA-ZÀ-ÿ\s\-']+$/",
        message: "Le prénom ne peut contenir que des lettres, des espaces, des tirets et des apostrophes."
    )]
    private ?string $first_name = null;

    #[ORM\Column(type: 'string', length: 255)]
    #[Assert\NotBlank(message: "Le nom de famille est obligatoire.")]
    #[Assert\Length(
        min: 2,
        max: 255,
        minMessage: "Le nom de famille doit contenir au moins {{ limit }} caractères.",
        maxMessage: "Le nom de famille ne doit pas dépasser {{ limit }} caractères."
    )]
    #[Assert\Regex(
        pattern: "/^[a-zA-ZÀ-ÿ\s\-']+$/",
        message: "Le nom de famille ne peut contenir que des lettres, des espaces, des tirets et des apostrophes."
    )]
    private ?string $last_name = null; 

    
    public function getFirstName(): ?string
    {
        return $this->first_name;
    }

    public function setFirstName(?string $first_name): self
    {
        $this->first_name = $first_name;
        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->last_name;
    }

    public function setLastName(?string $last_name): self
    {
        $this->last_name = $last_name;
        return $this;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }
    public function isBanned(): bool
    {
        return $this->banned;
    }
    
    public function setBanned(bool $banned): self
    {
        $this->banned = $banned;
        return $this;
    }
    public function getPasswordHash(): ?string
    {
        return $this->password_hash;
    }

    public function setPasswordHash(string $password_hash): static
    {
        $this->password_hash = $password_hash;

        return $this;
    }

    public function getRole(): ?string
    {
        return $this->role;
    }

    public function setRole(string $role): static
    {
        $this->role = $role;

        return $this;
    }

    public function getAuthMethod(): ?string
    {
        return $this->auth_method;
    }

    public function setAuthMethod(string $auth_method): static
    {
        $this->auth_method = $auth_method;

        return $this;
    }

    public function isVerified(): ?bool
    {
        return $this->verified;
    }

    public function setVerified(bool $verified): static
    {
        $this->verified = $verified;

        return $this;
    }
    
    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(?string $image): static
    {
        $this->image = $image;

        return $this;
    }

    public function getTelephone(): ?string
    {
        return $this->telephone;
    }

    public function setTelephone(?string $telephone): static
    {
        $this->telephone = $telephone;

        return $this;
    }

    public function getVehicule(): ?string
    {
        return $this->vehicule;
    }

    public function setVehicule(?string $vehicule): static
    {
        $this->vehicule = $vehicule;

        return $this;
    }

    public function getRoles(): array
    {
        return [$this->role];
    }

    public function eraseCredentials(): void
    {
        // Si vous stockez des données sensibles temporaires sur $this, effacez-les ici
    }

    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    public function getPassword(): ?string
    {
        return $this->password_hash;
    }
}