<?php

declare(strict_types=1);

namespace Paysera\Bundle\GedmoTranslatableIntegrationBundle\Tests\Unit\Service;

use Paysera\Bundle\GedmoTranslatableIntegrationBundle\Entity\TranslatableEntityInterface;
use Paysera\Bundle\GedmoTranslatableIntegrationBundle\Entity\TranslationStorage;
use Paysera\Bundle\GedmoTranslatableIntegrationBundle\Service\TranslationLoader;
use Paysera\Bundle\GedmoTranslatableIntegrationBundle\Service\TranslationProvider;
use PHPUnit\Framework\TestCase;
use PHPUnit_Framework_MockObject_MockObject;

class TranslationLoaderTest extends TestCase
{
    /**
     * @var TranslationProvider|PHPUnit_Framework_MockObject_MockObject
     */
    private $translationProvider;

    /**
     * @var TranslationLoader
     */
    private $translationLoader;

    public function setUp()
    {
        $this->translationProvider = $this->createMock(TranslationProvider::class);
        $this->translationLoader = new TranslationLoader($this->translationProvider);
    }

    public function testLoadTranslations()
    {
        $entity = $this->createMock(TranslatableEntityInterface::class);

        $this->translationProvider->expects($this->once())
            ->method('getTranslations')
            ->with($entity)
            ->willReturn([
                'field' => [
                    'en' => 'foo',
                    'lt' => 'bar',
                    'ru' => 'baz',
                ],
            ])
        ;

        $entity->expects($this->once())
            ->method('setTranslationStorage')
            ->with($this->callback(static function (TranslationStorage $storage) {
                $storage->getTranslations('field');
                return true;
            }))
            ->willReturnSelf()
        ;

        $this->translationLoader->loadTranslations($entity);

    }
}
