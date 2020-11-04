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

namespace nystudio107\recipe\fields;

use nystudio107\recipe\assetbundles\recipefield\RecipeFieldAsset;
use nystudio107\recipe\models\Recipe as RecipeModel;

use Craft;
use craft\base\ElementInterface;
use craft\base\Field;
use craft\elements\Asset;
use craft\helpers\Html;
use craft\helpers\Json;

use nystudio107\recipe\Recipe as RecipePlugin;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use yii\base\Exception;
use yii\base\InvalidConfigException;
use yii\db\Schema;

/**
 * @author    nystudio107
 * @package   Recipe
 * @since     1.0.0
 */
class Recipe extends Field
{
    // Public Properties
    // =========================================================================

    /**
     * @var array
     */
    public $assetSources = [];

    // Static Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public static function displayName(): string
    {
        return Craft::t('recipe', 'Recipe');
    }

    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public function rules()
    {
        $rules = parent::rules();
        $rules = array_merge($rules, [
        ]);

        return $rules;
    }

    /**
     * @inheritdoc
     */
    public function getContentColumnType(): string
    {
        return Schema::TYPE_TEXT;
    }

    /**
     * @inheritdoc
     * @since 1.2.1
     */
    public function useFieldset(): bool
    {
        return true;
    }

    /**
     * @inheritdoc
     */
    public function normalizeValue($value, ElementInterface $element = null)
    {
        if (is_string($value) && !empty($value)) {
            $value = Json::decode($value);
        }
        $model = new RecipeModel($value);

        return $model;
    }

    /**
     * @inheritdoc
     */
    public function getSettingsHtml()
    {
        // Render the settings template
        return Craft::$app->getView()->renderTemplate(
            'recipe/_components/fields/Recipe_settings',
            [
                'field' => $this,
                'assetSources' => $this->getSourceOptions(),
            ]
        );
    }

    /**
     * @inheritdoc
     */
    public function getInputHtml($value, ElementInterface $element = null): string
    {
        // Register our asset bundle
        try {
            Craft::$app->getView()->registerAssetBundle(RecipeFieldAsset::class);
        } catch (InvalidConfigException $e) {
            Craft::error($e->getMessage(), __METHOD__);
        }

        // Get our id and namespace
        $id = Craft::$app->getView()->formatInputId($this->handle);
        $nameSpacedId = Craft::$app->getView()->namespaceInputId($id);

        // Variables to pass down to our field JavaScript to let it namespace properly
        $jsonVars = [
            'id' => $id,
            'name' => $this->handle,
            'namespace' => $nameSpacedId,
            'prefix' => Craft::$app->getView()->namespaceInputId(''),
        ];
        $jsonVars = Json::encode($jsonVars);
        Craft::$app->getView()->registerJs("$('#{$nameSpacedId}-field').RecipeRecipe(".$jsonVars.");");

        // Set asset elements
        $elements = [];
        if ($value->imageId) {
            if (is_array($value->imageId)) {
                $value->imageId = $value->imageId[0];
            }
            $elements = [Craft::$app->getAssets()->getAssetById($value->imageId)];
        }

        // Render the input template
        try {
            return Craft::$app->getView()->renderTemplate(
                'recipe/_components/fields/Recipe_input',
                [
                    'name' => $this->handle,
                    'value' => $value,
                    'field' => $this,
                    'id' => $id,
                    'nameSpacedId' => $nameSpacedId,
                    'prefix' => Craft::$app->getView()->namespaceInputId(''),
                    'assetsSourceExists' => count(Craft::$app->getAssets()->findFolders()),
                    'elements' => $elements,
                    'elementType' => Asset::class,
                    'assetSources' => $this->assetSources,
                    'hasApiCredentials' => RecipePlugin::$plugin->settings->hasApiCredentials(),
                ]
            );
        } catch (LoaderError $e) {
            Craft::error($e->getMessage(), __METHOD__);
        } catch (RuntimeError $e) {
            Craft::error($e->getMessage(), __METHOD__);
        } catch (SyntaxError $e) {
            Craft::error($e->getMessage(), __METHOD__);
        } catch (Exception $e) {
            Craft::error($e->getMessage(), __METHOD__);
        }
    }

    /**
     * @inheritdoc
     */
    public function getSourceOptions(): array
    {
        $sourceOptions = [];

        foreach (Asset::sources('settings') as $key => $volume) {
            if (!isset($volume['heading'])) {
                $sourceOptions[] = [
                    'label' => Html::encode($volume['label']),
                    'value' => $volume['key']
                ];
            }
        }

        return $sourceOptions;
    }
}
