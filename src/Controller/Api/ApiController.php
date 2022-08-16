<?php

namespace App\Controller\Api;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class ApiController extends AbstractController
{
    public function json404(string $message = 'not found')
    {
        return $this->json(
            [
                "erreur" => $message,
                "code_error" => 404
            ],
            Response::HTTP_NOT_FOUND,// 404
        );
    }

    public function json200($data, $groups)
    {
        return $this->json(
            $data,
            Response::HTTP_OK,
            [],
            $groups
        );
    }

    public function json201($data, $groups)
    {
        return $this->json(
            $data,
            Response::HTTP_OK,
            [],
            $groups
        );
    }

    public function json422($errors, $data)
    {
        $messages = [];

        for ($i = 0; $i < count($errors); $i++) {
            $messages['error' . $i] = $errors[$i]->getMessage();
        }

        return $this->json(
            [$data, $messages],
            422
        );
    }
}