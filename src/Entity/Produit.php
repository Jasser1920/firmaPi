<?php

namespace App\Entity;

use App\Repository\ProduitRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProduitRepository::class)]
class Produit
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nom = null;

    #[ORM\Column(length: 255)]
    private ?string $categorie = null;

    #[ORM\Column(length: 255)]
    private ?string $description = null;

    #[ORM\Column(length: 255)]
    private ?string $image = null;

    #[ORM\Column]
    private ?int $quantite = null;

    #[ORM\Column(length: 255)]
    private ?string $unite = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 0)]
    private ?string $prix = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $dateExp = null;

    /**
     * @var Collection<int, Dons>
     */
    #[ORM\OneToMany(targetEntity: Dons::class, mappedBy: 'produits')]
    private Collection $dons;

    #[ORM\ManyToOne(inversedBy: 'listeProduit')]
    private ?Agriculteur $agriculteur = null;

    public function __construct()
    {
        $this->dons = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): static
    {
        $this->nom = $nom;

        return $this;
    }

    public function getCategorie(): ?string
    {
        return $this->categorie;
    }

    public function setCategorie(string $categorie): static
    {
        $this->categorie = $categorie;

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

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(string $image): static
    {
        $this->image = $image;

        return $this;
    }

    public function getQuantite(): ?int
    {
        return $this->quantite;
    }

    public function setQuantite(int $quantite): static
    {
        $this->quantite = $quantite;

        return $this;
    }

    public function getUnite(): ?string
    {
        return $this->unite;
    }

    public function setUnite(string $unite): static
    {
        $this->unite = $unite;

        return $this;
    }

    public function getPrix(): ?string
    {
        return $this->prix;
    }

    public function setPrix(string $prix): static
    {
        $this->prix = $prix;

        return $this;
    }

    public function getDateExp(): ?\DateTimeImmutable
    {
        return $this->dateExp;
    }

    public function setDateExp(\DateTimeImmutable $dateExp): static
    {
        $this->dateExp = $dateExp;

        return $this;
    }

    /**
     * @return Collection<int, Dons>
     */
    public function getDons(): Collection
    {
        return $this->dons;
    }

    public function addDon(Dons $don): static
    {
        if (!$this->dons->contains($don)) {
            $this->dons->add($don);
            $don->setProduits($this);
        }

        return $this;
    }

    public function removeDon(Dons $don): static
    {
        if ($this->dons->removeElement($don)) {
            // set the owning side to null (unless already changed)
            if ($don->getProduits() === $this) {
                $don->setProduits(null);
            }
        }

        return $this;
    }

    public function getAgriculteur(): ?Agriculteur
    {
        return $this->agriculteur;
    }

    public function setAgriculteur(?Agriculteur $agriculteur): static
    {
        $this->agriculteur = $agriculteur;

        return $this;
    }
}
