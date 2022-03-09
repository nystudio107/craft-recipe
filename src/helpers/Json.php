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

namespace nystudio107\recipe\helpers;

use Craft;

/**
 * @author    nystudio107
 * @package   Recipe
 * @since     1.0.0
 */
class Json extends \craft\helpers\Json
{
    // Static Properties
    // =========================================================================

    protected static $recursionLevel;

    // Static Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public static function encode(
        $value,
        $options =
        JSON_UNESCAPED_UNICODE
        | JSON_UNESCAPED_SLASHES
    ): string {
        // If `devMode` is enabled, make the JSON-LD human-readable
        if (Craft::$app->getConfig()->getGeneral()->devMode) {
            $options |= JSON_PRETTY_PRINT;
        }

        self::$recursionLevel = 0;

        return parent::encode($value, $options);
    }

    /**
     * @inheritdoc
     */
    protected static function processData($data, &$expressions, $expPrefix)
    {
        ++self::$recursionLevel;
        $result = parent::processData($data, $expressions, $expPrefix);
        --self::$recursionLevel;
        static::normalizeJsonLdArray($result, self::$recursionLevel);

        return $result;
    }


    // Private Methods
    // =========================================================================

    /**
     * Normalize the JSON-LD array recursively to remove empty values, change
     * 'type' to '@type' and have it be the first item in the array
     *
     * @param $array
     * @param $depth
     */
    protected static function normalizeJsonLdArray(&$array, $depth): void
    {
        $array = array_filter($array);
        $array = self::changeKey($array, 'context', '@context');
        $array = self::changeKey($array, 'type', '@type');
        ksort($array);
    }

    /**
     * Replace key values without reordering the array or converting numeric
     * keys to associative keys (which unset() does)
     *
     * @param $array
     * @param $oldKey
     * @param $newKey
     *
     * @return array
     */
    protected static function changeKey($array, $oldKey, $newKey)
    {
        if (!array_key_exists($oldKey, $array)) {
            return $array;
        }

        $keys = array_keys($array);
        $keys[array_search($oldKey, $keys)] = $newKey;

        return array_combine($keys, $array);
    }
}
