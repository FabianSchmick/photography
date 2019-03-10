<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * Tag.
 *
 * @ORM\Table(name="tag")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\TagRepository")
 * @UniqueEntity("name")
 * @Vich\Uploadable
 */
class Tag
{
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
     * @var string
     *
     * @Assert\NotBlank()
     * @Assert\Length(max=128)
     * @Gedmo\Translatable
     * @ORM\Column(type="string", length=128, unique=true)
     */
    private $name;

    /**
     * @var string
     *
     * @Assert\Length(max=65535)
     * @Gedmo\Translatable
     * @ORM\Column(type="text", length=65535, nullable=true)
     */
    private $description;

    /**
     * @var Entry
     *
     * @ORM\OneToOne(targetEntity="Entry", inversedBy="previewTag")
     * @ORM\JoinColumn(referencedColumnName="id", onDelete="SET NULL")
     */
    private $previewEntry;

    /**
     * @var int
     *
     * @Assert\Type("numeric")
     * @ORM\Column(type="integer", nullable=true)
     */
    private $sort;

    /**
     * @var ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="Entry", mappedBy="tags", cascade={"persist"})
     */
    private $entries;

    /**
     * @Gedmo\Translatable
     * @Gedmo\Slug(fields={"name"}, updatable=true)
     * @ORM\Column(type="string", unique=true)
     */
    private $slug;

    /**
     * @var \DateTime
     *
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(type="datetime")
     */
    private $created;

    /**
     * @var \DateTime
     *
     * @Gedmo\Timestampable(on="update")
     * @ORM\Column(type="datetime")
     */
    private $updated;

    /**
     * @Gedmo\Locale
     * Used locale to override Translation listener`s locale
     */
    private $locale;

    /**
     * Tag constructor.
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
     * @param string $name
     *
     * @return Tag
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set description.
     *
     * @param string $description
     *
     * @return Tag
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description.
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set previewEntry.
     *
     * @param Entry $previewEntry
     *
     * @return Tag
     */
    public function setPreviewEntry(Entry $previewEntry)
    {
        $this->previewEntry = $previewEntry;

        return $this;
    }

    /**
     * Get previewEntry.
     *
     * @return Entry
     */
    public function getPreviewEntry()
    {
        return $this->previewEntry;
    }

    /**
     * Set sort.
     *
     * @param int $sort
     *
     * @return Tag
     */
    public function setSort($sort)
    {
        $this->sort = $sort;

        return $this;
    }

    /**
     * Get sort.
     *
     * @return int
     */
    public function getSort()
    {
        return $this->sort;
    }

    /**
     * Get entries.
     *
     * @return ArrayCollection
     */
    public function getEntries()
    {
        return $this->entries;
    }

    /**
     * Set entries.
     *
     * @param ArrayCollection $entries
     */
    public function setEntries(ArrayCollection $entries)
    {
        $this->entries = $entries;
    }

    /**
     * Set slug.
     *
     * @param string $slug
     *
     * @return Tag
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
     * Set created.
     *
     * @param \DateTime $created
     *
     * @return Tag
     */
    public function setCreated($created)
    {
        $this->created = $created;

        return $this;
    }

    /**
     * Get created.
     *
     * @return \DateTime
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * Set updated.
     *
     * @param \DateTime $updated
     *
     * @return Tag
     */
    public function setUpdated(\DateTime $updated)
    {
        $this->updated = $updated;

        return $this;
    }

    /**
     * Get updated.
     *
     * @return \DateTime
     */
    public function getUpdated()
    {
        return $this->updated;
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
