<?php
/**
 * Recipe plugin for Craft CMS 3.x
 *
 * A comprehensive recipe FieldType for Craft CMS that includes metric/imperial
 * conversion, portion calculation, and JSON-LD microdata support
 *
 * @link      https://nystudio107.com
 * @copyright Copyright (c) 2020 nystudio107
 */

namespace nystudio107\recipe\integrations;

use Cake\Utility\Hash;
use craft\feedme\base\Field;
use craft\feedme\base\FieldInterface;
use craft\feedme\helpers\DataHelper;

/**
 * @author    nystudio107
 * @package   Recipe
 * @since     1.2.0
 */
class RecipeFeedMeField extends Field implements FieldInterface
{
    // Properties
    // =========================================================================

    public static $name = 'Recipe';
    public static $class = 'nystudio107\recipe\fields\Recipe';


    // Templates
    // =========================================================================

    public function getMappingTemplate()
    {
        return 'recipe/_integrations/feed-me';
    }


    // Public Methods
    // =========================================================================

    public function parseField()
    {
        $preppedData = [];

        $fields = Hash::get($this->fieldInfo, 'fields');

        if (!$fields) {
            return null;
        }

        foreach ($fields as $subFieldHandle => $subFieldInfo) {
            // Check for sub-sub fields - bit dirty...
            $subfields = Hash::get($subFieldInfo, 'fields');

            if ($subfields) {
                foreach ($subfields as $subSubFieldHandle => $subSubFieldInfo) {
                    // Handle array data, man I hate Feed Me's data mapping now...
                    $content = DataHelper::fetchArrayValue($this->feedData, $subSubFieldInfo);

                    if (is_array($content)) {
                        foreach ($content as $key => $value) {
                            $preppedData[$subFieldHandle][$key][$subSubFieldHandle] = $value;
                        }
                    } else {
                        $preppedData[$subFieldHandle][$subSubFieldHandle] = $content;
                    }
                }
            } else {
                $preppedData[$subFieldHandle] = DataHelper::fetchValue($this->feedData, $subFieldInfo);
            }
        }

        // Protect against sending an empty array
        if (!$preppedData) {
            return null;
        }

        return $preppedData;
    }

}
