# Recipe Changelog

## 1.3.0 - Unreleased
### Added
* Added the ability to fetch nutritional information from ingredients using the [Edamam Nutrition Analysis API](https://developer.edamam.com/edamam-nutrition-api).
* Added plugin settings for configuring API credentials.
* Added a console controller action that generates nutritional information for all entries in a section using the API.

## 1.2.0 - 2020.11.04
### Added
* Added support for importing recipes (including ingredients, directions and ratings) using Feed Me.

## 1.1.3 - 2020.05.25
### Fixed
* Add plural for grams and remove cups abreviation
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
