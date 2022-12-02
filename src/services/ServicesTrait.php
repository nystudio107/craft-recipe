<?php
/**
 * Recipe plugin for Craft CMS 3.x
 *
 * A comprehensive recipe FieldType for Craft CMS that includes metric/imperial
 * conversion, portion calculation, and JSON-LD microdata support
 *
 * @link      https://nystudio107.com
 * @copyright Copyright (c) 2022 nystudio107
 */

namespace nystudio107\recipe\services;

use yii\base\InvalidConfigException;

/**
 * @author    nystudio107
 * @package   Recipe
 * @since     4.0.3
 *
 * @property NutritionApi $nutritionApi
 */
trait ServicesTrait
{
    // Public Static Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public static function config(): array
    {
        return [
            'components' => [
                'nutritionApi' => NutritionApi::class,
            ]
        ];
    }

    // Public Methods
    // =========================================================================

    /**
     * Returns the nutritionApi service
     *
     * @return NutritionApi The nutritionApi service
     * @throws InvalidConfigException
     */
    public function getHelper(): NutritionApi
    {
        return $this->get('nutritionApi');
    }
}
