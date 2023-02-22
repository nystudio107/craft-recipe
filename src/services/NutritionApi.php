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
     * @param int|null $serves
     */
    public function getNutritionalInfo(array $ingredients, int $serves = null): array
    {
        if (!Recipe::$plugin->getSettings()->hasApiCredentials()) {
            return [];
        }

        $url = 'https://api.edamam.com/api/nutrition-details'
            . '?app_id=' . Craft::parseEnv(Recipe::$plugin->getSettings()->apiApplicationId)
            . '&app_key=' . Craft::parseEnv(Recipe::$plugin->getSettings()->apiApplicationKey);

        $data = [
            'ingr' => $ingredients,
        ];

        if ($serves) {
            $data['yield'] = $serves;
        }

        try {
            $response = Craft::createGuzzleClient()->post($url, ['json' => $data]);

            $result = json_decode($response->getBody(), null, 512, JSON_THROW_ON_ERROR);

            $yield = $result->yield ?: 1;

            return [
                'servingSize' => round($result->totalWeight ?? 0 / $yield, 0) . ' grams',
                'calories' => round($result->totalNutrients->ENERC_KCAL->quantity ?? 0 / $yield, 0),
                'carbohydrateContent' => round($result->totalNutrients->CHOCDF->quantity ?? 0 / $yield, 1),
                'cholesterolContent' => round($result->totalNutrients->CHOLE->quantity ?? 0 / $yield, 1),
                'fatContent' => round($result->totalNutrients->FAT->quantity ?? 0 / $yield, 1),
                'fiberContent' => round($result->totalNutrients->FIBTG->quantity ?? 0 / $yield, 1),
                'proteinContent' => round($result->totalNutrients->PROCNT->quantity ?? 0 / $yield, 1),
                'saturatedFatContent' => round($result->totalNutrients->FASAT->quantity ?? 0 / $yield, 1),
                'sodiumContent' => round($result->totalNutrients->NA->quantity ?? 0 / $yield, 1),
                'sugarContent' => round($result->totalNutrients->SUGAR->quantity ?? 0 / $yield, 1),
                'transFatContent' => round($result->totalNutrients->FATRN->quantity ?? 0 / $yield, 1),
                'unsaturatedFatContent' => round(($result->totalNutrients->FAMS->quantity ?? 0 + $result->totalNutrients->FAPU->quantity ?? 0) / $yield, 1),
            ];
        } catch (Exception $exception) {
            $message = 'Error fetching nutritional information from API. ';

            if ($exception->getCode() == 401) {
                $message .= 'Please verify your API credentials.';
            } elseif ($exception->getCode() == 555) {
                $message .= 'One or more ingredients could not be recognized.';
            }

            Craft::error($message . $exception->getMessage(), __METHOD__);

            return ['error' => $message];
        }
    }
}
