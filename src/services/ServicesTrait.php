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

use craft\helpers\ArrayHelper;
use yii\base\InvalidConfigException;

/**
 * @author    nystudio107
 * @package   Recipe
 * @since     1.4.1
 *
 * @property NutritionApi $nutritionApi
 */
trait ServicesTrait
{
    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public function __construct($id, $parent = null, array $config = [])
    {
        // Merge in the passed config, so it our config can be overridden by Plugins::pluginConfigs['recipe']
        // ref: https://github.com/craftcms/cms/issues/1989
        $config = ArrayHelper::merge([
            'components' => [
                'nutritionApi' => NutritionApi::class,]
        ], $config);

        parent::__construct($id, $parent, $config);
    }

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
