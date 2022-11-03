<?php

namespace App\Tests\Builder;

use App\Entity\TourCategory;
use Doctrine\Persistence\ObjectManager;

class TourCategoryBuilder
{
    private array $names = [];

    private ?int $sort = null;

    /**
     * TODO: Add enums.
     */
    private ?string $formulaType = null;

    private bool $hasLevelOfDifficulty = false;

    public function __construct(private readonly ObjectManager $manager)
    {
    }

    public function create(): TourCategory
    {
        $tourCategory = new TourCategory();
        $tourCategory->setSort($this->sort);
        $tourCategory->setFormulaType($this->formulaType);
        $tourCategory->setHasLevelOfDifficulty($this->hasLevelOfDifficulty);
        $tourCategory->setName($this->names[array_key_first($this->names)] ?? 'Tour-Category');

        $this->manager->persist($tourCategory);
        $this->manager->flush();

        unset($this->names[array_key_first($this->names)]);
        foreach ($this->names as $locale => $name) {
            $tourCategory->setName($name);
            $tourCategory->setTranslatableLocale($locale);

            $this->manager->persist($tourCategory);
            $this->manager->flush();
        }

        return $tourCategory;
    }

    public function setName(string $name, string $locale = 'en'): self
    {
        $this->names[$locale] = $name;

        return $this;
    }

    public function setSort(?int $sort): self
    {
        $this->sort = $sort;

        return $this;
    }

    public function setFormulaType(?string $formulaType): self
    {
        $this->formulaType = $formulaType;

        return $this;
    }

    public function setHasLevelOfDifficulty(bool $hasLevelOfDifficulty): self
    {
        $this->hasLevelOfDifficulty = $hasLevelOfDifficulty;

        return $this;
    }
}
