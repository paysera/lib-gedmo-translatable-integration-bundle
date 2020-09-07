<?php

declare(strict_types=1);

namespace Paysera\Bundle\GedmoTranslatableIntegrationBundle\Listener;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Paysera\Bundle\GedmoTranslatableIntegrationBundle\Entity\TranslatableEntityInterface;
use Paysera\Bundle\GedmoTranslatableIntegrationBundle\Service\EntityTranslator;
use Paysera\Bundle\GedmoTranslatableIntegrationBundle\Service\TranslationLoader;
use Paysera\Bundle\GedmoTranslatableIntegrationBundle\Service\TranslationProvider;

class TranslatableEntityListener
{
    private $translationLoader;
    private $translationProvider;
    private $entityTranslator;

    public function __construct(
        TranslationLoader $translationLoader,
        TranslationProvider $translationProvider,
        EntityTranslator $entityTranslator
    ) {
        $this->translationLoader = $translationLoader;
        $this->translationProvider = $translationProvider;
        $this->entityTranslator = $entityTranslator;
    }

    public function postLoad(LifecycleEventArgs $args)
    {
        $entity = $args->getObject();
        if (!$entity instanceof TranslatableEntityInterface) {
            return;
        }
        $entity->setLocale($this->translationProvider->getTranslationLocale());
        $this->translationLoader->loadTranslations($entity);
    }

    public function prePersist(LifecycleEventArgs $args)
    {
        $entity = $args->getObject();
        if (!$entity instanceof TranslatableEntityInterface) {
            return;
        }
        $this->entityTranslator->translate($entity, $this->translationProvider->getTranslatableFields($entity));
    }
}
