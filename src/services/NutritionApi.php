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
use GuzzleHttp\Exception\GuzzleException;
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
     * @param string $name
     * @param int $serves
     * @param array $ingredients
     *
     * @return array
     */
    public function getNutritionalInfo(string $name, int $serves, array $ingredients): array
    {
        if (Recipe::$plugin->settings->hasApiCredentials() === false) {
            return [];
        }

        $url = 'https://api.edamam.com/api/nutrition-details'
            .'?app_id='.Recipe::$plugin->settings->apiApplicationId
            .'&app_key='.Recipe::$plugin->settings->apiApplicationKey;

        $data = [
            'title' => $name,
            'yield' => $serves,
            'ingr' => $ingredients,
        ];

        try {
            $response = Craft::createGuzzleClient()
                ->post($url, ['json' => $data]);

            $result = json_decode($response->getBody());

            $nutritionalInfo = [
                'calories' => $result->totalNutrients->ENERC_KCAL->quantity,
                'carbohydrateContent' => $result->totalNutrients->CHOCDF->quantity,
                'cholesterolContent' => $result->totalNutrients->CHOLE->quantity,
                'fatContent' => $result->totalNutrients->FAT->quantity,
                'fiberContent' => $result->totalNutrients->FIBTG->quantity,
                'saturatedFatContent' => $result->totalNutrients->FASAT->quantity,
                'sodiumContent' => $result->totalNutrients->NA->quantity,
                'sugarContent' => $result->totalNutrients->SUGAR->quantity,
                'transFatContent' => $result->totalNutrients->FATRN->quantity,
                'unsaturatedFatContent' => $result->totalNutrients->FAMS->quantity + $result->totalNutrients->FAPU->quantity,
            ];

            foreach ($nutritionalInfo as $key => $value) {
                $nutritionalInfo[$key] = round($value / $serves);
            }

            $nutritionalInfo['servingSize'] = $result->totalWeight ? $result->totalWeight.' grams' : '';

            return $nutritionalInfo;
        }
        catch (GuzzleException $exception) {
            return [];
        }
    }
}
