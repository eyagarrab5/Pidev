<?php

namespace App\Entity;

use App\Enum\StatutReservation;
use App\Repository\ReservationRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\DBAL\Types\Types;

#[ORM\Entity(repositoryClass: ReservationRepository::class)]
class Reservation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $passager_id = null;


    #[ORM\Column(enumType: StatutReservation::class)]
    private ?StatutReservation $statut = null;

    #[ORM\ManyToOne(inversedBy: 'reservations')]
    private ?OffreCovoiturage $offre = null;
    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $createdAt = null;
    
    
    public function __construct()
    {
        $this->createdAt = new \DateTime();
    }
    
    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }
    
    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;
        return $this;
    }
    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): static
    {
        $this->id = $id;

        return $this;
    }

    public function getPassagerId(): ?int
    {
        return $this->passager_id;
    }

    public function setPassagerId(int $passager_id): static
    {
        $this->passager_id = $passager_id;

        return $this;
    }

   

    public function getStatut(): ?StatutReservation
    {
        return $this->statut;
    }

    public function setStatut(StatutReservation $statut): static
    {
        $this->statut = $statut;

        return $this;
    }

    public function getOffre(): ?OffreCovoiturage
    {
        return $this->offre;
    }

    public function setOffre(?OffreCovoiturage $offre): static
    {
        $this->offre = $offre;

        return $this;
    }
}
