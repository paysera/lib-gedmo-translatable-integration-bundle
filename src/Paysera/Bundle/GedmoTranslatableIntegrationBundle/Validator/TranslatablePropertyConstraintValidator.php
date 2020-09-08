<?php

declare(strict_types=1);

namespace Paysera\Bundle\GedmoTranslatableIntegrationBundle\Validator;

use Paysera\Bundle\GedmoTranslatableIntegrationBundle\Entity\TranslatableEntityInterface;
use Paysera\Bundle\GedmoTranslatableIntegrationBundle\Exception\EntityNotTranslatableException;
use Paysera\Bundle\GedmoTranslatableIntegrationBundle\Validator\Constraint\TranslatablePropertyConstraint;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

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
        if ($value === null) {
            return;
        }
        $object = $this->context->getObject();
        if (!$object instanceof TranslatableEntityInterface) {
            throw new EntityNotTranslatableException(
                'Unsupported object in validation context, epxected TranslatableEntityInterface'
            );
        }
        $propertyPath = $this->context->getPropertyPath();
        if ($object->getTranslations($propertyPath) === null) {
            $this->context
                ->buildViolation($constraint->missingDefaultLocaleTranslation)
                ->atPath($this->defaultLocale)
                ->setParameter('%locale%', $this->defaultLocale)
                ->addViolation()
            ;
        }
    }
}
