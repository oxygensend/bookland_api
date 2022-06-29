<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\BooleanFilter;
use App\Repository\CategoryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation\Slug;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Serializer\Annotation as Serializer;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

#[ApiResource(
    collectionOperations: ['get'],
    itemOperations: [
    ],
    formats: ['json', 'jsonld'],
    paginationItemsPerPage: 30,
)]
#[ApiFilter(BooleanFilter::class,properties: ['featured'])]
#[ORM\Entity(repositoryClass: CategoryRepository::class)]
class Category extends AbstractEntity
{

    private const IMG_DIR = '/images/uploads/categories';

    #[ORM\Column(type:"integer", nullable: true)]
    private int $parent;

    #[ORM\Column(type: 'string', length: 255)]
    private string $name;

    #[ORM\Column(type: 'boolean')]
    private bool $enabled;

    #[ORM\Column(type: 'boolean')]
    private bool $featured;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private string $image;

    #[Vich\UploadableField(mapping: "image_category", fileNameProperty: "image")]
    private string $imageFile;

    #[ORM\OneToMany(mappedBy: 'category', targetEntity: Book::class)]
    private Book $books;

    #[ORM\Column(type: 'string', length: 255)]
    #[Slug(fields: ['name'])]
    private string $slug;

    #[ORM\Column(type: 'string', length: 2048)]
    private string $categories;

    #[ORM\Column(type: 'float')]
    #[Serializer\Groups('getAll')]
    private float|int $popularityRate = 0;

    public function __construct()
    {
        $this->books = new ArrayCollection();

        $this->setCreatedAt(new \DateTime());
        $this->setUpdatedAt(new \DateTime());
    }


    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

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

    public function isFeatured(): ?bool
    {
        return $this->featured;
    }

    public function setFeatured(bool $featured): self
    {
        $this->featured = $featured;

        return $this;
    }

    public function getImageFile(): ?File
    {
        return $this->imageFile;
    }

    public function setImageName(?File $imageFile): self
    {
        $this->imageFile = $imageFile;

        if ($imageFile) {
            $this->updatedAt = new \DateTime('now');
        }

        return $this;
    }
    #[Serializer\SerializedName('item_path')]
    public function getImagePath(): ?string
    {
        if ($this->image) {
            return self::IMG_DIR.$this->image;
        }
        return '';
    }



    public function getBooks(): Collection
    {
        return $this->books;
    }

    public function addBook(Book $book): self
    {
        if (!$this->books->contains($book)) {
            $this->books[] = $book;
            $book->setCategory($this);
        }

        return $this;
    }

    public function removeBook(Book $book): self
    {
        if ($this->books->removeElement($book)) {
            // set the owning side to null (unless already changed)
            if ($book->getCategory() === $this) {
                $book->setCategory(null);
            }
        }

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


    public function getParent(): int
    {
        return $this->parent;
    }

    public function setParent(int $parent): void
    {
        $this->parent = $parent;
    }



    public function getCategories(): string
    {
        return $this->categories;
    }


    public function setCategories(string $categories): void
    {
        $this->categories = $categories;
    }


    public function getPopularityRate(): float|int
    {
        return $this->popularityRate;
    }


    public function setPopularityRate(float|int $popularityRate): void
    {
        $this->popularityRate = $popularityRate;
    }


    public function getImage(): string
    {
        return $this->image;
    }


    public function setImage(string $image): void
    {
        $this->image = $image;
    }


}
