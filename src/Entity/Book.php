<?php

namespace App\Entity;

use App\Entity\Author;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\BookRepository;
use Symfony\Component\HttpFoundation\File\File;
use Doctrine\Common\Collections\ArrayCollection;
use Vich\UploaderBundle\Mapping\Annotation as Vich;


/**
 * @ORM\Entity(repositoryClass=BookRepository::class)
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
     * @Vich\UploadableField(mapping="books", fileNameProperty="odtBookName", size="odtBookSize")
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


	//
	//
	//

	
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


}
