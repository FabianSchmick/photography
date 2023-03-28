<?php

namespace App\Tests\Builder;

use App\Entity\Post;
use App\Entity\PostImage;
use App\Entity\File;
use App\Entity\Location;
use App\Entity\Tag;
use App\Entity\Tour;
use App\Entity\User;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class PostBuilder
{
    private array $names = [];

    private array $descriptions = [];

    private ?User $author = null;

    private File $image;

    private ?Location $location = null;

    private ?\DateTimeInterface $timestamp = null;

    /**
     * @var Collection<Tag>
     */
    private Collection $tags;

    private ?Tour $tour = null;

    public function __construct(private readonly ObjectManager $manager)
    {
        $this->tags = new ArrayCollection();

        $this->image = new PostImage();
        $this->image->setFile(
            new UploadedFile(__DIR__.'/../../fixtures/img/example1.jpg', 'example1.jpg', 'image/jpeg', null, true)
        );
    }

    public function create(): Post
    {
        $post = new Post();
        $post->setAuthor($this->author);
        $post->setImage($this->image);
        $post->setLocation($this->location);
        $post->setTimestamp($this->timestamp);
        $post->setTags($this->tags);
        $post->setTour($this->tour);
        $post->setName($this->names[array_key_first($this->names)] ?? 'Post');
        $post->setDescription($this->descriptions[array_key_first($this->descriptions)] ?? null);

        $this->manager->persist($post);
        $this->manager->flush();

        unset($this->names[array_key_first($this->names)]);
        foreach ($this->names as $locale => $name) {
            $post->setName($name);
            $post->setTranslatableLocale($locale);

            $this->manager->persist($post);
            $this->manager->flush();
        }

        unset($this->descriptions[array_key_first($this->descriptions)]);
        foreach ($this->descriptions as $locale => $description) {
            $post->setDescription($description);
            $post->setTranslatableLocale($locale);

            $this->manager->persist($post);
            $this->manager->flush();
        }

        return $post;
    }

    public function setName(string $name, string $locale = 'en'): self
    {
        $this->names[$locale] = $name;

        return $this;
    }

    public function setDescription(?string $description, string $locale = 'en'): self
    {
        $this->descriptions[$locale] = $description;

        return $this;
    }

    public function setAuthor(?User $author): self
    {
        $this->author = $author;

        return $this;
    }

    public function setImage(UploadedFile $image): self
    {
        $this->image = new PostImage();
        $this->image->setFile($image);

        return $this;
    }

    public function setLocation(?Location $location): self
    {
        $this->location = $location;

        return $this;
    }

    public function setTimestamp(\DateTimeInterface $timestamp): self
    {
        $this->timestamp = $timestamp;

        return $this;
    }

    public function addTag(Tag $tag): self
    {
        $this->tags->add($tag);

        return $this;
    }

    public function removeTag(Tag $tag): self
    {
        $this->tags->removeElement($tag);

        return $this;
    }

    public function setTour(?Tour $tour): self
    {
        $this->tour = $tour;

        return $this;
    }
}
