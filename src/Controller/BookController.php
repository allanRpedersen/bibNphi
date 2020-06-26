<?php

namespace App\Controller;

use App\Entity\Book;
use App\Entity\Author;
use App\Form\BookType;
use App\Entity\BookSentence;
use App\Entity\BookParagraph;
use App\Repository\BookRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Vich\UploaderBundle\Form\Type\VichFileType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\PropertyAccess\PropertyPath;
use Vich\UploaderBundle\Templating\Helper\UploaderHelper;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
/**
 * @Route("/book")
 */
class BookController extends AbstractController
{

	/**
	 * XML parser
	 *
	 */
	private $parser;

	private $insideNote,
			$insideAnnotation,
			$counter,
			$text,
			$isNoteBody,
			$isNoteCitation,
			$noteBody,
			$noteCitation,
			$noteCollection;

	private $nbBookWords,
			$nbBookSentences,
			$nbBookParagraphs;

	private $book;

	public function __construct()
	{
	}

    /**
     * @Route("/", name="book_index", methods={"GET"})
     */
    public function index(BookRepository $bookRepository): Response
    {
        return $this->render('book/index.html.twig', [
			'books' => $bookRepository->findByTitle(),
		]);
    }

    /**
     * @Route("/new", name="book_new", methods={"GET","POST"})
	 * @IsGranted("ROLE_USER")
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

			$book->setNbParagraphs(0)
				->setNbSentences(0)
				->setNbWords(0)
				->setParsingTime(0)
				;

            $entityManager->persist($book);
			$entityManager->flush();
			
			$localPath = $uploaderHelper->asset($book, 'odtBookFile'); // $localPath is set once the entity is persisted ..
			$fileName = \pathinfo($localPath, PATHINFO_FILENAME);
			$fileExt = \pathinfo($localPath, PATHINFO_EXTENSION);

			$dirName = 'books/' . $fileName; // to rip leading slash !?
			$fileName = $dirName . '.' . $fileExt;

			//
			// unix cmd
			passthru('mkdir ' . $dirName . ' >>books/sorties_console 2>&1', $errCode );
			
			if (!$errCode){
				passthru('unzip '. $fileName . ' -d ' . $dirName . ' >>books/sorties_console 2>&1', $errCode);
			}

			//
			// xml parsing !
			$this->book = $book;
			$book->setParsingTime($this->parseXmlContent($dirName . '/content.xml'))
				->setNbParagraphs($this->nbBookParagraphs)
				->setNbSentences($this->nbBookSentences)
				->setNbWords($this->nbBookWords)
				;
			
			$entityManager->persist($book);
			$entityManager->flush();
			
			return $this->redirectToRoute('book_show', [
				'slug' => $book->getSlug()
			]);
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
	 * @IsGranted("ROLE_USER")
     */
    public function edit(Request $request, Book $book, UploaderHelper $uploaderHelper): Response
    {

		$odtBookSize = $book->getOdtBookSize(); // set if exists

		// dump($book);

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
					])
					->getForm();

		
		// $form = $this->createForm(BookType::class, $book);
		$form->handleRequest($request);
		
		if ($form->isSubmitted() && $form->isValid()) {
			
            $entityManager = $this->getDoctrine()->getManager();
			$entityManager->flush();
			
			if (null !== $book->getOdtBookFile()){

				// a new book file has been loaded ..
				// need to remove previous document directory
				
				
				// unix cmd
				// delete previous directory recursive
				passthru('rm -r ' . $dirName . ' >>books/sorties_console 2>&1', $errCode );
				
				// then create new document directory
				$localPath = $uploaderHelper->asset($book, 'odtBookFile');
				$fileName = \pathinfo($localPath, PATHINFO_FILENAME);
				$fileExt = \pathinfo($localPath, PATHINFO_EXTENSION);
		
				$dirName = 'books/' . $fileName; // to rip leading slash !?
				$fileName = $dirName . '.' . $fileExt;
		
				// unix cmd
				// create new directory
				passthru('mkdir ' . $dirName . ' >books/sorties_console 2>&1', $errCode );
				
				// and unzip in it !
				passthru('unzip ' . $fileName . ' -d ' . $dirName . ' >books/sorties_console 2>&1', $errCode);

				// if (!$errCode){}

				//
				// xml parsing !!
				$this->book = $book;
				$book->setParsingTime($this->parseXmlContent($dirName . '/content.xml'))
					->setNbParagraphs($this->nbBookParagraphs)
					->setNbSentences($this->nbBookSentences)
					->setNbWords($this->nbBookWords)
					;
				
				$entityManager->persist($book);
				$entityManager->flush();
				
			}
						
            return $this->redirectToRoute('book_show', [
				'slug' => $book->getSlug()
			]);
        }

        return $this->render('book/edit.html.twig', [
            'book' => $book,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{slug}", name="book_delete", methods={"DELETE"})
	 * @IsGranted("ROLE_USER")
     */
    public function delete(Request $request, Book $book): Response
    {
        if ($this->isCsrfTokenValid('delete'.$book->getId(), $request->request->get('_token'))) {
			$entityManager = $this->getDoctrine()->getManager();
			
			foreach( $book->getBookParagraphs() as $paragraph ){
				$book->removeBookParagraph($paragraph);
			}

			//
			//
			$dirName = $book->getOdtBookName();
			
			// remove .whatever to get directory name
			$dirName = substr($dirName, 0, strpos($dirName, '.'));

			// unix cmd
			// delete associated directory recursive
			passthru('rm -r books/' . $dirName . ' >>books/sorties_console 2>&1', $errCode );

			//
			//
            $entityManager->remove($book);
            $entityManager->flush();
        }

        return $this->redirectToRoute('book_index');
	}


	/**
	 *      O D T   X M L   p a r s i n g
	 */
	private function start_element_handler($parser, $element, $attribs)
	{

		switch($element){

			case "TEXT:P" ;
			case "TEXT:H" ;
				$this->counter++;
				// dump([$element, $attribs]);
				break;
			
			case "TEXT:SPAN":
			case "DRAW:FRAME" ;
			case "DRAW:IMAGE" ;
			// dump([$element, $attribs]);
			break;
			
			case "OFFICE:ANNOTATION" ;
				$this->insideAnnotation = true;
				break;

			case "TEXT:NOTE" ;
				$this->text .= '(#';
				$this->insideNote = true;
				break;
				
			case "TEXT:NOTE-CITATION" ;
				$this->isNoteCitation = TRUE;
				// dump([$element, $attribs]);
				break;
				
			case "TEXT:NOTE-BODY" ;
				$this->isNoteBody = true;
				// dump([$element, $attribs]);
				break;
			
		} 
	}

	private function end_element_handler($parser, $element)
	{
		switch($element){
			case "TEXT:P" ;
			case "TEXT:H" ;
				if (!$this->insideNote){

					$this->handleBookParagraph($this->text, $this->noteCollection);
					$this->text = '';
					$this->noteCollection = [];

				}
				break;

			case "OFFICE:ANNOTATION" ;
				$this->insideAnnotation = false;
				break;
			
			case "TEXT:NOTE" ;
				//
				$this->noteCollection[] = '[note#' . $this->noteCitation . ') ' . $this->noteBody . '#]';
				//
				$this->text .= ')'; // to end the note citation in the text
				$this->insideNote = false;
				$this->noteBody = '';
				break;

			case "TEXT:NOTE-CITATION" ;
				$this->isNoteCitation = FALSE;
				break;

			case "TEXT:NOTE-BODY" ;
				// 
				$this->isNoteBody = false;
				break;
			
			case "TEXT:LINE-BREAK" ;
				//
				$this->text .= ' ';
				break;
			
			case "TEXT:SPAN" ;
				break;

		}	

	}

	private function character_data_handler($parser, $data)
	{
		if ($this->isNoteBody) $this->noteBody .= $data;
		else if (!$this->insideAnnotation){
			$this->text .= $data;
			if ($this->isNoteCitation) $this->noteCitation = $data; 
		}
	}

	/**
	 * Parse the xml file 'content.xml' which contains an odt document.
	 *
	 * @param string $fileName
	 * @return void
	 */
	private function parseXmlContent( string $fileName ) : ?float
	{
		//
		$timeStart = microtime(true);

		// various initialization settings
		$this->noteCollection = [];
		$this->text = '';
		$this->nbBookWords = 0;
		$this->nbBookSentences = 0;
		$this->nbBookParagraphs = 0;

		// get file size
		$fileSize = filesize($fileName);
		$ratio = $fileSize / 16384;

		// setting no execution time out .. bbrrrr !! 
		ini_set('max_execution_time', '0');

		//
		//
		$fh = @fopen($fileName, 'rb');
		if ( $fh ){



			$this->parser = xml_parser_create();
			$this->counter = 0;

			//
			// set up the handlers
			xml_set_element_handler($this->parser, [$this, "start_element_handler"], [$this, "end_element_handler"]);
			xml_set_character_data_handler($this->parser, [$this, "character_data_handler"]);

			// fread vs fgets !! ??
			while (($buffer = fread($fh, 16384)) != false) {
				xml_parse($this->parser, $buffer);
			}
			xml_parse($this->parser, '', true); // finalize parsing
			xml_parser_free($this->parser);

			if (!feof($fh)) {
				echo "Erreur: fgets() a échoué\n";
			}

			fclose($fh);

		}
		else {
			return 0 ; // no parsing !!
		}

		// stop timer !
		$timeEnd = \microtime(true);

		// dd($timeStart, $timeEnd, $timeEnd - $timeStart);
		return($timeEnd - $timeStart);
	}

	private function handleBookParagraph($paragraph, $noteCollection)
	{
		if ($paragraph != ''){

			$entityManager = $this->getDoctrine()->getManager();
			$bookParagraph = NULL;
			
			// split the paragraph using the punctuation signs [.?!]
			// with a negative look-behind feature to exclude :
			// 			- roman numbers (example CXI.)
			//			- S. as St
			//			- ordered list ( 1. aaa 2. bbb 3. ccc etc)
			//
			$sentences = preg_split('/(?<![IVXLCM1234567890S].)(?<=[.?!])\s+/', $paragraph, -1, PREG_SPLIT_DELIM_CAPTURE);
			if ($sentences){
				foreach ($sentences as $sentence ){
					
					// remove all non-breaking space !!
					// regex / /u << unicode support
					$sentence = preg_replace("/[\x{00a0}\s]+/u", " ", $sentence);
					$sentence = ltrim($sentence);
					
					if ($sentence != ''){
						
						if ( NULL === $bookParagraph ){
							$bookParagraph = new BookParagraph();
							$bookParagraph->setBook($this->book);
						}

						$bookSentence = new BookSentence();
						$bookSentence->setBookParagraph($bookParagraph);
						$bookSentence->setContent($sentence);

						$this->nbBookSentences++;
						$entityManager->persist($bookSentence);
					}

				}
			}

			//
			if ( NULL !== $bookParagraph ){

				$this->nbBookParagraphs++;				
				$entityManager->persist($bookParagraph);

				//
				// then get notes if any for the paragraph
				if (!empty($noteCollection)){
					foreach($this->noteCollection as $note){
						$pNote = new BookParagraph();
						$pNote->SetBook($this->book);

						$sNote = new BookSentence();
						$sNote->setBookParagraph($pNote);
						$sNote->setContent($note);

						$entityManager->persist($sNote);
						$entityManager->persist($pNote);
					}
				}

				$entityManager->flush();
			}
			
		}
	}

}
