<?php

namespace App\Tests\Builder;

use App\Entity\Post;
use App\Entity\Tag;
use Doctrine\Persistence\ObjectManager;

class TagBuilder
{
    private array $names = [];

    private array $descriptions = [];

    private ?Post $previewPost = null;

    private ?int $sort = null;

    public function __construct(private readonly ObjectManager $manager)
    {
    }

    public function create(): Tag
    {
        $tag = new Tag();
        $tag->setPreviewPost($this->previewPost);
        $tag->setSort($this->sort);
        $tag->setName($this->names[array_key_first($this->names)] ?? 'Tag');
        $tag->setDescription($this->descriptions[array_key_first($this->descriptions)] ?? null);

        $this->manager->persist($tag);
        $this->manager->flush();

        unset($this->names[array_key_first($this->names)]);
        foreach ($this->names as $locale => $name) {
            $tag->setName($name);
            $tag->setTranslatableLocale($locale);

            $this->manager->persist($tag);
            $this->manager->flush();
        }

        unset($this->descriptions[array_key_first($this->descriptions)]);
        foreach ($this->descriptions as $locale => $description) {
            $tag->setDescription($description);
            $tag->setTranslatableLocale($locale);

            $this->manager->persist($tag);
            $this->manager->flush();
        }

        return $tag;
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

    public function setPreviewPost(?Post $previewPost): self
    {
        $this->previewPost = $previewPost;

        return $this;
    }

    public function setSort(?int $sort): self
    {
        $this->sort = $sort;

        return $this;
    }
}
