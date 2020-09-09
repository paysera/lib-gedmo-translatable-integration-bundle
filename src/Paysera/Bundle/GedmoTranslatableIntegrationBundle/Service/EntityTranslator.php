<?php

declare(strict_types=1);

namespace Paysera\Bundle\GedmoTranslatableIntegrationBundle\Service;

use RuntimeException;
use Doctrine\ORM\EntityManagerInterface;
use Gedmo\Translatable\TranslatableListener;
use Gedmo\Translatable\Entity\Repository\TranslationRepository;
use Paysera\Bundle\GedmoTranslatableIntegrationBundle\Entity\TranslatableEntityInterface;
use Paysera\Bundle\GedmoTranslatableIntegrationBundle\Entity\Translation;

class EntityTranslator
{
    private $translatableListener;
    private $entityManager;

    public function __construct(
        TranslatableListener $translatableListener,
        EntityManagerInterface $entityManager
    ) {
        $this->translatableListener = $translatableListener;
        $this->entityManager = $entityManager;
    }

    public function translate(TranslatableEntityInterface $entity, array $translatableFields)
    {
        $config = $this->translatableListener->getConfiguration($this->entityManager, get_class($entity));
        if (!isset($config['translationClass'])) {
            throw new RuntimeException('Translatable entity requires translation configuration');
        }
        /** @var TranslationRepository $repository */
        $repository = $this->entityManager->getRepository($config['translationClass']);
        foreach ($translatableFields as $field) {
            $translations = $entity->getTranslations($field);
            if (count($translations) === 0) {
                continue;
            }
            $meta = $this->entityManager->getClassMetadata(get_class($entity));
            $translatableLocale = $this->translatableListener->getTranslatableLocale($entity, $meta);
            $filteredTranslations = $this->removeDefaultTranslation($translations, $translatableLocale);
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
     * @param string $locale
     * @return Translation[]
     */
    private function removeDefaultTranslation(array $translations, string $locale): array
    {
        return array_filter(
            $translations,
            static function (Translation $translation) use ($locale) {
                return $translation->getLocale() !== $locale;
            }
        );
    }
}
