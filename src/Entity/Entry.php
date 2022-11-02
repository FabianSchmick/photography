<?php

namespace App\Entity;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * Entry.
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="App\Repository\EntryRepository")
 * @Vich\Uploadable
 */
class Entry
{
    final public const PAGINATION_QUANTITY = 10;

    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="CUSTOM")
     * @ORM\CustomIdGenerator(class="App\Doctrine\UniqueIdGenerator")
     */
    private int $id;

    /**
     * @Assert\NotBlank()
     * @Assert\Length(max=255)
     * @Gedmo\Translatable
     * @ORM\Column(type="string", length=255)
     */
    private ?string $name = null;

    /**
     * @Assert\Length(max=65535)
     * @Gedmo\Translatable
     * @ORM\Column(type="text", length=65535, nullable=true)
     */
    private ?string $description = null;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="entries", cascade={"persist"})
     * @ORM\JoinColumn(referencedColumnName="id", onDelete="SET NULL")
     */
    private ?User $author = null;

    /**
     * @Assert\NotBlank()
     * @ORM\OneToOne(targetEntity="EntryImage", inversedBy="entry", cascade={"persist", "remove"})
     */
    private ?File $image = null;

    /**
     * @ORM\ManyToOne(targetEntity="Location", inversedBy="entries", cascade={"persist"})
     * @ORM\JoinColumn(referencedColumnName="id", onDelete="SET NULL")
     */
    private ?Location $location = null;

    /**
     * @Assert\Type("\DateTimeInterface")
     * @Assert\LessThan(
     *     value="now",
     * )
     * @ORM\Column(type="datetime", nullable=true)
     */
    private ?DateTime $timestamp;

    /**
     * @Assert\NotBlank()
     * @ORM\ManyToMany(targetEntity="Tag", inversedBy="entries", cascade={"persist"})
     * @ORM\JoinTable(name="tag_to_entry",
     *     joinColumns={@ORM\JoinColumn(referencedColumnName="id")},
     *     inverseJoinColumns={@ORM\JoinColumn(referencedColumnName="id")}
     * )
     * @ORM\OrderBy({"sort"="DESC"})
     */
    private Collection $tags;

    /**
     * @ORM\OneToMany(targetEntity="Tag", mappedBy="previewEntry")
     */
    private Collection $previewTags;

    /**
     * @ORM\ManyToOne(targetEntity="Tour", inversedBy="entries", cascade={"persist"})
     * @ORM\JoinColumn(referencedColumnName="id", onDelete="SET NULL")
     */
    private ?Tour $tour = null;

    /**
     * @ORM\OneToOne(targetEntity="Tour", mappedBy="previewEntry")
     */
    private ?Tour $previewTour = null;

    /**
     * @Gedmo\Translatable
     * @Gedmo\Slug(fields={"name"}, updatable=true)
     * @ORM\Column(type="string", unique=true)
     */
    private ?string $slug = null;

    /**
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(type="datetime")
     */
    private ?DateTime $created = null;

    /**
     * @Gedmo\Timestampable(on="update")
     * @ORM\Column(type="datetime")
     */
    private ?DateTime $updated = null;

    /**
     * @Gedmo\Locale
     * Used locale to override Translation listener`s locale
     */
    private ?string $locale = null;

    /**
     * Entry constructor.
     */
    public function __construct()
    {
        $this->tags = new ArrayCollection();
        $this->previewTags = new ArrayCollection();
        $this->timestamp = new DateTime();
    }

    public function __toString(): string
    {
        return $this->getName();
    }

    /**
     * Get id.
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * Set name.
     */
    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name.
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * Set description.
     */
    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description.
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * Set author.
     */
    public function setAuthor(?User $author): self
    {
        $this->author = $author;

        return $this;
    }

    /**
     * Get author.
     *
     * @return User
     */
    public function getAuthor(): ?User
    {
        return $this->author;
    }

    /**
     * Set image.
     */
    public function setImage(?File $image): self
    {
        $this->image = $image;

        return $this;
    }

    /**
     * Get image.
     */
    public function getImage(): ?File
    {
        return $this->image;
    }

    /**
     * Set location.
     */
    public function setLocation(?Location $location): self
    {
        $this->location = $location;

        return $this;
    }

    /**
     * Get location.
     */
    public function getLocation(): ?Location
    {
        return $this->location;
    }

    /**
     * Set timestamp.
     */
    public function setTimestamp(?DateTime $timestamp): self
    {
        $this->timestamp = clone $timestamp;

        return $this;
    }

    /**
     * Get timestamp.
     */
    public function getTimestamp(): ?DateTime
    {
        return $this->timestamp;
    }

    /**
     * Set tags.
     */
    public function setTags(Collection $tags): self
    {
        $this->tags = $tags;

        return $this;
    }

    /**
     * Get tags.
     */
    public function getTags(): Collection
    {
        return $this->tags;
    }

    /**
     * Get previewTag.
     */
    public function getPreviewTags(): Collection
    {
        return $this->previewTags;
    }

    /**
     * Set tour.
     */
    public function setTour(?Tour $tour): self
    {
        $this->tour = $tour;

        return $this;
    }

    /**
     * Get tour.
     */
    public function getTour(): ?Tour
    {
        return $this->tour;
    }

    /**
     * Get previewTour.
     */
    public function getPreviewTour(): ?Tour
    {
        return $this->previewTour;
    }

    /**
     * Set slug.
     */
    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * Get slug.
     */
    public function getSlug(): ?string
    {
        return $this->slug;
    }

    /**
     * Set created.
     */
    public function setCreated(DateTime $created): self
    {
        $this->created = $created;

        return $this;
    }

    /**
     * Get created.
     */
    public function getCreated(): ?DateTime
    {
        return $this->created;
    }

    /**
     * Set updated.
     */
    public function setUpdated(DateTime $updated): self
    {
        $this->updated = $updated;

        return $this;
    }

    /**
     * Get updated.
     */
    public function getUpdated(): ?DateTime
    {
        return $this->updated;
    }

    /**
     * Set locale.
     */
    public function setTranslatableLocale(?string $locale): void
    {
        $this->locale = $locale;
    }
}
