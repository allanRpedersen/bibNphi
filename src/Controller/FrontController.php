<?php

namespace App\Controller;

use App\Entity\SentenceSearch;
use App\Form\SentenceSearchType;
use App\Repository\BookRepository;
use App\Repository\AuthorRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class FrontController extends AbstractController
{
    /**
     * @Route("/", name="front")
	 * @return Response
     */
    public function index(Request $request, BookRepository $bookRepository, AuthorRepository $authorRepository)
    {
		$search = new SentenceSearch();
		$form = $this->createForm(SentenceSearchType::class, $search);
		$form->handleRequest($request);

        return $this->render('front/index.html.twig', [
            'authors' => $authorRepository->findAll(),
			'books' => $bookRepository->findAll(),
			'form' => $form->createView(),
        ]);
    }
}
