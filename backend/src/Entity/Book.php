<?php

namespace App\Entity;

use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Link;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Delete;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\BookRepository;
use ApiPlatform\Metadata\ApiFilter;
use App\State\Book\MyBooksProvider;
use App\State\Book\BooksCollectionProvider;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use App\Controller\Book\TestBookController;
use Doctrine\Common\Collections\Collection;
use ApiPlatform\Doctrine\Orm\Filter\OrderFilter;
use Doctrine\Common\Collections\ArrayCollection;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use App\Controller\User\Book\GetMyBooksCollection;
use App\Controller\User\Book\IsBookInMyCollection;
use Symfony\Component\Serializer\Attribute\Groups;
use App\Controller\User\Book\AddBookToMyCollection;
use App\Controller\User\Book\DeleteBookFromMyCollection;

#[ORM\Entity(repositoryClass: BookRepository::class)]
#[ApiResource(
    operations: [
        new GetCollection(
            name: 'api_book_get_collection',
            normalizationContext: ['groups' => ['books:read']],
            uriTemplate: '/books',
            provider: BooksCollectionProvider::class,
        ),
        new Get(
            name: 'api_book_get_one',
            normalizationContext: ['groups' => ['book:read']],
            uriTemplate: '/books/{id}',
        ),
        new Get(
            name: 'api_user_is_book_in_my_collection',
            uriTemplate: '/me/is-book-in-my-collection/{book}',
            uriVariables: ['book' => new Link(fromClass: Book::class)],
            controller: IsBookInMyCollection::class,
            security: "is_granted('IS_AUTHENTICATED_FULLY')",
            read: false,
        ),
        new Post(
            name: 'api_user_add_book_to_my_collection',
            uriTemplate: '/me/add-book-to-my-collection/{book}',
            uriVariables: ['book' => new Link(fromClass: Book::class)],
            controller: AddBookToMyCollection::class,
            security: "is_granted('IS_AUTHENTICATED_FULLY')",
            read: false,
        ),

        new Delete(
            name: 'api_user_delete_book_from_my_collection',
            uriTemplate: '/me/delete-book-from-my-collection/{book}',
            uriVariables: ['book' => new Link(fromClass: Book::class)],
            controller: DeleteBookFromMyCollection::class,
            security: "is_granted('IS_AUTHENTICATED_FULLY')",
        ),
        new GetCollection(
            uriTemplate: '/me/books-collection',
            provider: MyBooksProvider::class,
            security: "is_granted('IS_AUTHENTICATED_FULLY')",
            normalizationContext: ['groups' => ['book:read']],
        )
    ],
    paginationItemsPerPage: 12,
    paginationClientItemsPerPage: true,
    paginationMaximumItemsPerPage: 50,
)]
#[ApiFilter(SearchFilter::class, properties: [
    'title' => 'ipartial',
    'authors.name' => 'ipartial',
    'categories.name' => 'ipartial',
])]
#[ApiFilter(OrderFilter::class, properties: ['createdAt', 'title'])]
class Book
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['book:read', 'books:read', 'review:read', 'book-collection:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['book:read', 'books:read', 'review:read', 'book-collection:read'])]
    private ?string $title = null;

    #[ORM\Column(type: 'text', nullable: true)]
    #[Groups(['book:read', 'books:read', 'review:read', 'book-collection:read'])]
    private ?string $summary = null;

    #[ORM\Column(type: 'datetime_immutable')]
    #[Groups(['book:read', 'review:read', 'book-collection:read'])]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(type: 'datetime_immutable')]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\ManyToMany(targetEntity: Author::class, inversedBy: 'books', cascade: ['persist'])]
    #[ORM\JoinTable(name: 'book_author')]
    #[Groups(['book:read', 'review:read' , 'books:read', 'book-collection:read'])]
    private Collection $authors;

    #[ORM\ManyToMany(targetEntity: Category::class, inversedBy: 'books', cascade: ['persist'])]
    #[ORM\JoinTable(name: 'book_category')]
    #[Groups(['book:read', 'review:read' , 'books:read', 'book-collection:read'])]
    private Collection $categories;

    #[ORM\ManyToMany(targetEntity: Library::class, mappedBy: 'booksCollection')]
    private Collection $libraries;

    #[ORM\OneToMany(targetEntity: Review::class, mappedBy: 'book', orphanRemoval: true)]
    #[Groups(['book:read', 'review:read'])]
    private Collection $reviews;

    public function __construct()
    {
        $this->authors = new ArrayCollection();
        $this->categories = new ArrayCollection();
        $this->createdAt = new \DateTimeImmutable();
        $this->updatedAt = new \DateTimeImmutable();
        $this->libraries = new ArrayCollection();
        $this->reviews = new ArrayCollection();
    }

    /**
     * Note: moyenne des notes calculée à la volée (non stockée en BDD).
     */
    #[Groups(['book:read', 'books:read', 'review:read'])]
    public function getAverageRate(): float
    {
        $sum = 0;
        $count = 0;
        foreach ($this->reviews as $review) {
            $sum += $review->getRate();
            $count++;
        }
        return $count > 0 ? $sum / $count : 0;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;
        $this->updatedAt = new \DateTimeImmutable();

        return $this;
    }

    public function getSummary(): ?string
    {
        return $this->summary;
    }

    public function setSummary(?string $summary): static
    {
        $this->summary = $summary;
        $this->updatedAt = new \DateTimeImmutable();

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeImmutable $updatedAt): static
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getAuthors(): Collection
    {
        return $this->authors;
    }

    public function addAuthor(Author $author): static
    {
        if (!$this->authors->contains($author)) {
            $this->authors->add($author);
        }

        return $this;
    }

    public function removeAuthor(Author $author): static
    {
        $this->authors->removeElement($author);

        return $this;
    }

    public function getCategories(): Collection
    {
        return $this->categories;
    }

    public function addCategory(Category $category): static
    {
        if (!$this->categories->contains($category)) {
            $this->categories->add($category);
        }

        return $this;
    }

    public function removeCategory(Category $category): static
    {
        $this->categories->removeElement($category);

        return $this;
    }

    /**
     * Méthode utilitaire pour obtenir les noms des auteurs sous forme de string
     */
    #[Groups(['book:read', 'books:read'])]
    public function getAuthorsNames(): string
    {
        $names = [];
        foreach ($this->authors as $author) {
            $names[] = $author->getName();
        }
        return implode(', ', $names);
    }

    /**
     * Méthode utilitaire pour obtenir les noms des catégories sous forme de string
     */
    #[Groups(['book:read', 'books:read'])]
    public function getCategoriesNames(): string
    {
        $names = [];
        foreach ($this->categories as $category) {
            $names[] = $category->getName();
        }
        return implode(', ', $names);
    }

    public function getLibraries(): Collection
    {
        return $this->libraries;
    }

    public function addLibrary(Library $library): static
    {
        if (!$this->libraries->contains($library)) {
            $this->libraries->add($library);
            $library->addBooksCollection($this);
        }

        return $this;
    }

    public function removeLibrary(Library $library): static
    {
        if ($this->libraries->removeElement($library)) {
            $library->removeBooksCollection($this);
        }

        return $this;
    }

    public function getReviews(): Collection
    {
        return $this->reviews;
    }

    public function addReview(Review $review): static
    {
        if (!$this->reviews->contains($review)) {
            $this->reviews->add($review);
            $review->setBook($this);
        }

        return $this;
    }

    public function removeReview(Review $review): static
    {
        if ($this->reviews->removeElement($review)) {
            // set the owning side to null (unless already changed)
            if ($review->getBook() === $this) {
                $review->setBook(null);
            }
        }

        return $this;
    }
}
