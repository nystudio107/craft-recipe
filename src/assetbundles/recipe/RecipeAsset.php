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

namespace nystudio107\recipe\assetbundles\recipe;

use craft\web\AssetBundle;
use craft\web\assets\cp\CpAsset;

/**
 * @author    nystudio107
 * @package   Recipe
 * @since     1.0.0
 */
class RecipeAsset extends AssetBundle
{
    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public function init(): void
    {
        $this->sourcePath = "@nystudio107/recipe/assetbundles/recipe/dist";

        $this->depends = [
            CpAsset::class,
        ];

        $this->js = [
            'js/Recipe.js',
        ];

        $this->css = [
            'css/Recipe.css',
        ];

        parent::init();
    }
}
