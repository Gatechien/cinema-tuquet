<?php

namespace App\Repository;

use App\Entity\Movie;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Movie>
 *
 * @method Movie|null find($id, $lockMode = null, $lockVersion = null)
 * @method Movie|null findOneBy(array $criteria, array $orderBy = null)
 * @method Movie[]    findAll()
 * @method Movie[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MovieRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Movie::class);
    }

    public function add(Movie $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Movie $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
    
    //* DQL : Doctrine Query Language
    // On veut créer une méthode qui nous renvoie tous les Movies triés par leur titre en ordre alphabétique
    public function findAllMoviesByTitleAscDQL()
    {
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery('
            SELECT m FROM App\Entity\Movie m ORDER BY m.title ASC
        ');

        return $query->getResult();
    }
    
    //* Query Builder
    // On veut créer une méthode qui nous renvoie tous les Movies triés par leur titre en ordre alphabétique
    public function findAllMoviesByTitleAscQb()
    {
        $results = $this->createQueryBuilder('m')
            ->orderBy('m.title', 'ASC')
            ->getQuery()
            ->getResult();

        return $results;
    }

    public function findLastestByReleaseDate()
    {
        $queryBuilder = $this->createQueryBuilder('m') ;
        $queryBuilder = $queryBuilder->orderBy('m.releaseDate', 'DESC') ;
        $queryBuilder = $queryBuilder->setMaxResults(10);

        $query = $queryBuilder->getQuery();
        $results = $query->getResult();

        return $results;

        /*
          return $this->createQueryBuilder('m')
            ->orderBy('m.releaseDate', 'DESC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult();
         */
    }

    public function findLatestByReleaseDateDql()
    {
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery('
            SELECT m FROM App\Entity\Movie m ORDER BY m.releaseDate DESC
        ');

        return $query->setMaxResults(10)->getResult();

    }

    public function findAllMovieByTitleOrderAscQb(int $page){

        $pageSize = 3;
        $firstResult = ($page - 1) * $pageSize;

        $query = $this->createQueryBuilder('m')
            ->orderBy('m.title', 'ASC')
            ->setFirstResult($firstResult)
            ->setMaxResults($pageSize);

        $paginator = new Paginator($query, true);
        
        return $paginator;
    }

    public function findRandomMovie()
    {
        $allMovie = $this->findAll();
        // @link https://www.php.net/manual/fr/function.array-rand.php
        $randomMovie = $allMovie[array_rand($allMovie)];
        
        return $randomMovie;
    }

    public function findAllMoviesByGenreSQL($id)
    {
        $sql = "SELECT * FROM movie 
        INNER JOIN genre_movie ON movie_id = movie.id
        INNER JOIN genre ON genre.id = genre_id
        WHERE genre.id =".$id."
        ORDER BY movie.title";

        $dbal = $this->getEntityManager()->getConnection();
        $statement = $dbal->prepare($sql);
        $result = $statement->executeQuery();

        return $result->fetchAllAssociative();
    }
    
//    /**
//     * @return Movie[] Returns an array of Movie objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('m')
//            ->andWhere('m.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('m.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Movie
//    {
//        return $this->createQueryBuilder('m')
//            ->andWhere('m.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
