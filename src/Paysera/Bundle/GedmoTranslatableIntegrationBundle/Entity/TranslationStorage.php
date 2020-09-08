<?php

declare(strict_types=1);

namespace Paysera\Bundle\GedmoTranslatableIntegrationBundle\Entity;

class TranslationStorage
{
    /**
     * @var Translation[]
     */
    private $translations;

    public function __construct()
    {
        $this->translations = [];
    }

    /**
     * @param string $property
     * @return Translation[]
     */
    public function getTranslations(string $property): array
    {
        return array_filter(
            $this->translations,
            static function (Translation $translation) use ($property) {
                return $translation->getProperty() === $property;
            }
        );
    }

    public function addTranslations(string $property, array $translations): self
    {
        foreach ($translations as $locale => $value) {
            $this->addTranslation($property, $locale, $value);
        }
        return $this;
    }

    public function setTranslations(array $translations): self
    {
        $this->translations = [];
        foreach ($translations as $locale => $translation) {
            foreach ($translation as $property => $value) {
                $this->addTranslation($property, $locale, $value);
            }
        }
        return $this;
    }

    private function addTranslation(string $property, string $locale, string $value): self
    {
        $this->translations[] = (new Translation())
            ->setProperty($property)
            ->setLocale($locale)
            ->setValue($value)
        ;
        return $this;
    }
}
