<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\RangeFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use App\Filters\BookSortFilter;
use App\Repository\BookRepository;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\BooleanFilter;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Serializer\Annotation as Serializer;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

#[ApiResource(
    collectionOperations: ['post', 'get'],
    itemOperations: [
        "get" => ["normalization_context" => [
            "groups" => "getOne"
        ]
        ],
    ],
    denormalizationContext: ['groups' => 'addNew'],
    formats: ['json', 'jsonld'],
    normalizationContext: ['groups' => 'getAll'], paginationItemsPerPage: 30
)
]
#[ApiFilter(BooleanFilter::class, properties: ['featured'])]
#[ApiFilter(RangeFilter::class, properties: ['price'])]
#[ApiFilter(SearchFilter::class, properties: [
    'category' => 'exact',
    'author' => 'exact',
])]
#[ApiFilter(BookSortFilter::class, properties: ['sort'])]
#[ORM\Entity(repositoryClass: BookRepository::class)]
class Book extends AbstractEntity
{
    private const IMAGE_DIR = '/images/uploads/books';

    #[ORM\Column(type: 'string', length: 13, unique: true)]
    #[Serializer\Groups(['getOne', 'addNew'])]
    private string $isbn;

    #[ORM\Column(type: 'string', length: 255)]
    #[Serializer\Groups(['getAll', 'getOne', 'addNew'])]
    private string $title;

    #[ORM\Column(type: 'float')]
    #[Serializer\Groups(['getAll', 'getOne', 'addNew'])]
    private float $price;

    #[ORM\Column(type: 'integer')]
    #[Serializer\Groups(['getAll', 'getOne', 'addNew'])]
    private int $amount;

    #[ORM\Column(type: 'boolean')]
    #[Serializer\Groups('addNew')]
    private bool $enabled = true;

    #[ORM\Column(type: 'boolean')]
    #[Serializer\Groups('addNew')]
    private bool $featured = false;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $image;

    #[Vich\UploadableField(mapping: "image_book", fileNameProperty: "image")]
    #[Serializer\Groups('addNew')]
    private File $imageFile;

    #[ORM\ManyToOne(targetEntity: Category::class, inversedBy: 'books')]
    #[Serializer\Groups(['getOne', 'addNew'])]
    private Category $category;

    #[ORM\Column(type: 'string', length: 255)]
    #[Gedmo\Slug(fields: ['title','id'])]
    #[Serializer\Groups(['getAll'])]
    private string $slug;

    #[ORM\Column(type: 'string', length: 255)]
    #[Serializer\Groups(['getOne'])]
    private string $description;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    #[Serializer\Groups(['getOne', 'addNew'])]
    private \DateTime $publicationDate;

    #[ORM\Column(type: 'string', length: 255)]
    #[Serializer\Groups(['getAll', 'getOne', 'addNew'])]
    private string $author;

    #[ORM\Column(type: 'float')]
    #[Serializer\Groups('getAll')]
    private float|int $popularityRate = 0;


    public function getIsbn(): string
    {
        return $this->isbn;
    }


    public function setIsbn(string $isbn): void
    {
        $this->isbn = $isbn;
    }


    public function getFeatured()
    {
        return $this->featured;
    }

    public function setFeatured($featured): void
    {
        $this->featured = $featured;
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

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(float $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getAmount(): ?int
    {
        return $this->amount;
    }

    public function setAmount(int $amount): self
    {
        $this->amount = $amount;

        return $this;
    }

    public function isEnabled(): ?bool
    {
        return $this->enabled;
    }

    public function setEnabled(bool $enabled): self
    {
        $this->enabled = $enabled;

        return $this;
    }

    #[Serializer\SerializedName('item_path')]
    public function getImage(): ?string
    {
        if ($this->image) {
            return self::IMAGE_DIR.$this->image;
        }
        return '';
    }


    public function setImage(?string $image): void
    {
        $this->image = $image;
    }

    public function getImageFile(): ?File
    {
        return $this->imageFile;
    }

    public function setImageFile(?File $imageFile): self
    {
        $this->imageFile = $imageFile;

        if ($imageFile) {
            $this->updatedAt = new \DateTime('now');
        }

        return $this;
    }


    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): self
    {
        $this->category = $category;

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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    #[Serializer\SerializedName('description')]
    #[Serializer\Groups('addNew')]
    public function setTextDescription(string $description): self
    {
        $this->description = nl2br($description);

        return $this;
    }

    public function getPublicationDate(): ?\DateTime
    {
        return $this->publicationDate;
    }

    public function setPublicationDate(\DateTime $publicationDate): self
    {
        $this->publicationDate = $publicationDate;

        return $this;
    }

    public function getAuthor(): ?string
    {
        return $this->author;
    }

    public function setAuthor(string $author): self
    {
        $this->author = $author;

        return $this;
    }


    public function getPopularityRate(): float|int
    {
        return $this->popularityRate;
    }


    public function setPopularityRate(float|int $popularityRate): void
    {
        $this->popularityRate = $popularityRate;
    }
}
