<?php

namespace App\DataFixtures;

use App\Entity\Casting;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

use App\Entity\Movie;
use App\Entity\Season;
use App\Entity\Genre;
use App\Entity\Person;

use App\DataFixtures\Provider\CineTProvider;
use App\Services\OmdbApi;
use Symfony\Component\String\Slugger\SluggerInterface;

use Faker;

use Doctrine\DBAL\Connection;

class AppFixtures extends Fixture
{
    private $connection;

    public function __construct(Connection $connection, SluggerInterface $slugger, OmdbApi $omdbApi)
    {
        $this->connection = $connection;
        $this->slugger = $slugger;
        $this->omdbApi = $omdbApi;
    }

    /**
     * Permet de TRUNCATE les tables et de remettre les Auto-incréments à 1
     */
    private function truncate()
    {
        $this->connection->executeQuery('SET foreign_key_checks = 0');

        $this->connection->executeQuery('TRUNCATE TABLE casting');
        $this->connection->executeQuery('TRUNCATE TABLE genre');
        $this->connection->executeQuery('TRUNCATE TABLE movie');
        $this->connection->executeQuery('TRUNCATE TABLE genre_movie');
        $this->connection->executeQuery('TRUNCATE TABLE person');
        $this->connection->executeQuery('TRUNCATE TABLE season');

        $this->connection->executeQuery('SET foreign_key_checks = 1');
    }

    /**
     *
     * @param ObjectManager $manager
     * @return void
     */
    public function load(ObjectManager $manager): void
    {
        $this->truncate();

        $cineTProvider = new CineTProvider();

        $faker = Faker\Factory::create('fr_FR');

        $moviesList = [];

        for ($i=1; $i<20; $i++) {

            $movie = new Movie();

            $movie->setTitle($cineTProvider->getRandomMovieTitle());
            $api = $this->omdbApi->fetch($movie->getTitle());

            if($api['Response'] === 'True') {

                if($api['Type'] === 'movie') {
                    $type = 'film';
                }  else {
                    $type ='série';
                }
                $movie->setType($type);
                $movie->setSummary($api['Plot']);
                $movie->setSynopsis($api['Plot']);
                $movie->setReleaseDate($faker->dateTimeBetween('-80 years'));
                $release = explode(" ", $api['Released']);
                $movie->setDuration($release [0]);
                $movie->setPoster($api['Poster']);
                $rating = $faker->randomFloat(1, 1, 5);
                $movie->setRating($rating);
                $movie->setSlug($this->slugger->slug($movie->getTitle())->lower());
            } else {

                $type = (mt_rand(1, 2) === 1) ? "série" : "film";
                $movie->setType($type);
                $movie->setSummary($faker->paragraph(2));
                $movie->setSynopsis($faker->realText(300));
                // Faker va nous générer une date aléatoire entre aujourd'hui et il y a 80 ans
                //?https://fakerphp.github.io/formatters/date-and-time/#datetimebetween
                $movie->setReleaseDate($faker->dateTimeBetween('-80 years'));
                $duration = $faker->numberBetween(30, 240);
                $movie->setDuration($duration);
                $movie->setPoster('https://amc-theatres-res.cloudinary.com/amc-cdn/static/images/fallbacks/DefaultOneSheetPoster.jpg');
                // Pour avoir une note avec des nombres décimaux
                //?https://fakerphp.github.io/formatters/numbers-and-strings/#randomfloat
                $rating = $faker->randomFloat(1, 1, 5);
                $movie->setRating($rating);
                $movie->setSlug($this->slugger->slug($movie->getTitle())->lower());
            }

            $moviesList[] = $movie;
            $manager->persist($movie);
        }

        //#Season

        foreach($moviesList as $key => $movie)
        {
            if ($movie->getType() === 'série' )
            {
                $nbMaxSeasons = mt_rand(1,10);

                for ($j=1; $j < $nbMaxSeasons; $j++)
                {
                    $season = new Season(); // création d'une saison
                    $season->setNumber($j); // je lui assigne son numéro de saison
                    $season->setEpisodesNumber(mt_rand(3,16)); // je génère et assigne son nombre d'épisodes
                    $season->setMovie($movie);
                    $manager->persist($season);
                }
            }
        }

        //#Genre

        $genresList = [];

        for ($k=1; $k<=20; $k++) {
            $genre = new Genre();
            $genre->setName($cineTProvider->getRandomMovieGenre());
            $genresList[] = $genre;
            $manager->persist($genre);
        }

        //#Person

        $personsList = [];

        for ($l=1; $l<=200; $l++) {
            $person = new Person();
            $person->setFirstname($faker->firstname());
            $person->setLastname($faker->lastname());
            $personsList[] = $person;
            $manager->persist($person);
        }

        //#Casting

         foreach($moviesList as $key => $movie)
         {
            $nbMaxCasting = mt_rand(2,5);

            for ($m=1; $m < $nbMaxCasting; $m++)
            {
                $casting = new Casting();
                $casting->setCreditOrder($m);
                //?https://fakerphp.github.io/#modifiers
                $casting->setRole($faker->unique()->name());
                $casting->setPerson($personsList[mt_rand(0,199)]);
                $casting->setMovie($movie);
                $manager->persist($casting);
            }
         }

        //# Relation entre Genre et Movie

        foreach($moviesList as $key => $movie)
        {
            $nbMaxGenre = mt_rand(1,3);
            for ($n=1; $n<=$nbMaxGenre; $n++) {

                $movie->addGenre( $genresList[ mt_rand(0, count($genresList) - 1) ] );
                $manager->persist($movie);
            }
        }
        
        $manager->flush();
    }
}
