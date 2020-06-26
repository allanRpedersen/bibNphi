<?php

namespace App\Entity;

use App\Repository\BookParagraphRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=BookParagraphRepository::class)
 */
class BookParagraph
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Book::class, inversedBy="bookParagraphs")
     * @ORM\JoinColumn(nullable=false)
     */
    private $book;

    /**
     * @ORM\OneToMany(targetEntity=BookSentence::class, mappedBy="bookParagraph")
     */
	private $sentences;
	
	private $matchingSentences;

    public function __construct()
    {
        $this->sentences = new ArrayCollection();
		$this->matchingSentences = new ArrayCollection();
		// dd('$$ BookParagraph __construct $$');
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getBook(): ?Book
    {
        return $this->book;
    }

    public function setBook(?Book $book): self
    {
        $this->book = $book;

        return $this;
    }

    /**
     * @return Collection|BookSentence[]
     */
    public function getSentences(): Collection
    {
        return $this->sentences;
    }

    public function addSentence(BookSentence $sentence): self
    {
        if (!$this->sentences->contains($sentence)) {
            $this->sentences[] = $sentence;
            $sentence->setBookParagraph($this);
        }

        return $this;
    }

    public function removeSentence(BookSentence $sentence): self
    {
        if ($this->sentences->contains($sentence)) {
            $this->sentences->removeElement($sentence);
            // set the owning side to null (unless already changed)
            if ($sentence->getBookParagraph() === $this) {
                $sentence->setBookParagraph(null);
            }
        }

        return $this;
	}
	
	public function getMatchingSentences($stringToSearch): ?Collection
	{
		$this->matchingSentences = new ArrayCollection();

		foreach($this->sentences as $sentence){

			//
			$iNeedle = mb_stripos($sentence->getContent(), $stringToSearch, 0, mb_detect_encoding($sentence->getContent()));

			if(FALSE !== $iNeedle){

				$this->matchingSentences->add($sentence);


			}
			
			// if (strlen(stristr($sentence->getContent(), $stringToSearch )) > 0 ){
			// }
			
			// split the paragraph using the punctuation signs [.?!]
			// with a negative look-behind feature to exclude roman numbers (example CXI.)
			//
			// $sentences = preg_split('/(?<![IVXLC].)(?<=[.?!])\s+/', $paragraph, -1, PREG_SPLIT_DELIM_CAPTURE);
			// if ($sentences){
				// 	foreach ($sentences as $sentence ){
					

		}
		return $this->matchingSentences;
	}
}
