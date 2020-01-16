<?php

namespace AppBundle\Entity;

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
 * @ORM\Entity(repositoryClass="AppBundle\Repository\TourRepository")
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
     * @ORM\CustomIdGenerator(class="AppBundle\Doctrine\UniqueIdGenerator")
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
    public function __toString()
    {
        return $this->getName();
    }

    /**
     * Get id.
     *
     * @return int
     */
    public function getId()
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
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name.
     *
     * @return string|null
     */
    public function getName()
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
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description.
     *
     * @return string|null
     */
    public function getDescription()
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
    public function setPreviewEntry(Entry $previewEntry)
    {
        $this->previewEntry = $previewEntry;

        return $this;
    }

    /**
     * Get previewEntry.
     *
     * @return Entry|null
     */
    public function getPreviewEntry()
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
    public function setFile(File $file)
    {
        $this->file = $file;

        return $this;
    }

    /**
     * Get file.
     *
     * @return File|null
     */
    public function getFile()
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
    public function setSlug($slug)
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * Get slug.
     *
     * @return string|null
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * Set created.
     *
     * @param \DateTime|null $created
     *
     * @return Tour
     */
    public function setCreated($created)
    {
        $this->created = $created;

        return $this;
    }

    /**
     * Get created.
     *
     * @return \DateTime|null
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * Set updated.
     *
     * @param \DateTime|null $updated
     *
     * @return Tour
     */
    public function setUpdated(\DateTime $updated)
    {
        $this->updated = $updated;

        return $this;
    }

    /**
     * Get updated.
     *
     * @return \DateTime|null
     */
    public function getUpdated()
    {
        return $this->updated;
    }

    /**
     * Get Entries.
     *
     * @return Collection
     */
    public function getEntries()
    {
        return $this->entries;
    }

    /**
     * Set Entries.
     *
     * @param Collection $entries
     */
    public function setEntries($entries)
    {
        $this->entries = $entries;
    }

    /**
     * Set locale.
     *
     * @param string|null $locale
     */
    public function setTranslatableLocale($locale)
    {
        $this->locale = $locale;
    }

    /**
     * @return Track|null
     */
    public function getGpxData()
    {
        return $this->gpxData;
    }

    /**
     * @param Track|null $gpxData
     */
    public function setGpxData(Track $gpxData)
    {
        $this->gpxData = $gpxData;
    }
}
