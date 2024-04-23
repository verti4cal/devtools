<?php

namespace App\Entity;

use App\Repository\ImageRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\HasLifecycleCallbacks;
use Symfony\UX\Turbo\Attribute\Broadcast;

#[ORM\Entity(repositoryClass: ImageRepository::class)]
#[Broadcast]
#[HasLifecycleCallbacks]
class Image
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(nullable: true)]
    private ?int $size = null;

    #[ORM\Column(nullable: true)]
    private ?int $compressedSize = null;

    #[ORM\Column(nullable: true)]
    private ?int $croppedSize = null;

    #[ORM\Column(type: 'datetime_immutable')]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private ?\DateTimeImmutable $deletedAt = null;

    #[ORM\Column(nullable: true)]
    private ?string $compressedFileName = null;

    #[ORM\Column]
    private ?string $safeFileName = null;

    #[ORM\Column(length: 255)]
    private ?string $extension = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $compressedExtension = null;

    #[ORM\Column(nullable: true)]
    private ?string $croppedFileName = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $croppedExtension = null;

    #[ORM\Column(nullable: true)]
    private ?bool $hasFavicon = null;

    #[ORM\OneToMany(targetEntity: Favicon::class, mappedBy: 'image', orphanRemoval: true)]
    private Collection $favicons;

    public function __construct()
    {
        $this->favicons = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFullFileName(): ?string
    {
        return $this->safeFileName . '.' . $this->extension;
    }

    public function getFullCompressedFileName(): ?string
    {
        return $this->compressedFileName . '.' . $this->compressedExtension;
    }

    public function getFullCroppedFileName(): ?string
    {
        return $this->croppedFileName . '.' . $this->croppedExtension;
    }

    public function getSize(): ?int
    {
        return $this->size;
    }

    public function setSize(int $size): static
    {
        $this->size = $size;

        return $this;
    }

    public function getCompressedSize(): ?int
    {
        return $this->compressedSize;
    }

    public function setCompressedSize(int $compressedSize): static
    {
        $this->compressedSize = $compressedSize;

        return $this;
    }

    public function getCroppedSize(): ?int
    {
        return $this->croppedSize;
    }

    public function setCroppedSize(int $croppedSize): static
    {
        $this->croppedSize = $croppedSize;

        return $this;
    }

    #[ORM\PrePersist]
    public function setCreatedAtValue(): void
    {
        $this->createdAt = new \DateTimeImmutable();
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    #[ORM\PreUpdate]
    public function setUpdatedAtValue(): void
    {
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    #[ORM\PreRemove]
    public function setDeletedAtValue(): void
    {
        $this->deletedAt = new \DateTimeImmutable();
    }

    public function getDeletedAt(): ?\DateTimeImmutable
    {
        return $this->deletedAt;
    }

    public function getCompressedFileName(): ?string
    {
        return $this->compressedFileName;
    }

    public function setCompressedFileName(?string $compressedFileName): static
    {
        $this->compressedFileName = $compressedFileName;

        return $this;
    }

    public function getSafeFileName(): ?string
    {
        return $this->safeFileName;
    }

    public function setSafeFileName(string $safeFileName): static
    {
        $this->safeFileName = $safeFileName;

        return $this;
    }

    public function getExtension(): ?string
    {
        return $this->extension;
    }

    public function setExtension(string $extension): static
    {
        $this->extension = $extension;

        return $this;
    }

    public function getCompressedExtension(): ?string
    {
        return $this->compressedExtension;
    }

    public function setCompressedExtension(?string $compressedExtension): static
    {
        $this->compressedExtension = $compressedExtension;

        return $this;
    }

    public function getCroppedFileName(): ?string
    {
        return $this->croppedFileName;
    }

    public function setCroppedFileName(?string $croppedFileName): static
    {
        $this->croppedFileName = $croppedFileName;

        return $this;
    }

    public function getCroppedExtension(): ?string
    {
        return $this->croppedExtension;
    }

    public function setCroppedExtension(?string $croppedExtension): static
    {
        $this->croppedExtension = $croppedExtension;

        return $this;
    }

    public function hasFavicon(): ?bool
    {
        return $this->hasFavicon;
    }

    public function setHasFavicon(bool $hasFavicon): static
    {
        $this->hasFavicon = $hasFavicon;

        return $this;
    }

    /**
     * @return Collection<int, Favicon>
     */
    public function getFavicons(): Collection
    {
        return $this->favicons;
    }

    public function addFavicon(Favicon $favicon): static
    {
        if (!$this->favicons->contains($favicon)) {
            $this->favicons->add($favicon);
            $favicon->setImage($this);
        }

        return $this;
    }

    public function removeFavicon(Favicon $favicon): static
    {
        if ($this->favicons->removeElement($favicon)) {
            // set the owning side to null (unless already changed)
            if ($favicon->getImage() === $this) {
                $favicon->setImage(null);
            }
        }

        return $this;
    }
}
