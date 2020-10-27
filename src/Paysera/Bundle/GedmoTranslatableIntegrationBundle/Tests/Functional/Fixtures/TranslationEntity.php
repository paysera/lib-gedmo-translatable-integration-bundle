<?php

declare(strict_types=1);

namespace Paysera\Bundle\GedmoTranslatableIntegrationBundle\Tests\Functional\Fixtures;

use Gedmo\Translatable\Entity\MappedSuperclass\AbstractTranslation;
use Doctrine\ORM\Mapping\Entity;

/**
 * @Entity
 */
class TranslationEntity extends AbstractTranslation
{

}
