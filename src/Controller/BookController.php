<?php

namespace App\Controller;

use App\Entity\Book;
use App\Entity\Author;
use App\Form\BookType;
use App\Repository\BookRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Vich\UploaderBundle\Form\Type\VichFileType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\PropertyAccess\PropertyPath;
use Vich\UploaderBundle\Templating\Helper\UploaderHelper;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
/**
 * @Route("/book")
 */
class BookController extends AbstractController
{
    /**
     * @Route("/", name="book_index", methods={"GET"})
     */
    public function index(BookRepository $bookRepository): Response
    {
        return $this->render('book/index.html.twig', [
            'books' => $bookRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="book_new", methods={"GET","POST"})
     */
    public function new(Request $request, UploaderHelper $uploaderHelper ): Response
    {
        $book = new Book();
        $form = $this->createForm(BookType::class, $book);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
			$entityManager = $this->getDoctrine()->getManager();
			
			//
			$odtBookFile = $book->getOdtBookFile();
			$odtOriginalName = $odtBookFile->getClientOriginalName();

            $entityManager->persist($book);
			$entityManager->flush();
			
			$localPath = $uploaderHelper->asset($book, 'odtBookFile'); // $localPath is set once te entity is persisted ..
			$fileName = \pathinfo($localPath, PATHINFO_FILENAME);
			$fileExt = \pathinfo($localPath, PATHINFO_EXTENSION);

			$dirName = 'books/' . $fileName; // to rip leading slash !?
			$fileName = $dirName . '.' . $fileExt;

			dump($localPath, $dirName, $fileName);

			// unix cmd
			passthru('mkdir ' . $dirName . ' >>books/sorties_console 2>&1', $errCode );
			dump($errCode, $odtOriginalName, $localPath);
			
			if (!$errCode){
				passthru('unzip '. $fileName . ' -d ' . $dirName . ' >>books/sorties_console 2>&1', $errCode);
			}
			dd('blik', $errCode);
			
			return $this->redirectToRoute('book_index');
        }

        return $this->render('book/new.html.twig', [
            'book' => $book,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{slug}", name="book_show", methods={"GET"})
     */
    public function show(Book $book): Response
    {
        return $this->render('book/show.html.twig', [
            'book' => $book,
        ]);
    }

    /**
     * @Route("/{slug}/edit", name="book_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Book $book, UploaderHelper $uploaderHelper): Response
    {

		$odtBookSize = $book->getOdtBookSize(); // set if exists

		dump($book);

		$localPath = $uploaderHelper->asset($book, 'odtBookFile');
		$fileName = \pathinfo($localPath, PATHINFO_FILENAME);
		$fileExt = \pathinfo($localPath, PATHINFO_EXTENSION);

		$dirName = 'books/' . $fileName; // to rip leading slash !?
		$fileName = $dirName . '.' . $fileExt;

		$form = $this->createFormBuilder($book)
								->add('title')
								->add('summary')
								->add('publishedYear')
								->add('author', EntityType::class, [
									'class' => Author::class,
									'choice_label' => 'lastName'
								])
								->add('odtBookFile', VichFileType::class, [
									'label' => 'Document au format odt',
									'required' => false,
									'allow_delete' => false,
									'download_label' => new PropertyPath('odtBookName')
									
									// static function (Book $book) {
									// 	return $book->getTitle();
									// },
								])
					
								// ->add('odtBookName')
								// ->add('odtBookSize')
								// ->add('updatedAt')
								// ->add('author')
								->getForm();

		
		// $form = $this->createForm(BookType::class, $book);
		$form->handleRequest($request);
		
		if ($form->isSubmitted() && $form->isValid()) {
			
			$this->getDoctrine()->getManager()->flush();
			
			if (null !== $book->getOdtBookFile()){

				// a new book file has been loaded ..
				// need to remove previous document directory
				
				
				// unix cmd
				// delete previous directory recursive
				passthru('rm -r ' . $dirName . ' >>books/sorties_console 2>&1', $errCode );
				dump($errCode);
				
				// then create new document directory
				$localPath = $uploaderHelper->asset($book, 'odtBookFile');
				$fileName = \pathinfo($localPath, PATHINFO_FILENAME);
				$fileExt = \pathinfo($localPath, PATHINFO_EXTENSION);
		
				$dirName = 'books/' . $fileName; // to rip leading slash !?
				$fileName = $dirName . '.' . $fileExt;
		

				passthru('mkdir ' . $dirName . ' >>books/sorties_console 2>&1', $errCode );
				dump($errCode);
				
				// and unzip in it !
				passthru('unzip ' . $fileName . ' -d ' . $dirName . ' >>books/sorties_console 2>&1', $errCode);
				dump($errCode);

				if (!$errCode){}

			}
						

            return $this->redirectToRoute('book_index');
        }

        return $this->render('book/edit.html.twig', [
            'book' => $book,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{slug}", name="book_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Book $book): Response
    {
        if ($this->isCsrfTokenValid('delete'.$book->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($book);
            $entityManager->flush();
        }

        return $this->redirectToRoute('book_index');
    }
}
