<?php

declare(strict_types=1);

namespace Paysera\Bundle\GedmoTranslatableIntegrationBundle\Tests\Service;

use Doctrine\ORM\EntityManager;
use Gedmo\Translatable\Entity\Repository\TranslationRepository;
use Gedmo\Translatable\TranslatableListener;
use Paysera\Bundle\GedmoTranslatableIntegrationBundle\Entity\TranslatableEntityInterface;
use Paysera\Bundle\GedmoTranslatableIntegrationBundle\Entity\Translation;
use Paysera\Bundle\GedmoTranslatableIntegrationBundle\Service\EntityTranslator;
use PHPUnit\Framework\TestCase;
use PHPUnit_Framework_MockObject_MockObject;

class EntityTranslatorTest extends TestCase
{
    /**
     * @var TranslationRepository|PHPUnit_Framework_MockObject_MockObject
     */
    private $translationRepository;
    /**
     * @var TranslatableListener|PHPUnit_Framework_MockObject_MockObject
     */
    private $translatableListener;
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
        $this->translatableListener = $this->createMock(TranslatableListener::class);
        $this->entityManager = $this->createMock(EntityManager::class);
        $this->entityTranslator = new EntityTranslator(
            $this->translationRepository,
            $this->translatableListener,
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

        $this->translatableListener->expects($this->any())
            ->method('getTranslatableLocale')
            ->willReturn('en')
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

        $this->entityTranslator->translate($entity, ['field']);
    }
}
