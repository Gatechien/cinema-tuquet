<?php

namespace App\Controller\Front;

use App\Repository\MovieRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class FavoritesController extends AbstractController
{

    private $sessionTab;

    public function __construct(SessionInterface $session)
    {
        $this->sessionTab = $session->get('favoris') ?? [];
    }

    /**
     * @Route("/favorites", name="favorites_list", methods={"GET"})
     * 
     * @param MovieRepository $movieRepository
     * @return Response
     */
    public function list(MovieRepository $movieRepository): Response
    {;
        $favoritesList = [];
        foreach($this->sessionTab as $idMovie) {
            $favoritesList[] = $movieRepository->find($idMovie);
        }

        return $this->render('front/favorites/index.html.twig', [
            'favoritesList' => $favoritesList,
        ]);
    }

    // Ajout d'un nouveau film
    /**
     * @Route("/favorites/add", name="favorites_add", methods={"POST"})
     *
     * @param SessionInterface $session
     * @param Request $request
     * @return Response
     */
    public function add(Request $request, SessionInterface $session) :Response
    {
        $id_favorite = $request->request->get('id_favorite');

        array_push($this->sessionTab, $id_favorite);
        $this->sessionTab = array_unique($this->sessionTab);
        
        $session->set('favoris', $this->sessionTab);

        return $this->redirectToRoute('favorites_list');
    }

    // suppression d'un film
    /**
     * @Route("/favorites/delete/{id}", name="favorites_delete", methods={"POST"}, requirements={"id"="\d+"})
     *
     * @param SessionInterface $session
     * @param Request $request
     * @return Response
     */
    public function delete(Request $request, SessionInterface $session) :Response
    {
        //$id_favorite = $request->request->get('id_favorite');

        return $this->redirectToRoute('favorites_list');
    }

    // suppression de tous les films
    /**
     * @Route("/favorites/delete-all", name="favorites_delete-all", methods={"POST"})
     * 
     * @param SessionInterface $session
     * @param Request $request
     * @return Response
     */
    public function deleteAll(SessionInterface $session) :Response
    {
        $session->remove('favoris', $this->sessionTab);

        return $this->redirectToRoute('favorites_list');
    }
}
