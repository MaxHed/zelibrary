<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Metadata\ApiResource;
use App\Repository\LibraryRepository;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Get;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: LibraryRepository::class)]
#[ApiResource(
    operations: [
        new GetCollection(
            name: 'api_library_get_collection',
            normalizationContext: ['groups' => ['library:read']],
            uriTemplate: '/libraries',
        ),
        new Get(
            name: 'api_library_get_one',
            normalizationContext: ['groups' => ['library:read', 'library:detail']],
            uriTemplate: '/libraries/{id}',
        )
    ]
)]
class Library
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['library:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['library:read'])]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    #[Groups(['library:read'])]
    private ?string $address = null;

    #[ORM\Column(length: 20, nullable: true)]
    #[Groups(['library:read'])]
    private ?string $phone = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['library:read'])]
    private ?string $email = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['library:read'])]
    private ?string $logo = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['library:read'])]
    private ?string $website = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Groups(['library:read'])]
    private ?string $description = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['library:read'])]
    private ?int $borrowLimit = null;

    /**
     * @var Collection<int, User>
     */
    #[ORM\OneToMany(targetEntity: User::class, mappedBy: 'employedAt')]
    private Collection $employees;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $updatedAt = null;

    /**
     * @var Collection<int, Book>
     */
    #[ORM\ManyToMany(targetEntity: Book::class, inversedBy: 'libraries')]
    #[Groups(['library:detail'])]
    private Collection $booksCollection;

    public function __construct()
    {
        $this->employees = new ArrayCollection();
        $this->booksCollection = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(string $address): static
    {
        $this->address = $address;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(?string $phone): static
    {
        $this->phone = $phone;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function getLogo(): ?string
    {
        return $this->logo;
    }

    public function setLogo(?string $logo): static
    {
        $this->logo = $logo;

        return $this;
    }

    public function getWebsite(): ?string
    {
        return $this->website;
    }

    public function setWebsite(?string $website): static
    {
        $this->website = $website;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getBorrowLimit(): ?int
    {
        return $this->borrowLimit;
    }

    public function setBorrowLimit(?int $borrowLimit): static
    {
        $this->borrowLimit = $borrowLimit;

        return $this;
    }


    /**
     * @return Collection<int, User>
     */
    public function getEmployees(): Collection
    {
        return $this->employees;
    }

    public function addEmployee(User $employee): static
    {
        if (!$this->employees->contains($employee)) {
            $this->employees->add($employee);
            $employee->setEmployedAt($this);
        }

        return $this;
    }

    public function removeEmployee(User $employee): static
    {
        if ($this->employees->removeElement($employee)) {
            // set the owning side to null (unless already changed)
            if ($employee->getEmployedAt() === $this) {
                $employee->setEmployedAt(null);
            }
        }

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
     * @return Collection<int, Book>
     */
    public function getBooksCollection(): Collection
    {
        return $this->booksCollection;
    }

    public function addBooksCollection(Book $booksCollection): static
    {
        if (!$this->booksCollection->contains($booksCollection)) {
            $this->booksCollection->add($booksCollection);
        }

        return $this;
    }

    public function removeBooksCollection(Book $booksCollection): static
    {
        $this->booksCollection->removeElement($booksCollection);

        return $this;
    }
}
