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
    private DateTimeInterface $created;

    /**
     * @Gedmo\Timestampable(on="update")
     * @ORM\Column(type="datetime")
     */
    private DateTimeInterface $updated;

    /**
     * @ORM\OneToMany(targetEntity="Tour", mappedBy="tourCategory", cascade={"persist"})
     * @ORM\OrderBy({"sort"="DESC"})
     *
     * @var Collection<Tour>
     */
    private Collection $tours;

    /**
     * @Gedmo\Locale
     * Used locale to override Translation listener`s locale
     */
    private ?string $locale = null;

    public function __construct()
    {
        $this->tours = new ArrayCollection();
        $this->created = new DateTime();
        $this->updated = new DateTime();
    }

    public function __toString(): string
    {
        return $this->getName() ?? '';
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setFormulaType(?string $formulaType): self
    {
        $this->formulaType = $formulaType;

        return $this;
    }

    public function getFormulaType(): ?string
    {
        return $this->formulaType;
    }

    public function setHasLevelOfDifficulty(bool $hasLevelOfDifficulty): self
    {
        $this->hasLevelOfDifficulty = $hasLevelOfDifficulty;

        return $this;
    }

    public function isHasLevelOfDifficulty(): bool
    {
        return $this->hasLevelOfDifficulty;
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

    public function getTours(): Collection
    {
        return $this->tours;
    }

    public function setCreated(DateTimeInterface $created): self
    {
        $this->created = $created;

        return $this;
    }

    public function getCreated(): DateTimeInterface
    {
        return $this->created;
    }

    public function setUpdated(DateTimeInterface $updated): self
    {
        $this->updated = $updated;

        return $this;
    }

    public function getUpdated(): DateTimeInterface
    {
        return $this->updated;
    }

    public function setTranslatableLocale(?string $locale): void
    {
        $this->locale = $locale;
    }
}
