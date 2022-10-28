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
    const PAGINATION_QUANTITY = 10;

    /**
     * @var int
     *
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="CUSTOM")
     * @ORM\CustomIdGenerator(class="App\Doctrine\UniqueIdGenerator")
     */
    private $id;

    /**
     * @var string|null
     *
     * @Assert\NotBlank()
     * @Assert\Length(max=255)
     * @Gedmo\Translatable
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @var string|null
     *
     * @Assert\Length(max=65535)
     * @Gedmo\Translatable
     * @ORM\Column(type="text", length=65535, nullable=true)
     */
    private $description;

    /**
     * @var User|null
     *
     * @ORM\ManyToOne(targetEntity="User", inversedBy="entries", cascade={"persist"})
     * @ORM\JoinColumn(referencedColumnName="id", onDelete="SET NULL")
     */
    private $author;

    /**
     * @var File|null
     *
     * @Assert\NotBlank()
     * @ORM\OneToOne(targetEntity="EntryImage", inversedBy="entry", cascade={"persist", "remove"})
     */
    private $image;

    /**
     * @var Location|null
     *
     * @ORM\ManyToOne(targetEntity="Location", inversedBy="entries", cascade={"persist"})
     * @ORM\JoinColumn(referencedColumnName="id", onDelete="SET NULL")
     */
    private $location;

    /**
     * @var \DateTime|null
     *
     * @Assert\Type("\DateTimeInterface")
     * @Assert\LessThan(
     *     value="now",
     * )
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $timestamp;

    /**
     * @var Collection
     *
     * @Assert\NotBlank()
     * @ORM\ManyToMany(targetEntity="Tag", inversedBy="entries", cascade={"persist"})
     * @ORM\JoinTable(name="tag_to_entry",
     *     joinColumns={@ORM\JoinColumn(referencedColumnName="id")},
     *     inverseJoinColumns={@ORM\JoinColumn(referencedColumnName="id")}
     * )
     * @ORM\OrderBy({"sort"="DESC"})
     */
    private $tags;

    /**
     * @var Tag|null
     *
     * @ORM\OneToMany(targetEntity="Tag", mappedBy="previewEntry")
     */
    private $previewTag;

    /**
     * @var Tour|null
     *
     * @ORM\ManyToOne(targetEntity="Tour", inversedBy="entries", cascade={"persist"})
     * @ORM\JoinColumn(referencedColumnName="id", onDelete="SET NULL")
     */
    private $tour;

    /**
     * @var Tour|null
     *
     * @ORM\OneToOne(targetEntity="Tour", mappedBy="previewEntry")
     */
    private $previewTour;

    /**
     * @var string|null
     *
     * @Gedmo\Translatable
     * @Gedmo\Slug(fields={"name"}, updatable=true)
     * @ORM\Column(type="string", unique=true)
     */
    private $slug;

    /**
     * @var \DateTime|null
     *
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(type="datetime")
     */
    private $created;

    /**
     * @var \DateTime|null
     *
     * @Gedmo\Timestampable(on="update")
     * @ORM\Column(type="datetime")
     */
    private $updated;

    /**
     * @var string|null
     *
     * @Gedmo\Locale
     * Used locale to override Translation listener`s locale
     */
    private $locale;

    /**
     * Entry constructor.
     */
    public function __construct()
    {
        $this->tags = new ArrayCollection();
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
     *
     * @return Entry
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
     *
     * @return Entry
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
     *
     * @return Entry
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
     *
     * @return Entry
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
     *
     * @return Entry
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
     *
     * @return Entry
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
     *
     * @return Entry
     */
    public function setTags(Collection $tags): self
    {
        $this->tags = $tags;

        return $this;
    }

    /**
     * Get tags.
     *
     * @return Collection|null
     */
    public function getTags(): Collection
    {
        return $this->tags;
    }

    /**
     * Get previewTag.
     */
    public function getPreviewTag(): ?Tag
    {
        return $this->previewTag;
    }

    /**
     * Set tour.
     *
     * @return Entry
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
     *
     * @return Entry
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
     *
     * @return Entry
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
     *
     * @return Entry
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
