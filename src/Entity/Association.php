<?php

namespace App\Entity;

use App\Repository\AssociationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AssociationRepository::class)]
class Association
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nomAssociation = null;

    /**
     * @var Collection<int, Evenement>
     */
    #[ORM\OneToMany(targetEntity: Evenement::class, mappedBy: 'association')]
    private Collection $historiqueEvenement;

    /**
     * @var Collection<int, Dons>
     */
    #[ORM\OneToMany(targetEntity: Dons::class, mappedBy: 'association')]
    private Collection $listeDon;

    public function __construct()
    {
        $this->historiqueEvenement = new ArrayCollection();
        $this->listeDon = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNomAssociation(): ?string
    {
        return $this->nomAssociation;
    }

    public function setNomAssociation(string $nomAssociation): static
    {
        $this->nomAssociation = $nomAssociation;

        return $this;
    }

    /**
     * @return Collection<int, Evenement>
     */
    public function getHistoriqueEvenement(): Collection
    {
        return $this->historiqueEvenement;
    }

    public function addHistoriqueEvenement(Evenement $historiqueEvenement): static
    {
        if (!$this->historiqueEvenement->contains($historiqueEvenement)) {
            $this->historiqueEvenement->add($historiqueEvenement);
            $historiqueEvenement->setAssociation($this);
        }

        return $this;
    }

    public function removeHistoriqueEvenement(Evenement $historiqueEvenement): static
    {
        if ($this->historiqueEvenement->removeElement($historiqueEvenement)) {
            // set the owning side to null (unless already changed)
            if ($historiqueEvenement->getAssociation() === $this) {
                $historiqueEvenement->setAssociation(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Dons>
     */
    public function getListeDon(): Collection
    {
        return $this->listeDon;
    }

    public function addListeDon(Dons $listeDon): static
    {
        if (!$this->listeDon->contains($listeDon)) {
            $this->listeDon->add($listeDon);
            $listeDon->setAssociation($this);
        }

        return $this;
    }

    public function removeListeDon(Dons $listeDon): static
    {
        if ($this->listeDon->removeElement($listeDon)) {
            // set the owning side to null (unless already changed)
            if ($listeDon->getAssociation() === $this) {
                $listeDon->setAssociation(null);
            }
        }

        return $this;
    }
}
