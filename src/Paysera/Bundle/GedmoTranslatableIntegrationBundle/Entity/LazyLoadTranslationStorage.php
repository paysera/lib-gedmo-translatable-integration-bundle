<?php

declare(strict_types=1);

namespace Paysera\Bundle\GedmoTranslatableIntegrationBundle\Entity;

use Closure;

class LazyLoadTranslationStorage extends TranslationStorage
{
    private $initializer;

    /**
     * @var bool
     */
    private $initialized;

    public function __construct(Closure $initializer)
    {
        parent::__construct();

        $this->initializer = $initializer;
        $this->initialized = false;
    }

    /**
     * @param string $property
     * @return Translation[]
     */
    public function getTranslations(string $property): array
    {
        $this->initialize();
        return parent::getTranslations($property);
    }

    public function addTranslations(string $property, array $translations): parent
    {
        $this->initialize();
        return parent::addTranslations($property, $translations);
    }

    public function setTranslations(array $translations): parent
    {
        $this->initialize();
        return parent::setTranslations($translations);
    }

    private function initialize()
    {
        if (!$this->initialized) {
            $this->initialized = true;
            $initializer = $this->initializer;
            $initializer($this);
        }
    }
}
