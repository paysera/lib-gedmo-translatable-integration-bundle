<?php

declare(strict_types=1);

namespace Functional\Service;

use Doctrine\ORM\EntityManager;
use Paysera\Bundle\GedmoTranslatableIntegrationBundle\Entity\TranslationSearchConfiguration;
use Paysera\Bundle\GedmoTranslatableIntegrationBundle\Service\QueryBuilderTranslationSearchModifier;
use PHPUnit_Framework_MockObject_MockObject;
use Paysera\Bundle\GedmoTranslatableIntegrationBundle\Service\TranslationProvider;
use Paysera\Bundle\GedmoTranslatableIntegrationBundle\Tests\Functional\Fixtures\TestEntity;
use Paysera\Bundle\GedmoTranslatableIntegrationBundle\Tests\Functional\Fixtures\TranslationEntity;
use Paysera\Bundle\GedmoTranslatableIntegrationBundle\Tests\Functional\Service\AbstractFunctionalTestCase;

class TranslationSearchTest extends AbstractFunctionalTestCase
{
    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @var TranslationProvider|PHPUnit_Framework_MockObject_MockObject
     */
    private $translationProvider;

    /**
     * @var QueryBuilderTranslationSearchModifier
     */
    private $modifier;

    protected function setUp()
    {
        $this->entityManager = $this->createTestEntityManager();

        $this->translationProvider = $this->createMock(TranslationProvider::class);

        $this->modifier = new QueryBuilderTranslationSearchModifier($this->translationProvider);
    }

    public function testTranslationSearchReturnsValidResult()
    {
        $this->persistTestData();

        $alias = 'te';

        $queryBuilder = $this->entityManager->createQueryBuilder();
        $queryBuilder
            ->select($alias)
            ->from('TranslationSearchTest:TestEntity', $alias)
            ->where($alias . '.enabled = :enabled')
            ->setParameter('enabled', true)
        ;

        $configuration = (new TranslationSearchConfiguration())
            ->setClassName(TestEntity::class)
            ->setAlias($alias)
            ->setLocale('lt')
            ->setField('number')
            ->setValue('%vienas%')
        ;

        $this->translationProvider->expects(self::once())
            ->method('getTranslationClass')
            ->with($configuration->getClassName())
            ->willReturn(TranslationEntity::class)
        ;

        $this->modifier->modifyQueryBuilder($queryBuilder, $configuration);

        $result = $queryBuilder
            ->getQuery()
            ->getResult()
        ;
        $this->assertCount(1, $result);
        /** @var TestEntity $testEntity */
        $testEntity = reset($result);
        $this->assertEquals(1, $testEntity->getId());

        $configuration
            ->setLocale('es')
            ->setValue('%uno%')
        ;
    }

    public function testTranslationSearchReturnsEmptyResult()
    {
        $this->persistTestData();

        $alias = 'te';

        $queryBuilder = $this->entityManager->createQueryBuilder();
        $queryBuilder
            ->select($alias)
            ->from('TranslationSearchTest:TestEntity', $alias)
            ->where($alias . '.enabled = :enabled')
            ->setParameter('enabled', true)
        ;

        $configuration = (new TranslationSearchConfiguration())
            ->setClassName(TestEntity::class)
            ->setAlias($alias)
            ->setLocale('lt')
            ->setField('number')
            ->setValue('%uno%')
        ;

        $this->translationProvider->expects(self::once())
            ->method('getTranslationClass')
            ->with($configuration->getClassName())
            ->willReturn(TranslationEntity::class)
        ;

        $this->modifier->modifyQueryBuilder($queryBuilder, $configuration);

        $result = $queryBuilder
            ->getQuery()
            ->getResult()
        ;
        $this->assertCount(0, $result);
    }

    private function persistTestData()
    {
        $testEntity1 = (new TestEntity())->setNumber('one');
        $testEntity2 = (new TestEntity())->setNumber('two');
        $this->entityManager->persist($testEntity1);
        $this->entityManager->persist($testEntity2);
        $this->entityManager->flush();

        $testEntity1LithuanianTranslation = (new TranslationEntity())
            ->setObjectClass(TestEntity::class)
            ->setField('number')
            ->setLocale('lt')
            ->setContent('vienas')
            ->setForeignKey(1)
        ;
        $testEntity1SpanishTranslation = (new TranslationEntity())
            ->setObjectClass(TestEntity::class)
            ->setField('number')
            ->setLocale('es')
            ->setContent('uno')
            ->setForeignKey(1)
        ;
        $this->entityManager->persist($testEntity1LithuanianTranslation);
        $this->entityManager->persist($testEntity1SpanishTranslation);
        $this->entityManager->flush();
    }
}
