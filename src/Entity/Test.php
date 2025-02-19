<?php

namespace App\Entity;

use App\Repository\TestRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TestRepository::class)]
class Test
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $testeya = null;

    #[ORM\Column]
    private ?float $testsara = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTesteya(): ?string
    {
        return $this->testeya;
    }

    public function setTesteya(string $testeya): static
    {
        $this->testeya = $testeya;

        return $this;
    }

    public function getTestsara(): ?float
    {
        return $this->testsara;
    }

    public function setTestsara(float $testsara): static
    {
        $this->testsara = $testsara;

        return $this;
    }
}
