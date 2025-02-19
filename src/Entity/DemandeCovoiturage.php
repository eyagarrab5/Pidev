<?php
namespace App\Entity;

use App\Enum\StatutDemande;
use App\Repository\DemandeCovoiturageRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: DemandeCovoiturageRepository::class)]
class DemandeCovoiturage
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    #[Assert\NotBlank(message: "L'ID du passager ne peut pas être vide.")]
    private ?int $passager_id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Le lieu de départ ne peut pas être vide.")]
    private ?string $depart = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "La destination ne peut pas être vide.")]
    private ?string $destination = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Assert\NotBlank(message: "La date ne peut pas être vide.")]
    #[Assert\GreaterThan("today", message: "La date doit être supérieure à la date actuelle.")]
    private ?\DateTimeInterface $date = null;

    #[ORM\Column(enumType: StatutDemande::class)]
    private ?StatutDemande $statut = null;

    #[ORM\Column]
#[Assert\NotBlank(message: "Le budget ne peut pas être vide.")]
#[Assert\GreaterThan(value: 0, message: "Le budget doit être supérieur à zéro.")]
private ?float $budget = null;

    /**
     * @var Collection<int, PropositionCovoiturage>
     */
    #[ORM\OneToMany(targetEntity: PropositionCovoiturage::class, mappedBy: 'demande', cascade: ['remove'])]
    private Collection $propositionCovoiturages;

    public function __construct()
    {

        $this->date = new \DateTime(); // Date/heure actuelle par défaut

        $this->propositionCovoiturages = new ArrayCollection();
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

    public function getDepart(): ?string
    {
        return $this->depart;
    }

    public function setDepart(string $depart): static
    {
        $this->depart = $depart;
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

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

   

    // In DemandeCovoiturage entity
public function setDate(?\DateTimeInterface $date): static
{
    $this->date = $date;
    return $this;
}
    public function getStatut(): ?StatutDemande
    {
        return $this->statut;
    }

    public function setStatut(StatutDemande $statut): static
    {
        $this->statut = $statut;
        return $this;
    }

    public function getBudget(): ?float
    {
        return $this->budget;
    }

    public function setBudget(float $budget): static
    {
       
        $this->budget = $budget;
        return $this;
    }

    /**
     * @return Collection<int, PropositionCovoiturage>
     */
    public function getPropositionCovoiturages(): Collection
    {
        return $this->propositionCovoiturages;
    }

    public function addPropositionCovoiturage(PropositionCovoiturage $propositionCovoiturage): static
    {
        if (!$this->propositionCovoiturages->contains($propositionCovoiturage)) {
            $this->propositionCovoiturages->add($propositionCovoiturage);
            $propositionCovoiturage->setDemande($this);
        }

        return $this;
    }

    public function removePropositionCovoiturage(PropositionCovoiturage $propositionCovoiturage): static
    {
        if ($this->propositionCovoiturages->removeElement($propositionCovoiturage)) {
            if ($propositionCovoiturage->getDemande() === $this) {
                $propositionCovoiturage->setDemande(null);
            }
        }

        return $this;
    }
}
