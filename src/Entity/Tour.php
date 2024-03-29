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
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="App\Repository\TourRepository")
 * @UniqueEntity("name")
 * @Vich\Uploadable
 */
class Tour
{
    final public const PAGINATION_QUANTITY = 6;

    final public const LEVEL_OF_DIFFICULTY = [
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
     * @ORM\Column(type="string", unique=true)
     */
    private string $name;

    /**
     * @Assert\Length(max=65535)
     * @Gedmo\Translatable
     * @ORM\Column(type="text", nullable=true)
     */
    private ?string $description = null;

    /**
     * @Assert\Length(max=65535)
     * @Gedmo\Translatable
     * @ORM\Column(type="text", nullable=true)
     */
    private ?string $directions = null;

    /**
     * @Assert\Length(max=65535)
     * @Gedmo\Translatable
     * @ORM\Column(type="text", nullable=true)
     */
    private ?string $equipmentAndSafety = null;

    /**
     * @var float|null in kilometers
     *
     * @Assert\Range(min=0, max=100000)
     * @ORM\Column(type="decimal", precision=6, scale=1, nullable=true)
     */
    private ?float $distance = null;

    /**
     * @ORM\Column(type="dateinterval", nullable=true)
     */
    private ?\DateInterval $duration = null;

    /**
     * @var int|null in meters
     *
     * @Assert\Range(min=-1000, max=100000)
     * @Assert\GreaterThanOrEqual(propertyPath="minAltitude")
     * @ORM\Column(type="smallint", nullable=true)
     */
    private ?int $maxAltitude = null;

    /**
     * @var int|null in meters
     *
     * @Assert\Range(min=-1000, max=100000)
     * @Assert\LessThanOrEqual(propertyPath="maxAltitude")
     * @ORM\Column(type="smallint", nullable=true)
     */
    private ?int $minAltitude = null;

    /**
     * @var int|null in meters
     *
     * @Assert\Range(min=-1000, max=100000)
     * @ORM\Column(type="smallint", nullable=true)
     */
    private ?int $cumulativeElevationGain = null;

    /**
     * @var int|null in meters
     *
     * @Assert\Range(min=-1000, max=100000)
     * @ORM\Column(type="smallint", nullable=true)
     */
    private ?int $cumulativeElevationLoss = null;

    /**
     * @var int|null in meters
     *
     * @Assert\Choice(choices=Tour::LEVEL_OF_DIFFICULTY)
     * @ORM\Column(type="smallint", nullable=true)
     */
    private ?int $levelOfDifficulty = null;

    /**
     * @Assert\Type("numeric")
     * @ORM\Column(type="integer", nullable=true)
     */
    private ?int $sort = null;

    /**
     * @ORM\ManyToMany(targetEntity="Location", inversedBy="tours", cascade={"persist"})
     * @ORM\JoinTable(name="location_to_tour",
     *     joinColumns={@ORM\JoinColumn(referencedColumnName="id")},
     *     inverseJoinColumns={@ORM\JoinColumn(referencedColumnName="id")}
     * )
     * @ORM\OrderBy({"name"="ASC"})
     *
     * @var Collection<Location>
     */
    private Collection $locations;

    /**
     * @ORM\OneToOne(targetEntity="Post", inversedBy="previewTour")
     * @ORM\JoinColumn(referencedColumnName="id", onDelete="SET NULL")
     */
    private ?Post $previewPost = null;

    /**
     * @Assert\NotBlank()
     * @ORM\OneToOne(targetEntity="TourFile", inversedBy="tour", cascade={"persist", "remove"})
     */
    private ?File $file = null;

    /**
     * @Gedmo\Translatable
     * @Gedmo\Slug(fields={"name"}, updatable=true)
     * @ORM\Column(type="string", unique=true)
     */
    private string $slug;

    /**
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(type="datetime")
     */
    private \DateTimeInterface $created;

    /**
     * @Gedmo\Timestampable(on="update")
     * @ORM\Column(type="datetime")
     */
    private \DateTimeInterface $updated;

    /**
     * @ORM\OneToMany(targetEntity="Post", mappedBy="tour", cascade={"persist"})
     * @ORM\OrderBy({"timestamp"="DESC"})
     *
     * @var Collection<Post>
     */
    private Collection $posts;

    /**
     * @ORM\ManyToOne(targetEntity="TourCategory", inversedBy="tours")
     * @ORM\JoinColumn(referencedColumnName="id", onDelete="SET NULL")
     */
    private ?TourCategory $tourCategory = null;

    /**
     * @Gedmo\Locale
     * Used locale to override Translation listener`s locale
     */
    private ?string $locale = null;

    /**
     * Helper variable for gpx segments (coordinates).
     */
    private ?array $segments = null;

    public function __construct()
    {
        $this->posts = new ArrayCollection();
        $this->locations = new ArrayCollection();
        $this->created = new \DateTime();
        $this->updated = new \DateTime();
    }

    public function __toString(): string
    {
        return $this->getName() ?? '';
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDirections(?string $directions): self
    {
        $this->directions = $directions;

        return $this;
    }

    public function getDirections(): ?string
    {
        return $this->directions;
    }

    public function setEquipmentAndSafety(?string $equipmentAndSafety): self
    {
        $this->equipmentAndSafety = $equipmentAndSafety;

        return $this;
    }

    public function getEquipmentAndSafety(): ?string
    {
        return $this->equipmentAndSafety;
    }

    public function setDistance(?float $distance): self
    {
        $this->distance = $distance;

        return $this;
    }

    public function getDistance(): ?float
    {
        return $this->distance;
    }

    public function setDuration(?\DateInterval $duration): self
    {
        $this->duration = $duration;

        return $this;
    }

    public function getDuration(): ?\DateInterval
    {
        return $this->duration;
    }

    public function setMaxAltitude(?int $maxAltitude): self
    {
        $this->maxAltitude = $maxAltitude;

        return $this;
    }

    public function getMaxAltitude(): ?int
    {
        return $this->maxAltitude;
    }

    public function setMinAltitude(?int $minAltitude): self
    {
        $this->minAltitude = $minAltitude;

        return $this;
    }

    public function getMinAltitude(): ?int
    {
        return $this->minAltitude;
    }

    public function setCumulativeElevationGain(?int $cumulativeElevationGain): self
    {
        $this->cumulativeElevationGain = $cumulativeElevationGain;

        return $this;
    }

    public function getCumulativeElevationGain(): ?int
    {
        return $this->cumulativeElevationGain !== 0 ? $this->cumulativeElevationGain : null;
    }

    public function setCumulativeElevationLoss(?int $cumulativeElevationLoss): self
    {
        $this->cumulativeElevationLoss = $cumulativeElevationLoss;

        return $this;
    }

    public function getCumulativeElevationLoss(): ?int
    {
        return $this->cumulativeElevationLoss !== 0 ? $this->cumulativeElevationLoss : null;
    }

    public function setLevelOfDifficulty(?int $levelOfDifficulty): self
    {
        $this->levelOfDifficulty = $levelOfDifficulty;

        return $this;
    }

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

    public function setSort(?int $sort): self
    {
        $this->sort = $sort;

        return $this;
    }

    public function getSort(): ?int
    {
        return $this->sort;
    }

    public function setLocations(?Collection $locations): self
    {
        $this->locations = $locations;

        return $this;
    }

    public function getLocations(): ?Collection
    {
        return $this->locations;
    }

    public function setPreviewPost(?Post $previewPost): self
    {
        $this->previewPost = $previewPost;

        return $this;
    }

    public function getPreviewPost(): ?Post
    {
        return $this->previewPost;
    }

    public function setFile(?File $file): self
    {
        $this->file = $file;

        return $this;
    }

    public function getFile(): ?File
    {
        return $this->file;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function setCreated(\DateTimeInterface $created): self
    {
        $this->created = $created;

        return $this;
    }

    public function getCreated(): \DateTimeInterface
    {
        return $this->created;
    }

    public function setUpdated(\DateTimeInterface $updated): self
    {
        $this->updated = $updated;

        return $this;
    }

    public function getUpdated(): \DateTimeInterface
    {
        return $this->updated;
    }

    /**
     * Get Posts with $this->previewPost as first element.
     */
    public function getPosts(): Collection
    {
        if (!$this->previewPost) {
            return $this->posts;
        }

        $previewPostId = $this->previewPost->getId();
        $posts = $this->posts->toArray();
        usort($posts, function (Post $a, Post $b) use ($previewPostId) {
            if ($a->getId() != $previewPostId && $b->getId() == $previewPostId) {
                return 1;
            } elseif ($a->getId() == $previewPostId && $b->getId() != $previewPostId) {
                return -1;
            }

            return $b->getTimestamp() > $a->getTimestamp();
        });

        return new ArrayCollection($posts);
    }

    public function getTourCategory(): ?TourCategory
    {
        return $this->tourCategory;
    }

    public function setTourCategory(?TourCategory $tourCategory): self
    {
        $this->tourCategory = $tourCategory;

        return $this;
    }

    public function setSegments(?array $segments): self
    {
        $this->segments = $segments;

        return $this;
    }

    public function getSegments(): ?array
    {
        return $this->segments;
    }

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
