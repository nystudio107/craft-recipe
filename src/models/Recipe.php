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

use nystudio107\recipe\helpers\Json;
use nystudio107\recipe\helpers\PluginTemplate;

use Craft;
use craft\base\Model;
use craft\helpers\Template;

use Twig\Markup;

/**
 * @author    nystudio107
 * @package   Recipe
 * @since     1.0.0
 */
class Recipe extends Model
{
    // Constants
    // =========================================================================

    const US_RDA = [
        'calories' => 2000,
        'carbohydrateContent' => 275,
        'cholesterolContent' => 300,
        'fatContent' => 78,
        'fiberContent' => 28,
        'proteinContent' => 50,
        'saturatedFatContent' => 20,
        'sodiumContent' => 2300,
        'sugarContent' => 50,
    ];

    // Mapping to convert any of the incorrect plural values
    const NORMALIZE_PLURALS = [
        'tsps' => 'tsp',
        'tbsps' => 'tbsp',
        'flozs' => 'floz',
        'cups' => 'cups',
        'ozs' => 'oz',
        'lbs' => 'lb',
        'mls' => 'ml',
        'ls' => 'l',
        'mgs' => 'mg',
        'gs' => 'g',
        'kg' => 'kg',
    ];

    // Public Properties
    // =========================================================================

    /**
     * @var string
     */
    public $name;

    /**
     * @var string
     */
    public $description;

    /**
     * @var string
     */
    public $skill = 'intermediate';

    /**
     * @var int
     */
    public $serves = 1;

    /**
     * @var string
     */
    public $servesUnit = '';

    /**
     * @var array
     */
    public $ingredients = [];

    /**
     * @var array
     */
    public $directions = [];

    /**
     * @var int
     */
    public $imageId = 0;

    /**
     * @var int
     */
    public $prepTime;

    /**
     * @var int
     */
    public $cookTime;

    /**
     * @var int
     */
    public $totalTime;

    /**
     * @var array
     */
    public $ratings = [];

    /**
     * @var string
     */
    public $servingSize;

    /**
     * @var int
     */
    public $calories;

    /**
     * @var int
     */
    public $carbohydrateContent;

    /**
     * @var int
     */
    public $cholesterolContent;

    /**
     * @var int
     */
    public $fatContent;

    /**
     * @var int
     */
    public $fiberContent;

    /**
     * @var int
     */
    public $proteinContent;

    /**
     * @var int
     */
    public $saturatedFatContent;

    /**
     * @var int
     */
    public $sodiumContent;

    /**
     * @var int
     */
    public $sugarContent;

    /**
     * @var int
     */
    public $transFatContent;

    /**
     * @var int
     */
    public $unsaturatedFatContent;

    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        // Fix any of the incorrect plural values
        if (!empty($this->ingredients)) {
            foreach ($this->ingredients as &$row) {
                if (!empty($row['units']) && !empty(self::NORMALIZE_PLURALS[$row['units']])) {
                    $row['units'] = self::NORMALIZE_PLURALS[$row['units']];
                }
            }
            unset($row);
        }
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['name', 'string'],
            ['name', 'default', 'value' => ''],
            ['description', 'string'],
            ['skill', 'string'],
            ['serves', 'integer'],
            ['imageId', 'integer'],
            ['prepTime', 'integer'],
            ['cookTime', 'integer'],
            ['totalTime', 'integer'],
            ['servingSize', 'string'],
            ['calories', 'integer'],
            ['carbohydrateContent', 'integer'],
            ['cholesterolContent', 'integer'],
            ['fatContent', 'integer'],
            ['fiberContent', 'integer'],
            ['proteinContent', 'integer'],
            ['saturatedFatContent', 'integer'],
            ['sodiumContent', 'integer'],
            ['sugarContent', 'integer'],
            ['transFatContent', 'integer'],
            ['unsaturatedFatContent', 'integer'],
        ];
    }

    /**
     * Render the JSON-LD Structured Data for this recipe
     *
     * @param bool $raw
     *
     * @return string|\Twig_Markup
     */
    public function renderRecipeJSONLD($raw = true)
    {
        $recipeJSONLD = [
            'context' => 'http://schema.org',
            'type' => 'Recipe',
            'name' => $this->name,
            'image' => $this->getImageUrl(),
            'description' => $this->description,
            'recipeYield' => $this->getServes(),
            'recipeIngredient' => $this->getIngredients('imperial', 0, false),
            'recipeInstructions' => $this->getDirections(false),
        ];
        $recipeJSONLD = array_filter($recipeJSONLD);

        $nutrition = [
            'type' => 'NutritionInformation',
            'servingSize' => $this->servingSize,
            'calories' => $this->calories,
            'carbohydrateContent' => $this->carbohydrateContent,
            'cholesterolContent' => $this->cholesterolContent,
            'fatContent' => $this->fatContent,
            'fiberContent' => $this->fiberContent,
            'proteinContent' => $this->proteinContent,
            'saturatedFatContent' => $this->saturatedFatContent,
            'sodiumContent' => $this->sodiumContent,
            'sugarContent' => $this->sugarContent,
            'transFatContent' => $this->transFatContent,
            'unsaturatedFatContent' => $this->unsaturatedFatContent,
        ];
        $nutrition = array_filter($nutrition);
        $recipeJSONLD['nutrition'] = $nutrition;
        if (count($recipeJSONLD['nutrition']) === 1) {
            unset($recipeJSONLD['nutrition']);
        }
        $aggregateRating = $this->getAggregateRating();
        if ($aggregateRating) {
            $aggregateRatings = [
                'type' => 'AggregateRating',
                'ratingCount' => $this->getRatingsCount(),
                'bestRating' => '5',
                'worstRating' => '1',
                'ratingValue' => $aggregateRating,
            ];
            $aggregateRatings = array_filter($aggregateRatings);
            $recipeJSONLD['aggregateRating'] = $aggregateRatings;

            $reviews = [];
            foreach ($this->ratings as $rating) {
                $review = [
                    'type' => 'Review',
                    'author' => $rating['author'],
                    'name' => $this->name . ' ' . Craft::t('recipe', 'Review'),
                    'description' => $rating['review'],
                    'reviewRating' => [
                        'type' => 'Rating',
                        'bestRating' => '5',
                        'worstRating' => '1',
                        'ratingValue' => $rating['rating'],
                    ],
                ];
                $reviews[] = $review;
            }
            $reviews = array_filter($reviews);
            $recipeJSONLD['review'] = $reviews;
        }

        if ($this->prepTime) {
            $recipeJSONLD['prepTime'] = 'PT' . $this->prepTime . 'M';
        }
        if ($this->cookTime) {
            $recipeJSONLD['cookTime'] = 'PT' . $this->cookTime . 'M';
        }
        if ($this->totalTime) {
            $recipeJSONLD['totalTime'] = 'PT' . $this->totalTime . 'M';
        }

        return $this->renderJsonLd($recipeJSONLD, $raw);
    }

    /**
     * Get the URL to the recipe's image
     *
     * @param null $transform
     *
     * @return null|string
     */
    public function getImageUrl($transform = null)
    {
        $result = '';
        if (isset($this->imageId) && $this->imageId) {
            $image = Craft::$app->getAssets()->getAssetById($this->imageId[0]);
            if ($image) {
                $result = $image->getUrl($transform);
            }
        }

        return $result;
    }

    /**
     * Render the Nutrition Facts template
     *
     * @param array $rda
     * @return Markup
     */
    public function renderNutritionFacts(array $rda = self::US_RDA): Markup {
        return PluginTemplate::renderPluginTemplate(
            'recipe-nutrition-facts',
            [
                'value' => $this,
                'rda' => $rda,
            ]
        );
    }

    /**
     * Get all of the ingredients for this recipe
     *
     * @param string $outputUnits
     * @param int    $serving
     * @param bool   $raw
     *
     * @return array
     */
    public function getIngredients($outputUnits = 'imperial', $serving = 0, $raw = true): array
    {
        $result = [];

        if (!empty($this->ingredients)) {
            foreach ($this->ingredients as $row) {
                $convertedUnits = '';
                $ingredient = '';
                if ($row['quantity']) {
                    // Multiply the quantity by how many servings we want
                    $multiplier = 1;
                    if ($serving > 0) {
                        $multiplier = $serving / $this->serves;
                    }
                    $quantity = $row['quantity'] * $multiplier;
                    $originalQuantity = $quantity;

                    // Do the imperial->metric units conversion
                    if ($outputUnits === 'imperial') {
                        switch ($row['units']) {
                            case 'ml':
                                $convertedUnits = 'tsp';
                                $quantity *= 0.2;
                                break;
                            case 'l':
                                $convertedUnits = 'cups';
                                $quantity *= 4.2;
                                break;
                            case 'mg':
                                $convertedUnits = 'oz';
                                $quantity *= 0.000035274;
                                break;
                            case 'g':
                                $convertedUnits = 'oz';
                                $quantity *= 0.035274;
                                break;
                            case 'kg':
                                $convertedUnits = 'lb';
                                $quantity *= 2.2046226218;
                                break;
                        }
                    }
                    // Do the metric->imperial units conversion
                    if ($outputUnits === 'metric') {
                        switch ($row['units']) {
                            case 'tsp':
                                $convertedUnits = 'ml';
                                $quantity *= 4.929;
                                break;
                            case 'tbsp':
                                $convertedUnits = 'ml';
                                $quantity *= 14.787;
                                break;
                            case 'floz':
                                $convertedUnits = 'ml';
                                $quantity *= 29.574;
                                break;
                            case 'cups':
                                $convertedUnits = 'l';
                                $quantity *= 0.236588;
                                break;
                            case 'oz':
                                $convertedUnits = 'g';
                                $quantity *= 28.3495;
                                break;
                            case 'lb':
                                $convertedUnits = 'kg';
                                $quantity *= 0.45359237;
                                break;
                        }

                        $quantity = round($quantity, 1);
                    }

                    // Convert units to nice fractions
                    $quantity = $this->convertToFractions($quantity);

                    $ingredient .= $quantity;

                    if ($row['units']) {
                        $units = $row['units'];
                        if ($convertedUnits) {
                            $units = $convertedUnits;
                        }
                        if ($originalQuantity <= 1) {
                            $units = rtrim($units);
                            $units = rtrim($units, 's');
                        }
                        $ingredient .= ' ' . $units;
                    }
                }
                if ($row['ingredient']) {
                    $ingredient .= ' ' . $row['ingredient'];
                }
                if ($raw) {
                    $ingredient = Template::raw($ingredient);
                }
                $result[] = $ingredient;
            }
        }

        return $result;
    }

    /**
     * Convert decimal numbers into fractions
     *
     * @param $quantity
     *
     * @return string
     */
    private function convertToFractions($quantity)
    {
        $whole = floor($quantity);
        // Round the mantissa so we can do a floating point comparison without
        // weirdness, per: https://www.php.net/manual/en/language.types.float.php#113703
        $fraction = round($quantity - $whole, 3);
        switch ($fraction) {
            case 0:
                $fraction = '';
                break;
            case 0.25:
                $fraction = ' &frac14;';
                break;
            case 0.33:
                $fraction = ' &frac13;';
                break;
            case 0.66:
                $fraction = ' &frac23;';
                break;
            case 0.165:
                $fraction = ' &frac16;';
                break;
            case 0.5:
                $fraction = ' &frac12;';
                break;
            case 0.75:
                $fraction = ' &frac34;';
                break;
            case 0.125:
                $fraction = ' &#x215B;';
                break;
            case 0.375:
                $fraction = ' &#x215C;';
                break;
            case 0.625:
                $fraction = ' &#x215D;';
                break;
            case 0.875:
                $fraction = ' &#x215E;';
                break;
            default:
                $precision = 1;
                $pnum = round($fraction, $precision);
                $denominator = 10 ** $precision;
                $numerator = $pnum * $denominator;
                $fraction = ' <sup>'
                    .$numerator
                    . '</sup>&frasl;<sub>'
                    .$denominator
                    . '</sub>';
                break;
        }
        if ($whole == 0) {
            $whole = '';
        }

        return $whole.$fraction;
    }

    /**
     * Get all of the directions for this recipe
     *
     * @param bool $raw
     *
     * @return array
     */
    public function getDirections($raw = true)
    {
        $result = [];
        if (!empty($this->directions)) {
            foreach ($this->directions as $row) {
                $direction = $row['direction'];
                if ($raw) {
                    $direction = Template::raw($direction);
                }
                $result[] = $direction;
            }
        }

        return $result;
    }

    /**
     * Get the aggregate rating from all of the ratings
     *
     * @return float|int|string
     */
    public function getAggregateRating()
    {
        $result = 0;
        $total = 0;
        if (isset($this->ratings) && !empty($this->ratings)) {
            foreach ($this->ratings as $row) {
                $result += $row['rating'];
                $total++;
            }
            $result /= $total;
        } else {
            $result = '';
        }

        return $result;
    }

    /**
     * Get the total number of ratings
     *
     * @return int
     */
    public function getRatingsCount(): int
    {
        return count($this->ratings);
    }

    /**
     * Returns concatenated serves with its unit
     */
    public function getServes(): string
    {
        if(!empty($this->servesUnit)) {
            return $this->serves . ' ' . $this->servesUnit;
        } else {
            return $this->serves;
        }
    }

    // Private Methods
    // =========================================================================

    /**
     * Renders a JSON-LD representation of the schema
     *
     * @param      $json
     * @param bool $raw
     *
     * @return string|\Twig_Markup
     */
    private function renderJsonLd($json, $raw = true)
    {
        $linebreak = '';

        // If `devMode` is enabled, make the JSON-LD human-readable
        if (Craft::$app->getConfig()->getGeneral()->devMode) {
            $linebreak = PHP_EOL;
        }

        // Render the resulting JSON-LD
        $result = '<script type="application/ld+json">'
            .$linebreak
            .Json::encode($json)
            .$linebreak
            .'</script>';

        if ($raw === true) {
            $result = Template::raw($result);
        }

        return $result;
    }
}
