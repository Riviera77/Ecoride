<?php

namespace App\Entity;

use App\Repository\CarRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CarRepository::class)]
class Car
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    private ?string $registration = null;

    #[ORM\Column(length: 50)]
    private ?string $dateFirstRegistration = null;

    #[ORM\Column(length: 50)]
    private ?string $model = null;

    #[ORM\Column(length: 50)]
    private ?string $color = null;

    #[ORM\Column(length: 50)]
    private ?string $mark = null;

    #[ORM\Column]
    private ?bool $energy = null;

    #[ORM\ManyToOne(inversedBy: 'cars')]
    private ?User $users = null;

    /**
     * @var Collection<int, Carpooling>
     */
    #[ORM\OneToMany(targetEntity: Carpooling::class, mappedBy: 'cars')]
    private Collection $carpoolings;

    public function __construct()
    {
        $this->carpoolings = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRegistration(): ?string
    {
        return $this->registration;
    }

    public function setRegistration(string $registration): static
    {
        $this->registration = $registration;

        return $this;
    }

    public function getDateFirstRegistration(): ?string
    {
        return $this->dateFirstRegistration;
    }

    public function setDateFirstRegistration(string $dateFirstRegistration): static
    {
        $this->dateFirstRegistration = $dateFirstRegistration;

        return $this;
    }

    public function getModel(): ?string
    {
        return $this->model;
    }

    public function setModel(string $model): static
    {
        $this->model = $model;

        return $this;
    }

    public function getColor(): ?string
    {
        return $this->color;
    }

    public function setColor(string $color): static
    {
        $this->color = $color;

        return $this;
    }

    public function getMark(): ?string
    {
        return $this->mark;
    }

    public function setMark(string $mark): static
    {
        $this->mark = $mark;

        return $this;
    }

    public function isEnergy(): ?bool
    {
        return $this->energy;
    }

    public function setEnergy(bool $energy): static
    {
        $this->energy = $energy;

        return $this;
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
     * @return Collection<int, Carpooling>
     */
    public function getCarpoolings(): Collection
    {
        return $this->carpoolings;
    }

    public function addCarpooling(Carpooling $carpooling): static
    {
        if (!$this->carpoolings->contains($carpooling)) {
            $this->carpoolings->add($carpooling);
            $carpooling->setCars($this);
        }

        return $this;
    }

    public function removeCarpooling(Carpooling $carpooling): static
    {
        if ($this->carpoolings->removeElement($carpooling)) {
            // set the owning side to null (unless already changed)
            if ($carpooling->getCars() === $this) {
                $carpooling->setCars(null);
            }
        }

        return $this;
    }
}
