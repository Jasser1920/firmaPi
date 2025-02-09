<?php

namespace App\Entity;

use App\Repository\AgriculteurRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AgriculteurRepository::class)]
class Agriculteur
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    private ?string $nom = null;

    #[ORM\Column(length: 50)]
    private ?string $prenom = null;

    #[ORM\Column(length: 100)]
    private ?string $email = null;

    #[ORM\Column(length: 100)]
    private ?string $mdp = null;

    #[ORM\Column(length: 100)]
    private ?string $role = null;

    /**
     * @var Collection<int, Produit>
     */
    #[ORM\OneToMany(targetEntity: Produit::class, mappedBy: 'agriculteur')]
    private Collection $listeProduit;

    public function __construct()
    {
        $this->listeProduit = new ArrayCollection();
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

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): static
    {
        $this->prenom = $prenom;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function getMdp(): ?string
    {
        return $this->mdp;
    }

    public function setMdp(string $mdp): static
    {
        $this->mdp = $mdp;

        return $this;
    }

    public function getRole(): ?string
    {
        return $this->role;
    }

    public function setRole(string $role): static
    {
        $this->role = $role;

        return $this;
    }

    /**
     * @return Collection<int, Produit>
     */
    public function getListeProduit(): Collection
    {
        return $this->listeProduit;
    }

    public function addListeProduit(Produit $listeProduit): static
    {
        if (!$this->listeProduit->contains($listeProduit)) {
            $this->listeProduit->add($listeProduit);
            $listeProduit->setAgriculteur($this);
        }

        return $this;
    }

    public function removeListeProduit(Produit $listeProduit): static
    {
        if ($this->listeProduit->removeElement($listeProduit)) {
            // set the owning side to null (unless already changed)
            if ($listeProduit->getAgriculteur() === $this) {
                $listeProduit->setAgriculteur(null);
            }
        }

        return $this;
    }
}
