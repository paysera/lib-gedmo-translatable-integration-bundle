# lib-gedmo-translatable-integration-bundle

This library provides means to easily integrate [Gedmo Translatable](https://github.com/Atlantic18/DoctrineExtensions/blob/v2.4.x/doc/translatable.md) doctrine extension into your project.

## Why?

Managing entities translations sometimes can become an overhead, that adds unnecessary cognitive-load for developers. This
library helps you by loading all translations and translating the entity automatically, without the necessity to do it manually.

## Installation

Append `new Paysera\Bundle\GedmoTranslatableIntegrationBundle\PayseraGedmoTranslatableIntegrationBundle()` to your project
Symfony kernel bundles.

## Usage

#### Define translatable entities

```php
<?php

namespace App\Entity;

use Paysera\Bundle\GedmoTranslatableIntegrationBundle\Entity\TranslatableEntityInterface;
use Paysera\Bundle\GedmoTranslatableIntegrationBundle\Entity\TranslatableEntityTrait;

class Entity implements TranslatableEntityInterface
{
    use TranslatableEntityTrait;

    // ... properties
    // ... setters/getters
}
```

Complete Doctrine ORM configuration for the translatable properties for 
[Gedmo Translatable](https://github.com/Atlantic18/DoctrineExtensions/blob/v2.4.x/doc/translatable.md) to work following 
the official [documentation](https://github.com/Atlantic18/DoctrineExtensions/blob/v2.4.x/doc/translatable.md).

#### Translating entity or reading translations

With bundle:

```php
<?php

namespace App\Service;

use App\Entity\Post;
use Paysera\Bundle\GedmoTranslatableIntegrationBundle\Entity\Translation;

class PostManager
{
    public function translate(Post $post)
    {
        $post->addTranslations('name', [
            'lt' => 'Translation for LT',
            'en' => 'Translation for EN',
        ]);
        // Done. Bundle will seamlessly make sure, that this entity will be translated using Gedmo extension.
    }
    
    /**
    * @param Post $post
    * @return Translation[]
    */  
    public function getTranslations(Post $post): array
    {
        return $post->getTranslations('name');
        // Done. Bundle makes sure that translations are lazy-loaded, when accessed. This is done without necessity to
        // manually fetch from repositories. 
    }
}
```

Without bundle:

```php
<?php

namespace App\Service;

use App\Entity\Post;

class PostManager
{
    private $translationRepository;

    public function translate(Post $post, array $translations)
    {
        // Your translation structure might be different. Now Post object is coupled with translations and have to be 
        // passed and moved along.
        foreach ($translations as $field => $translation) {
            foreach ($translation as $locale => $value) {
                $this->translationRepository->translate($post, $field, $locale, $value);
            }
        }
    }
    
    public function getTranslations(Post $post)
    {
        // Not lazy-loaded. Now you have to fetch all translations and set them somewhere, but not sure if they will be
        // accessed.
        // Or implement custom repository method by extending the default Gedmo repository.
        $result = [];
        $translations = $this->translationRepository->findTranslations($post);
        foreach ($translations as $locale => $translation) {
            foreach ($translation as $property => $value) {
                if ($property === 'name') {
                    $result[$locale] = $value;
                }
            }
        }   
        return $result;
    }
}
```
