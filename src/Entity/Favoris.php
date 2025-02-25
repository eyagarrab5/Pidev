<?php

namespace App\Entity;

use App\Repository\FavorisRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: FavorisRepository::class)]
class Favoris
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'favoris')]
    private ?DemandeCovoiturage $demande = null;

    #[ORM\Column]
    private ?int $id_passager = null;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getIdPassager(): ?int
    {
        return $this->id_passager;
    }

    public function setIdPassager(int $id_passager): static
    {
        $this->id_passager = $id_passager;

        return $this;
    }
}
