<?php

namespace App\Controller\Back;

use App\Entity\Movie;
use App\Form\MovieType;
use App\Repository\MovieRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
//use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * @link https://symfony.com/bundles/SensioFrameworkExtraBundle/current/annotations/security.html#isgranted
 * //@IsGranted("ROLE_ADMIN")
 * 
 * @Route("/back/movie")
 */
class MovieController extends AbstractController
{   
    /**
     * @Route("/", name="app_back_movie_index", methods={"GET"})
     */
    public function index(MovieRepository $movieRepository): Response
    {
        //$this->denyAccessUnlessGranted('ROLE_ADMIN');
        return $this->render('back/movie/index.html.twig', [
            'movies' => $movieRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="app_back_movie_new", methods={"GET", "POST"})
     */
    public function new(Request $request, MovieRepository $movieRepository, SluggerInterface $slugger): Response
    {
        $movie = new Movie();
        $form = $this->createForm(MovieType::class, $movie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            
            $slug = $slugger->slug($movie->getTitle())->lower();
            $movie->setSlug($slug);
            $movieRepository->add($movie, true);

            return $this->redirectToRoute('app_back_movie_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('back/movie/new.html.twig', [
            'movie' => $movie,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{slug}", name="app_back_movie_show", methods={"GET"}, requirements={"slug"="[\w-]+"})
     */
    public function show(Movie $movie): Response
    {
        return $this->render('back/movie/show.html.twig', [
            'movie' => $movie,
        ]);
    }

    /**
     * @Route("/{slug}/edit", name="app_back_movie_edit", methods={"GET", "POST"}, requirements={"slug"="[\w-]+"})
     */
    public function edit(Request $request, Movie $movie, MovieRepository $movieRepository, SluggerInterface $slugger): Response
    {
        //$this->denyAccessUnlessGranted('MOVIE_EDIT', $movie);

        $form = $this->createForm(MovieType::class, $movie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            
            $slug = $slugger->slug($movie->getTitle())->lower();
            $movie->setSlug($slug);
            $movieRepository->add($movie, true);

            return $this->redirectToRoute('app_back_movie_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('back/movie/edit.html.twig', [
            'movie' => $movie,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{slug}", name="app_back_movie_delete", methods={"POST"}, requirements={"slug"="[\w-]+"})
     */
    public function delete(Request $request, Movie $movie, MovieRepository $movieRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$movie->getId(), $request->request->get('_token'))) {
            $movieRepository->remove($movie, true);
        }

        return $this->redirectToRoute('app_back_movie_index', [], Response::HTTP_SEE_OTHER);
    }
}
