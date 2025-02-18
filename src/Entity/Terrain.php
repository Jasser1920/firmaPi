<?php

namespace App\Entity;

use App\Repository\TerrainRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: TerrainRepository::class)]
class Terrain
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "La description est obligatoire.")]
    #[Assert\Length(min: 10, max: 255, minMessage: "La description doit contenir au moins 10 caractères.", maxMessage: "La description ne peut pas dépasser 255 caractères.")]
    private ?string $description = null;

    #[ORM\Column(type: 'float')]
    #[Assert\NotBlank(message: "La superficie est obligatoire.")]
    #[Assert\Positive(message: "La superficie doit être un nombre positif.")]
    #[Assert\Range(min: 1, max: 10000, notInRangeMessage: "La superficie doit être comprise entre 1 et 10 000 m².")]
    private ?float $superficie = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "La localisation est obligatoire.")]
    #[Assert\Length(min: 3, max: 255, minMessage: "La localisation doit contenir au moins 3 caractères.", maxMessage: "La localisation ne peut pas dépasser 255 caractères.")]
    private ?string $localisation = null;

    #[ORM\Column(type: 'float')]
    #[Assert\NotBlank(message: "Le prix de location est obligatoire.")]
    #[Assert\Positive(message: "Le prix de location doit être un nombre positif.")]
    private ?float $prix_location = null;

    #[ORM\Column(type: 'boolean')]
    #[Assert\NotNull(message: "La disponibilité doit être spécifiée.")]
    private ?bool $disponibilite = false;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $date_creation = null;

    #[ORM\ManyToOne(inversedBy: 'terrain')]
    private ?Utilisateur $utilisateur = null;

    #[ORM\OneToMany(targetEntity: Location::class, mappedBy: 'terrain')]
    private Collection $Location;

    public function __construct()
    {
        $this->Location = new ArrayCollection();
    }

    public function getId(): ?int { return $this->id; }
    
    public function getDescription(): ?string { return $this->description; }
    public function setDescription(string $description): static { $this->description = $description; return $this; }

    public function getSuperficie(): ?float { return $this->superficie; }
    public function setSuperficie(float $superficie): static { $this->superficie = $superficie; return $this; }

    public function getLocalisation(): ?string { return $this->localisation; }
    public function setLocalisation(string $localisation): static { $this->localisation = $localisation; return $this; }

    public function getPrixLocation(): ?float { return $this->prix_location; }
    public function setPrixLocation(float $prix_location): static { $this->prix_location = $prix_location; return $this; }

    public function isDisponibilite(): ?bool { return $this->disponibilite; }
    public function setDisponibilite(bool $disponibilite): static { $this->disponibilite = $disponibilite; return $this; }

    public function getDateCreation(): ?\DateTimeInterface { return $this->date_creation; }
    public function setDateCreation(\DateTimeInterface $date_creation): static { $this->date_creation = $date_creation; return $this; }

    public function getUtilisateur(): ?Utilisateur { return $this->utilisateur; }
    public function setUtilisateur(?Utilisateur $utilisateur): static { $this->utilisateur = $utilisateur; return $this; }

    public function getLocation(): Collection { return $this->Location; }
    
    public function addLocation(Location $location): static { 
        if (!$this->Location->contains($location)) {
            $this->Location->add($location);
            $location->setTerrain($this);
        }
        return $this;
    }

    public function removeLocation(Location $location): static {
        if ($this->Location->removeElement($location)) {
            if ($location->getTerrain() === $this) {
                $location->setTerrain(null);
            }
        }
        return $this;
    }
}
