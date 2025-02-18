<?php

namespace App\Entity;

use App\Enum\StatutCommande;
use App\Repository\CommandeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;


#[ORM\Entity(repositoryClass: CommandeRepository::class)]
class Commande
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[Assert\NotBlank(message: "La date de commande ne peut pas être vide.")]
    #[Assert\Date(message: "La date de commande doit être une date valide.")]
    private ?\DateTimeInterface $date_commande = null;

    #[ORM\Column]
    #[Assert\NotBlank(message: "Le total ne peut pas être vide.")]
    #[Assert\Type(type: "numeric", message: "Le total doit être un nombre.")]
    #[Assert\Positive(message: "Le total doit être un nombre positif.")]
    private ?float $total = null;

    // #[ORM\Column(type: 'string', enumType: StatutCommande::class)]
    #[ORM\Column(type: 'string')]

    #[Assert\NotBlank(message: "Le statut de la commande est obligatoire.")]

    private StatutCommande $statut;
   


    /**
     * @var Collection<int, Produit>
     */
    #[ORM\ManyToMany(targetEntity: Produit::class)]
    #[Assert\NotBlank(message: "La commande doit contenir au moins un produit.")]

    private Collection $produits;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    private ?Livraison $livraison = null;

    public function __construct()
    {
        $this->date_commande = new \DateTime(); // Set the order date automatically
        $this->statut = StatutCommande::EN_ATTENTE; // Default status
        $this->produits = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDateCommande(): ?\DateTimeInterface
    {
        return $this->date_commande;
    }

    public function setDateCommande(\DateTimeInterface $date_commande): static
    {
        $this->date_commande = $date_commande;

        return $this;
    }

    public function getTotal(): ?float
    {
        return $this->total;
    }

    public function setTotal(float $total): static
    {
        $this->total = $total;

        return $this;
    }

    public function getStatut(): StatutCommande
    {
        return $this->statut;
    }
    public function getStatutAsString(): string
    {
        return $this->statut->value;
    }

    // public function setStatut(StatutCommande $statut): static
    // {
    //     $this->statut = $statut;

    //     return $this;
    // }
    // public function setStatut($statut): static
    // {
    //     // Si le statut est une chaîne, convertissez-le en une instance de l'énumération
    //     if (is_string($statut)) {
    //         $statut = StatutCommande::from($statut);  // Convertir la chaîne en une instance d'énumération
    //     } elseif (!$statut instanceof StatutCommande) {
    //         throw new \InvalidArgumentException('Statut invalide.');
    //     }
    
    //     $this->statut = $statut;
    
    //     return $this;
    // }
    public function setStatut($statut): static
{
    // Vérifie si la valeur est une chaîne
    if (is_string($statut)) {
        $statut = StatutCommande::from($statut);  // Convertit la chaîne en instance de l'énumération
    } elseif (!$statut instanceof StatutCommande) {
        throw new \InvalidArgumentException('Statut invalide.');
    }

    $this->statut = $statut;

    return $this;
}

    


    /**
     * @return Collection<int, Produit>
     */
    public function getProduits(): Collection
    {
        return $this->produits;
    }

    public function addProduit(Produit $produit): static
    {
        if (!$this->produits->contains($produit)) {
            $this->produits->add($produit);
        }

        return $this;
    }

    public function removeProduit(Produit $produit): static
    {
        $this->produits->removeElement($produit);

        return $this;
    }

    public function getLivraison(): ?Livraison
    {
        return $this->livraison;
    }

    public function setLivraison(?Livraison $livraison): static
    {
        $this->livraison = $livraison;

        return $this;
    }
}