<?php

declare(strict_types=1);

namespace Paysera\Bundle\GedmoTranslatableIntegrationBundle\Tests\Unit\Service;

use Doctrine\ORM\EntityManager;
use Gedmo\Translatable\Entity\Repository\TranslationRepository;
use Paysera\Bundle\GedmoTranslatableIntegrationBundle\Entity\TranslatableEntityInterface;
use Paysera\Bundle\GedmoTranslatableIntegrationBundle\Entity\Translation;
use Paysera\Bundle\GedmoTranslatableIntegrationBundle\Service\EntityTranslator;
use Paysera\Bundle\GedmoTranslatableIntegrationBundle\Service\TranslationProvider;
use PHPUnit\Framework\TestCase;
use PHPUnit_Framework_MockObject_MockObject;

class EntityTranslatorTest extends TestCase
{
    /**
     * @var TranslationRepository|PHPUnit_Framework_MockObject_MockObject
     */
    private $translationRepository;
    /**
     * @var TranslationProvider|PHPUnit_Framework_MockObject_MockObject
     */
    private $translationProvider;
    /**
     * @var EntityManager|PHPUnit_Framework_MockObject_MockObject
     */
    private $entityManager;
    /**
     * @var EntityTranslator
     */
    private $entityTranslator;

    public function setUp()
    {
        $this->translationRepository = $this->createMock(TranslationRepository::class);
        $this->translationProvider = $this->createMock(TranslationProvider::class);
        $this->entityManager = $this->createMock(EntityManager::class);
        $this->entityTranslator = new EntityTranslator(
            $this->translationProvider,
            $this->entityManager
        );
    }

    public function testTranslate()
    {
        $translations = [
            (new Translation())
                ->setProperty('field')
                ->setLocale('lt')
                ->setValue('foo')
            ,
            (new Translation())
                ->setProperty('field')
                ->setLocale('en')
                ->setValue('bar')
            ,
        ];

        $this->entityManager->expects($this->once())
            ->method('getRepository')
            ->willReturn($this->translationRepository)
        ;

        $this->translationProvider->expects($this->any())
            ->method('getTranslationLocale')
            ->willReturn('en')
        ;
        $this->translationProvider->expects($this->any())
            ->method('getTranslatableFields')
            ->willReturn(['field'])
        ;

        $entity = $this->createMock(TranslatableEntityInterface::class);
        $entity
            ->expects($this->any())
            ->method('getTranslations')
            ->willReturn($translations)
        ;

        $this->translationRepository->expects($this->once())
            ->method('translate')
            ->with($entity, 'field', 'lt', 'foo')
        ;

        $this->entityTranslator->translate($entity);
    }
}
