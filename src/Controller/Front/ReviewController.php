<?php

namespace App\Controller\Front;

use App\Entity\Review;
use App\Entity\Movie;
use App\Form\ReviewType;
use App\Services\AutoRating;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ReviewController extends AbstractController
{
    /**
     * @Route("/add/review/{slug}", name="add_review", methods={"POST","GET"}, requirements={"slug"="[\w-]+"})
     * @param Movie $movie
     * @return Response
     */
    public function add(ManagerRegistry $doctrine, Request $request, Movie $movie, AutoRating $serviceRating): Response
    {
        $newReview = new Review();

        $form = $this->createForm(ReviewType::class, $newReview);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $newReview->setMovie($movie);

            $newRating = $serviceRating->calculRating($movie, $newReview->getRating());
            $movie->setRating($newRating);

            $entityManager = $doctrine->getManager();
            $entityManager->persist($newReview);
            $entityManager->flush();

            return $this->redirectToRoute('movieShow', ['slug' => $movie->getSlug()]);
        }
        return $this->renderForm('front/review/add.html.twig', [
            'form' => $form,
            'movie' => $movie
        ]);
    }
}
