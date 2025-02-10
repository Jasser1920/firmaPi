<?php

namespace App\Entity;

use App\Repository\ReclammationRepository;
use App\Enum\StatutReclammation;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ReclammationRepository::class)]
class Reclammation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $description = null;

    #[ORM\Column(enumType:StatutReclammation::class)]
     private ?StatutReclammation $status = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): static
    {
        $this->id = $id;
        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;
        return $this;
    }

    public function getStatus(): ?StatutReclammation
    {
        return $this->status;
    }

    public function setStatus(StatutReclammation $statusReclammation): self
    {
        $this->status = $statusReclammation;
        return $this;
    }
}
