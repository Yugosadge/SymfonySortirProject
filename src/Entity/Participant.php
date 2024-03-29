<?php

namespace App\Entity;

use App\Repository\ParticipantRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\DateTimeType;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
#[ORM\Entity(repositoryClass: ParticipantRepository::class)]
#[UniqueEntity(fields: ['email'], message: 'Cet email est déjà utilisé')]
#[UniqueEntity(fields: ['username'], message: 'Ce pseudo est déjà utilisé')]
class Participant implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180, unique: true)]
    #[Assert\Email(message: 'Veuillez entrer une adresse email valide')]
    #[Assert\NotBlank(message: 'Veuillez entrer votre email')]
    private ?string $email = null;

    /**
     * @var list<string> The user roles
     */
    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;

    #[ORM\Column(length: 255)]
    #[Assert\Type(type: TYPES::STRING, message: 'Veuillez entrer un nom valide')]
    #[Assert\NotBlank(message: 'Veuillez saisir votre nom')]
    #[Assert\Length(min: 2, minMessage: 'Le nom doit contenir au moins {{ limit }} caractères')]
    private ?string $lastname = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'Veuillez saisir votre prénom')]
    #[Assert\Length(min: 2, minMessage: 'Le prénom doit contenir au moins {{ limit }} caractères')]
    private ?string $firstname = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $phoneNumber = null;

    #[ORM\Column]
    private ?bool $active = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'Veuillez entrer votre pseudo')]
    #[Assert\Length(min: 2, minMessage: 'Le pseudo doit contenir au moins {{ limit }} caractères')]
    private ?string $username = null;

    #[ORM\ManyToOne(inversedBy: 'participants')]
    private ?Site $site = null;

    #[ORM\ManyToMany(targetEntity: Sortie::class, inversedBy: 'participants')]
    private Collection $sortiesInscri;

    #[ORM\OneToMany(targetEntity: Sortie::class, mappedBy: 'organisateur')]
    private Collection $sortieOrga;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $image = null;

    #[ORM\ManyToMany(targetEntity: Groups::class, mappedBy: 'participants')]
    private Collection $groupes;

    #[ORM\OneToMany(targetEntity: Groups::class, mappedBy: 'groupCreator')]
    private Collection $groupsCreated;

    public function __construct()
    {
        $this->sortiesInscri = new ArrayCollection();
        $this->sortieOrga = new ArrayCollection();
        $this->roles = ['ROLE_USER'];
        $this->groupes = new ArrayCollection();
        $this->groupsCreated = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     *
     * @return list<string>
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    /**
     * @param list<string> $roles
     */
    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(string $lastname): static
    {
        $this->lastname = $lastname;

        return $this;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(string $firstname): static
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getPhoneNumber(): ?string
    {
        return $this->phoneNumber;
    }

    public function setPhoneNumber(?string $phoneNumber): static
    {
        $this->phoneNumber = $phoneNumber;

        return $this;
    }

    public function isActive(): ?bool
    {
        return $this->active;
    }

    public function setActive(bool $active): static
    {
        $this->active = $active;

        return $this;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): static
    {
        $this->username = $username;

        return $this;
    }

    public function getSite(): ?Site
    {
        return $this->site;
    }

    public function setSite(?Site $site): static
    {
        $this->site = $site;

        return $this;
    }

    /**
     * @return Collection<int, Sortie>
     */
    public function getSortiesInscri(): Collection
    {
        return $this->sortiesInscri;
    }

    public function addSortiesInscri(Sortie $sortiesInscri): static
    {
        if (!$this->sortiesInscri->contains($sortiesInscri)) {
            $this->sortiesInscri->add($sortiesInscri);
        }

        return $this;
    }

    public function removeSortiesInscri(Sortie $sortiesInscri): static
    {
        $this->sortiesInscri->removeElement($sortiesInscri);

        return $this;
    }

    /**
     * @return Collection<int, Sortie>
     */
    public function getSortieOrga(): Collection
    {
        return $this->sortieOrga;
    }

    public function addSortieOrga(Sortie $sortieOrga): static
    {
        if (!$this->sortieOrga->contains($sortieOrga)) {
            $this->sortieOrga->add($sortieOrga);
            $sortieOrga->setOrganisateur($this);
        }

        return $this;
    }

    public function removeSortieOrga(Sortie $sortieOrga): static
    {
        if ($this->sortieOrga->removeElement($sortieOrga)) {
            // set the owning side to null (unless already changed)
            if ($sortieOrga->getOrganisateur() === $this) {
                $sortieOrga->setOrganisateur(null);
            }
        }

        return $this;
    }


    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(?string $image): void
    {
        $this->image = $image;

    }

    /**
     * @return Collection<int, Groups>
     */
    public function getGroupes(): Collection
    {
        return $this->groupes;
    }

    public function addGroupe(Groups $groupe): static
    {
        if (!$this->groupes->contains($groupe)) {
            $this->groupes->add($groupe);
            $groupe->addParticipant($this);
        }

        return $this;
    }

    public function removeGroupe(Groups $groupe): static
    {
        if ($this->groupes->removeElement($groupe)) {
            $groupe->removeParticipant($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, Groups>
     */
    public function getGroupsCreated(): Collection
    {
        return $this->groupsCreated;
    }

    public function addGroupsCreated(Groups $groupsCreated): static
    {
        if (!$this->groupsCreated->contains($groupsCreated)) {
            $this->groupsCreated->add($groupsCreated);
            $groupsCreated->setGroupCreator($this);
        }

        return $this;
    }

    public function removeGroupsCreated(Groups $groupsCreated): static
    {
        if ($this->groupsCreated->removeElement($groupsCreated)) {
            // set the owning side to null (unless already changed)
            if ($groupsCreated->getGroupCreator() === $this) {
                $groupsCreated->setGroupCreator(null);
            }
        }

        return $this;
    }

}
