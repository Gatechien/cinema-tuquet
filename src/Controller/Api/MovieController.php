<?php

namespace App\Controller\Api;

use App\Entity\Genre;
use App\Entity\Movie;
use App\Entity\Review;
use App\Repository\MovieRepository;
use Doctrine\Persistence\ManagerRegistry;
use Exception;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @Route("/api/movies", name="api_movies_")
 */
class MovieController extends ApiController
{
    /**
     * @Route("/", name="browse", methods={"GET"})
     */
    public function browse(MovieRepository $movieRepository): JsonResponse
    {
        $movies = $movieRepository->findAll();
        return $this->json(
            $movies,
            Response::HTTP_OK,
            [],
            ["groups" =>[
                "app_api_movie_browse"
            ]]
        );
    }

    /**
     * @Route("/{id}", name="read", methods={"GET"})
     */
    public function read(Movie $movie = null): JsonResponse
    {
        if($movie === null)
        {
            return $this->json404("le film n'a pas été trouvé");
        }
        return $this->json(
            $movie,
            Response::HTTP_OK,
            [],
            ["groups" =>[
                "app_api_movie"
            ]]
        );
    }

    /**
     * @Route("/advanced/{id}", name="advanced_read", methods={"GET"})
     */
    public function readAdvanced(Movie $movie = null): JsonResponse
    {
        if($movie === null)
        {
            return $this->json404("le film n'a pas été trouvé");
        }
        return $this->json200(
            $movie,
            ["groups" => [
                "app_api_movie"
            ]]
        );
    }

    /**
     * @Route("/{id}/review", name="app_api_movie_review_show", methods={"GET"})
     */
    public function showReview(Movie $movieReview = null): JsonResponse
    {
        if($movieReview === null)
        {
            return $this->json404("le film n'a pas de commentaire");
        }
        return $this->json201(
            $movieReview,
            ["groups" => [
                "app_api_review"
            ]]
        );
    }

    /**
     * @Route("/{id}/season", name="app_api_movie_season_show", methods={"GET"})
     */
    public function showSeason(Movie $movieSeason = null): JsonResponse
    {
        if($movieSeason === null)
        {
            return $this->json404("c'est un film et non une série");
        }
        return $this->json200(
            $movieSeason,
            ["groups" => [
                "app_api_season"
            ]]
        );
    }

    /**
     * @Route("",name="add", methods={"POST"})
     *
     * @param Request $request
     * @param ManagerRegistry $manager
     * @param SerializerInterface $serializerInterface
     * @param ValidatorInterface $validator
     * @return JsonResponse
     */
    public function add(Request $request, ManagerRegistry $manager, SerializerInterface $serializerInterface, ValidatorInterface $validator): JsonResponse
    {
        if (!$this->isGranted("ROLE_MANAGER"))
        {
            return $this->json(["error"=>"Authorised user only"], Response::HTTP_FORBIDDEN);
        }

        $jsonContent = $request->getContent();

        try
        {
            $newMovie = $serializerInterface->deserialize($jsonContent, Movie::class, 'json');    
        }
        catch(Exception $e)
        {
            return $this->json("Le JSON est mal formé", Response::HTTP_BAD_REQUEST);
        }

        $errors = $validator->validate($newMovie);
        
        if (count($errors)> 0)
        {
            return $this->json422($errors, Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $em = $manager->getManager();
        $em->persist($newMovie);
        $em->flush();

        return $this->json(
            $newMovie,
            Response::HTTP_CREATED,
            [
                'Location' => $this->generateUrl('api_movies_read', ['id' => $newMovie->getId()])
            ],
            [
                "groups" => "app_api_movie"
            ]
        );
    }

    /**
     * @Route("/{id}",name="edit", methods={"PUT", "PATCH"}, requirements={"id"="\d+"})
     */
    public function edit(Movie $movie = null, Request $request, ManagerRegistry $doctrine, SerializerInterface $serializerInterface): JsonResponse
    {
        if ($movie === null){ return $this->json404(); }

        $jsonContent = $request->getContent();

        $serializerInterface->deserialize($jsonContent,Movie::class, 'json', [AbstractNormalizer::OBJECT_TO_POPULATE => $movie]);

        $doctrine->getManager()->flush();
        
        return $this->json(
            $movie,
            Response::HTTP_PARTIAL_CONTENT,
            [
                'Location' => $this->generateUrl('api_movies_read', ['id' => $movie->getId()])
            ],
            [
                "groups" => "app_api_movie"
            ]
        );
    }

    /**
     * @Route("/{id}",name="delete", methods={"DELETE"}, requirements={"id"="\d+"})
     * @param MovieRepository $movieRepository
     * @param Movie $movie
     */
    public function delete(Movie $movie = null, MovieRepository $movieRepository)
    {
        if ($movie === null){ return $this->json404(); }
        
        $movieRepository->remove($movie, true);

        return $this->json(
            null,
            Response::HTTP_NO_CONTENT,
            [
                'Location' => $this->generateUrl('api_movies_browse')
            ]
        );
    }
}
