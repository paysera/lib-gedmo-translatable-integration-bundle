# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [3.1.1] - 2020-10-29
### Fixed
- Fixed a bug, where current request locale translations were ignored. In `\Paysera\Bundle\GedmoTranslatableIntegrationBundle\Service\EntityTranslator::translate` instead of getting translatable locale, we inject `default_locale` which is a configurable parameter.

## [3.1.0] - 2020-10-23
### Added
- Added `\Paysera\Bundle\GedmoTranslatableIntegrationBundle\Service\QueryBuilderTranslationSearchModifier` which allows to modify the query builder in order to join entities translation search.

## [3.0.0] - 2020-10-20
### Changed
- `\Paysera\Bundle\GedmoTranslatableIntegrationBundle\Service\TranslationProvider::getTranslationClass`, 
`\Paysera\Bundle\GedmoTranslatableIntegrationBundle\Service\TranslationProvider::getTranslatableFields` takes entity class name 
as a string. Previously parameter was an instance of `TranslatableEntityInterface`, however the instance is not required in the method since only class name is needed.

## [2.0.0] - 2020-10-08
### Added
- Added `\Paysera\Bundle\GedmoTranslatableIntegrationBundle\Entity\TranslatableEntityTrait::getTranslationStorage`.
### Changed
- `\Paysera\Bundle\GedmoTranslatableIntegrationBundle\Service\EntityTranslator::translate` no longer requires `translatableFields`. Fields will be resolved in the method itself.

## [1.0.2] - 2020-09-28
### Fixed
- Fixes `TranslatablePropertyConstraint` validator bug.

## [1.0.1] - 2020-09-24
### Fixed
- Fixed  `\Paysera\Bundle\GedmoTranslatableIntegrationBundle\Validator\Constraint\TranslatablePropertyConstraint::validatedBy`, where it was using a left-over service ID after namespace refactoring.
- Fixed a bug where `\Paysera\Bundle\GedmoTranslatableIntegrationBundle\Validator\TranslatablePropertyConstraintValidator::validate` would early return and not build a violation message. 
- Fixed a bug where `\Paysera\Bundle\GedmoTranslatableIntegrationBundle\Validator\TranslatablePropertyConstraintValidator::validate` was expecting `getTranslations` to return `null`, rather an empty array.
