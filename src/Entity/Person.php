<?php

namespace App\Entity;

use App\Repository\PersonRepository;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Groups;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=PersonRepository::class)
 */
class Person
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups("app_api_person")
     * @Groups("app_api_person_browse")
     * @Groups("app_api_casting_browse")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=50)
     * @Groups("app_api_person")
     * @Groups("app_api_person_browse")
     * @Groups("app_api_movie")
     * @Groups("app_api_casting")
     * @Groups("app_api_casting_browse")
     */
    private $firstname;

    /**
     * @ORM\Column(type="string", length=50)
     * @Assert\NotBlank
     * @Groups("app_api_person")
     * @Groups("app_api_person_browse")
     * @Groups("app_api_movie")
     * @Groups("app_api_casting")
     * @Groups("app_api_casting_browse")
     */
    private $lastname;

    /**
     * @ORM\OneToMany(targetEntity=Casting::class, mappedBy="person")
     * @Assert\NotBlank
     * @Groups("app_api_person")
     */
    private $castings;

    public function __construct()
    {
        $this->castings = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(string $firstname): self
    {
        $this->firstname = $firstname;

        return $this;
    }
    
    public function getcompletName(): string
    {
        $name = $this->firstname .' '. $this->lastname;
        
        return $name;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(string $lastname): self
    {
        $this->lastname = $lastname;

        return $this;
    }

    /**
     * @return Collection<int, Casting>
     */
    public function getCastings(): Collection
    {
        return $this->castings;
    }

    public function addCasting(Casting $casting): self
    {
        if (!$this->castings->contains($casting)) {
            $this->castings[] = $casting;
            $casting->setPerson($this);
        }

        return $this;
    }

    public function removeCasting(Casting $casting): self
    {
        if ($this->castings->removeElement($casting)) {
            // set the owning side to null (unless already changed)
            if ($casting->getPerson() === $this) {
                $casting->setPerson(null);
            }
        }

        return $this;
    }
}
