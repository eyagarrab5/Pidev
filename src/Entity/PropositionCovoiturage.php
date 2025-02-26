<?php

namespace App\Entity;
use Symfony\Component\Validator\Constraints as Assert;
use App\Enum\StatutProposition;
use App\Repository\PropositionCovoiturageRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\DBAL\Types\Types;


#[ORM\Entity(repositoryClass: PropositionCovoiturageRepository::class)]
class PropositionCovoiturage
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $conducteur_id = null;

    
    #[ORM\Column(enumType: StatutProposition::class)]
    private StatutProposition $statut;

    #[ORM\ManyToOne(inversedBy: 'propositionCovoiturages')]
    private ?DemandeCovoiturage $demande = null;
    #[ORM\Column]
    #[Assert\Positive(message: 'Le nombre de places doit être supérieur à zéro.')]
    #[Assert\LessThanOrEqual(value: 8, message: 'Le nombre de places ne peut pas dépasser 8.')]
    private ?int $placesDispo = null;
    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $createdAt = null;
    
    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): static
    {
        $this->id = $id;

        return $this;
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
    public function getConducteurId(): ?int
    {
        return $this->conducteur_id;
    }

    public function __construct()
    {
        $this->createdAt = new \DateTime();
    }
    
    public function setConducteurId(int $conducteur_id): static
    {
        $this->conducteur_id = $conducteur_id;

        return $this;
    }

    

    public function getStatut(): ?StatutProposition
    {
        return $this->statut;
    }

    public function setStatut(StatutProposition $statut): static
    {
        $this->statut = $statut;

        return $this;
    }

    public function getDemande(): ?DemandeCovoiturage
    {
        return $this->demande;
    }

    public function setDemande(?DemandeCovoiturage $demande): static
    {
        $this->demande = $demande;

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
}
