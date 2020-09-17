<?php

declare(strict_types=1);

namespace Paysera\Bundle\GedmoTranslatableIntegrationBundle\Entity;

interface TranslatableEntityInterface
{
    public function getTranslations(string $property): array;

    /**
     * @param string $property
     * @param array $translations
     * @return TranslationStorage|null
     */
    public function addTranslations(string $property, array $translations);

    /**
     * @param TranslationStorage $translationStorage
     * @return $this
     */
    public function setTranslationStorage(TranslationStorage $translationStorage);

    /**
     * @param string $locale
     * @return $this
     */
    public function setLocale(string $locale);

    /**
     * @return string|null
     */
    public function getLocale();
}
