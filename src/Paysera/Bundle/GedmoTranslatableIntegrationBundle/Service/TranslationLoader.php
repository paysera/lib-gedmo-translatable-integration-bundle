<?php

declare(strict_types=1);

namespace Paysera\Bundle\GedmoTranslatableIntegrationBundle\Service;

use Paysera\Bundle\GedmoTranslatableIntegrationBundle\Entity\LazyLoadTranslationStorage;
use Paysera\Bundle\GedmoTranslatableIntegrationBundle\Entity\TranslatableEntityInterface;
use Paysera\Bundle\GedmoTranslatableIntegrationBundle\Entity\TranslationStorage;

class TranslationLoader
{
    private $translationProvider;

    public function __construct(TranslationProvider $translationProvider)
    {
        $this->translationProvider = $translationProvider;
    }

    public function loadTranslations(TranslatableEntityInterface $entity)
    {
        $translationProvider = $this->translationProvider;
        $initializer = static function (TranslationStorage $storage) use ($entity, $translationProvider) {
            $storage->setTranslations($translationProvider->getTranslations($entity));
        };
        $entity->setTranslationStorage(new LazyLoadTranslationStorage($initializer));
    }
}
