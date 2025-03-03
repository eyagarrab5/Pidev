<?php

namespace App\Entity;

use App\Entity\Comments; 
use App\Entity\User;
use App\Repository\ForumPostsRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ForumPostsRepository::class)]
class ForumPosts
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Le titre est requis.")]
    #[Assert\Length(min: 5, max: 100, minMessage: "Le titre doit contenir au moins 5 caractères.")]
    private ?string $title = null;
    

    #[ORM\Column(type: Types::TEXT)]
    #[Assert\NotBlank(message: "Le contenu est requis.")]
    private ?string $content = null;

    #[ORM\Column(type: "datetime", nullable: false)]
    private ?\DateTimeInterface $createdAt = null;
    
    #[ORM\Column(type: "datetime", nullable: false)]
    private ?\DateTimeInterface $updatedAt = null;

    #[ORM\Column(length: 100)]
    #[Assert\NotBlank(message: "La catégorie est requise.")]
    private ?string $category = null;

    /*#[ORM\Column(type: 'string', length: 255)]
    private ?string $subCategory = null;
*/
    #[ORM\Column(nullable: true)]
    private ?int $likes = null;

    #[ORM\Column(nullable: true)]
    private ?int $dislikes = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Assert\Length(
        max: 255,
        maxMessage: "Les tags ne peuvent pas dépasser {{ limit }} caractères."
    )]
    private ?string $tags = null;

    #[ORM\Column(type: "string", length: 255, nullable: true)]
    private ?string $attachment = null;

    #[ORM\Column(type: "integer", nullable: false)]
    private ?int $comments_count = 0; // Initialisé à 0 par défaut

    #[ORM\OneToMany(mappedBy: 'forumPost', targetEntity: Comments::class, orphanRemoval: true)]
    private Collection $comments;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\Column(type: "boolean", options: ["default" => false])]
    private bool $isPinned = false;

    #[ORM\Column(type: 'json')]
    private array $notifications = [];

    #[ORM\Column(type: 'json')]
    private array $favoritedBy = [];



    public function __construct()
    {
        $this->comments = new ArrayCollection();
        $this->notifications = [];
        $this->favoritedBy = [];
    }



    public function getComments(): \Doctrine\Common\Collections\Collection
    {
        return $this->comments;
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

   public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeInterface $updatedAt): static
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getCategory(): ?string
    {
        return $this->category;
    }

    public function setCategory(string $category): static
    {
        $this->category = $category;

        return $this;
    }

    public function getLikes(): ?int
    {
        return $this->likes;
    }

    public function setLikes(?int $likes): static
    {
        $this->likes = $likes;

        return $this;
    }

    public function getTags(): ?string
    {
        return $this->tags;
    }

    public function setTags(?string $tags): static
    {
        $this->tags = $tags;

        return $this;
    }

    public function getAttachment(): ?string
    {
        return $this->attachment;
    }

    public function setAttachment(?string $attachment): self
    {
        $this->attachment = $attachment;

        return $this;
    }

        public function getCommentsCount(): ?int
    {
        return $this->comments_count;
    }

    public function setCommentsCount(int $comments_count): self
    {
        $this->comments_count = $comments_count;

        return $this;
    }

    public function incrementLikes(): void
    {
        $this->likes++;
    }

    #[ORM\PrePersist]
    public function setCreatedAtValue(): void
    {
        $this->createdAt = new \DateTime();
        $this->updatedAt = new \DateTime(); // On initialise aussi updatedAt
    }

    #[ORM\PreUpdate]
    public function setUpdatedAtValue(): void
    {
        $this->updatedAt = new \DateTime();
    }
    
    public function updateCommentsCount(): void
    {
    $this->comments_count = $this->comments->count();
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;
        return $this;
    }

    public function getDislikes(): ?int
    {
        return $this->dislikes;
    }

    public function setDislikes(?int $dislikes): static
    {
        $this->dislikes = $dislikes;

        return $this;
    }

    public function incrementDislikes(): void
    {
        $this->dislikes++;
    }

    public function isPinned(): bool
    {
        return $this->isPinned;
    }

    public function setIsPinned(bool $isPinned): self
    {
        $this->isPinned = $isPinned;
        return $this;
    }

    public function getNotifications(): array
    {
        return $this->notifications;
    }

    public function addNotification(string $message): self
    {
        $this->notifications[] = [
            'message' => $message,
            'createdAt' => (new \DateTime())->format('Y-m-d H:i:s'),
        ];
        return $this;
    }

    public function clearNotifications(): self
    {
        $this->notifications = [];
        return $this;
    }

    public function getFavoritedBy(): array
    {
        return $this->favoritedBy;
    }

    public function addFavoritedBy(int $userId): self
    {
        if (!in_array($userId, $this->favoritedBy, true)) {
            $this->favoritedBy[] = $userId;
        }
    }

    public function removeFavoritedBy(int $userId): self
    {
        $this->favoritedBy = array_filter($this->favoritedBy, fn ($id) => $id !== $userId);
        return $this;
    }

    public function isFavoritedBy(int $userId): bool
    {
        return in_array($userId, $this->favoritedBy, true);
    }

    
}
