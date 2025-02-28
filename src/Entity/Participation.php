<?php

namespace App\Entity;

use App\Repository\ParticipationRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ParticipationRepository::class)]
class Participation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'participations')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Utilisateur $user = null;

    #[ORM\ManyToOne(inversedBy: 'participations')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Evenemment $evenement = null;

    #[ORM\Column(type: 'datetime')]
    private ?\DateTimeInterface $participationDate = null;

    public function __construct()
    {
        $this->participationDate = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?Utilisateur
    {
        return $this->user;
    }

    public function setUser(?Utilisateur $user): static
    {
        $this->user = $user;
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

    public function getParticipationDate(): ?\DateTimeInterface
    {
        return $this->participationDate;
    }

    public function setParticipationDate(\DateTimeInterface $participationDate): static
    {
        $this->participationDate = $participationDate;
        return $this;
    }
}