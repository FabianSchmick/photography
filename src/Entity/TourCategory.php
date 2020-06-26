<?php

namespace App\Entity;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * TourCategory.
 *
 * @ORM\Entity(repositoryClass="App\Repository\TourCategoryRepository")
 * @UniqueEntity("name")
 */
class TourCategory
{
    /**
     * @var int
     *
     * @ORM\Column(type="integer")
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="CUSTOM")
     * @ORM\CustomIdGenerator(class="App\Doctrine\UniqueIdGenerator")
     */
    private $id;

    /**
     * @var string|null
     *
     * @Assert\NotBlank()
     * @Assert\Length(max=128)
     * @Gedmo\Translatable
     * @ORM\Column(type="string", length=128, unique=true)
     */
    private $name;

    /**
     * @var int|null
     *
     * @Assert\Type("numeric")
     * @ORM\Column(type="integer", nullable=true)
     */
    private $sort;

    /**
     * @var string|null
     *
     * @ORM\Column(type="string", length=32, nullable=true)
     */
    private $formulaType;

    /**
     * @var bool
     *
     * @ORM\Column(type="boolean", options={"default" : 0})
     */
    private $hasLevelOfDifficulty;

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
     * @ORM\OneToMany(targetEntity="Tour", mappedBy="tourCategory", cascade={"persist"})
     * @ORM\OrderBy({"sort"="DESC"})
     */
    private $tours;

    /**
     * @var string|null
     *
     * @Gedmo\Locale
     * Used locale to override Translation listener`s locale
     */
    private $locale;

    /**
     * Tour constructor.
     */
    public function __construct()
    {
        $this->tours = new ArrayCollection();
        $this->hasLevelOfDifficulty = false;
    }

    public function __toString(): string
    {
        return $this->getName();
    }

    /**
     * Get id.
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Set name.
     *
     * @return TourCategory
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
     * Set formulaType.
     *
     * @return TourCategory
     */
    public function setFormulaType(?string $formulaType): self
    {
        $this->formulaType = $formulaType;

        return $this;
    }

    /**
     * Get formulaType.
     */
    public function getFormulaType(): ?string
    {
        return $this->formulaType;
    }

    /**
     * Set hasLevelOfDifficulty.
     *
     * @return TourCategory
     */
    public function setHasLevelOfDifficulty(bool $hasLevelOfDifficulty): self
    {
        $this->hasLevelOfDifficulty = $hasLevelOfDifficulty;

        return $this;
    }

    /**
     * Get hasLevelOfDifficulty.
     */
    public function isHasLevelOfDifficulty(): bool
    {
        return $this->hasLevelOfDifficulty;
    }

    /**
     * Set sort.
     *
     * @return TourCategory
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
     * Get tours.
     *
     * @return Collection|Tour[]
     */
    public function getTours(): Collection
    {
        return $this->tours;
    }

    /**
     * Set created.
     *
     * @return TourCategory
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
     * @return TourCategory
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
