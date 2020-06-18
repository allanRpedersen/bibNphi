<?php

namespace App\Entity;

use App\Entity\Author;
use Cocur\Slugify\Slugify;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\BookRepository;
use Symfony\Component\HttpFoundation\File\File;
use Doctrine\Common\Collections\ArrayCollection;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * @ORM\Entity(repositoryClass=BookRepository::class)
 * @ORM\HasLifecycleCallbacks
 * @Vich\Uploadable
 */
class Book
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
	private $title;
	
	/**
	 * @ORM\Column(type="string", length=255, unique=true)
	 */
	private $slug;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $summary;

    /**
     * @ORM\ManyToOne(targetEntity=Author::class, inversedBy="books")
     * @ORM\JoinColumn(nullable=false)
     */
    private $author;

    /**
     * @ORM\Column(type="string", length=11, nullable=true)
     */
    private $publishedYear;

	    /**
     * NOTE: This is not a mapped field of entity metadata, just a simple property.
     * 
	 * @Assert\File(
     *     mimeTypes = {"application/vnd.oasis.opendocument.text"},
     *     mimeTypesMessage = "Veuillez indiquer un document au format ODT !"
     * )
     * @Vich\UploadableField(mapping="books",
	 * 							fileNameProperty="odtBookName",
	 * 							size="odtBookSize",
	 * 							mimeType="bookMimeType")
     * 
     * @var File|null
     */
    private $odtBookFile;

    /**
     * @ORM\Column(type="string")
     *
     * @var string|null
     */
    private $odtBookName;

    /**
     * @ORM\Column(type="integer")
     *
     * @var int|null
     */
    private $odtBookSize;

    /**
     * @ORM\Column(type="datetime")
     *
     * @var \DateTimeInterface|null
     */
    private $updatedAt;

	/**
	 * Undocumented variable
	 *
	 */
	private $bookMimeType;

    /**
     * @ORM\OneToMany(targetEntity=BookParagraph::class, mappedBy="book", orphanRemoval=true)
     */
    private $bookParagraphs;

    /**
     * @ORM\Column(type="integer")
     */
    private $nbParagraphs;

    /**
     * @ORM\Column(type="integer")
     */
    private $nbSentences;

    /**
     * @ORM\Column(type="integer")
     */
    private $nbWords;

    /**
     * @ORM\Column(type="float")
     */
    private $parsingTime;

    public function __construct()
    {
        $this->bookParagraphs = new ArrayCollection();
    }
	//
	//
	//

	
	/**
	 * Initialisation du slug avant le persist ..
	 * 
	 * @ORM\PrePersist
	 * @ORM\PreUpdate
	 *
	 * @return void
	 */
	public function InitializeSlug()
	{
		// if ( empty($this->slug) ){}

		// le slug est systèmatiquement recalculé ..
		$slugify = new Slugify();
		$this->slug = $slugify->slugify($this->author->getlastName() . '-' . $this->title);
	}


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }


    public function getSummary(): ?string
    {
        return $this->summary;
    }

    public function setSummary(?string $summary): self
    {
        $this->summary = $summary;

        return $this;
    }

    public function getAuthor(): ?Author
    {
        return $this->author;
    }

    public function setAuthor(?Author $author): self
    {
        $this->author = $author;

        return $this;
    }

    public function getPublishedYear(): ?string
    {
        return $this->publishedYear;
    }

    public function setPublishedYear(?string $publishedYear): self
    {
        $this->publishedYear = $publishedYear;

        return $this;
    }

	/**
     * If manually uploading a file (i.e. not using Symfony Form) ensure an instance
     * of 'UploadedFile' is injected into this setter to trigger the update. If this
     * bundle's configuration parameter 'inject_on_load' is set to 'true' this setter
     * must be able to accept an instance of 'File' as the bundle will inject one here
     * during Doctrine hydration.
     *
     * @param File|\Symfony\Component\HttpFoundation\File\UploadedFile|null $odtBookFile
     */
    public function setOdtBookFile(?File $odtBookFile = null): void
    {
        $this->odtBookFile = $odtBookFile;

        if (null !== $odtBookFile) {
            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost
            $this->updatedAt = new \DateTimeImmutable();
        }
    }

    public function getOdtBookFile(): ?File
    {
        return $this->odtBookFile;
    }

    public function setOdtBookName(?string $odtBookName): void
    {
        $this->odtBookName = $odtBookName;
    }

    public function getOdtBookName(): ?string
    {
        return $this->odtBookName;
    }
    
    public function setOdtBookSize(?int $odtBookSize): void
    {
        $this->odtBookSize = $odtBookSize;
    }

    public function getOdtBookSize(): ?int
    {
        return $this->odtBookSize;
    }

	public function setBookMimeType(?string $bookMimeType): void
	{
		$this->bookMimeType = $bookMimeType;
	}

    public function getBookMimeType(): ?string
    {
        return $this->bookMimeType;
    }

    /**
     * @return Collection|BookParagraph[]
     */
    public function getBookParagraphs(): Collection
    {
        return $this->bookParagraphs;
    }

    public function addBookParagraph(BookParagraph $bookParagraph): self
    {
        if (!$this->bookParagraphs->contains($bookParagraph)) {
            $this->bookParagraphs[] = $bookParagraph;
            $bookParagraph->setBook($this);
        }

        return $this;
    }

    public function removeBookParagraph(BookParagraph $bookParagraph): self
    {
		foreach( $bookParagraph->getSentences() as $sentence ){
			$bookParagraph->removeSentence($sentence);
		}

        if ($this->bookParagraphs->contains($bookParagraph)) {
            $this->bookParagraphs->removeElement($bookParagraph);
            // set the owning side to null (unless already changed)
            if ($bookParagraph->getBook() === $this) {
                $bookParagraph->setBook(null);
            }
        }

        return $this;
    }

    public function getNbParagraphs(): ?int
    {
        return $this->nbParagraphs;
    }

    public function setNbParagraphs(int $nbParagraphs): self
    {
        $this->nbParagraphs = $nbParagraphs;

        return $this;
    }

    public function getNbSentences(): ?int
    {
        return $this->nbSentences;
    }

    public function setNbSentences(int $nbSentences): self
    {
        $this->nbSentences = $nbSentences;

        return $this;
    }

    public function getNbWords(): ?int
    {
        return $this->nbWords;
    }

    public function setNbWords(int $nbWords): self
    {
        $this->nbWords = $nbWords;

        return $this;
    }

    public function getParsingTime(): ?float
    {
        return $this->parsingTime;
    }

    public function setParsingTime(float $parsingTime): self
    {
        $this->parsingTime = $parsingTime;

        return $this;
    }
    
}
