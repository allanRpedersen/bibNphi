<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class ParagraphController extends AbstractController
{
    /**
     * @Route("/paragraph", name="paragraph")
     */
    public function index()
    {
        return $this->render('paragraph/index.html.twig', [
            'controller_name' => 'ParagraphController',
        ]);
	}
	
	
}
