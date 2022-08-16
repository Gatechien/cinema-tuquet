<?php

namespace App\Controller\Api;

use App\Entity\Casting;
use App\Repository\CastingRepository;
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
 * @Route("/api/castings", name="api_castings_")
 */
class CastingController extends ApiController
{
    /**
     * @Route("/", name="browse", methods={"GET"})
     */
    public function browse(CastingRepository $castingRepository): JsonResponse
    {
        $castings = $castingRepository->findAll();

        return $this->json(
            $castings,
            Response::HTTP_OK,
            [],
            ["groups" =>[
                "app_api_casting_browse"
            ]]
        );
    }

    /**
     * @Route("/{id}", name="read", methods={"GET"})
     */
    public function read(Casting $casting = null): JsonResponse
    {
        if($casting === null)
        {
            return $this->json404();
        }
        return $this->json200(
            $casting,
            ["groups" => [
                "app_api_casting"
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
            $newCasting = $serializerInterface->deserialize($jsonContent, Casting::class, 'json');
        }
        catch(Exception $e) 
        {
            return $this->json("Le JSON est mal formé", Response::HTTP_BAD_REQUEST);
        }

        $errors = $validator->validate($newCasting);
        
        if (count($errors)> 0)
        {
            return $this->json($errors, Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $em = $manager->getManager();
        $em->persist($newCasting);
        $em->flush();

        return $this->json(
            $newCasting,
            Response::HTTP_CREATED,
            [
                'Location' => $this->generateUrl('api_castings_read', ['id' => $newCasting->getId()])
            ],
            [
                "groups" => "app_api_casting"
            ]
        );
    }

    /**
     * @Route("/{id}",name="edit", methods={"PUT", "PATCH"}, requirements={"id"="\d+"})
     */
    public function edit(Casting $casting = null, Request $request, ManagerRegistry $doctrine, SerializerInterface $serializerInterface): JsonResponse
    {
        if ($casting === null){ return $this->json404(); }

        $jsonContent = $request->getContent();

        $serializerInterface->deserialize($jsonContent,Casting::class, 'json', [AbstractNormalizer::OBJECT_TO_POPULATE => $casting]);

        $doctrine->getManager()->flush();
        
        return $this->json(
            $casting,
            Response::HTTP_PARTIAL_CONTENT,
            [
                'Location' => $this->generateUrl('api_castings_read', ['id' => $casting->getId()])
            ],
            [
                "groups" => "app_api_casting"
            ]
        );
    }

    /**
     * @Route("/{id}",name="delete", methods={"DELETE"}, requirements={"id"="\d+"})
     * @param CastingRepository $castingRepository
     * @param Casting $casting
     */
    public function delete(Casting $casting = null, CastingRepository $castingRepository)
    {
        if ($casting === null){ return $this->json404(); }
        
        $castingRepository->remove($casting, true);

        return $this->json(
            null,
            Response::HTTP_NO_CONTENT,
            [
                'Location' => $this->generateUrl('api_castings_browse')
            ]
        );
    }
}
