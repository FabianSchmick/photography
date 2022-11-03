<?php

namespace App\Tests\Builder;

use App\Entity\Location;
use Doctrine\Persistence\ObjectManager;

class LocationBuilder
{
    private array $names = [];

    public function __construct(private readonly ObjectManager $manager)
    {
    }

    public function create(): Location
    {
        $location = new Location();
        $location->setName($this->names[array_key_first($this->names)] ?? 'Location');

        $this->manager->persist($location);
        $this->manager->flush();

        unset($this->names[array_key_first($this->names)]);
        foreach ($this->names as $locale => $name) {
            $location->setName($name);
            $location->setTranslatableLocale($locale);

            $this->manager->persist($location);
            $this->manager->flush();
        }

        return $location;
    }

    public function setName(string $name, string $locale = 'en'): self
    {
        $this->names[$locale] = $name;

        return $this;
    }
}
