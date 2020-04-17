<?php

namespace App\Entity;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
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

    /** Values according to: DIN 33466 */
    const UP_METERS_PER_HOUR = 300;
    const DOWN_METERS_PER_HOUR = 500;
    const HORIZONTAL_METERS_PER_HOUR = 4;

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
     * @var float|null in kilometers
     *
     * @Assert\Range(min=0, max=100000)
     * @ORM\Column(type="decimal", precision=6, scale=1, nullable=true)
     */
    private $distance;

    /**
     * @var DateTime|null
     *
     * @Assert\Time()
     * @ORM\Column(type="time", nullable=true)
     */
    private $duration;

    /**
     * @var int|null in meters
     *
     * @Assert\Range(min=-1000, max=100000)
     * @Assert\GreaterThanOrEqual(propertyPath="minAltitude")
     * @ORM\Column(type="smallint", nullable=true)
     */
    private $maxAltitude;

    /**
     * @var int|null in meters
     *
     * @Assert\Range(min=-1000, max=100000)
     * @Assert\LessThanOrEqual(propertyPath="maxAltitude")
     * @ORM\Column(type="smallint", nullable=true)
     */
    private $minAltitude;

    /**
     * @var int|null in meters
     *
     * @Assert\Range(min=-1000, max=100000)
     * @ORM\Column(type="smallint", nullable=true)
     */
    private $cumulativeElevationGain;

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
     * Helper variable for gpx segments (coordinates).
     *
     * @var array|null
     */
    private $segments;

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
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * Set name.
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
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * Set description.
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
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * Set distance.
     *
     * @return Tour
     */
    public function setDistance(?float $distance): self
    {
        $this->distance = $distance;

        return $this;
    }

    /**
     * Get distance.
     */
    public function getDistance(): ?float
    {
        return $this->distance;
    }

    /**
     * Set duration.
     *
     * @return Tour
     */
    public function setDuration(?DateTime $duration): self
    {
        $this->duration = $duration;

        return $this;
    }

    /**
     * Get duration.
     */
    public function getDuration(): ?DateTime
    {
        return $this->duration;
    }

    /**
     * Set maxAltitude.
     *
     * @return Tour
     */
    public function setMaxAltitude(?int $maxAltitude): self
    {
        $this->maxAltitude = $maxAltitude;

        return $this;
    }

    /**
     * Get maxAltitude.
     */
    public function getMaxAltitude(): ?int
    {
        return $this->maxAltitude;
    }

    /**
     * Set minAltitude.
     *
     * @return Tour
     */
    public function setMinAltitude(?int $minAltitude): self
    {
        $this->minAltitude = $minAltitude;

        return $this;
    }

    /**
     * Get minAltitude.
     */
    public function getMinAltitude(): ?int
    {
        return $this->minAltitude;
    }

    /**
     * Set cumulativeElevationGain.
     *
     * @return Tour
     */
    public function setCumulativeElevationGain(?int $cumulativeElevationGain): self
    {
        $this->cumulativeElevationGain = $cumulativeElevationGain;

        return $this;
    }

    /**
     * Get cumulativeElevationGain.
     */
    public function getCumulativeElevationGain(): ?int
    {
        return $this->cumulativeElevationGain != 0 ? $this->cumulativeElevationGain : null;
    }

    /**
     * Set previewEntry.
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
     */
    public function getPreviewEntry(): ?Entry
    {
        return $this->previewEntry;
    }

    /**
     * Set file.
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
     */
    public function getFile(): ?File
    {
        return $this->file;
    }

    /**
     * Set slug.
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
     */
    public function getSlug(): ?string
    {
        return $this->slug;
    }

    /**
     * Set created.
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
     */
    public function getCreated(): ?DateTime
    {
        return $this->created;
    }

    /**
     * Set updated.
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
     */
    public function getUpdated(): ?DateTime
    {
        return $this->updated;
    }

    /**
     * Get Entries.
     */
    public function getEntries(): Collection
    {
        return $this->entries;
    }

    /**
     * Set segments.
     *
     * @return Tour
     */
    public function setSegments(?array $segments): self
    {
        $this->segments = $segments;

        return $this;
    }

    /**
     * Get segments.
     */
    public function getSegments(): ?array
    {
        return $this->segments;
    }

    /**
     * Set locale.
     */
    public function setTranslatableLocale(?string $locale): void
    {
        $this->locale = $locale;
    }
}
