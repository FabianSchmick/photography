<?php

namespace App\Entity;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use phpGPX\Models\Track;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * Tour.
 *
 * @ORM\Table(name="tour")
 * @ORM\Entity(repositoryClass="App\Repository\TourRepository")
 * @UniqueEntity("name")
 * @Vich\Uploadable
 */
class Tour
{
    const PAGINATION_QUANTITY = 6;

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
     * @ORM\Column(type="string", length=255, unique=true)
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
     * @var Entry|null
     *
     * @ORM\OneToOne(targetEntity="Entry", inversedBy="previewTour")
     * @ORM\JoinColumn(referencedColumnName="id", onDelete="SET NULL")
     */
    private $previewEntry;

    /**
     * @var File|null
     *
     * @Assert\NotBlank()
     * @ORM\OneToOne(targetEntity="TourFile", inversedBy="tour", cascade={"persist", "remove"})
     */
    private $file;

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
     * @var Collection
     *
     * @ORM\OneToMany(targetEntity="Entry", mappedBy="tour", cascade={"persist"})
     */
    private $entries;

    /**
     * @var string|null
     *
     * @Gedmo\Locale
     * Used locale to override Translation listener`s locale
     */
    private $locale;

    /**
     * Helper variable for gpx data like distance, altitude, duration etc.
     *
     * @var Track|null
     */
    private $gpxData;

    /**
     * Tour constructor.
     */
    public function __construct()
    {
        $this->entries = new ArrayCollection();
    }

    /**
     * @return string
     */
    public function __toString(): ?string
    {
        return $this->getName();
    }

    /**
     * Get id.
     *
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * Set name.
     *
     * @param string|null $name
     *
     * @return Tour
     */
    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name.
     *
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * Set description.
     *
     * @param string|null $description
     *
     * @return Tour
     */
    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description.
     *
     * @return string|null
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * Set previewEntry.
     *
     * @param Entry|null $previewEntry
     *
     * @return Tour
     */
    public function setPreviewEntry(?Entry $previewEntry): self
    {
        $this->previewEntry = $previewEntry;

        return $this;
    }

    /**
     * Get previewEntry.
     *
     * @return Entry|null
     */
    public function getPreviewEntry(): ?Entry
    {
        return $this->previewEntry;
    }

    /**
     * Set file.
     *
     * @param File|null $file
     *
     * @return Tour
     */
    public function setFile(?File $file): self
    {
        $this->file = $file;

        return $this;
    }

    /**
     * Get file.
     *
     * @return File|null
     */
    public function getFile(): ?File
    {
        return $this->file;
    }

    /**
     * Set slug.
     *
     * @param string|null $slug
     *
     * @return Tour
     */
    public function setSlug(?string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * Get slug.
     *
     * @return string|null
     */
    public function getSlug(): ?string
    {
        return $this->slug;
    }

    /**
     * Set created.
     *
     * @param \DateTime $created
     *
     * @return Tour
     */
    public function setCreated(DateTime $created): self
    {
        $this->created = $created;

        return $this;
    }

    /**
     * Get created.
     *
     * @return \DateTime|null
     */
    public function getCreated(): ?DateTime
    {
        return $this->created;
    }

    /**
     * Set updated.
     *
     * @param \DateTime $updated
     *
     * @return Tour
     */
    public function setUpdated(DateTime $updated): self
    {
        $this->updated = $updated;

        return $this;
    }

    /**
     * Get updated.
     *
     * @return \DateTime|null
     */
    public function getUpdated(): ?DateTime
    {
        return $this->updated;
    }

    /**
     * Get Entries.
     *
     * @return Collection
     */
    public function getEntries(): Collection
    {
        return $this->entries;
    }

    /**
     * @param Track|null $gpxData
     *
     * @return Tour
     */
    public function setGpxData(?Track $gpxData): self
    {
        $this->gpxData = $gpxData;

        return $this;
    }

    /**
     * @return Track|null
     */
    public function getGpxData(): ?Track
    {
        return $this->gpxData;
    }

    /**
     * Set locale.
     *
     * @param string|null $locale
     */
    public function setTranslatableLocale(?string $locale): void
    {
        $this->locale = $locale;
    }
}
