<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
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
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="CUSTOM")
     * @ORM\CustomIdGenerator(class="AppBundle\Doctrine\UniqueIdGenerator")
     */
    private $id;

    /**
     * @var string
     *
     * @Gedmo\Translatable
     * @Assert\NotBlank()
     * @ORM\Column(name="title", type="string", length=255)
     */
    private $title;

    /**
     * @var array
     *
     * @Gedmo\Translatable
     * @ORM\Column(name="description", type="array", nullable=true)
     */
    private $description;

    /**
     * @var Author
     *
     * @ORM\ManyToOne(targetEntity="Author", inversedBy="entries", cascade={"persist"})
     * @ORM\JoinColumn(name="author_id", referencedColumnName="id")
     */
    private $author;

    /**
     * @var File
     *
     * @Assert\NotBlank()
     * @ORM\OneToOne(targetEntity="EntryImage", inversedBy="entry", cascade={"persist", "remove"})
     */
    private $image;

    /**
     * @var Location
     *
     * @ORM\ManyToOne(targetEntity="Location", inversedBy="entries", cascade={"persist"})
     * @ORM\JoinColumn(name="location_id", referencedColumnName="id")
     */
    private $location;

    /**
     * @var \DateTime
     *
     * @Assert\Date()
     * @Assert\LessThan(
     *     value="now",
     * )
     * @ORM\Column(name="timestamp", type="datetime", nullable=true)
     */
    private $timestamp;

    /**
     * @var ArrayCollection
     *
     * @Assert\NotBlank()
     * @ORM\ManyToMany(targetEntity="Tag", inversedBy="entries", cascade={"persist"})
     * @ORM\JoinTable(name="tag_to_entry",
     *     joinColumns={@ORM\JoinColumn(name="entry_id", referencedColumnName="id")},
     *     inverseJoinColumns={@ORM\JoinColumn(name="tag_id", referencedColumnName="id")}
     *     )
     */
    private $tags;

    /**
     * @Gedmo\Translatable
     * @Gedmo\Slug(fields={"title"}, updatable=true)
     * @ORM\Column(type="string", unique=true)
     */
    private $slug;

    /**
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
     * Set title.
     *
     * @param string $title
     *
     * @return Entry
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title.
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set description.
     *
     * @param array $description
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
     * @return array
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set author.
     *
     * @param Author $author
     *
     * @return Entry
     */
    public function setAuthor($author)
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
     * @param File $image
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
     * @return File
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * Set location.
     *
     * @param Location $location
     *
     * @return Entry
     */
    public function setLocation($location)
    {
        $this->location = $location;

        return $this;
    }

    /**
     * Get location.
     *
     * @return Location
     */
    public function getLocation()
    {
        return $this->location;
    }

    /**
     * Set timestamp.
     *
     * @param \DateTime $timestamp
     *
     * @return Entry
     */
    public function setTimestamp($timestamp)
    {
        $this->timestamp = $timestamp;

        return $this;
    }

    /**
     * Get timestamp.
     *
     * @return \DateTime
     */
    public function getTimestamp()
    {
        return $this->timestamp;
    }

    /**
     * Get tags.
     *
     * @return ArrayCollection
     */
    public function getTags()
    {
        return $this->tags;
    }

    /**
     * Set tags.
     *
     * @param ArrayCollection $tags
     */
    public function setTags(ArrayCollection $tags)
    {
        $this->tags = $tags;
    }

    /**
     * Set slug.
     *
     * @param string $slug
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
     * @return string
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * Set locale.
     *
     * @param $locale
     */
    public function setTranslatableLocale($locale)
    {
        $this->locale = $locale;
    }
}
