<?php

declare(strict_types=1);

namespace Paysera\Bundle\GedmoTranslatableIntegrationBundle\Validator\Constraint;

use Symfony\Component\Validator\Constraint;

class TranslatablePropertyConstraint extends Constraint
{
    /**
     * @var string
     */
    public $translationPath;

    /**
     * @var string
     */
    public $missingDefaultLocaleTranslation;

    public function __construct()
    {
        parent::__construct();

        $this->missingDefaultLocaleTranslation = 'paysera_gedmo_translatable_integration.validator.missing_default_locale_translation';
    }

    public function validatedBy(): string
    {
        return 'paysera_gedmo_translatable_integration.validator.translatable_property';
    }

    public function getTargets(): string
    {
        return Constraint::PROPERTY_CONSTRAINT;
    }
}
