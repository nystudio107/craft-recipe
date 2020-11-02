<?php
/**
 * Recipe plugin for Craft CMS 3.x
 *
 * A comprehensive recipe FieldType for Craft CMS that includes metric/imperial
 * conversion, portion calculation, and JSON-LD microdata support
 *
 * @link      https://nystudio107.com
 * @copyright Copyright (c) 2017 nystudio107
 */

namespace nystudio107\recipe\integrations\feedme;

use Cake\Utility\Hash;
use Craft;
use craft\feedme\base\Field;
use craft\feedme\base\FieldInterface;
use craft\feedme\helpers\DataHelper;
use nystudio107\recipe\fields\Recipe as RecipeField;

/**
 * @author    nystudio107
 * @package   Recipe
 * @since     1.2.0
 */
class Recipe extends Field implements FieldInterface
{
    public static $name = 'Recipe';
    public static $class = RecipeField::class;

    public function getMappingTemplate()
    {
        return 'recipe/_integrations/feed-me/recipe';
    }

    public function parseField()
    {
        $preppedData = [];

        $fields = Hash::get($this->fieldInfo, 'fields');

        if (!$fields) {
            return null;
        }

        Craft::dd($fields);
        foreach ($fields as $fieldHandle => $fieldInfo) {
            $preppedData[$fieldHandle] = DataHelper::fetchValue($this->feedData, $fieldInfo);
        }

        // Protect against sending an empty array
        if (!$preppedData) {
            return null;
        }

        return $preppedData;
    }
}
