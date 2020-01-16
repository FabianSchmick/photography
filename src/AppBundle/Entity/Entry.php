<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * Entry.
 *
 * @ORM\Table(name="entry")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\EntryRepository")
 * @Vich\Uploadable
 */
class Entry
{
    const PAGINATION_QUANTITY = 10;

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
     * @ORM\Column(type="string", length=255)
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
     * @var Author|null
     *
     * @ORM\ManyToOne(targetEntity="Author", inversedBy="entries", cascade={"persist"})
     * @ORM\JoinColumn(referencedColumnName="id", onDelete="SET NULL")
     */
    private $author;

    /**
     * @var File|null
     *
     * @Assert\NotBlank()
     * @ORM\OneToOne(targetEntity="EntryImage", inversedBy="entry", cascade={"persist", "remove"})
     */
    private $image;

    /**
     * @var Location|null
     *
     * @ORM\ManyToOne(targetEntity="Location", inversedBy="entries", cascade={"persist"})
     * @ORM\JoinColumn(referencedColumnName="id", onDelete="SET NULL")
     */
    private $location;

    /**
     * @var \DateTime|null
     *
     * @Assert\Date()
     * @Assert\LessThan(
     *     value="now",
     * )
     * @ORM\Column(name="timestamp", type="datetime", nullable=true)
     */
    private $timestamp;

    /**
     * @var Collection
     *
     * @Assert\NotBlank()
     * @ORM\ManyToMany(targetEntity="Tag", inversedBy="entries", cascade={"persist"})
     * @ORM\JoinTable(name="tag_to_entry",
     *     joinColumns={@ORM\JoinColumn(referencedColumnName="id")},
     *     inverseJoinColumns={@ORM\JoinColumn(referencedColumnName="id")}
     * )
     * @ORM\OrderBy({"sort"="DESC"})
     */
    private $tags;

    /**
     * @var Tag|null
     *
     * @ORM\OneToMany(targetEntity="Tag", mappedBy="previewEntry")
     */
    private $previewTag;

    /**
     * @var Tour|null
     *
     * @ORM\ManyToOne(targetEntity="Tour", inversedBy="entries", cascade={"persist"})
     * @ORM\JoinColumn(referencedColumnName="id", onDelete="SET NULL")
     */
    private $tour;

    /**
     * @var Tour|null
     *
     * @ORM\OneToOne(targetEntity="Tour", mappedBy="previewEntry")
     */
    private $previewTour;

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
     * @var string|null
     *
     * @Gedmo\Locale
     * Used locale to override Translation listener`s locale
     */
    private $locale;

    /**
     * Entry constructor.
     */
    public function __construct()
    {
        $this->tags = new ArrayCollection();
        $this->timestamp = new \DateTime();
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
     * @param string $name
     *
     * @return Entry|null
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
     * @return Entry
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
     * Set author.
     *
     * @param Author|null $author
     *
     * @return Entry
     */
    public function setAuthor(Author $author)
    {
        $this->author = $author;

        return $this;
    }

    /**
     * Get author.
     *
     * @return Author
     */
    public function getAuthor()
    {
        return $this->author;
    }

    /**
     * Set image.
     *
     * @param File|null $image
     *
     * @return Entry
     */
    public function setImage(File $image)
    {
        $this->image = $image;

        return $this;
    }

    /**
     * Get image.
     *
     * @return File|null
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * Set location.
     *
     * @param Location|null $location
     *
     * @return Entry
     */
    public function setLocation(Location $location)
    {
        $this->location = $location;

        return $this;
    }

    /**
     * Get location.
     *
     * @return Location|null
     */
    public function getLocation()
    {
        return $this->location;
    }

    /**
     * Set timestamp.
     *
     * @param \DateTime|null $timestamp
     *
     * @return Entry
     */
    public function setTimestamp(\DateTime $timestamp)
    {
        $this->timestamp = clone $timestamp;

        return $this;
    }

    /**
     * Get timestamp.
     *
     * @return \DateTime|null
     */
    public function getTimestamp()
    {
        return $this->timestamp;
    }

    /**
     * Get tags.
     *
     * @return Collection|null
     */
    public function getTags()
    {
        return $this->tags;
    }

    /**
     * Set tags.
     *
     * @param Collection|null $tags
     */
    public function setTags(Collection $tags)
    {
        $this->tags = $tags;
    }

    /**
     * Get previewTag.
     *
     * @return Tag|null
     */
    public function getPreviewTag()
    {
        return $this->previewTag;
    }

    /**
     * Set previewTag.
     *
     * @param Tag|null $previewTag
     *
     * @return Entry
     */
    public function setPreviewTag(Tag $previewTag)
    {
        $this->previewTag = $previewTag;

        return $this;
    }

    /**
     * Set tour.
     *
     * @param Tour|null $tour
     *
     * @return Entry
     */
    public function setTour(Tour $tour)
    {
        $this->tour = $tour;

        return $this;
    }

    /**
     * Get tour.
     *
     * @return Tour|null
     */
    public function getTour()
    {
        return $this->tour;
    }

    /**
     * Get previewTour.
     *
     * @return Tour|null
     */
    public function getPreviewTour()
    {
        return $this->previewTour;
    }

    /**
     * Set previewTour.
     *
     * @param Tour|null $previewTour
     *
     * @return Entry
     */
    public function setPreviewTour(Tour $previewTour)
    {
        $this->previewTour = $previewTour;

        return $this;
    }

    /**
     * Set slug.
     *
     * @param string|null $slug
     *
     * @return Entry
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
     * @return Entry
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
     * @return Entry
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
     * Set locale.
     *
     * @param string|null $locale
     */
    public function setTranslatableLocale($locale)
    {
        $this->locale = $locale;
    }
}
