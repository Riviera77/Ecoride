<?php

namespace App\Entity;

use App\Entity\Car;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\CarpoolingRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

#[ORM\Entity(repositoryClass: CarpoolingRepository::class)]
class Carpooling
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    private ?string $departureAddress = null;

    #[ORM\Column(length: 50)]
    private ?string $arrivalAddress = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $departureDate = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $arrivalDate = null;

    #[ORM\Column(type: Types::TIME_MUTABLE)]
    private ?\DateTimeInterface $departureTime = null;

    #[ORM\Column(type: Types::TIME_MUTABLE)]
    private ?\DateTimeInterface $arrivalTime = null;

    #[ORM\Column]
    private ?float $price = null;

    #[ORM\Column]
    private ?int $numberSeats = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $preference = null;

    #[ORM\Column(length: 50)]
    private ?string $status = null;

    #[ORM\ManyToOne(inversedBy: 'carpoolings')]
    private ?Car $cars = null;

    #[ORM\ManyToOne(inversedBy: 'carpoolingsAsDriver')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $users = null;

    /**
     * @var Collection<int, User>
     */
    #[ORM\ManyToMany(targetEntity: User::class, inversedBy: 'carpoolingsAsPassenger')]
    #[ORM\JoinTable(name: 'carpooling_passengers')]
    private Collection $passengers;

    public function __construct()
    {
        $this->passengers = new ArrayCollection();
    }


    /**
     * @var Collection<int, User>
     */
    /* #[ORM\ManyToMany(targetEntity: User::class, inversedBy: 'carpoolings')]
    private Collection $users; */

    /* public function __construct()
    {
        $this->users = new ArrayCollection();
    } */

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDepartureAddress(): ?string
    {
        return $this->departureAddress;
    }

    public function setDepartureAddress(string $departureAddress): static
    {
        $this->departureAddress = $departureAddress;

        return $this;
    }

    public function getArrivalAddress(): ?string
    {
        return $this->arrivalAddress;
    }

    public function setArrivalAddress(string $arrivalAddress): static
    {
        $this->arrivalAddress = $arrivalAddress;

        return $this;
    }

    public function getDepartureDate(): ?\DateTimeInterface
    {
        return $this->departureDate;
    }

    public function setDepartureDate(\DateTimeInterface $departureDate): static
    {
        $this->departureDate = $departureDate;

        return $this;
    }

    public function getArrivalDate(): ?\DateTimeInterface
    {
        return $this->arrivalDate;
    }

    public function setArrivalDate(\DateTimeInterface $arrivalDate): static
    {
        $this->arrivalDate = $arrivalDate;

        return $this;
    }

    public function getDepartureTime(): ?\DateTimeInterface
    {
        return $this->departureTime;
    }

    public function setDepartureTime(\DateTimeInterface $departureTime): static
    {
        $this->departureTime = $departureTime;

        return $this;
    }

    public function getArrivalTime(): ?\DateTimeInterface
    {
        return $this->arrivalTime;
    }

    public function setArrivalTime(\DateTimeInterface $arrivalTime): static
    {
        $this->arrivalTime = $arrivalTime;

        return $this;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(float $price): static
    {
        $this->price = $price;

        return $this;
    }

    public function getNumberSeats(): ?int
    {
        return $this->numberSeats;
    }

    public function setNumberSeats(int $numberSeats): static
    {
        $this->numberSeats = $numberSeats;

        return $this;
    }

    public function getPreference(): ?string
    {
        return $this->preference;
    }

    public function setPreference(?string $preference): static
    {
        $this->preference = $preference;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getCars(): ?Car
    {
        return $this->cars;
    }

    public function setCars(?Car $cars): static
    {
        $this->cars = $cars;

        return $this;
    }

    /**
     * @return Collection<int, User>
     */
    /* public function getUsers(): Collection
    {
        return $this->users;
    }

    public function addUser(User $user): static
    {
        if (!$this->users->contains($user)) {
            $this->users->add($user);
        }

        return $this;
    }

    public function removeUser(User $user): static
    {
        $this->users->removeElement($user);

        return $this;
    } */

    public function getDuration(): ?string
    {
        // On vérifie que les heures de départ et d’arrivée sont bien définies
        // sinon on ne tente pas de calcul, on retourne null
        if (!$this->departureTime || !$this->arrivalTime) {
            return null;
        }
        // On utilise la méthode diff() fournie par DateTime pour calculer 
        // la différence entre l’heure de départ et celle d’arrivée.
        $interval = $this->departureTime->diff($this->arrivalTime);

        // Si l’arrivée est avant le départ (ex : 22h -> 1h), on corrige avec +1 jour
        if ($interval->invert) {
            $arrival = ($this->arrivalTime instanceof \DateTime)
            ? clone $this->arrivalTime
            : \DateTime::createFromInterface($this->arrivalTime);

        $arrival->modify('+1 day');
        $interval = $this->departureTime->diff($arrival);
        }

        $hours = $interval->h;
        $minutes = $interval->i;

        // 	%dh → l’heure sans zéro en début (ex : 2h)
        // 	%02dmin → les minutes, toujours sur 2 chiffres (ex : 05min)
        return sprintf('%dh %02dmin', $hours, $minutes);
    }

    public function getUsers(): ?User
    {
        return $this->users;
    }

    public function setUsers(?User $users): static
    {
        $this->users = $users;

        return $this;
    }

    /**
     * @return Collection<int, User>
     */
    public function getPassengers(): Collection
    {
        return $this->passengers;
    }

    public function addPassenger(User $passenger): static
    {
        if (!$this->passengers->contains($passenger)) {
            $this->passengers->add($passenger);
        }

        return $this;
    }

    public function removePassenger(User $passenger): static
    {
        $this->passengers->removeElement($passenger);

        return $this;
    }
}