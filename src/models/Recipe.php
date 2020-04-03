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

use Craft;
use craft\base\Model;
use craft\helpers\Template;

/**
 * @author    nystudio107
 * @package   Recipe
 * @since     1.0.0
 */
class Recipe extends Model
{
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
            "context" => "http://schema.org",
            "type" => "Recipe",
            "name" => $this->name,
            "image" => $this->getImageUrl(),
            "description" => $this->description,
            "recipeYield" => $this->serves,
            "recipeIngredient" => $this->getIngredients("imperial", 0, false),
            "recipeInstructions" => $this->getDirections(false),
        ];
        $recipeJSONLD = array_filter($recipeJSONLD);

        $nutrition = [
            "type" => "NutritionInformation",
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
        if (count($recipeJSONLD['nutrition']) == 1) {
            unset($recipeJSONLD['nutrition']);
        }
        $aggregateRating = $this->getAggregateRating();
        if ($aggregateRating) {
            $aggregateRatings = [
                "type" => "AggregateRating",
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
                    "type" => "Review",
                    'author' => $rating['author'],
                    'name' => $this->name." ".Craft::t("recipe", "Review"),
                    'description' => $rating['review'],
                    'reviewRating' => [
                        "type" => "Rating",
                        'bestRating' => '5',
                        'worstRating' => '1',
                        'ratingValue' => $rating['rating'],
                    ],
                ];
                array_push($reviews, $review);
            }
            $reviews = array_filter($reviews);
            $recipeJSONLD['review'] = $reviews;
        }

        if ($this->prepTime) {
            $recipeJSONLD['prepTime'] = "PT".$this->prepTime."M";
        }
        if ($this->cookTime) {
            $recipeJSONLD['cookTime'] = "PT".$this->cookTime."M";
        }
        if ($this->totalTime) {
            $recipeJSONLD['totalTime'] = "PT".$this->totalTime."M";
        }

        return $this->renderJsonLd($recipeJSONLD, $raw);
    }

    /**
     * Get the URL to the recipe's image
     *
     * @return null|string
     */
    public function getImageUrl()
    {
        $result = "";
        if (isset($this->imageId) && $this->imageId) {
            $image = Craft::$app->getAssets()->getAssetById($this->imageId[0]);
            if ($image) {
                $result = $image->url;
            }
        }

        return $result;
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
    public function getIngredients($outputUnits = "imperial", $serving = 0, $raw = true)
    {
        $result = [];

        if ($this->ingredients != '') {
            foreach ($this->ingredients as $row) {
                $convertedUnits = "";
                $ingredient = "";
                if ($row['quantity']) {
                    // Multiply the quantity by how many servings we want
                    $multiplier = 1;
                    if ($serving > 0) {
                        $multiplier = $serving / $this->serves;
                    }
                    $quantity = $row['quantity'] * $multiplier;
                    $originalQuantity = $quantity;

                    // Do the units conversion

                    if ($outputUnits == 'imperial') {
                        if ($row['units'] == "mls") {
                            $convertedUnits = "tsps";
                            $quantity = $quantity * 0.2;
                        }

                        if ($row['units'] == "ls") {
                            $convertedUnits = "cups";
                            $quantity = $quantity * 4.2;
                        }

                        if ($row['units'] == "mgs") {
                            $convertedUnits = "ozs";
                            $quantity = $quantity * 0.000035274;
                        }

                        if ($row['units'] == "gs") {
                            $convertedUnits = "ozs";
                            $quantity = $quantity * 0.035274;
                        }
                    }

                    if ($outputUnits == 'metric') {
                        if ($row['units'] == "tsps") {
                            $convertedUnits = "mls";
                            $quantity = $quantity * 4.929;
                        }

                        if ($row['units'] == "tbsps") {
                            $convertedUnits = "mls";
                            $quantity = $quantity * 14.787;
                        }

                        if ($row['units'] == "flozs") {
                            $convertedUnits = "mls";
                            $quantity = $quantity * 29.574;
                        }

                        if ($row['units'] == "cups") {
                            $convertedUnits = "ls";
                            $quantity = $quantity * 0.236588;
                        }

                        if ($row['units'] == "ozs") {
                            $convertedUnits = "gs";
                            $quantity = $quantity * 28.3495;
                        }

                        $quantity = round($quantity, 1);
                    }

                    // Convert imperial units to nice fractions

                    if ($outputUnits == 'imperial') {
                        $quantity = $this->convertToFractions($quantity);
                    }
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
                        $ingredient .= " ".$units;
                    }
                }
                if ($row['ingredient']) {
                    $ingredient .= " ".$row['ingredient'];
                }
                if ($raw) {
                    $ingredient = Template::raw($ingredient);
                }
                array_push($result, $ingredient);
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
                $fraction = "";
                break;

            case 0.25:
                $fraction = " &frac14;";
                break;

            case 0.33:
                $fraction = " &frac13;";
                break;

            case 0.66:
                $fraction = " &frac23;";
                break;

            case 0.165:
                $fraction = " &frac16;";
                break;

            case 0.5:
                $fraction = " &frac12;";
                break;

            case 0.75:
                $fraction = " &frac34;";
                break;

            case 0.125:
                $fraction = " &#x215B;";
                break;

            case 0.375:
                $fraction = " &#x215C;";
                break;

            case 0.625:
                $fraction = " &#x215D;";
                break;

            case 0.875:
                $fraction = " &#x215E;";
                break;

            default:
                $precision = 5;
                $pnum = round($fraction, $precision);
                $denominator = pow(10, $precision);
                $numerator = $pnum * $denominator;
                $fraction = "<sup>"
                    .$numerator
                    ."</sup>&frasl;<sub>"
                    .$denominator
                    ."</sub>";
                break;
        }
        if ($whole == 0) {
            $whole = "";
        }
        $result = $whole.$fraction;

        return $result;
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
        if ($this->directions != '') {
            foreach ($this->directions as $row) {
                $direction = $row['direction'];
                if ($raw) {
                    $direction = Template::raw($direction);
                }
                array_push($result, $direction);
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
            $result = $result / $total;
        } else {
            $result = "";
        }

        return $result;
    }

    // Private Methods
    // =========================================================================

    /**
     * Get the total number of ratings
     *
     * @return int
     */
    public function getRatingsCount()
    {
        return count($this->ratings);
    }

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
        $linebreak = "";

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
