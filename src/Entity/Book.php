<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\RangeFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use App\Repository\BookRepository;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\BooleanFilter;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Serializer\Annotation as Serializer;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

#[ApiResource(
    collectionOperations: ['post', 'get'],
    itemOperations: [
        "get" => ["normalization_context" => [
            "groups" => "getOne"
        ]
        ],
        "patch",
        "delete"
    ],
    denormalizationContext: ['groups' => 'addNew'],
    formats: ['json', 'jsonld'],
    normalizationContext: ['groups' => 'getAll'], paginationItemsPerPage: 10
)
]
#[ApiFilter(BooleanFilter::class, properties: ['enabled', 'featured'])]
#[ApiFilter(RangeFilter::class, properties: ['price'])]
#[ApiFilter(SearchFilter::class, properties: [
    'title' => 'partial',
    'category' => 'exact',
    'author' => 'exact',
])]
#[ORM\Entity(repositoryClass: BookRepository::class)]
class Book extends AbstractEntity
{
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

    #[ORM\Column(type: 'string', length: 255)]
    #[Serializer\Groups('addNew')]
    private string $imageName;

    #[ORM\Column(type: 'string', length: 255)]
    #[Serializer\Groups(['getAll', 'getOne'])]
    #[Vich\UploadableField(mapping: "book_image", fileNameProperty: "image")]
    private string $imagePath;

    #[ORM\ManyToOne(targetEntity: Category::class, inversedBy: 'books')]
    #[Serializer\Groups('getOne', 'addNew')]
    private Category $category;

    #[ORM\Column(type: 'string', length: 255)]
    #[Gedmo\Slug(fields: ['title','id'])]
    #[Serializer\Groups(['getAll'])]
    private string $slug;

    #[ORM\Column(type: 'string', length: 255)]
    #[Serializer\Groups(['getOne'])]
    private string $description;

    #[ORM\Column(type: 'date_immutable', nullable: true)]
    #[Serializer\Groups(['getOne', 'addNew'])]
    private \DateTimeImmutable $publicationDate;

    #[ORM\Column(type: 'string', length: 255)]
    #[Serializer\Groups(['getAll', 'getOne', 'addNew'])]
    private string $author;


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

    public function getImageName(): ?string
    {
        return $this->imageName;
    }

    public function setImageName(string $imageName): self
    {
        $this->imageName = $imageName;

        return $this;
    }

    public function getImagePath(): ?string
    {
        return $this->imagePath;
    }

    public function setImagePath(string $imagePath): self
    {
        $this->imagePath = $imagePath;

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

    public function getPublicationDate(): ?\DateTimeImmutable
    {
        return $this->publicationDate;
    }

    public function setPublicationDate(\DateTimeImmutable $publicationDate): self
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
}
