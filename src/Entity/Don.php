<?php

namespace App\Entity;

use App\Repository\DonRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: DonRepository::class)]
class Don
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $donateur = null;

    #[ORM\Column(type: Types::TEXT)] // Changed from length: 255 to TEXT to support max: 500
    #[Assert\Length(
        min: 50,
        max: 500,
        minMessage: "La description doit contenir au moins {{ limit }} caractères.",
        maxMessage: "La description ne doit pas dépasser {{ limit }} caractères."
    )]
    private ?string $description = null;

    public function __construct()
    {
        $this->date = new \DateTime(); // Initialise avec la date du jour
    }

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $date = null;

    #[ORM\ManyToOne(inversedBy: 'dons')]
    private ?Evenemment $evenement = null;

    #[ORM\ManyToOne(inversedBy: 'dons')]
    private ?Utilisateur $dons_user = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDonateur(): ?string
    {
        return $this->donateur;
    }

    public function setDonateur(string $donateur): static
    {
        $this->donateur = $donateur;
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

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): static
    {
        $this->date = $date;
        return $this;
    }

    public function getEvenement(): ?Evenemment
    {
        return $this->evenement;
    }

    public function setEvenement(?Evenemment $evenement): static
    {
        $this->evenement = $evenement;
        return $this;
    }

    public function getDonsUser(): ?Utilisateur
    {
        return $this->dons_user;
    }

    public function setDonsUser(?Utilisateur $dons_user): static
    {
        $this->dons_user = $dons_user;
        return $this;
    }
}