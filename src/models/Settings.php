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

namespace nystudio107\recipe\models;

use craft\base\Model;

/**
 * @author    nystudio107
 * @package   Recipe
 * @since     1.3.0
 */
class Settings extends Model
{
    /**
     * An application ID for the Edamam Nutrition Analysis API.
     * https://developer.edamam.com/edamam-nutrition-api
     *
     * @var string|null
     */
    public $apiApplicationId;

    /**
     * An application key for the Edamam Nutrition Analysis API.
     * https://developer.edamam.com/edamam-nutrition-api
     *
     * @var string|null
     */
    public $apiApplicationKey;

    /**
     * Returns whether the settings have API credentials.
     *
     * @return bool
     */
    public function hasApiCredentials(): bool
    {
        return ($this->apiApplicationId && $this->apiApplicationKey);
    }
}
