<?php

namespace App\Entity;

use App\Enum\Role;
use App\Repository\UtilisateurRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: UtilisateurRepository::class)]
#[UniqueEntity(fields: ['email'], message: 'There is already an account with this email')]
class Utilisateur implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Le nom ne peut pas être vide.")]
    #[Assert\Length(min: 2, max: 255, minMessage: "Le nom doit contenir au moins {{ limit }} caractères.", maxMessage: "Le nom ne peut pas dépasser {{ limit }} caractères.")]
    private ?string $nom = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Le prénom ne peut pas être vide.")]
    #[Assert\Length(min: 2, max: 255, minMessage: "Le prénom doit contenir au moins {{ limit }} caractères.", maxMessage: "Le prénom ne peut pas dépasser {{ limit }} caractères.")]
    private ?string $prenom = null;

    #[ORM\Column(length: 255, unique: true)]
    #[Assert\NotBlank(message: "L'email ne peut pas être vide.")]
    #[Assert\Email(message: "L'email '{{ value }}' n'est pas valide.")]
    private ?string $email = null;

    #[ORM\Column(length: 255)]
    #[Assert\Length(min: 8, max: 255, minMessage: "Le mot de passe doit contenir au moins {{ limit }} caractères.")]
    private ?string $motdepasse = null;

    #[ORM\Column(length: 20)]
    #[Assert\NotBlank(message: "Le numéro de téléphone ne peut pas être vide.")]
    #[Assert\Regex(pattern: "/^[0-9]{8}$/", message: "Le numéro de téléphone doit contenir exactement 10 chiffres.")]
    private ?string $telephone = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "L'adresse ne peut pas être vide.")]
    #[Assert\Length(min: 5, max: 255, minMessage: "L'adresse doit contenir au moins {{ limit }} caractères.")]
    private ?string $adresse = null;

    #[ORM\Column(type: 'string', enumType: Role::class)]
    #[Assert\NotNull(message: "Le rôle ne peut pas être nul.")]
    private Role $role;
    
    #[ORM\Column(type:"string", length:255, nullable:true)]
    private $profilePicture;

    #[ORM\Column(type: 'boolean')]
    private bool $blocked = false;


    #[ORM\Column(type: 'boolean')]
    private bool $isVerified = false;

    #[ORM\Column(type: 'string', length: 6, nullable: true)]
    private ?string $confirmationCode = null;

    /**
     * @var Collection<int, Don>
     */
    #[ORM\OneToMany(targetEntity: Don::class, mappedBy: 'dons_user')]
    private Collection $dons;

    /**
     * @var Collection<int, Evenemment>
     */
    #[ORM\OneToMany(targetEntity: Evenemment::class, mappedBy: 'utilisateur')]
    private Collection $evenement;

    /**
     * @var Collection<int, Reclammation>
     */
    #[ORM\OneToMany(targetEntity: Reclammation::class, mappedBy: 'utilisateur')]
    private Collection $reclamation;

    /**
     * @var Collection<int, Terrain>
     */
    #[ORM\OneToMany(targetEntity: Terrain::class, mappedBy: 'utilisateur')]
    private Collection $terrain;

    /**
     * @var Collection<int, Produit>
     */
    #[ORM\OneToMany(targetEntity: Produit::class, mappedBy: 'utilisateur')]
    private Collection $produit;

    public function __construct()
    {
        $this->dons = new ArrayCollection();
        $this->evenement = new ArrayCollection();
        $this->reclamation = new ArrayCollection();
        $this->terrain = new ArrayCollection();
        $this->produit = new ArrayCollection();
    }

    // Getters and Setters (unchanged)
    public function isVerified(): bool
    {
        return $this->isVerified;
    }

    public function setIsVerified(bool $isVerified): self
    {
        $this->isVerified = $isVerified;
        return $this;
    }

    public function getConfirmationCode(): ?string
    {
        return $this->confirmationCode;
    }

    public function setConfirmationCode(?string $confirmationCode): self
    {
        $this->confirmationCode = $confirmationCode;
        return $this;
    }
    public function isBlocked(): bool
    {
        return $this->blocked;
    }

    public function setBlocked(bool $blocked): self
    {
        $this->blocked = $blocked;
        return $this;
    }
    public function getProfilePicture(): ?string
    {
        return $this->profilePicture;
    }

    public function setProfilePicture(?string $profilePicture): self
    {
        $this->profilePicture = $profilePicture;

        return $this;
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

    public function getMotdepasse(): ?string
    {
        return $this->motdepasse;
    }

    public function setMotdepasse(string $motdepasse): static
    {
        $this->motdepasse = $motdepasse;
        return $this;
    }

    public function getTelephone(): ?string
    {
        return $this->telephone;
    }

    public function setTelephone(string $telephone): static
    {
        $this->telephone = $telephone;
        return $this;
    }

    public function getAdresse(): ?string
    {
        return $this->adresse;
    }

    public function setAdresse(string $adresse): static
    {
        $this->adresse = $adresse;
        return $this;
    }

    public function getRole(): Role
    {
        return $this->role;
    }

    public function setRole(Role $role): self
    {
        $this->role = $role;
        return $this;
    }

    // Implement UserInterface and PasswordAuthenticatedUserInterface

    public function getRoles(): array
    {
        // Return the role as an array
        return ['ROLE_' . $this->role->value];
    }

    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
    }

    public function getUserIdentifier(): string
    {
        // Use the email as the unique identifier
        return $this->email;
    }

    public function getPassword(): string
    {
        // Return the password (motdepasse)
        return $this->motdepasse;
    }

    // Other methods (unchanged)

    /**
     * @return Collection<int, Don>
     */
    public function getDons(): Collection
    {
        return $this->dons;
    }

    public function addDon(Don $don): static
    {
        if (!$this->dons->contains($don)) {
            $this->dons->add($don);
            $don->setDonsUser($this);
        }

        return $this;
    }

    public function removeDon(Don $don): static
    {
        if ($this->dons->removeElement($don)) {
            // set the owning side to null (unless already changed)
            if ($don->getDonsUser() === $this) {
                $don->setDonsUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Evenemment>
     */
    public function getEvenement(): Collection
    {
        return $this->evenement;
    }

    public function addEvenement(Evenemment $evenement): static
    {
        if (!$this->evenement->contains($evenement)) {
            $this->evenement->add($evenement);
            $evenement->setUtilisateur($this);
        }

        return $this;
    }

    public function removeEvenement(Evenemment $evenement): static
    {
        if ($this->evenement->removeElement($evenement)) {
            // set the owning side to null (unless already changed)
            if ($evenement->getUtilisateur() === $this) {
                $evenement->setUtilisateur(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Reclammation>
     */
    public function getReclamation(): Collection
    {
        return $this->reclamation;
    }

    public function addReclamation(Reclammation $reclamation): static
    {
        if (!$this->reclamation->contains($reclamation)) {
            $this->reclamation->add($reclamation);
            $reclamation->setUtilisateur($this);
        }

        return $this;
    }

    public function removeReclamation(Reclammation $reclamation): static
    {
        if ($this->reclamation->removeElement($reclamation)) {
            // set the owning side to null (unless already changed)
            if ($reclamation->getUtilisateur() === $this) {
                $reclamation->setUtilisateur(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Terrain>
     */
    public function getTerrain(): Collection
    {
        return $this->terrain;
    }

    public function addTerrain(Terrain $terrain): static
    {
        if (!$this->terrain->contains($terrain)) {
            $this->terrain->add($terrain);
            $terrain->setUtilisateur($this);
        }

        return $this;
    }

    public function removeTerrain(Terrain $terrain): static
    {
        if ($this->terrain->removeElement($terrain)) {
            // set the owning side to null (unless already changed)
            if ($terrain->getUtilisateur() === $this) {
                $terrain->setUtilisateur(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Produit>
     */
    public function getProduit(): Collection
    {
        return $this->produit;
    }

    public function addProduit(Produit $produit): static
    {
        if (!$this->produit->contains($produit)) {
            $this->produit->add($produit);
            $produit->setUtilisateur($this);
        }

        return $this;
    }

    public function removeProduit(Produit $produit): static
    {
        if ($this->produit->removeElement($produit)) {
            // set the owning side to null (unless already changed)
            if ($produit->getUtilisateur() === $this) {
                $produit->setUtilisateur(null);
            }
        }

        return $this;
    }
}