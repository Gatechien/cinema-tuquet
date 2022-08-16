<?php

namespace App\Controller\Api;

use App\Entity\Person;
use App\Repository\PersonRepository;
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
 * @Route("/api/persons", name="api_persons_")
 */
class PersonController extends ApiController
{
    /**
     * @Route("/", name="browse", methods={"GET"})
     */
    public function browse(PersonRepository $personRepository): JsonResponse
    {
        $persons = $personRepository->findAll();

        return $this->json(
            $persons,
            Response::HTTP_OK,
            [],
            ["groups" =>[
                "app_api_person_browse"
            ]]
        );
    }

    /**
     * @Route("/{id}", name="read", methods={"GET"})
     */
    public function read(Person $person = null): JsonResponse
    {
        if($person === null)
        {
            return $this->json404();
        }
        return $this->json200(
            $person,
            ["groups" => [
                "app_api_person"
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
            $newPerson = $serializerInterface->deserialize($jsonContent, Person::class, 'json');
        }
        catch(Exception $e) 
        {
            return $this->json("Le JSON est mal formé", Response::HTTP_BAD_REQUEST);
        }

        $errors = $validator->validate($newPerson);
        
        if (count($errors)> 0)
        {
            return $this->json($errors, Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $em = $manager->getManager();
        $em->persist($newPerson);
        $em->flush();

        return $this->json(
            $newPerson,
            Response::HTTP_CREATED,
            [
                'Location' => $this->generateUrl('api_persons_read', ['id' => $newPerson->getId()])
            ],
            [
                "groups" => "app_api_person"
            ]
        );
    }

    /**
     * @Route("/{id}",name="edit", methods={"PUT", "PATCH"}, requirements={"id"="\d+"})
     */
    public function edit(Person $person = null, Request $request, ManagerRegistry $doctrine, SerializerInterface $serializerInterface): JsonResponse
    {
        if ($person === null){ return $this->json404(); }

        $jsonContent = $request->getContent();

        $serializerInterface->deserialize($jsonContent,Person::class, 'json', [AbstractNormalizer::OBJECT_TO_POPULATE => $person]);

        $doctrine->getManager()->flush();
        
        return $this->json(
            $person,
            Response::HTTP_PARTIAL_CONTENT,
            [
                'Location' => $this->generateUrl('api_persons_read', ['id' => $person->getId()])
            ],
            [
                "groups" => "app_api_person"
            ]
        );
    }

    /**
     * @Route("/{id}",name="delete", methods={"DELETE"}, requirements={"id"="\d+"})
     * @param PersonRepository $personRepository
     * @param Person $person
     */
    public function delete(Person $person = null, PersonRepository $personRepository)
    {
        if ($person === null){ return $this->json404(); }
        
        $personRepository->remove($person, true);

        return $this->json(
            null,
            Response::HTTP_NO_CONTENT,
            [
                'Location' => $this->generateUrl('api_persons_browse')
            ]
        );
    }
}
