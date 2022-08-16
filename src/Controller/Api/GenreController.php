<?php

namespace App\Controller\Api;

use App\Entity\Genre;
use App\Repository\GenreRepository;
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
 * @Route("/api/genres", name="api_genres_")
 */
class GenreController extends ApiController
{
    /**
     * @Route("/", name="browse", methods={"GET"})
     */
    public function browse(GenreRepository $genreRepository): JsonResponse
    {
        $genres = $genreRepository->findAll();

        return $this->json(
            $genres,
            Response::HTTP_OK,
            [],
            ["groups" =>[
                "app_api_genre_browse"
            ]]
        );
    }

    /**
     * @Route("/{id}", name="read", methods={"GET"})
     */
    public function read(Genre $genre = null): JsonResponse
    {
        if($genre === null)
        {
            return $this->json404("le genre n'a pas été trouvé");
        }
        return $this->json200(
            $genre,
            ["groups" => [
                "app_api_genre"
            ]]
        );
    }

    /**
     * @Route("",name="add", methods={"POST"})
     *
     * @param Request $request
     * @param GenreRepository $genreRepository
     * @param SerializerInterface $serializerInterface
     * @param ValidatorInterface $validator
     * @return JsonResponse
     */
    public function add(Request $request, GenreRepository $genreRepository, SerializerInterface $serializerInterface, ValidatorInterface $validator): JsonResponse
    {
        if (!$this->isGranted("ROLE_MANAGER"))
        {
            return $this->json(["error"=>"Authorised user only"], Response::HTTP_FORBIDDEN);
        }

        $jsonContent = $request->getContent();

        try 
        { 
            $newGenre = $serializerInterface->deserialize($jsonContent, Genre::class, 'json');
        }
        catch(Exception $e) 
        {
            return $this->json("Le JSON est mal formé", Response::HTTP_BAD_REQUEST);
        }

        $errors = $validator->validate($newGenre);
        
        if (count($errors)> 0)
        {
            return $this->json($errors, Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $genreRepository->add($newGenre, true);

        return $this->json(
            $newGenre,
            Response::HTTP_CREATED,
            [
                'Location' => $this->generateUrl('api_genres_read', ['id' => $newGenre->getGenreId()])
            ],
            [
                "groups" => "app_api_genre"
            ]
        );
    }
    

    /**
     * @Route("/{id}",name="edit", methods={"PUT", "PATCH"}, requirements={"id"="\d+"})
     */
    public function edit(Genre $genre = null, Request $request, ManagerRegistry $doctrine, SerializerInterface $serializerInterface): JsonResponse
    {
        if ($genre === null){ return $this->json404(); }

        $jsonContent = $request->getContent();

        $serializerInterface->deserialize($jsonContent,Genre::class, 'json', [AbstractNormalizer::OBJECT_TO_POPULATE => $genre]);

        $doctrine->getManager()->flush();
        
        return $this->json(
            $genre,
            Response::HTTP_PARTIAL_CONTENT,
            [
                'Location' => $this->generateUrl('api_genres_read', ['id' => $genre->getGenreId()])
            ],
            [
                "groups" => "app_api_genre"
            ]
        );
    }

    /**
     * @Route("/{id}",name="delete", methods={"DELETE"}, requirements={"id"="\d+"})
     * @param GenreRepository $genreRepository
     * @param Genre $genre
     */
    public function delete(Genre $genre = null, GenreRepository $genreRepository)
    {
        if ($genre === null){ return $this->json404(); }
        
        $genreRepository->remove($genre, true);

        return $this->json(
            null,
            Response::HTTP_NO_CONTENT,
            [
                'Location' => $this->generateUrl('api_genres_browse')
            ]
        );
    }
}
