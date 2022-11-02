<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Location.
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="App\Repository\LocationRepository")
 * @UniqueEntity("name")
 */
class Location
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private int $id;

    /**
     * @Assert\NotBlank()
     * @Assert\Length(max=255)
     * @Gedmo\Translatable
     * @ORM\Column(type="string", length=255, unique=true)
     */
    private ?string $name = null;

    /**
     * @ORM\OneToMany(targetEntity="Entry", mappedBy="location", cascade={"persist"})
     */
    private Collection $entries;

    /**
     * @ORM\ManyToMany(targetEntity="Tour", mappedBy="locations", cascade={"persist"})
     */
    private Collection $tours;

    /**
     * @Gedmo\Locale
     * Used locale to override Translation listener`s locale
     */
    private ?string $locale = null;

    /**
     * Location constructor.
     */
    public function __construct()
    {
        $this->entries = new ArrayCollection();
        $this->tours = new ArrayCollection();
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
     * Get Entries.
     */
    public function getEntries(): Collection
    {
        return $this->entries;
    }

    /**
     * Get Tours.
     */
    public function getTours(): Collection
    {
        return $this->tours;
    }

    /**
     * Set locale.
     */
    public function setTranslatableLocale(?string $locale): void
    {
        $this->locale = $locale;
    }
}
