<?php

namespace App\Entity;

use App\Enum\StatutLivraison;
use App\Repository\LivraisonRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;


#[ORM\Entity(repositoryClass: LivraisonRepository::class)]
class Livraison
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Le nom de la société est obligatoire.")]
    #[Assert\Length(
        max: 255,
        maxMessage: "Le nom de la société ne peut pas dépasser {{ limit }} caractères."
    )]
    private ?string $nom_societe = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Assert\NotBlank(message: "L'adresse de livraison est obligatoire.")]
    #[Assert\Length(
        max: 1000,
        maxMessage: "L'adresse de livraison ne peut pas dépasser {{ limit }} caractères."
    )]
    private ?string $adresse_livraison = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $date_livraison = null;

    #[ORM\Column(type: 'string', enumType: StatutLivraison::class)]
    private StatutLivraison $statut;

    public function __construct()
    {
        $this->statut = StatutLivraison::EN_ATTENTE; // Default status
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNomSociete(): ?string
    {
        return $this->nom_societe;
    }

    public function setNomSociete(string $nom_societe): static
    {
        $this->nom_societe = $nom_societe;

        return $this;
    }

    public function getAdresseLivraison(): ?string
    {
        return $this->adresse_livraison;
    }

    public function setAdresseLivraison(string $adresse_livraison): static
    {
        $this->adresse_livraison = $adresse_livraison;

        return $this;
    }

    public function getDateLivraison(): ?\DateTimeInterface
    {
        return $this->date_livraison;
    }

    public function setDateLivraison(\DateTimeInterface $date_livraison): static
    {
        $this->date_livraison = $date_livraison;

        return $this;
    }

    public function getStatut(): StatutLivraison
    {
        return $this->statut;
    }

    public function setStatut(StatutLivraison $statut): static
    {
        $this->statut = $statut;

        return $this;
    }
}