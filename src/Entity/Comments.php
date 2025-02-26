<?php

namespace App\Entity;

use App\Entity\ForumPosts;
use App\Entity\User;
use App\Repository\CommentsRepository;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CommentsRepository::class)]
class Comments
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Assert\NotBlank(message: "Le contenu est requis.")]
    private ?string $content = null;

    #[ORM\Column(type: "datetime", nullable: false)]
    private ?\DateTimeInterface $createdAt = null;
    
    #[ORM\Column(type: "datetime", nullable: false)]
    private ?\DateTimeInterface $updatedAt = null;

    #[ORM\Column(nullable: true)]
    private ?int $likes = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Assert\Length(
        max: 255,
        maxMessage: "Les tags ne peuvent pas dépasser {{ limit }} caractères."
    )]
    private ?string $tags = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $attachments = null;

    #[ORM\ManyToOne(targetEntity: ForumPosts::class, inversedBy: 'comments')]
    #[ORM\JoinColumn(nullable: false)]
    private ?ForumPosts $forumPost = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    public function getForumPost(): ?ForumPosts
    {
        return $this->forumPost;
    }

    public function setForumPost(?ForumPosts $forumPost): self
    {
        $this->forumPost = $forumPost;

        return $this;
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function setTags(string $tags): static
    {
        $this->tags = $tags;

        return $this;
    }

    public function getAttachments(): ?string
    {
        return $this->attachments;
    }

    public function setAttachments(?string $attachments): static
    {
        $this->attachments = $attachments;

        return $this;
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
    
    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;
        return $this;
    }
}
