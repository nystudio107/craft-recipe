# Recipe Changelog

## 4.0.5 - UNRELEASED
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

## 1.4.0 - 2021.10.05
### Added
* Added `.createRecipeMetaJsonLd()` to the Recipes field, to allow the creation of SEOmatic MetaJsonLd objects that can be manipulated and rendered on the page by SEOmatic
* Added **Recipe Equipment** ([#32](https://github.com/nystudio107/craft-recipe/issues/32))
* Added `recipeCategory` and `recipeCuisine` fields ([#50](https://github.com/nystudio107/craft-recipe/issues/50))
* Added the field `servesUnit` and a method called `getServes()` which is just concatenating the serves with its unit when it's not empty ([#37](https://github.com/nystudio107/craft-recipe/pull/37/))
* Added a Recipe Video field that can be accessed via `.getVideoUrl()` ([#50](https://github.com/nystudio107/craft-recipe/issues/50))
* Added `author` and `keywords` fields ([#50](https://github.com/nystudio107/craft-recipe/issues/50))

### Changed
* Switched over to using VitePress for the documentation

## 1.3.0 - 2021.03.05
### Added
* Added the ability to fetch nutritional information from ingredients using the [Edamam Nutrition Analysis API](https://developer.edamam.com/edamam-nutrition-api).
* Added plugin settings for configuring API credentials.
* Added a console controller action that generates nutritional information for all entries in a section using the API.
* Added docs buildchain

## 1.2.1 - 2021.01.28
### Added
* Added sodium content to nutrition facts template.
* Implemented `useFieldset` for Craft 3.6

### Changed
* Non-imperial units are converted to nice fractions too
* Nice fraction now has a space before it
* Changed fraction precision to `1`

## 1.2.0 - 2020.11.04
### Added
* Added support for importing recipes (including ingredients, directions and ratings) using Feed Me.

## 1.1.3 - 2020.05.25
### Fixed
* Add plural for grams and remove cups abbreviation
* Correct prefix of 0 for measurements and use 'cups' instead of c

## 1.1.2 - 2020.04.16
### Fixed
* Fixed Asset Bundle namespace case

## 1.1.1 - 2020.04.06
### Changed
* An error is no longer logged if there isn't a frontend template for the Nutrition Facts
* Updated missing translations

## 1.1.0 - 2020.04.03
### Added
* Added support for imperial pounds and metric kilograms
* Added support for passing in an image transform to `.getImageUrl()`

### Fixed
* Fixed errant display of the 1.66 quantity by rounding the mantissa so we can do a floating point comparison without weirdness, per: https://www.php.net/manual/en/language.types.float.php#113703
* Fix the abbreviations to be be the same whether singular or plural as per [Measurement Abbreviations](https://abbreviations.yourdictionary.com/articles/measurement-abbreviations.html)

## 1.0.11 - 2019.07.10
### Changed
* Add support for 2/3 fraction
* Fixed an issue where controls to add ratings didn't work when you switched tabs until you resized the window

## 1.0.10 - 2019.03.08
### Changed
* Fixed the Asset Sources settings to work with Craft 3.1

## 1.0.9 - 2019.03.08
### Changed
* Add 1/3 and 1/6 fractions
* Prevent error on no directions or ingredients

## 1.0.8 - 2019.03.08
### Changed
* Fixed an issue where tabs didn't work properly in the Field

## 1.0.7 - 2018.03.02
### Changed
* Fixed deprecation errors from Craft CMS 3 RC13

## 1.0.6 - 2018.02.01
### Added
* Renamed the composer package name to `craft-recipe`

## 1.0.5 - 2017.12.06
### Changed
* Updated to require craftcms/cms `^3.0.0-RC1`

## 1.0.4 - 2017.03.24
### Changed
* `hasSettings` -> `hasCpSettings` for Craft 3 beta 8 compatibility
* Modified config service calls for Craft 3 beta 8

## 1.0.3 - 2017.03.12
### Added
* Added `craft/cms` as a composer dependency
* Added code inspection typehinting for the plugin

## 1.0.2 - 2017.03.03
### Fixed
- Redirect to the Welcome message only when the `Recipe` plugin is installed

## 1.0.1 - 2017.02.15
### Fixed
- Fixed an issue that would cause you to be unable to choose an asset
- Merged the Field rules with parent

## 1.0.0 - 2017.02.10
### Added
- Initial release
