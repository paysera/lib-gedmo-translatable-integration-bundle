<?php

declare(strict_types=1);

namespace Paysera\Bundle\GedmoTranslatableIntegrationBundle\Entity;

trait TranslatableEntityTrait
{
    /**
     * @var TranslationStorage
     */
    private $translationStorage;

    /**
     * @var string|null
     */
    private $locale;

    /**
     * @param string $property
     * @return Translation[]
     */
    public function getTranslations(string $property): array
    {
        $this->initializeStorage();
        return $this->translationStorage->getTranslations($property);
    }

    /**
     * @param string $property
     * @param array $translations
     * @return $this
     */
    public function addTranslations(string $property, array $translations)
    {
        $this->initializeStorage();
        $this->translationStorage->addTranslations($property, $translations);
        return $this;
    }

    /**
     * @param TranslationStorage $translationStorage
     * @return $this
     */
    public function setTranslationStorage(TranslationStorage $translationStorage)
    {
        $this->translationStorage = $translationStorage;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getLocale()
    {
        return $this->locale;
    }

    /**
     * @param string $locale
     * @return $this
     */
    public function setLocale(string $locale)
    {
        $this->locale = $locale;
        return $this;
    }

    private function initializeStorage()
    {
        if ($this->translationStorage === null) {
            $this->translationStorage = new TranslationStorage();
        }
    }
}
