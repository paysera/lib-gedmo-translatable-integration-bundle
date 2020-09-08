<?php

declare(strict_types=1);

namespace Paysera\Bundle\GedmoTranslatableIntegrationBundle\Service;

use Doctrine\ORM\EntityManagerInterface;
use Gedmo\Translatable\TranslatableListener;
use Gedmo\Translatable\Entity\Repository\TranslationRepository;
use Paysera\Bundle\GedmoTranslatableIntegrationBundle\Entity\TranslatableEntityInterface;
use Paysera\Bundle\GedmoTranslatableIntegrationBundle\Entity\Translation;

class EntityTranslator
{
    private $translationRepository;
    private $translatableListener;
    private $entityManager;

    public function __construct(
        TranslationRepository $translationRepository,
        TranslatableListener $translatableListener,
        EntityManagerInterface $entityManager
    ) {
        $this->translationRepository = $translationRepository;
        $this->translatableListener = $translatableListener;
        $this->entityManager = $entityManager;
    }

    public function translate(TranslatableEntityInterface $entity, array $translatableFields)
    {
        foreach ($translatableFields as $field) {
            $translations = $entity->getTranslations($field);
            if ($translations === null) {
                continue;
            }
            $meta = $this->entityManager->getClassMetadata(get_class($entity));
            $translatableLocale = $this->translatableListener->getTranslatableLocale($entity, $meta);
            $filteredTranslations = $this->removeTranslation($translations, $translatableLocale);
            foreach ($filteredTranslations as $translation) {
                $this->translationRepository->translate(
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
     * @param string $locale
     * @return Translation[]
     */
    private function removeTranslation(array $translations, string $locale): array
    {
        return array_filter(
            $translations,
            static function (Translation $translation) use ($locale) {
                return $translation->getLocale() !== $locale;
            }
        );
    }
}
