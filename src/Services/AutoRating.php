<?php

namespace App\Services;

use App\Entity\Movie;

class AutoRating
{
    /**
     * Calcul d'un rating avant l'insertion en BDD d'un review
     *
     * @param Movie $movie le film concerné
     * @param integer $newRating le rating de la nouvelle critique
     * @return float le nouveau rating du film
     */
    public function calculRating(Movie $movie, int $newRating): float
    {
        $allReviews = $movie->getReviews();

        $totalRating = 0;
        foreach ($allReviews as $review)
        {
            $rating = $review->getRating();
            $totalRating += $rating;
        }

        $totalRating += $newRating;
   
        $nombreDeReview = count($allReviews) + 1;

        // @link https://www.php.net/manual/fr/function.round.php
        $calculRating = round($totalRating / $nombreDeReview, 2);

        return $calculRating;
    }
}