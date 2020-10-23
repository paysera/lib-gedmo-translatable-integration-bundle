<?php

declare(strict_types=1);

namespace Paysera\Bundle\GedmoTranslatableIntegrationBundle\Service;

use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\QueryBuilder;
use Paysera\Bundle\GedmoTranslatableIntegrationBundle\Entity\TranslationSearchConfiguration;

class QueryBuilderTranslationSearchModifier
{
    private $translationProvider;

    public function __construct(TranslationProvider $translationProvider)
    {
        $this->translationProvider = $translationProvider;
    }

    public function modifyQueryBuilder(QueryBuilder $queryBuilder, TranslationSearchConfiguration $configuration)
    {
        $translationClass = $this->translationProvider->getTranslationClass($configuration->getClassName());
        $translationAlias = '_t';
        $queryBuilder->leftJoin(
            $translationClass,
            $translationAlias,
            Join::WITH,
            sprintf('%s.foreignKey = %s.id', $translationAlias, $configuration->getAlias())
        );

        $this->buildTranslationExpression($queryBuilder, $configuration, $translationAlias);
    }

    private function buildTranslationExpression(
        QueryBuilder $queryBuilder,
        TranslationSearchConfiguration $configuration,
        string $translationAlias
    ) {
        $queryBuilder
            ->setParameter('object_class', $configuration->getClassName())
            ->setParameter('field', $configuration->getField())
            ->setParameter('locale', $configuration->getLocale())
            ->setParameter('content', $configuration->getValue())
        ;
        $translationExpression = ($queryBuilder->expr()->andX())
            ->add($translationAlias . '.objectClass = :object_class')
            ->add($translationAlias . '.field = :field')
            ->add($translationAlias . '.locale = :locale')
            ->add($translationAlias . '.content LIKE :content')
        ;

        $queryBuilder->andWhere($queryBuilder->expr()->orX(
            $translationExpression,
            $queryBuilder->expr()
                ->orX()
                ->add(sprintf(
                    '%s.%s LIKE :content',
                    $configuration->getAlias(),
                    $configuration->getField()
                ))
        ));
    }
}
