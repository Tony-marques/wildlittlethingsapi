<?php

namespace App\Entity;

use App\Repository\ArticleRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: ArticleRepository::class)]
#[UniqueEntity("title", "Le titre doit être unique")]
class Article
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['article:read', "category:read", "subcategory:read"])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['article:read', "category:read", "subcategory:read"])]
    private ?string $title = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Groups(['article:read', "category:read", "subcategory:read"])]
    private ?string $description = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Groups(['article:read', "category:read", "subcategory:read"])]
    private ?string $content = null;

    #[ORM\Column(length: 255)]
    #[Groups(['article:read', "category:read", "subcategory:read"])]
    private ?string $slug = null;

    #[ORM\Column(length: 255)]
    #[Groups(['article:read', "category:read", "subcategory:read"])]
    private ?string $destination = null;

    #[ORM\Column]
    #[Groups(['article:read', "category:read", "subcategory:read"])]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['article:read', "category:read", "subcategory:read"])]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\Column(length: 255)]
    #[Groups(['article:read', "category:read", "subcategory:read"])]
    private ?string $mainImage1 = null;

    #[ORM\Column(length: 255)]
    #[Groups(['article:read', "category:read", "subcategory:read"])]
    private ?string $mainImage2 = null;

    /**
     * @var Collection<int, Category>
     */
    #[ORM\ManyToMany(targetEntity: Category::class, mappedBy: 'articles')]
    #[Groups(['article:read'])]
    private Collection $categories;

    #[ORM\ManyToOne(inversedBy: 'articles')]
    #[Groups(['article:read', "subcategory:read"])]
    private ?subcategory $subCategory = null;

    public function __construct()
    {
        $this->categories = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): static
    {
        $this->content = $content;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): static
    {
        $this->slug = $slug;

        return $this;
    }

    public function getDestination(): ?string
    {
        return $this->destination;
    }

    public function setDestination(string $destination): static
    {
        $this->destination = $destination;

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

    public function setUpdatedAt(?\DateTimeImmutable $updatedAt): static
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

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
            $category->addArticle($this);
        }

        return $this;
    }

    public function removeCategory(Category $category): static
    {
        if ($this->categories->removeElement($category)) {
            $category->removeArticle($this);
        }

        return $this;
    }

    public function getMainImage1(): ?string
    {
        return $this->mainImage1;
    }

    public function setMainImage1(string $mainImage1): static
    {
        $this->mainImage1 = $mainImage1;

        return $this;
    }

    public function getMainImage2(): ?string
    {
        return $this->mainImage2;
    }

    public function setMainImage2(string $mainImage2): static
    {
        $this->mainImage2 = $mainImage2;

        return $this;
    }

    public function getSubCategory(): ?subcategory
    {
        return $this->subCategory;
    }

    public function setSubCategory(?subcategory $subCategory): static
    {
        $this->subCategory = $subCategory;

        return $this;
    }
}
