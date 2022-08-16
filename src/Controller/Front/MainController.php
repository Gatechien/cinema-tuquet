<?php
// src/Controller/MainController.php 
namespace App\Controller\Front;

use App\Entity\Movie;
use App\Repository\GenreRepository;
use App\Repository\MovieRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class MainController extends AbstractController
{
    /**
     * @Route("/", name="home", methods={"GET"})
     * 
     * @param MovieRepository $movieRepository
     * @param GenreRepository $genreRepository
     * @return Response
     */
    public function home(MovieRepository $movieRepository, GenreRepository $genreRepository):Response
    {
        $moviesList = $movieRepository->findAllMoviesByTitleAscDQL();
        $genresList = $genreRepository->findAll();

        return $this->render('front/main/home.html.twig',[
            'moviesList' => $moviesList,
            'genresList' => $genresList
        ]);
    }

    /**
     * @Route("/genre/{id}", name="homeByGenre", methods={"GET"}, requirements={"id"="\d+"})
     *  
     * @param MovieRepository $movieRepository
     * @param GenreRepository $genreRepository
     * @return Response
     */
    public function homeByGenre(int $id, MovieRepository $movieRepository, GenreRepository $genreRepository):Response
    {
        $moviesList = $movieRepository->findAllMoviesByGenreSQL($id);
        $genresList = $genreRepository->findAll();

        return $this->render('front/main/home.html.twig',[
            'genresList' => $genresList,
            'moviesList' => $moviesList
        ]);
    }

    /**
     * @Route("/back", name="back_home", methods={"GET"})
     * 
     * @return Response
     */
    public function back():Response
    {
        return $this->render('back/main/home.html.twig');
    }

    /**
     * @Route("/movie/{slug}", name="movieShow", methods={"GET"}, requirements={"slug"="[\w-]+"})
     *  
     * @param MovieRepository $movieRepository
     * @param Movie $movie
     * @return Response
     */
    public function movieShow(MovieRepository $movieRepository, Movie $movie):Response
    {
        $movie = $movieRepository->find($movie);
        //dd($movie);
        if (is_null($movie)) {
            throw $this->createNotFoundException('Le film ou la Série n\'existe pas.');
        }
        return $this->render('front/main/movie-show.html.twig',[
            'movie' => $movie,
        ]);
    }

    /**
     * @Route("/list", name="list", methods={"GET"},)
     * 
     * @param MovieRepository $movieRepository
     * @param GenreRepository $genreRepository
     * @return Response
     */
    public function list(MovieRepository $movieRepository, GenreRepository $genreRepository):Response
    {
        $moviesList = $movieRepository->findAllMoviesByTitleAscQb();
        $genresList = $genreRepository->findAll();
        return $this->render('front/main/list.html.twig',[
            'moviesList' => $moviesList,
            'genresList' => $genresList
        ]);
    }
}
