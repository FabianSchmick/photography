<?php

namespace App\Entity;

use DateTime;
use DateTimeInterface;
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
     * @ORM\Column(type="integer")
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="CUSTOM")
     * @ORM\CustomIdGenerator(class="App\Doctrine\UniqueIdGenerator")
     */
    private int $id;

    /**
     * @Assert\NotBlank()
     * @Assert\Length(max=128)
     * @Gedmo\Translatable
     * @ORM\Column(type="string", length=128, unique=true)
     */
    private ?string $name = null;

    /**
     * @Assert\Type("numeric")
     * @ORM\Column(type="integer", nullable=true)
     */
    private ?int $sort = null;

    /**
     * @ORM\Column(type="string", length=32, nullable=true)
     */
    private ?string $formulaType = null;

    /**
     * @ORM\Column(type="boolean", options={"default" : 0})
     */
    private bool $hasLevelOfDifficulty = false;

    /**
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(type="datetime")
     */
    private ?DateTimeInterface $created = null;

    /**
     * @Gedmo\Timestampable(on="update")
     * @ORM\Column(type="datetime")
     */
    private ?DateTimeInterface $updated = null;

    /**
     * @ORM\OneToMany(targetEntity="Tour", mappedBy="tourCategory", cascade={"persist"})
     * @ORM\OrderBy({"sort"="DESC"})
     */
    private Collection $tours;

    /**
     * @Gedmo\Locale
     * Used locale to override Translation listener`s locale
     */
    private ?string $locale = null;

    /**
     * Tour constructor.
     */
    public function __construct()
    {
        $this->tours = new ArrayCollection();
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
     */
    public function getTours(): Collection
    {
        return $this->tours;
    }

    /**
     * Set created.
     */
    public function setCreated(DateTimeInterface $created): self
    {
        $this->created = $created;

        return $this;
    }

    /**
     * Get created.
     */
    public function getCreated(): ?DateTimeInterface
    {
        return $this->created;
    }

    /**
     * Set updated.
     */
    public function setUpdated(DateTimeInterface $updated): self
    {
        $this->updated = $updated;

        return $this;
    }

    /**
     * Get updated.
     */
    public function getUpdated(): ?DateTimeInterface
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
