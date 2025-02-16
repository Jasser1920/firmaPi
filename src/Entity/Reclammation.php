<?php

namespace App\Entity;

use App\Enum\StatutReclammation;
use App\Repository\ReclammationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ReclammationRepository::class)]
class Reclammation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $description = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $date_creation = null;

    #[ORM\Column(type: 'string', enumType: StatutReclammation::class)]
    private StatutReclammation $statut;

    #[ORM\ManyToOne(inversedBy: 'reclamation')]
    private ?Utilisateur $utilisateur = null;

    #[ORM\OneToMany(targetEntity: ReponseReclamation::class, mappedBy: 'reclamation')]
    private Collection $reponseReclamations;

    public function __construct()
    {
        $this->date_creation = new \DateTime();
        $this->statut = StatutReclammation::EN_ATTENTE;
        $this->reponseReclamations = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getDateCreation(): ?\DateTimeInterface
    {
        return $this->date_creation;
    }

    public function setDateCreation(\DateTimeInterface $date_creation): static
    {
        $this->date_creation = $date_creation;
        return $this;
    }

    public function getStatut(): StatutReclammation
    {
        return $this->statut;
    }

    public function getStatutAsString(): string
    {
        return $this->statut->value;
    }

    public function setStatut(StatutReclammation $statut): static
    {
        $this->statut = $statut;
        return $this;
    }

    public function getUtilisateur(): ?Utilisateur
    {
        return $this->utilisateur;
    }

    public function setUtilisateur(?Utilisateur $utilisateur): static
    {
        $this->utilisateur = $utilisateur;
        return $this;
    }

    public function getReponseReclamations(): Collection
    {
        return $this->reponseReclamations;
    }
}
