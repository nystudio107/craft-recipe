<?php
/**
 * Recipe plugin for Craft CMS 3.x
 *
 * A comprehensive recipe FieldType for Craft CMS that includes metric/imperial conversion, portion calculation,
 * and JSON-LD microdata support
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
     * Encodes the given value into a JSON string.
     * We override this to keep track of the recursion level, and set the default options
     * The method enhances `json_encode()` by supporting JavaScript expressions.
     * In particular, the method will not encode a JavaScript expression that is
     * represented in terms of a [[JsExpression]] object.
     * @param mixed $value the data to be encoded.
     * @param integer $options the encoding options. For more details please refer to
     * <http://www.php.net/manual/en/function.json-encode.php>. Default is
     * `JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE`.
     * @return string the encoding result.
     * @throws InvalidParamException if there is any encoding error.
     */
    public static function encode($value, $options =
        JSON_UNESCAPED_UNICODE |
        JSON_UNESCAPED_SLASHES
        )
    {
        /* -- If `devMode` is enabled, make the JSON-LD human-readable */
        if (Craft::$app->config->get('devMode')) {
            $options |= JSON_PRETTY_PRINT;
        }
        self::$recursionLevel = 0;
        $result = parent::encode($value, $options);
        return $result;
    }

    /**
     * Pre-processes the data before sending it to `json_encode()`.
     * We extend the class to normalize the JSON-LD array before returning it
     * @param mixed $data the data to be processed
     * @param array $expressions collection of JavaScript expressions
     * @param string $expPrefix a prefix internally used to handle JS expressions
     * @return mixed the processed data
     */
    protected static function processData($data, &$expressions, $expPrefix)
    {
        self::$recursionLevel++;
        $result = parent::processData($data, $expressions, $expPrefix);
        self::$recursionLevel--;
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
    protected static function normalizeJsonLdArray(&$array, $depth)
    {
        $array = array_filter($array);
        $array = self::changeKey($array, 'context', '@context');
        $array = self::changeKey($array, 'type', '@type');
        ksort($array);
    }

    /**
     * Replace key values without reordering the array or converting numeric keys to associative keys
     * (which unset() does)
     *
     * @param $array
     * @param $oldKey
     * @param $newKey
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
