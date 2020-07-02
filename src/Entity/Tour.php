<?php

namespace App\Entity;

use DateInterval;
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

    const FORMULA_DEFINITIONS = [
        'HIKING' => [
            /* Values according to: DIN 33466 */
            'UP_METERS_PER_HOUR' => 300,
            'DOWN_METERS_PER_HOUR' => 500,
            'HORIZONTAL_METERS_PER_HOUR' => 4,
        ],
        'MTB' => [
            'UP_METERS_PER_HOUR' => 500,
            'HORIZONTAL_METERS_PER_HOUR' => 12,
        ],
        'VIA_FERRATA' => [
            'UP_METERS_PER_HOUR' => 200,
            'DOWN_METERS_PER_HOUR' => 400,
        ],
    ];

    const LEVEL_OF_DIFFICULTY = [
        'A' => 0,
        'A/B' => 1,
        'B' => 2,
        'B/C' => 3,
        'C' => 4,
        'C/D' => 5,
        'D' => 6,
        'D/E' => 7,
        'E' => 8,
        'I' => 9,
        'II' => 10,
        'III' => 11,
        'IV' => 12,
        'V' => 13,
        'VI' => 14,
    ];

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
     * @var string|null
     *
     * @Assert\Length(max=65535)
     * @Gedmo\Translatable
     * @ORM\Column(type="text", length=65535, nullable=true)
     */
    private $directions;

    /**
     * @var string|null
     *
     * @Assert\Length(max=65535)
     * @Gedmo\Translatable
     * @ORM\Column(type="text", length=65535, nullable=true)
     */
    private $equipmentAndSafety;

    /**
     * @var float|null in kilometers
     *
     * @Assert\Range(min=0, max=100000)
     * @ORM\Column(type="decimal", precision=6, scale=1, nullable=true)
     */
    private $distance;

    /**
     * @var DateInterval|null
     *
     * @ORM\Column(type="dateinterval", nullable=true)
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
     * @var int|null in meters
     *
     * @Assert\Choice(choices=Tour::LEVEL_OF_DIFFICULTY)
     * @ORM\Column(type="smallint", nullable=true)
     */
    private $levelOfDifficulty;

    /**
     * @var int|null
     *
     * @Assert\Type("numeric")
     * @ORM\Column(type="integer", nullable=true)
     */
    private $sort;

    /**
     * @var Collection
     *
     * @ORM\ManyToMany(targetEntity="Location", inversedBy="tours", cascade={"persist"})
     * @ORM\JoinTable(name="location_to_tour",
     *     joinColumns={@ORM\JoinColumn(referencedColumnName="id")},
     *     inverseJoinColumns={@ORM\JoinColumn(referencedColumnName="id")}
     * )
     * @ORM\OrderBy({"name"="ASC"})
     */
    private $locations;

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
     * @ORM\OrderBy({"timestamp"="DESC"})
     */
    private $entries;

    /**
     * @var TourCategory|null
     *
     * @ORM\ManyToOne(targetEntity="TourCategory", inversedBy="tours")
     * @ORM\JoinColumn(referencedColumnName="id", onDelete="SET NULL")
     */
    private $tourCategory;

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
        $this->locations = new ArrayCollection();
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
     * Set directions.
     *
     * @return Tour
     */
    public function setDirections(?string $directions): self
    {
        $this->directions = $directions;

        return $this;
    }

    /**
     * Get directions.
     */
    public function getDirections(): ?string
    {
        return $this->directions;
    }

    /**
     * Set equipmentAndSafety.
     *
     * @return Tour
     */
    public function setEquipmentAndSafety(?string $equipmentAndSafety): self
    {
        $this->equipmentAndSafety = $equipmentAndSafety;

        return $this;
    }

    /**
     * Get equipmentAndSafety.
     */
    public function getEquipmentAndSafety(): ?string
    {
        return $this->equipmentAndSafety;
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
    public function setDuration(?DateInterval $duration): self
    {
        $this->duration = $duration;

        return $this;
    }

    /**
     * Get duration.
     */
    public function getDuration(): ?DateInterval
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
        return $this->cumulativeElevationGain !== 0 ? $this->cumulativeElevationGain : null;
    }

    /**
     * Set levelOfDifficulty.
     *
     * @return Tour
     */
    public function setLevelOfDifficulty(?int $levelOfDifficulty): self
    {
        $this->levelOfDifficulty = $levelOfDifficulty;

        return $this;
    }

    /**
     * Get levelOfDifficulty.
     */
    public function getLevelOfDifficulty(): ?int
    {
        return $this->levelOfDifficulty;
    }

    /**
     * Get the real value (not the key).
     */
    public function getValueLevelOfDifficulty()
    {
        return array_search($this->levelOfDifficulty, self::LEVEL_OF_DIFFICULTY);
    }

    /**
     * Set sort.
     *
     * @return Tour
     */
    public function setSort(?int $sort): self
    {
        $this->sort = $sort;

        return $this;
    }

    /**
     * Get sort.
     */
    public function getSort(): ?int
    {
        return $this->sort;
    }

    /**
     * Set locations.
     *
     * @return Tour
     */
    public function setLocation(?Collection $locations): self
    {
        $this->locations = $locations;

        return $this;
    }

    /**
     * Get locations.
     */
    public function getLocations(): ?Collection
    {
        return $this->locations;
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
     * Get Entries with $this->previewEntry as first element.
     */
    public function getEntries(): Collection
    {
        if (!$this->previewEntry) {
            return $this->entries;
        }

        $previewEntryId = $this->previewEntry->getId();
        $entries = $this->entries->toArray();
        usort($entries, function (Entry $a, Entry $b) use ($previewEntryId) {
            if ($a->getId() != $previewEntryId && $b->getId() == $previewEntryId) {
                return 1;
            } elseif ($a->getId() == $previewEntryId && $b->getId() != $previewEntryId) {
                return -1;
            }

            return $b->getTimestamp() > $a->getTimestamp();
        });

        return new ArrayCollection($entries);
    }

    /**
     * Get tourCategory.
     */
    public function getTourCategory(): ?TourCategory
    {
        return $this->tourCategory;
    }

    /**
     * Set tourCategory.
     *
     * @return Tour
     */
    public function setTourCategory(?TourCategory $tourCategory): self
    {
        $this->tourCategory = $tourCategory;

        return $this;
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

    /**
     * Returns the formula to apply for calculating the tour duration.
     */
    public function getFormulaType(): ?string
    {
        return !$this->getTourCategory() ?: $this->getTourCategory()->getFormulaType();
    }
}
