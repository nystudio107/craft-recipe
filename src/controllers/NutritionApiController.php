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
use yii\web\BadRequestHttpException;

/**
 * @author    nystudio107
 * @package   Recipe
 * @since     1.3.0
 */
class NutritionApiController extends Controller
{
    /**
     * Returns nutritional information about a recipe.
     *
     * @throws BadRequestHttpException
     */
    public function actionGetNutritionalInfo()
    {
        $this->requireAcceptsJson();

        $ingredients = Craft::$app->getRequest()->getRequiredParam('ingredients');

        $nutritionalInfo = Recipe::$plugin->nutritionApi->getNutritionalInfo($ingredients);

        return $this->asJson($nutritionalInfo);
    }
}
