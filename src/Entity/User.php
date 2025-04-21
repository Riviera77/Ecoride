<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_EMAIL', fields: ['email'])]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180)]
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

    #[ORM\Column(length: 50)]
    private ?string $username = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $photo = null;

    /**
     * @var Collection<int, Credit>
     */
    #[ORM\OneToMany(targetEntity: Credit::class, mappedBy: 'users')]
    private Collection $credits;

    /**
     * @var Collection<int, Car>
     */
    #[ORM\OneToMany(targetEntity: Car::class, mappedBy: 'users')]
    private Collection $cars;

    /**
     * @var Collection<int, CarpoolingParticipation>
     */
    #[ORM\OneToMany(targetEntity: CarpoolingParticipation::class, mappedBy: 'users')]
    private Collection $carpoolingParticipations;

    /**
     * @var Collection<int, Carpooling>
     */
    /* #[ORM\ManyToMany(targetEntity: Carpooling::class, mappedBy: 'users')]
    private Collection $carpoolings; */

    public function __construct()
    {
        $this->credits = new ArrayCollection();
        $this->cars = new ArrayCollection();
        /* $this->carpoolings = new ArrayCollection(); */
        $this->carpoolingParticipations = new ArrayCollection();
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
    public function getPassword(): ?string
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

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): static
    {
        $this->username = $username;

        return $this;
    }

    public function getPhoto()
    {
        return $this->photo;
    }

    public function setPhoto(?string $photo): static
    {
        $this->photo = $photo;

        return $this;
    }

    /**
     * @return Collection<int, Credit>
     */
    public function getCredits(): Collection
    {
        return $this->credits;
    }

    public function addCredit(Credit $credit): static
    {
        if (!$this->credits->contains($credit)) {
            $this->credits->add($credit);
            $credit->setUsers($this);
        }

        return $this;
    }

    public function removeCredit(Credit $credit): static
    {
        if ($this->credits->removeElement($credit)) {
            // set the owning side to null (unless already changed)
            if ($credit->getUsers() === $this) {
                $credit->setUsers(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Car>
     */
    public function getCars(): Collection
    {
        return $this->cars;
    }

    public function addCar(Car $car): static
    {
        if (!$this->cars->contains($car)) {
            $this->cars->add($car);
            $car->setUsers($this);
        }

        return $this;
    }

    public function removeCar(Car $car): static
    {
        if ($this->cars->removeElement($car)) {
            // set the owning side to null (unless already changed)
            if ($car->getUsers() === $this) {
                $car->setUsers(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Carpooling>
     */
    /* public function getCarpoolings(): Collection
    {
        return $this->carpoolings;
    } */

    /* public function addCarpooling(Carpooling $carpooling): static
    {
        if (!$this->carpoolings->contains($carpooling)) {
            $this->carpoolings->add($carpooling);
            $carpooling->addUser($this);
        }

        return $this;
    }

    public function removeCarpooling(Carpooling $carpooling): static
    {
        if ($this->carpoolings->removeElement($carpooling)) {
            $carpooling->removeUser($this);
        }

        return $this;
    } */

    /**
     * @return Collection<int, CarpoolingParticipation>
     */
    public function getCarpoolingParticipations(): Collection
    {
        return $this->carpoolingParticipations;
    }

    public function addCarpoolingParticipation(CarpoolingParticipation $carpoolingParticipation): static
    {
        if (!$this->carpoolingParticipations->contains($carpoolingParticipation)) {
            $this->carpoolingParticipations->add($carpoolingParticipation);
            $carpoolingParticipation->setUsers($this);
        }

        return $this;
    }

    public function removeCarpoolingParticipation(CarpoolingParticipation $carpoolingParticipation): static
    {
        if ($this->carpoolingParticipations->removeElement($carpoolingParticipation)) {
            // set the owning side to null (unless already changed)
            if ($carpoolingParticipation->getUsers() === $this) {
                $carpoolingParticipation->setUsers(null);
            }
        }

        return $this;
    }
}