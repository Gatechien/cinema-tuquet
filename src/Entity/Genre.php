<?php
// src/Entity/Genre.php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Groups;
// ! Pour utiliser les possibilités de mapping de Doctrine
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class Genre
{
   

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer", name="id")
     * @Groups("app_api_genre")
     * @Groups("app_api_genre_browse")
     */
    private $genreId;

    /**
     * @ORM\Column(length=50)
     * @Assert\NotBlank
     * @Groups("app_api_genre")
     * @Groups("app_api_genre_browse")
     * @Groups("app_api_movie")
     */
    private $name;

    /**
     * @ORM\ManyToMany(targetEntity=Movie::class, inversedBy="genres")
     * @Groups("app_api_genre")
     */
    private $movie;

    public function __construct()
    {
        $this->movie = new ArrayCollection();
    }

    /**
     * retourne l'identifiant du genre
     */ 
    public function getGenreId():?int
    {
        return $this->genreId;
    }

    /**
     * @return string|null
     */ 
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param string
     * @return  self
     */ 
    public function setName(string $name):self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return Collection<int, Movie>
     */
    public function getMovie(): Collection
    {
        return $this->movie;
    }

    public function addMovie(Movie $movie): self
    {
        if (!$this->movie->contains($movie)) {
            $this->movie[] = $movie;
        }

        return $this;
    }

    public function removeMovie(Movie $movie): self
    {
        $this->movie->removeElement($movie);

        return $this;
    }
}