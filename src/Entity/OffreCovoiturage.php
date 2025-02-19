<?php

namespace App\Entity;

use App\Enum\StatutOffre;
use App\Repository\OffreCovoiturageRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: OffreCovoiturageRepository::class)]
class OffreCovoiturage
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Le lieu de départ est obligatoire.")]
    #[Assert\Length(
        max: 255,
        maxMessage: "Le départ ne peut pas dépasser {{ limit }} caractères."
    )]
    private ?string $depart = null;

    #[ORM\Column]
    #[Assert\NotBlank(message: "L'ID conducteur est obligatoire.")]
    #[Assert\Positive(message: "L'ID conducteur doit être positif.")]
    private ?int $conducteur_id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "La destination est obligatoire.")]
    #[Assert\Length(
        max: 255,
        maxMessage: "La destination ne peut pas dépasser {{ limit }} caractères."
    )]
    private ?string $destination = null;

    #[ORM\Column]
#[Assert\NotBlank(message: "Le matricule du véhicule est obligatoire.")]
#[Assert\Positive(message: "Le matricule doit être un nombre positif.")]
#[Assert\Range(
    min: 10000,    // 5 chiffres minimum
    max: 9999999,  // 7 chiffres maximum
    notInRangeMessage: "Le matricule doit contenir entre 5 et 7 chiffres."
)]
private ?int $matVehicule = null;

    #[ORM\Column]
    #[Assert\NotBlank(message: "Le nombre de places est obligatoire.")]
    #[Assert\Positive(message: "Le nombre de places doit être positif.")]
    private ?int $placesDispo = null;

  
    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Assert\NotBlank(message: "La date ne peut pas être vide.")]
    #[Assert\GreaterThan("today", message: "La date doit être supérieure à la date actuelle.")]
    private ?\DateTimeInterface $date = null;

    #[ORM\Column(enumType: StatutOffre::class)]
    private ?StatutOffre $statut = null;

    #[ORM\OneToMany(targetEntity: Reservation::class, mappedBy: 'offre', cascade: ['remove'])]
    private Collection $reservations;
    
    #[ORM\Column]
    #[Assert\NotBlank(message: "Le prix est obligatoire.")]
    #[Assert\Positive(message: "Le prix doit être positif.")]
    private ?float $prix = null;

    public function __construct()
    {
        $this->date = new \DateTime(); // Date/heure actuelle par défaut

        $this->reservationCovoiturages = new ArrayCollection();
        $this->reservations = new ArrayCollection();
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

    

    public function getDepart(): ?string
    {
        return $this->depart;
    }

    public function setDepart(string $depart): static
    {
        $this->depart = $depart;

        return $this;
    }

    public function getConducteurId(): ?int
    {
        return $this->conducteur_id;
    }

    public function setConducteurId(int $conducteur_id): static
    {
        $this->conducteur_id = $conducteur_id;

        return $this;
    }

    public function getDestination(): ?string
    {
        return $this->destination;
    }

    public function setDestination(string $destination): static
    {
        $this->destination = $destination;

        return $this;
    }

    public function getMatVehicule(): ?int
    {
        return $this->matVehicule;
    }

    public function setMatVehicule(int $matVehicule): static
    {
        $this->matVehicule = $matVehicule;

        return $this;
    }

    public function getPlacesDispo(): ?int
    {
        return $this->placesDispo;
    }

    public function setPlacesDispo(int $placesDispo): static
    {
        $this->placesDispo = $placesDispo;

        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): static
    {
        $this->date = $date;

        return $this;
    }

    public function getStatut(): ?StatutOffre
    {
        return $this->statut;
    }

    public function setStatut(StatutOffre $statut): static
    {
        $this->statut = $statut;

        return $this;
    }

    /**
     * @return Collection<int, ReservationCovoiturage>
     */
    public function getReservationCovoiturages(): Collection
    {
        return $this->reservationCovoiturages;
    }

    public function addReservationCovoiturage(ReservationCovoiturage $reservationCovoiturage): static
    {
        if (!$this->reservationCovoiturages->contains($reservationCovoiturage)) {
            $this->reservationCovoiturages->add($reservationCovoiturage);
            $reservationCovoiturage->setOffre($this);
        }

        return $this;
    }

    public function removeReservationCovoiturage(ReservationCovoiturage $reservationCovoiturage): static
    {
        if ($this->reservationCovoiturages->removeElement($reservationCovoiturage)) {
            // set the owning side to null (unless already changed)
            if ($reservationCovoiturage->getOffre() === $this) {
                $reservationCovoiturage->setOffre(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Reservation>
     */
    public function getReservations(): Collection
    {
        return $this->reservations;
    }

    public function addReservation(Reservation $reservation): static
    {
        if (!$this->reservations->contains($reservation)) {
            $this->reservations->add($reservation);
            $reservation->setOffre($this);
        }

        return $this;
    }

    public function removeReservation(Reservation $reservation): static
    {
        if ($this->reservations->removeElement($reservation)) {
            // set the owning side to null (unless already changed)
            if ($reservation->getOffre() === $this) {
                $reservation->setOffre(null);
            }
        }

        return $this;
    }

    public function getPrix(): ?float
    {
        return $this->prix;
    }

    public function setPrix(float $prix): static
    {
        $this->prix = $prix;

        return $this;
    }
}
