<?php

namespace App\Entity;

use App\Repository\CommandeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CommandeRepository::class)]
class Commande
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $date = null;

    #[ORM\Column(length: 255)]
    private ?string $statut = null;

    #[ORM\Column]
    private ?float $montant = null;

    /**
     * @var Collection<int, Produit>
     */
    #[ORM\ManyToMany(targetEntity: Produit::class, inversedBy: 'commandes')]
    private Collection $produitCommande;

    #[ORM\ManyToOne(inversedBy: 'commandes')]
    private ?Agriculteur $agriculteur = null;

    #[ORM\ManyToOne(inversedBy: 'commandes')]
    private ?Client $client = null;

    public function __construct()
    {
        $this->produitCommande = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getStatut(): ?string
    {
        return $this->statut;
    }

    public function setStatut(string $statut): static
    {
        $this->statut = $statut;

        return $this;
    }

    public function getMontant(): ?float
    {
        return $this->montant;
    }

    public function setMontant(float $montant): static
    {
        $this->montant = $montant;

        return $this;
    }

    /**
     * @return Collection<int, Produit>
     */
    public function getProduitCommande(): Collection
    {
        return $this->produitCommande;
    }

    public function addProduitCommande(Produit $produitCommande): static
    {
        if (!$this->produitCommande->contains($produitCommande)) {
            $this->produitCommande->add($produitCommande);
        }

        return $this;
    }

    public function removeProduitCommande(Produit $produitCommande): static
    {
        $this->produitCommande->removeElement($produitCommande);

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

    public function getClient(): ?Client
    {
        return $this->client;
    }

    public function setClient(?Client $client): static
    {
        $this->client = $client;

        return $this;
    }
}
