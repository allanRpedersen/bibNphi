<?php

namespace App\Service;

class XmlParser {
    
    private $nbWords;
    private $nbSentences;
    private $nbParagraphs;
    private $xmlFileSize;
    private $parsingTime;

	private $timeStart;
	private $noteCollection;

	private $parser;

	private $insideNote,
			$insideAnnotation,
			$counter,
			$text,
			$isNoteBody,
			$isNoteCitation,
			$noteBody,
			$noteCitation;

	/**
	 * 
	 */
	private $fileBufferSize = 65536;
	private $nbBuffers;

    /**
     * Get the value of nbWords
     */ 
    public function getNbWords()
    {
        return $this->nbWords;
    }

    /**
     * Set the value of nbWords
     *
     * @return  self
     */ 
    public function setNbWords($nbWords)
    {
        $this->nbWords = $nbWords;

        return $this;
    }

    /**
     * Get the value of nbSentences
     */ 
    public function getNbSentences()
    {
        return $this->nbSentences;
    }

    /**
     * Set the value of nbSentences
     *
     * @return  self
     */ 
    public function setNbSentences($nbSentences)
    {
        $this->nbSentences = $nbSentences;

        return $this;
    }

    /**
     * Get the value of nbParagraphs
     */ 
    public function getNbParagraphs()
    {
        return $this->nbParagraphs;
    }

    /**
     * Set the value of nbParagraphs
     *
     * @return  self
     */ 
    public function setNbParagraphs($nbParagraphs)
    {
        $this->nbParagraphs = $nbParagraphs;

        return $this;
    }

    /**
     * Get the value of xmlFileSize
     */ 
    public function getXmlFileSize()
    {
        return $this->xmlFileSize;
    }

    /**
     * Set the value of xmlFileSize
     *
     * @return  self
     */ 
    public function setXmlFileSize($xmlFileSize)
    {
        $this->xmlFileSize = $xmlFileSize;

        return $this;
    }

    /**
     * Get the value of parsingTime
     */ 
    public function getParsingTime()
    {
        return $this->parsingTime;
    }

    /**
     * Set the value of parsingTime
     *
     * @return  self
     */ 
    public function setParsingTime($parsingTime)
    {
        $this->parsingTime = $parsingTime;

        return $this;
    }


	/**
	 * 
	 *
	 * 
	 * 
	 * 
	 */



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
	 * Parse the xml file which contains the odt document.
	 *
	 * @param string $fileName
	 * @return float | null
	 */
	private function parse( string $fileName ) : ?float
	{
		//
		$this->timeStart = microtime(true);

		// various initialization settings
		$this->noteCollection = [];
		$this->text = '';
		$this->nbBookWords = 0;
		$this->nbBookSentences = 0;
		$this->nbBookParagraphs = 0;

		// get file size
		$this->xmlFileSize = filesize($fileName);
		$this->nbBuffers = $this->xmlFileSize / $fileBufferSize;

		// setting no execution time out .. bbrrrr !! 
		if ($this->nbBuffers > 1) ini_set('max_execution_time', '0');

		//
		// $fh = @fopen() 
		// ( @ symbol supresses any php driven error message !? )
		//
		$fh = fopen($fileName, 'rb');
		if ( $fh ){

			$nbBuffer = 0;

			$this->parser = xml_parser_create();
			$this->counter = 0; // nb de paragraphes !!?

			//
			// set up the handlers
			xml_set_element_handler($this->parser, [$this, "start_element_handler"], [$this, "end_element_handler"]);
			xml_set_character_data_handler($this->parser, [$this, "character_data_handler"]);

			// fread vs fgets !! ??
			while (($buffer = fread($fh, $fileBufferSize)) != false){
				//
				// 
				$nbBuffer++;
				xml_parse($this->parser, $buffer);

				sleep(1); // ?? cf err 503 !
				passthru('echo \'n° fread_buffer:' . $nbBuffer . '\' >>books/sorties_console 2>&1', $errCode );
				//
				//
			}
			xml_parse($this->parser, '', true); // finalize parsing
			xml_parser_free($this->parser);

			if (!feof($fh)) {
				passthru('echo "Erreur: fread() a échoué ..." >>books/sorties_console 2>&1', $errCode);
				return 0;
			}

			fclose($fh);

		}
		else {
			passthru('echo "Erreur: fopen a retourné FALSE !!" >>books/sorties_console 2>&1', $errCode);
			return 0 ; // no parsing !!
		}

		// stop timer !
		//$timeEnd = \microtime(true);

		$duration = \microtime(true) - $timeStart;
		passthru('echo \'Parsing duration:' . $duration . '\' >>books/sorties_console 2>&1', $errCode );

		// dd($timeStart, $timeEnd, $timeEnd - $timeStart);
		return($duration);
	}

	private function handleBookParagraph($paragraph, $noteCollection)
	{
		if ($paragraph != ''){

			$entityManager = $this->getDoctrine()->getManager();
			$bookParagraph = NULL;
			
			// split the paragraph using the punctuation signs [.?!]
			// with a negative look-behind feature to exclude :
			// 			- roman numbers (example CXI.)
			//			- ordered list ( 1. aaa 2. bbb 3. ccc etc)
			//			- S. as St, Saint
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
						$entityManager->persist($pNote);

						$sNote = new BookSentence();
						$sNote->setBookParagraph($pNote);
						$sNote->setContent($note);
						$entityManager->persist($sNote);

					}
				}

				$entityManager->flush();
			}
			
		}
	}

}



