<?php

namespace App\Entity;

use App\Enum\StatutCommande;
use App\Repository\CommandeRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CommandeRepository::class)]
class Commande
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $date_commande = null;

    #[ORM\Column]
    private ?float $total = null;

    #[ORM\Column(type: 'string', enumType: StatutCommande::class)]
    private StatutCommande $statut;

    public function __construct()
    {
        $this->date_commande = new \DateTime(); // Set the order date automatically
        $this->statut = StatutCommande::EN_ATTENTE; // Default status
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

    public function setStatut(StatutCommande $statut): static
    {
        $this->statut = $statut;

        return $this;
    }
}