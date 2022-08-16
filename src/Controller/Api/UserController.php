<?php

namespace App\Controller\Api;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\Persistence\ManagerRegistry;
use Exception;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @Route("/api/users", name="api_users_")
 */
class UserController extends ApiController
{
    /**
     * @Route("/", name="browse", methods={"GET"})
     */
    public function browse(UserRepository $userRepository): JsonResponse
    {
        $users = $userRepository->findAll();

        return $this->json(
            $users,
            Response::HTTP_OK,
            [],
            ["groups" =>[
                "app_api_user_browse"
            ]]
        );
    }

    /**
     * @Route("/{id}", name="read", methods={"GET"})
     */
    public function show(User $user = null): JsonResponse
    {
        if($user === null)
        {
            return $this->json404();
        }
        return $this->json200(
            $user,
            ["groups" => [
                "app_api_user"
            ]]
        );
    }

    /**
     * @Route("",name="add", methods={"POST"})
     *
     * @param Request $request
     * @param UserRepository $userRepository
     * @param SerializerInterface $serializerInterface
     * @param ValidatorInterface $validator
     * @return JsonResponse
     */
    public function add(Request $request, UserRepository $userRepository, SerializerInterface $serializerInterface, ValidatorInterface $validator, UserPasswordHasherInterface $hasher): JsonResponse
    {
        if (!$this->isGranted("ROLE_MANAGER"))
        {
            return $this->json(["error"=>"Authorised user only"], Response::HTTP_FORBIDDEN);
        }

        $jsonContent = $request->getContent();

        try 
        { 
            $newUser = $serializerInterface->deserialize($jsonContent, User::class, 'json');
        }
        catch(Exception $e) 
        {
            return $this->json("Le JSON est mal formé", Response::HTTP_BAD_REQUEST);
        }

        $errors = $validator->validate($newUser);
        
        if (count($errors)> 0)
        {
            return $this->json($errors, Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        $plaintextPassword = $newUser->getPassword();
        $hashedPassword = $hasher->hashPassword(
            $newUser,
            $plaintextPassword
        );
        $newUser->setPassword($hashedPassword);
        $userRepository->add($newUser, true);

        return $this->json(
            $newUser,
            Response::HTTP_CREATED,
            [
                'Location' => $this->generateUrl('api_users_read', ['id' => $newUser->getId()])
            ],
            [
                "groups" => "app_api_user"
            ]
        );
    }
    

    /**
     * @Route("/{id}",name="edit", methods={"PUT", "PATCH"}, requirements={"id"="\d+"})
     */
    public function edit(User $user = null, Request $request, ManagerRegistry $doctrine, SerializerInterface $serializerInterface, UserPasswordHasherInterface $hasher): JsonResponse
    {
        if ($user === null){ return $this->json404(); }

        $jsonContent = $request->getContent();

        $serializerInterface->deserialize($jsonContent,User::class, 'json', [AbstractNormalizer::OBJECT_TO_POPULATE => $user]);

        $plaintextPassword = $user->getPassword();
        $hashedPassword = $hasher->hashPassword(
            $user,
            $plaintextPassword
        );
        $user->setPassword($hashedPassword);

        $doctrine->getManager()->flush();
        
        return $this->json(
            $user,
            Response::HTTP_PARTIAL_CONTENT,
            [
                'Location' => $this->generateUrl('api_users_read', ['id' => $user->getId()])
            ],
            [
                "groups" => "app_api_user"
            ]
        );
    }

    /**
     * @Route("/{id}",name="delete", methods={"DELETE"}, requirements={"id"="\d+"})
     * @param UserRepository $userRepository
     * @param User $user
     */
    public function delete(User $user = null, UserRepository $userRepository)
    {
        if ($user === null){ return $this->json404(); }
        
        $userRepository->remove($user, true);

        return $this->json(
            null,
            Response::HTTP_NO_CONTENT,
            [
                'Location' => $this->generateUrl('api_users_browse')
            ]
        );
    }
}
