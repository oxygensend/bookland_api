<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\BooleanFilter;
use App\CategorySortFilter;
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
    itemOperations: [ 'get' => ['normalization_context' => [
        'groups' => 'category:getOne'
    ]]],
    formats: ['json', 'jsonld'],
    normalizationContext: ['groups' => 'category:getAll'],
    paginationEnabled: false
)]
#[ApiFilter(BooleanFilter::class,properties: ['featured'])]
#[ApiFilter(CategorySortFilter::class, properties: ['sort'])]
#[ORM\Entity(repositoryClass: CategoryRepository::class)]
class Category extends AbstractEntity
{

    private const IMG_DIR = '/images/uploads/categories';

    #[ORM\ManyToOne(targetEntity: Category::class, inversedBy: 'children')]
    #[Serializer\Groups(['category:getAll', 'category:getOne'])]
    private self $parent;

    #[ORM\OneToMany(mappedBy: 'parent', targetEntity: Category::class)]
    private Collection $children;

    #[ORM\Column(type: 'string', length: 255)]
    #[Serializer\Groups(['category:getAll', 'category:getOne'])]
    private string $name;

    #[ORM\Column(type: 'boolean')]
    private bool $enabled;

    #[ORM\Column(type: 'boolean')]
    private bool $featured;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private string $image;

    #[Vich\UploadableField(mapping: "image_category", fileNameProperty: "image")]
    private ?File $imageFile;

    #[ORM\OneToMany(mappedBy: 'category', targetEntity: Book::class)]
    private Collection $books;

    #[ORM\Column(type: 'string', length: 255)]
    #[Slug(fields: ['name'])]
    #[Serializer\Groups(['category:getAll', 'category:getOne'])]
    private string $slug;

    #[ORM\Column(type: 'text', nullable: true)]
    #[Serializer\Groups(['category:getOne'])]
    private ?string $description;

    #[ORM\Column(type: 'string', length: 2048)]
    #[Serializer\Groups(['category:getAll'])]
    private string $categories;

    #[ORM\Column(type: 'float')]
    private float|int $popularityRate = 0;

    #[Serializer\Groups(['category:getAll', 'category:getOne'])]
    private string $tree;

    public function __construct()
    {
        $this->books = new ArrayCollection();
        parent::__construct();
    }


    public function getTree(): string
    {
        return $this->tree;

    }

    public function setTree(string $tree): self
    {
        $this->tree = $tree;
        return $this;
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
    #[Serializer\SerializedName('image_path')]
    #[Serializer\Groups(['category:getAll'])]
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


    public function getParent(): self
    {
        return $this->parent;
    }

    public function setParent(?self $parent): void
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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }

    public function getChildren(): Collection
    {
        return $this->children;
    }

    public function addChild(self $child): self
    {
        if (!$this->children->contains($child)) {
            $this->children[] = $child;
            $child->setParent($this);
        }

        return $this;
    }

    public function removeChild(self $child): self
    {
        if ($this->children->contains($child)) {
            $this->children->removeElement($child);
            // set the owning side to null (unless already changed)
            if ($child->getParent() === $this) {
                $child->setParent(null);
            }
        }

        return $this;
    }


}
