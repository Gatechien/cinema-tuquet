<?php

namespace App\Entity;

use App\Repository\MovieRepository;
use App\Entity\Review;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=MovieRepository::class)
 */
class Movie
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups("app_api_movie")
     * @Groups("app_api_movie_browse")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank
     * @Groups("app_api_movie")
     * @Groups("app_api_movie_browse")
     * @Groups("app_api_casting")
     * @Groups("app_api_season")
     * @Groups("app_api_genre")
     */
    private $title;

    /**
     * @ORM\Column(type="string", length=20)
     * @Assert\NotBlank
     * @Groups("app_api_movie")
     * @Groups("app_api_movie_browse")
     * @Groups("app_api_casting")
     * @Groups("app_api_genre")
     * @Groups("app_api_person")
     */
    private $type;

    /**
     * @ORM\Column(type="date", nullable=true)
     * @Assert\NotBlank
     * @Groups("app_api_movie")
     */
    private $releaseDate;

    /**
     * @ORM\OneToMany(targetEntity=Season::class, mappedBy="movie", orphanRemoval=true)
     * @Groups("app_api_movie")
     * @Groups("app_api_season")
     */
    private $seasons;

    /**
     * @ORM\ManyToMany(targetEntity=Genre::class, mappedBy="movie")
     * @Groups("app_api_movie")
     */
    private $genres;

    /**
     * @ORM\OneToMany(targetEntity=Casting::class, mappedBy="movie", orphanRemoval=true)
     * @ORM\OrderBy({"creditOrder" = "ASC"})
     * @Groups("app_api_movie")
     * 
     * #https://www.doctrine-project.org/projects/doctrine-orm/en/2.11/reference/annotations-reference.html#annref_orderby
     */
    private $castings;

    /**
     * @ORM\Column(type="integer")
     * @Assert\NotBlank
     * @Groups("app_api_movie")
     * @Groups("app_api_movie_browse")
     */
    private $duration;

    /**
     * @ORM\Column(type="string", length=600, nullable=true)
     * @Assert\NotBlank
     * @Groups("app_api_movie")
     * @Groups("app_api_movie_browse")
     */
    private $summary;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @Assert\NotBlank
     * @Groups("app_api_movie")
     * @Groups("app_api_movie_browse")
     */
    private $synopsis;

    /**
     * @ORM\Column(type="text", length=2000, nullable=true)
     * @Assert\Url(
     * relativeProtocol = true)
     * @Groups("app_api_movie")
     * @Groups("app_api_movie_browse")
     */
    private $poster;

    /**
     * @ORM\Column(type="float", nullable=true)
     * @Groups("app_api_movie")
     * @Groups("app_api_movie_browse")
     */
    private $rating;

    /**
     * @ORM\OneToMany(targetEntity=Review::class, mappedBy="movie")
     * @Groups("app_api_movie")
     * @Groups("app_api_review")
     */
    private $reviews;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups("app_api_movie")
     * @Groups("app_api_movie_show")
     */
    private $slug;

    public function __construct()
    {
        $this->seasons = new ArrayCollection();
        $this->genres = new ArrayCollection();
        $this->castings = new ArrayCollection();
        $this->reviews = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function getTitleType(): ?string
    {
        $titleType = $this->title . ' est un(e) ' . $this->type;

        return $titleType;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getReleaseDate(): ?\DateTimeInterface
    {
        return $this->releaseDate;
    }

    public function setReleaseDate(?\DateTimeInterface $releaseDate): self
    {
        $this->releaseDate = $releaseDate;

        return $this;
    }

    /**
     * @return Collection<int, Season>
     */
    public function getSeasons(): Collection
    {
        return $this->seasons;
    }

    public function addSeason(Season $season): self
    {
        if (!$this->seasons->contains($season)) {
            $this->seasons[] = $season;
            $season->setMovie($this);
        }

        return $this;
    }

    public function removeSeason(Season $season): self
    {
        if ($this->seasons->removeElement($season)) {
            // set the owning side to null (unless already changed)
            if ($season->getMovie() === $this) {
                $season->setMovie(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Genre>
     */
    public function getGenres(): Collection
    {
        return $this->genres;
    }

    public function addGenre(Genre $genre): self
    {
        if (!$this->genres->contains($genre)) {
            $this->genres[] = $genre;
            $genre->addMovie($this);
        }

        return $this;
    }

    public function removeGenre(Genre $genre): self
    {
        if ($this->genres->removeElement($genre)) {
            $genre->removeMovie($this);
        }

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
            $casting->setMovie($this);
        }

        return $this;
    }

    public function removeCasting(Casting $casting): self
    {
        if ($this->castings->removeElement($casting)) {
            // set the owning side to null (unless already changed)
            if ($casting->getMovie() === $this) {
                $casting->setMovie(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Review>
     */
    public function getReviews(): Collection
    {
        return $this->reviews;
    }

    public function addReview(Review $review): self
    {
        if (!$this->reviews->contains($review)) {
            $this->reviews[] = $review;
            $review->setMovie($this);
        }

        return $this;
    }

    public function removeReview(Review $review): self
    {
        if ($this->reviews->removeElement($review)) {
            // set the owning side to null (unless already changed)
            if ($review->getMovie() === $this) {
                $review->setMovie(null);
            }
        }

        return $this;
    }

    public function getDuration(): ?int
    {
        return $this->duration;
    }

    public function setDuration(int $duration): self
    {
        $this->duration = $duration;

        return $this;
    }

    public function getSummary(): ?string
    {
        return $this->summary;
    }

    public function setSummary(?string $summary): self
    {
        $this->summary = $summary;

        return $this;
    }

    public function getSynopsis(): ?string
    {
        return $this->synopsis;
    }

    public function setSynopsis(?string $synopsis): self
    {
        $this->synopsis = $synopsis;

        return $this;
    }

    public function getPoster(): ?string
    {
        return $this->poster;
    }

    public function setPoster(?string $poster): self
    {
        $this->poster = $poster;

        return $this;
    }

    public function getRating(): ?int
    {
        return $this->rating;
    }

    public function setRating(?int $rating): self
    {
        $this->rating = $rating;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(?string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }
}
