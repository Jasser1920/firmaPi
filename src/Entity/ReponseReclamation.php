<?php

namespace App\Entity;

use App\Repository\ReponseReclamationRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ReponseReclamationRepository::class)]
class ReponseReclamation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Assert\NotBlank(message: "Le message de la réponse ne peut pas être vide.")]
    private ?string $message = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Assert\NotNull(message: "La date de réponse est obligatoire.")]
    #[Assert\GreaterThan(
        value: "today",
        message: "La date de réponse doit être ultérieure à aujourd'hui."
    )]
    private ?\DateTimeInterface $date_reponse = null;

    #[ORM\ManyToOne(inversedBy: 'reponseReclamations')]
    #[Assert\NotNull(message: "Une réclamation doit être associée à la réponse.")]
    private ?Reclammation $reclamation = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function setMessage(string $message): static
    {
        $this->message = $message;
        return $this;
    }

    public function getDateReponse(): ?\DateTimeInterface
    {
        return $this->date_reponse;
    }

    public function setDateReponse(\DateTimeInterface $date_reponse): static
    {
        $this->date_reponse = $date_reponse;
        return $this;
    }

    public function getReclamation(): ?Reclammation
    {
        return $this->reclamation;
    }

    public function setReclamation(?Reclammation $reclamation): static
    {
        $this->reclamation = $reclamation;
        return $this;
    }
}
