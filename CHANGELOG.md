# Recipe Changelog

## 4.0.9 - UNRELEASED
### Added
* Add `phpstan` and `ecs` code linting
* Add `code-analysis.yaml` GitHub action

## 4.0.8 - 2024.01.14
### Added
* Automate release generation via GitHub action

### Changed
* Updated docs to use node 20 & a new sitemap plugin

### Fixed
* Fixes compatibility with FeedMe's base FieldInterface ([#69](https://github.com/nystudio107/craft-recipe/pull/69))

## 4.0.7 - 2023.02.23
### Fixed
* Fixed an issue where the Nutritional Information display didn't take into account the number of servings ([#66](https://github.com/nystudio107/craft-recipe/issues/66))

## 4.0.6 - 2023.02.22
### Fixed
* Fix API Requests ([#65](https://github.com/nystudio107/craft-recipe/pull/65))

## 4.0.5 - 2023.02.21
### Changed
* Refactored the docs buildchain to use a dynamic docker container setup
* Updated the docs to include instructions on how to do Multi-Component recipes

### Fixed
* Allow Recipe fields to work properly if they are embedded in a Matrix block ([#63](https://github.com/nystudio107/craft-recipe/issues/63))

## 4.0.4 - 2022.12.04
### Changed
* Fix the display of the Recipe icon logo on the Welcome screen

## 4.0.3 - 2022.12.01
### Changed
* Move to using `ServicesTrait` and add getter methods for services
* Switch to VitePress `^1.0.0-alpha.29` for the documentation
* Add `allow-plugins` to the `composer.json` to enable CI to work
* Wrap the fields in `<fieldset>` tags for semantic HTML & a11y
* Remove the odd Craft `modifiedAttributes` styling when a field value is changed ([#12403](https://github.com/craftcms/cms/issues/12403))

## 4.0.2 - 2022.05.10
### Fixed
* Fix array access on new type Int ([#59](https://github.com/nystudio107/craft-recipe/pull/59))

## 4.0.1 - 2022.05.09
### Fixed
* Fixed an issue where model properties were not converted from an array to an `int` before saving ((#57)[https://github.com/nystudio107/craft-recipe/issues/57])
* Fixed an issue with assets not showing up by removing `'enabledForSite': null,` from the `criteria`

## 4.0.0 - 2022.05.09
### Added
* Initial Craft CMS 4 release

### Fixed
* Fixed an issue where certain model properties were typed as `?array` when they should have been typed as `?int`, causing a type error to be thrown ((#57)[https://github.com/nystudio107/craft-recipe/issues/57])

## 4.0.0-beta.3 - 2022.04.13

### Fixed
* Fixed an issue where an exception would be thrown if the FeedMe beta plugin was installed ([#56](https://github.com/nystudio107/craft-recipe/issues/56))

## 4.0.0-beta.2 - 2022.03.14

### Fixed
* Fix issues with editable table fields, due to the change in default values for Craft 4

## 4.0.0-beta.1 - 2022.03.09

### Added

* Initial Craft CMS 4 compatibility
