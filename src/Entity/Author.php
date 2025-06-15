<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\AuthorRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AuthorRepository::class), ORM\Table(name: 'authors')]
class Author
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'id', type: Types::INTEGER)]
    private int $id;

    #[ORM\Column(name: 'name', type: Types::STRING, length: 255)]
    private string $name;

    #[ORM\OneToMany(targetEntity: Book::class, mappedBy: 'author', orphanRemoval: true)]
    private Collection $books;

    /**
     * @param string $name
     */
    public function __construct(string $name)
    {
        $this->name = $name;
        $this->books = new ArrayCollection();
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return Collection<Book>
     */
    public function getBooks(): Collection
    {
        return $this->books;
    }

    /**
     * @param Book $book
     */
    public function addBook(Book $book): void
    {
        if (!$this->books->contains($book)) {
            $this->books->add($book);
            $book->setAuthor($this);
        }
    }

    /**
     * @param Book $book
     */
    public function removeBook(Book $book): void
    {
        if (!$this->books->contains($book)) {
            $this->books->removeElement($book);
        }
    }
}
