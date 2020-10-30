<?php

declare(strict_types=1);

namespace Paysera\Bundle\GedmoTranslatableIntegrationBundle\Service;

use Doctrine\ORM\EntityManagerInterface;
use Gedmo\Translatable\Entity\Repository\TranslationRepository;
use Paysera\Bundle\GedmoTranslatableIntegrationBundle\Entity\TranslatableEntityInterface;
use Paysera\Bundle\GedmoTranslatableIntegrationBundle\Entity\Translation;

class EntityTranslator
{
    private $translationProvider;
    private $entityManager;
    private $defaultLocale;

    public function __construct(
        TranslationProvider $translationProvider,
        EntityManagerInterface $entityManager,
        string $defaultLocale
    ) {
        $this->translationProvider = $translationProvider;
        $this->entityManager = $entityManager;
        $this->defaultLocale = $defaultLocale;
    }

    public function translate(TranslatableEntityInterface $entity)
    {
        $translationClass = $this->translationProvider->getTranslationClass(get_class($entity));
        /** @var TranslationRepository $repository */
        $repository = $this->entityManager->getRepository($translationClass);
        $translatableFields = $this->translationProvider->getTranslatableFields(get_class($entity));
        foreach ($translatableFields as $field) {
            $translations = $entity->getTranslations($field);
            if (count($translations) === 0) {
                continue;
            }
            $filteredTranslations = $this->removeDefaultTranslation($translations);
            foreach ($filteredTranslations as $translation) {
                $repository->translate(
                    $entity,
                    $field,
                    $translation->getLocale(),
                    $translation->getValue()
                );
            }
        }
    }

    /**
     * @param Translation[] $translations
     * @return Translation[]
     */
    private function removeDefaultTranslation(array $translations): array
    {
        $defaultLocale = $this->defaultLocale;
        return array_filter(
            $translations,
            static function (Translation $translation) use ($defaultLocale) {
                return $translation->getLocale() !== $defaultLocale;
            }
        );
    }
}
