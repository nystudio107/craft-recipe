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

use Craft;
use craft\base\ElementInterface;
use craft\base\Field;
use craft\elements\Asset;
use craft\helpers\Html;
use craft\helpers\Json;
use nystudio107\recipe\assetbundles\recipefield\RecipeFieldAsset;
use nystudio107\recipe\models\Recipe as RecipeModel;
use nystudio107\recipe\Recipe as RecipePlugin;
use Throwable;
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
    public array $assetSources = [];

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
    public function rules(): array
    {
        $rules = parent::rules();

        return array_merge($rules, [
        ]);
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
    public function normalizeValue(mixed $value, ?ElementInterface $element = null): RecipeModel
    {
        $config = [];
        // If we already have a model, just return it
        if ($value instanceof RecipeModel) {
            return $value;
        }
        // If we have a non-empty string, try to JSON-decode it
        if (is_string($value)) {
            $value = Json::decodeIfJson($value);
        }
        // If we have an array, use that for our config data
        if (is_array($value)) {
            $config = $value;
        }
        // Ensure we save our asset ids as integers, not arrays
        if (isset($config['imageId'])) {
            if (is_array($config['imageId'])) {
                $config['imageId'] = $config['imageId'][0];
            }
        }
        if (isset($config['videoId'])) {
            if (is_array($config['videoId'])) {
                $config['videoId'] = $config['videoId'][0];
            }
        }

        return new RecipeModel($config);
    }

    /**
     * @inheritdoc
     */
    public function getSettingsHtml(): ?string
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
    public function getInputHtml(mixed $value, ?ElementInterface $element = null): string
    {
        // Register our asset bundle
        try {
            Craft::$app->getView()->registerAssetBundle(RecipeFieldAsset::class);
        } catch (InvalidConfigException $invalidConfigException) {
            Craft::error($invalidConfigException->getMessage(), __METHOD__);
        }

        // Get our id and namespace
        $id = Html::id($this->handle);
        $nameSpacedId = Craft::$app->getView()->namespaceInputId($id);

        // Variables to pass down to our field JavaScript to let it namespace properly
        $jsonVars = [
            'id' => $id,
            'name' => $this->handle,
            'namespace' => $nameSpacedId,
            'prefix' => Craft::$app->getView()->namespaceInputId(''),
        ];
        $jsonVars = Json::encode($jsonVars);
        Craft::$app->getView()->registerJs(sprintf('$(\'#%s-field\').RecipeRecipe(', $nameSpacedId) . $jsonVars . ");");

        // Set asset elements
        $elements = [];
        if ($value->imageId) {
            if (is_array($value->imageId)) {
                $value->imageId = $value->imageId[0];
            }

            $elements = [Craft::$app->getAssets()->getAssetById($value->imageId)];
        }

        $videoElements = [];
        if ($value->videoId) {
            if (is_array($value->videoId)) {
                $value->videoId = $value->videoId[0];
            }

            $videoElements = [Craft::$app->getAssets()->getAssetById($value->videoId)];
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
                    'assetsSourceExists' => is_countable(Craft::$app->getAssets()->findFolders()) ? count(Craft::$app->getAssets()->findFolders()) : 0,
                    'elements' => $elements,
                    'videoElements' => $videoElements,
                    'elementType' => Asset::class,
                    'assetSources' => $this->assetSources,
                    'hasApiCredentials' => RecipePlugin::$plugin->getSettings()->hasApiCredentials(),
                ]
            );
        } catch (Throwable $throwable) {
            Craft::error($throwable->getMessage(), __METHOD__);
            return '';
        }
    }

    /**
     * Get the asset sources
     */
    public function getSourceOptions(): array
    {
        $sourceOptions = [];

        foreach (Asset::sources('settings') as $volume) {
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
