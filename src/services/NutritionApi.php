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

namespace nystudio107\recipe\services;

use Craft;
use craft\base\Component;
use Exception;
use nystudio107\recipe\Recipe;

/**
 * @author    nystudio107
 * @package   Recipe
 * @since     1.3.0
 */
class NutritionApi extends Component
{
    /**
     * Returns nutritional information about a recipe.
     *
     * @param array $ingredients
     *
     * @return array
     */
    public function getNutritionalInfo(array $ingredients): array
    {
        if (Recipe::$plugin->settings->hasApiCredentials() === false) {
            return [];
        }

        $url = 'https://api.edamam.com/api/nutrition-details'
            .'?app_id='.Craft::parseEnv(Recipe::$plugin->settings->apiApplicationId)
            .'&app_key='.Craft::parseEnv(Recipe::$plugin->settings->apiApplicationKey);

        $data = [
            'ingr' => $ingredients,
        ];

        try {
            $response = Craft::createGuzzleClient()
                ->post($url, ['json' => $data]);

            $result = json_decode($response->getBody());

            return [
                'servingSize' => round($result->totalWeight, 0).' grams',
                'calories' => round($result->totalNutrients->ENERC_KCAL->quantity, 0),
                'carbohydrateContent' => round($result->totalNutrients->CHOCDF->quantity, 1),
                'cholesterolContent' => round($result->totalNutrients->CHOLE->quantity, 1),
                'fatContent' => round($result->totalNutrients->FAT->quantity, 1),
                'fiberContent' => round($result->totalNutrients->FIBTG->quantity, 1),
                'proteinContent' => round($result->totalNutrients->PROCNT->quantity, 1),
                'saturatedFatContent' => round($result->totalNutrients->FASAT->quantity, 1),
                'sodiumContent' => round($result->totalNutrients->NA->quantity, 1),
                'sugarContent' => round($result->totalNutrients->SUGAR->quantity, 1),
                'transFatContent' => round($result->totalNutrients->FATRN->quantity, 1),
                'unsaturatedFatContent' => round($result->totalNutrients->FAMS->quantity + $result->totalNutrients->FAPU->quantity, 1),
            ];
        }
        catch (Exception $exception) {
            $message = 'Error fetching nutritional information from API. ';

            switch ($exception->getCode()) {
                case 401:
                    $message .= 'Please verify your API credentials.';
                    break;
                case 555:
                    $message .= 'One or more ingredients could not be recognized.';
                    break;
            }

            Craft::error($message.$exception->getMessage(), __METHOD__);

            return ['error' => $message];
        }
    }
}
