<?php

declare(strict_types=1);

namespace Paysera\Bundle\GedmoTranslatableIntegrationBundle\Validator;

use Paysera\Bundle\GedmoTranslatableIntegrationBundle\Entity\Translation;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Paysera\Bundle\GedmoTranslatableIntegrationBundle\Entity\TranslatableEntityInterface;
use Paysera\Bundle\GedmoTranslatableIntegrationBundle\Exception\EntityNotTranslatableException;
use Paysera\Bundle\GedmoTranslatableIntegrationBundle\Validator\Constraint\TranslatablePropertyConstraint;

class TranslatablePropertyConstraintValidator extends ConstraintValidator
{
    private $defaultLocale;

    public function __construct(string $defaultLocale)
    {
        $this->defaultLocale = $defaultLocale;
    }

    /**
     * @param mixed $value
     * @param Constraint|TranslatablePropertyConstraint $constraint
     * @throws EntityNotTranslatableException
     */
    public function validate($value, Constraint $constraint)
    {
        $object = $this->context->getObject();
        if (!$object instanceof TranslatableEntityInterface) {
            throw new EntityNotTranslatableException(
                'Unsupported object in validation context, epxected TranslatableEntityInterface'
            );
        }
        $propertyName = $this->context->getPropertyName();
        $translations = $object->getTranslations($propertyName);
        if ($this->getDefaultTranslation($translations) === null) {
            $this->context
                ->buildViolation($constraint->missingDefaultLocaleTranslation)
                ->atPath($this->defaultLocale)
                ->setParameter('%locale%', $this->defaultLocale)
                ->addViolation()
            ;
        }
    }

    /**
     * @param Translation[] $translations
     * @return Translation|null
     */
    private function getDefaultTranslation(array $translations)
    {
        foreach ($translations as $translation) {
            if ($translation->getLocale() === $this->defaultLocale) {
                return $translation;
            }
        }
        return null;
    }
}
