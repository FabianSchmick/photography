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

    public function __construct()
    {
        $this->entries = new ArrayCollection();
        $this->tours = new ArrayCollection();
    }

    public function __toString(): string
    {
        return $this->getName();
    }

    public function getId(): int
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

    public function getEntries(): Collection
    {
        return $this->entries;
    }

    public function getTours(): Collection
    {
        return $this->tours;
    }

    public function setTranslatableLocale(?string $locale): void
    {
        $this->locale = $locale;
    }
}
