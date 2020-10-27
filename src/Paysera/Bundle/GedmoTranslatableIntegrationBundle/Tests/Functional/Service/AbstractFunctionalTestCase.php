<?php

declare(strict_types=1);

namespace Paysera\Bundle\GedmoTranslatableIntegrationBundle\Tests\Functional\Service;

use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\AnnotationRegistry;
use Doctrine\Common\Cache\ArrayCache;
use Doctrine\ORM\Configuration;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\Driver\AnnotationDriver;
use Doctrine\ORM\Tools\SchemaTool;
use Paysera\Bundle\GedmoTranslatableIntegrationBundle\Tests\Functional\Fixtures\TestEntity;
use Paysera\Bundle\GedmoTranslatableIntegrationBundle\Tests\Functional\Fixtures\TranslationEntity;
use PHPUnit\Framework\TestCase;

abstract class AbstractFunctionalTestCase extends TestCase
{
    protected function createTestEntityManager(): EntityManager
    {
        if (!extension_loaded('pdo_sqlite')) {
            $this->markTestSkipped('Extension pdo_sqlite is required.');
        }

        $entityManager = EntityManager::create(
            [
                'driver' => 'pdo_sqlite',
                'memory' => true,
            ],
            $this->createTestConfiguration()
        );

        $schemaTool = new SchemaTool($entityManager);
        $metadataFactory = $entityManager->getMetadataFactory();
        $metadataFactory->getMetadataFor(TestEntity::class);
        $metadataFactory->getMetadataFor(TranslationEntity::class);
        $metadata = $metadataFactory->getLoadedMetadata();
        $schemaTool->dropSchema($metadata);
        $schemaTool->createSchema($metadata);

        return $entityManager;
    }

    protected function createTestConfiguration(): Configuration
    {
        $config = new Configuration();
        $config->setEntityNamespaces(
            ['TranslationSearchTest' => 'Paysera\Bundle\GedmoTranslatableIntegrationBundle\Tests\Functional\Fixtures']
        );
        $config->setAutoGenerateProxyClasses(true);
        $config->setProxyDir(sys_get_temp_dir());
        $config->setProxyNamespace('Paysera\Bundle\GedmoTranslatableIntegrationBundle\Tests\Functional\Proxy');
        $config->setMetadataDriverImpl(new AnnotationDriver(new AnnotationReader()));
        $config->setQueryCacheImpl(new ArrayCache());
        $config->setMetadataCacheImpl(new ArrayCache());
        AnnotationRegistry::registerLoader('class_exists');

        return $config;
    }
}
