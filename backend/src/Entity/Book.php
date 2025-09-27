<?php

namespace App\Entity;

use ApiPlatform\Metadata\Get;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\BookRepository;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: BookRepository::class)]
#[ApiResource(
    operations: [
        new GetCollection(
            name: 'api_book_get_collection',
            normalizationContext: ['groups' => ['book:read']],
            uriTemplate: '/books',
        ),
        new Get(
            name: 'api_book_get_one',
            normalizationContext: ['groups' => ['book:read']],
            uriTemplate: '/books/{id}',
        )
    ]
)]
class Book
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['book:read'])]
    private ?string $title = null;

    #[ORM\Column(type: 'text', nullable: true)]
    #[Groups(['book:read'])]
    private ?string $summary = null;

    #[ORM\Column(type: 'json', nullable: true)]
    private ?array $languages = null;

    #[ORM\Column(type: 'boolean')]
    private bool $copyright = false;

    #[ORM\Column(length: 50)]
    #[Groups(['book:read'])]
    private ?string $mediaType = null;

    #[ORM\Column(type: 'json', nullable: true)]
    #[Groups(['book:read'])]
    private ?array $formats = null;


    #[ORM\Column(type: 'integer', unique: true)]
    private ?int $gutendexId = null;

    #[ORM\Column(type: 'datetime_immutable')]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(type: 'datetime_immutable')]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\ManyToMany(targetEntity: Author::class, inversedBy: 'books', cascade: ['persist'])]
    #[ORM\JoinTable(name: 'book_author')]
    #[Groups(['book:read'])]
    private Collection $authors;

    #[ORM\ManyToMany(targetEntity: Category::class, inversedBy: 'books', cascade: ['persist'])]
    #[ORM\JoinTable(name: 'book_category')]
    #[Groups(['book:read'])]
    private Collection $categories;

    /**
     * @var Collection<int, Library>
     */
    #[ORM\ManyToMany(targetEntity: Library::class, mappedBy: 'booksCollection')]
    private Collection $libraries;

    /**
     * @var Collection<int, Review>
     */
    #[ORM\OneToMany(targetEntity: Review::class, mappedBy: 'book', orphanRemoval: true)]
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

    public function getLanguages(): ?array
    {
        return $this->languages;
    }

    public function setLanguages(?array $languages): static
    {
        $this->languages = $languages;
        $this->updatedAt = new \DateTimeImmutable();

        return $this;
    }

    public function isCopyright(): bool
    {
        return $this->copyright;
    }

    public function setCopyright(bool $copyright): static
    {
        $this->copyright = $copyright;
        $this->updatedAt = new \DateTimeImmutable();

        return $this;
    }

    public function getMediaType(): ?string
    {
        return $this->mediaType;
    }

    public function setMediaType(string $mediaType): static
    {
        $this->mediaType = $mediaType;
        $this->updatedAt = new \DateTimeImmutable();

        return $this;
    }

    public function getFormats(): ?array
    {
        return $this->formats;
    }

    public function setFormats(?array $formats): static
    {
        $this->formats = $formats;
        $this->updatedAt = new \DateTimeImmutable();

        return $this;
    }


    public function getGutendexId(): ?int
    {
        return $this->gutendexId;
    }

    public function setGutendexId(int $gutendexId): static
    {
        $this->gutendexId = $gutendexId;
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

    /**
     * @return Collection<int, Author>
     */
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

    /**
     * @return Collection<int, Category>
     */
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
    public function getCategoriesNames(): string
    {
        $names = [];
        foreach ($this->categories as $category) {
            $names[] = $category->getName();
        }
        return implode(', ', $names);
    }

    /**
     * @return Collection<int, Library>
     */
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

    /**
     * @return Collection<int, Review>
     */
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
