<?php

namespace App\Entity;

use App\Repository\VehiculeRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\UX\Turbo\Attribute\Broadcast;
use Doctrine\common\Collections\ArrayCollection;
use Doctrine\common\collectios\Collection;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: VehiculeRepository::class)]
    
class Vehicule
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Assert\NotBlank(message: "le type de vehicule est obligatoire")]
    #[Assert\Length(
        min: 3,
        max: 255,
        minMessage: "le type de vehicule doit comporter au moins 3 caractéres",
        maxMessage:"le type de vehicule doit comporter au maximum 255 caractere"

    )]
    private ?string $type_vehicule = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Assert\NotBlank(message: "le model de vehicule est obligatoire")]
    #[Assert\Length(
        min: 3,
        max: 255,
        minMessage: "le model de vehicule doit comporter au moins 3 caractéres",
        maxMessage:"le model de vehicule doit comporter au maximum 255 caractere"

    )]
    private ?string $modele = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Assert\NotBlank(message: "le role de vehicule est obligatoire")]
    #[Assert\Length(
        min: 3,
        max: 255,
        minMessage: "le role de vehicule doit comporter au moins 3 caractéres",
        maxMessage:"le role de vehicule doit comporter au maximum 255 caractere"

    )]
    private ?string $role = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Assert\NotBlank(message: "le prix de vehicule est obligatoire")]
    #[Assert\Regex(
    pattern: '/^\d+(\.\d{1,2})?$/',
    message: "le prix de vehicule doit être un nombre décimal"
)]

    private ?string $prix_par_heure = null;

    #[ORM\Column(length: 255, nullable: true)]
    
    #[Assert\NotBlank(message: "le prix de vehicule est obligatoire")]
    #[Assert\Regex(
    pattern: '/^\d+(\.\d{1,2})?$/',
    message: "le prix de vehicule doit être un nombre décimal"
)]

    private ?string $prix_par_jour = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Assert\NotBlank(message: "la disponibilite de vehicule est obligatoire")]
    #[Assert\Length(
        min: 3,
        max: 255,
        minMessage: "la disponibilite de vehicule doit comporter au moins 3 caractéres",
        maxMessage:"la disponibilite de vehicule doit comporter au maximum 255 caractere"

    )]
    private ?string $disponibilite = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Assert\NotBlank(message: "le lieu_retrait de vehicule est obligatoire")]
    #[Assert\Length(
        min: 3,
        max: 255,
        minMessage: "le lieu_retrait de vehicule doit comporter au moins 3 caractéres",
        maxMessage:"le lieu_retrait de vehicule doit comporter au maximum 255 caractere"

    )]
    private ?string $lieu_retrait = null;

    #[ORM\OneToOne(mappedBy: 'id_vehicule', cascade: ['persist', 'remove'])]
    private ?ReservationVehicule $reservationVehicule = null;

    #[ORM\Column(type: 'json')]
    private array $notifications = [];

    #[ORM\Column(type: 'integer', options: ['default' => 0])]
    private int $likes = 0;

    #[ORM\Column(type: 'integer', options: ['default' => 0])]
    private int $dislikes = 0;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTypeVehicule(): ?string
    {
        return $this->type_vehicule;
    }

    public function setTypeVehicule(?string $type_vehicule): static
    {
        $this->type_vehicule = $type_vehicule;

        return $this;
    }

    public function getModele(): ?string
    {
        return $this->modele;
    }

    public function setModele(?string $modele): static
    {
        $this->modele = $modele;

        return $this;
    }

    public function getRole(): ?string
    {
        return $this->role;
    }

    public function setRole(?string $role): static
    {
        $this->role = $role;

        return $this;
    }

    public function getPrixParHeure(): ?string
    {
        return $this->prix_par_heure;
    }

    public function setPrixParHeure(?string $prix_par_heure): static
    {
        $this->prix_par_heure = $prix_par_heure;

        return $this;
    }

    public function getPrixParJour(): ?string
    {
        return $this->prix_par_jour;
    }

    public function setPrixParJour(?string $prix_par_jour): static
    {
        $this->prix_par_jour = $prix_par_jour;

        return $this;
    }

    public function getDisponibilite(): ?string
    {
        return $this->disponibilite;
    }

    public function setDisponibilite(?string $disponibilite): self
    {
        $this->disponibilite = $disponibilite;

        return $this;
    }

    public function getLieuRetrait(): ?string
    {
        return $this->lieu_retrait;
    }

    public function setLieuRetrait(?string $lieu_retrait): static
    {
        $this->lieu_retrait = $lieu_retrait;

        return $this;
    }

    public function getReservationVehicule(): ?ReservationVehicule
    {
        return $this->reservationVehicule;
    }
    #[ORM\Column(type: "boolean", options: ["default" => false])]
    private bool $isPinned = false;

    public function isPinned(): bool
{
    return $this->isPinned;
}

public function setIsPinned(bool $isPinned): self
{
    $this->isPinned = $isPinned;
    return $this;
}

    #[ORM\Column(length: 255, nullable: true)]
private ?string $image = null;

public function getImage(): ?string
{
    return $this->image;
}

public function setImage(?string $image): self
{
    $this->image = $image;
    return $this;
}


    public function setReservationVehicule(?ReservationVehicule $reservationVehicule): static
    {
       // Si la réservation est différente de celle actuellement associée
    if ($this->reservationVehicule !== $reservationVehicule) {
        // Détacher l'ancienne réservation
        if ($this->reservationVehicule !== null) {
            $this->reservationVehicule->setIdVehicule(null);
        }

        // Attacher la nouvelle réservation
        $this->reservationVehicule = $reservationVehicule;

        // Mettre à jour la relation inverse
        if ($reservationVehicule !== null) {
            $reservationVehicule->setIdVehicule($this);
        }
    }

    return $this;
    }

    public function getNotifications(): array
    {
        return $this->notifications;
    }

    public function addNotification(string $message): self
    {
        $this->notifications[] = [
            'message' => $message,
            'createdAt' => (new \DateTime())->format('Y-m-d H:i:s'),
        ];
        return $this;
    }

    public function clearNotifications(): self
    {
        $this->notifications = [];
        return $this;
    }

    
public function getLikes(): int
{
    return $this->likes;
}

public function setLikes(int $likes): self
{
    $this->likes = $likes;
    return $this;
}

public function getDislikes(): int
{
    return $this->dislikes;
}

public function setDislikes(int $dislikes): self
{
    $this->dislikes = $dislikes;
    return $this;
}
}
