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

    #[ORM\Column(length: 255)]
    private ?string $nom = null;

    #[ORM\Column(length: 255)]
    private ?string $prenom = null;

    #[ORM\Column(length: 255)]
    private ?string $email = null;

    #[ORM\Column(length: 255)]
    private ?string $motDePasse = null;

    #[ORM\Column(length: 255)]
    private ?string $role = null;

    #[ORM\Column(length: 255)]
    private ?string $favoris = null;

    #[ORM\Column(length: 255)]
    private ?string $listeProduit = null;

    #[ORM\Column(length: 255)]
    private ?string $historiqueDon = null;

    #[ORM\Column(length: 255)]
    private ?string $listeCommandes = null;

    #[ORM\Column(length: 255)]
    private ?string $reclamations = null;

    #[ORM\Column(length: 255)]
    private ?string $OneToMany = null;

    /**
     * @var Collection<int, Commande>
     */
    #[ORM\OneToMany(targetEntity: Commande::class, mappedBy: 'agriculteur')]
    private Collection $commandes;

    public function __construct()
    {
        $this->commandes = new ArrayCollection();
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

    public function getMotDePasse(): ?string
    {
        return $this->motDePasse;
    }

    public function setMotDePasse(string $motDePasse): static
    {
        $this->motDePasse = $motDePasse;

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

    public function getFavoris(): ?string
    {
        return $this->favoris;
    }

    public function setFavoris(string $favoris): static
    {
        $this->favoris = $favoris;

        return $this;
    }

    public function getListeProduit(): ?string
    {
        return $this->listeProduit;
    }

    public function setListeProduit(string $listeProduit): static
    {
        $this->listeProduit = $listeProduit;

        return $this;
    }

    public function getHistoriqueDon(): ?string
    {
        return $this->historiqueDon;
    }

    public function setHistoriqueDon(string $historiqueDon): static
    {
        $this->historiqueDon = $historiqueDon;

        return $this;
    }

    public function getListeCommandes(): ?string
    {
        return $this->listeCommandes;
    }

    public function setListeCommandes(string $listeCommandes): static
    {
        $this->listeCommandes = $listeCommandes;

        return $this;
    }

    public function getReclamations(): ?string
    {
        return $this->reclamations;
    }

    public function setReclamations(string $reclamations): static
    {
        $this->reclamations = $reclamations;

        return $this;
    }

    public function getOneToMany(): ?string
    {
        return $this->OneToMany;
    }

    public function setOneToMany(string $OneToMany): static
    {
        $this->OneToMany = $OneToMany;

        return $this;
    }

    /**
     * @return Collection<int, Commande>
     */
    public function getCommandes(): Collection
    {
        return $this->commandes;
    }

    public function addCommande(Commande $commande): static
    {
        if (!$this->commandes->contains($commande)) {
            $this->commandes->add($commande);
            $commande->setAgriculteur($this);
        }

        return $this;
    }

    public function removeCommande(Commande $commande): static
    {
        if ($this->commandes->removeElement($commande)) {
            // set the owning side to null (unless already changed)
            if ($commande->getAgriculteur() === $this) {
                $commande->setAgriculteur(null);
            }
        }

        return $this;
    }
}
