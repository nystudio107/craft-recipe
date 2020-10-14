<?php
namespace nystudio107\recipe\integrations;

use Cake\Utility\Hash;
use craft\feedme\base\Field;
use craft\feedme\base\FieldInterface;
use craft\feedme\helpers\DataHelper;

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
                    $content = DataHelper::fetchValue($this->feedData, $subSubFieldInfo);

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