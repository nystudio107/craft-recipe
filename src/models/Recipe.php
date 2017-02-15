<?php
/**
 * Recipe plugin for Craft CMS 3.x
 *
 * A comprehensive recipe FieldType for Craft CMS that includes metric/imperial conversion, portion calculation,
 * and JSON-LD microdata support
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
     * @return string the URL to the image
     */
    public function getImageUrl()
    {
        $result = "";
        if (isset($this->imageId) && $this->imageId) {
            $image = Craft::$app->getAssets()->getAssetById($this->imageId);
            if ($image) {
                $result = $image->url;
            }
        }
        return $result;
    }

    /**
     * @param string $outputUnits
     * @param int $serving
     * @param bool $raw
     * @return array of strings for the ingredients
     */
    public function getIngredients($outputUnits = "imperial", $serving = 0, $raw = true)
    {
        $result = [];
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
                    $ingredient .= " " . $units;
                }
            }
            if ($row['ingredient']) {
                $ingredient .= " " . $row['ingredient'];
            }
            if ($raw) {
                $ingredient = Template::raw($ingredient);
            }
            array_push($result, $ingredient);
        }
        return $result;
    }

    /**
     * @param bool $raw
     * @return array of strings for the directions
     */
    public function getDirections($raw = true)
    {
        $result = array();
        foreach ($this->directions as $row) {
            $direction = $row['direction'];
            if ($raw) {
                $direction = Template::raw($direction);
            }
            array_push($result, $direction);
        }
        return $result;
    }

    /**
     * @return string the aggregate rating for this recipe
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

    /**
     * @return string the number of ratings
     */
    public function getRatingsCount()
    {
        return count($this->ratings);
    }

    /**
     * @param bool $raw
     * @return string|\Twig_Markup the rendered HTML JSON-LD microdata
     */
    public function renderRecipeJSONLD($raw = true)
    {
        $recipeJSONLD = array(
            "context" => "http://schema.org",
            "type" => "Recipe",
            "name" => $this->name,
            "image" => $this->getImageUrl(),
            "description" => $this->description,
            "recipeYield" => $this->serves,
            "recipeIngredient" => $this->getIngredients("imperial", 0, false),
            "recipeInstructions" => $this->getDirections(false),
        );
        $recipeJSONLD = array_filter($recipeJSONLD);

        $nutrition = array(
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
        );
        $nutrition = array_filter($nutrition);
        $recipeJSONLD['nutrition'] = $nutrition;
        if (count($recipeJSONLD['nutrition']) == 1) {
            unset($recipeJSONLD['nutrition']);
        }
        $aggregateRating = $this->getAggregateRating();
        if ($aggregateRating) {
            $aggregateRatings = array(
                "type" => "AggregateRating",
                'ratingCount' => $this->getRatingsCount(),
                'bestRating' => '5',
                'worstRating' => '1',
                'ratingValue' => $aggregateRating,
            );
            $aggregateRatings = array_filter($aggregateRatings);
            $recipeJSONLD['aggregateRating'] = $aggregateRatings;

            $reviews = array();
            foreach ($this->ratings as $rating) {
                $review = array(
                    "type" => "Review",
                    'author' => $rating['author'],
                    'name' => $this->name . " " . Craft::t("recipe", "Review"),
                    'description' => $rating['review'],
                    'reviewRating' => array(
                        "type" => "Rating",
                        'bestRating' => '5',
                        'worstRating' => '1',
                        'ratingValue' => $rating['rating'],
                    ),
                );
                array_push($reviews, $review);
            }
            $reviews = array_filter($reviews);
            $recipeJSONLD['review'] = $reviews;
        }

        if ($this->prepTime) {
            $recipeJSONLD['prepTime'] = "PT" . $this->prepTime . "M";
        }
        if ($this->cookTime) {
            $recipeJSONLD['cookTime'] = "PT" . $this->cookTime . "M";
        }
        if ($this->totalTime) {
            $recipeJSONLD['totalTime'] = "PT" . $this->totalTime . "M";
        }

        return $this->renderJsonLd($recipeJSONLD, $raw);
    }

    // Private Methods
    // =========================================================================

    /**
     * @param $quantity
     * @return string the fractionalized string
     */
    private function convertToFractions($quantity)
    {
        $whole = floor($quantity);
        $fraction = $quantity - $whole;
        switch ($fraction) {
            case 0:
                $fraction = "";
                break;

            case 0.25:
                $fraction = " &frac14;";
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
                $fraction = "<sup>" . $numerator . "</sup>&frasl;<sub>" . $denominator . "</sub>";
                break;
        }
        if ($whole == 0) {
            $whole = "";
        }
        $result = $whole . $fraction;
        return $result;
    }

    /**
     * Renders a JSON-LD representation of the schema
     *
     * @param $json
     * @param bool $raw
     * @return string|\Twig_Markup
     */
    private function renderJsonLd($json, $raw = true)
    {
        $linebreak = "";

        // If `devMode` is enabled, make the JSON-LD human-readable
        if (Craft::$app->config->get('devMode')) {
            $linebreak = PHP_EOL;
        }

        // Render the resulting JSON-LD
        $result = '<script type="application/ld+json">'
            . $linebreak
            . Json::encode($json)
            . $linebreak
            . '</script>';

        if ($raw === true) {
            $result = Template::raw($result);
        }

        return $result;
    }
}
