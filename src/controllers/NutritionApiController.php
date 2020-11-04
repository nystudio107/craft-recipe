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

namespace nystudio107\recipe\controllers;

use Craft;
use craft\web\Controller;
use nystudio107\recipe\Recipe;

/**
 * @author    nystudio107
 * @package   Recipe
 * @since     1.3.0
 */
class NutritionApiController extends Controller
{
    /**
     * Returns nutritional information about a recipe.
     */
    public function actionGetNutritionalInfo()
    {
        $this->requireAcceptsJson();

        $request = Craft::$app->getRequest();
        $name = $request->getRequiredParam('name');
        $serves = $request->getRequiredParam('serves');
        $ingredients = $request->getRequiredParam('ingredients');

        $nutritionalInfo = Recipe::$plugin->nutritionApi->getNutritionalInfo($name, $serves, $ingredients);

        return $this->asJson($nutritionalInfo);
    }
}
