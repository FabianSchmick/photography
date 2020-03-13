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
 * @ORM\Table(name="location")
 * @ORM\Entity(repositoryClass="App\Repository\LocationRepository")
 * @UniqueEntity("name")
 */
class Location
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string|null
     *
     * @Assert\NotBlank()
     * @Assert\Length(max=255)
     * @Gedmo\Translatable
     * @ORM\Column(name="name", type="string", length=255, unique=true)
     */
    private $name;

    /**
     * @var Collection
     *
     * @ORM\OneToMany(targetEntity="Entry", mappedBy="location", cascade={"persist"})
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
     * Location constructor.
     */
    public function __construct()
    {
        $this->entries = new ArrayCollection();
    }

    /**
     * @return string
     */
    public function __toString(): string
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
     * @return Location
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
     * Get Entries.
     *
     * @return Collection
     */
    public function getEntries(): Collection
    {
        return $this->entries;
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
