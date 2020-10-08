<?php

declare(strict_types=1);

namespace Paysera\Bundle\GedmoTranslatableIntegrationBundle\Service;

use Doctrine\ORM\EntityManagerInterface;
use Gedmo\Translatable\Entity\Repository\TranslationRepository;
use Gedmo\Translatable\TranslatableListener;
use Paysera\Bundle\GedmoTranslatableIntegrationBundle\Entity\TranslatableEntityInterface;
use RuntimeException;

class TranslationProvider
{
    private $translatableListener;
    private $entityManager;

    public function __construct(TranslatableListener $translatableListener, EntityManagerInterface $entityManager)
    {
        $this->translatableListener = $translatableListener;
        $this->entityManager = $entityManager;
    }

    public function getTranslationLocale(): string
    {
        return $this->translatableListener->getListenerLocale();
    }

    public function getTranslationClass(TranslatableEntityInterface $entity): string
    {
        $config = $this->translatableListener->getConfiguration($this->entityManager, get_class($entity));
        if (!isset($config['translationClass'])) {
            throw new RuntimeException('Translatable entity requires translation configuration');
        }
        return $config['translationClass'];
    }

    public function getTranslatableFields(TranslatableEntityInterface $entity): array
    {
        $config = $this->translatableListener->getConfiguration($this->entityManager, get_class($entity));
        if (!isset($config['fields'])) {
            throw new RuntimeException('Translatable entity requires translation configuration');
        }
        return $config['fields'];
    }

    public function getTranslations(TranslatableEntityInterface $entity): array
    {
        $config = $this->translatableListener->getConfiguration($this->entityManager, get_class($entity));
        if (!isset($config['translationClass'])) {
            throw new RuntimeException('Translatable entity requires translation configuration');
        }
        /** @var TranslationRepository $repository */
        $repository = $this->entityManager->getRepository($config['translationClass']);
        return $repository->findTranslations($entity);
    }
}
