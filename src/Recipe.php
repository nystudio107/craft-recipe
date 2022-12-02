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

namespace nystudio107\recipe;

use Craft;
use craft\base\Plugin;
use craft\events\PluginEvent;
use craft\events\RegisterComponentTypesEvent;
use craft\feedme\events\RegisterFeedMeFieldsEvent;
use craft\feedme\services\Fields as FeedMeFields;
use craft\helpers\UrlHelper;
use craft\services\Fields;
use craft\services\Plugins;
use nystudio107\recipe\fields\Recipe as RecipeField;
use nystudio107\recipe\integrations\RecipeFeedMeField;
use nystudio107\recipe\models\Settings;
use nystudio107\recipe\services\ServicesTrait;
use yii\base\Event;

/**
 * Class Recipe
 *
 * @author    nystudio107
 * @package   Recipe
 * @since     1.0.0
 *
 * @property Settings $settings
 */
class Recipe extends Plugin
{
    // Traits
    // =========================================================================

    use ServicesTrait;

    // Static Properties
    // =========================================================================

    /**
     * @var Recipe
     */
    public static $plugin;

    // Public Properties
    // =========================================================================

    /**
     * @var string
     */
    public $schemaVersion = '1.0.0';

    /**
     * @var bool
     */
    public $hasCpSection = false;

    /**
     * @var bool
     */
    public $hasCpSettings = true;

    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        self::$plugin = $this;

        // Register our Field
        Event::on(
            Fields::class,
            Fields::EVENT_REGISTER_FIELD_TYPES,
            function (RegisterComponentTypesEvent $event) {
                $event->types[] = RecipeField::class;
            }
        );

        // Show our "Welcome to Recipe" message
        Event::on(
            Plugins::class,
            Plugins::EVENT_AFTER_INSTALL_PLUGIN,
            function (PluginEvent $event) {
                if (!Craft::$app->getRequest()->getIsConsoleRequest()
                    && ($event->plugin === $this)) {
                    Craft::$app->getResponse()->redirect(UrlHelper::cpUrl('recipe/welcome'))->send();
                }
            }
        );

        $feedMeInstalled = Craft::$app->getPlugins()->isPluginInstalled('feed-me') && Craft::$app->getPlugins()->isPluginEnabled('feed-me');

        if ($feedMeInstalled) {
            Event::on(FeedMeFields::class, FeedMeFields::EVENT_REGISTER_FEED_ME_FIELDS, function (RegisterFeedMeFieldsEvent $e) {
                $e->fields[] = RecipeFeedMeField::class;
            });
        }

        Craft::info(
            Craft::t(
                'recipe',
                '{name} plugin loaded',
                ['name' => $this->name]
            ),
            __METHOD__
        );
    }

    /**
     * @inheritdoc
     */
    protected function createSettingsModel(): Settings
    {
        return new Settings();
    }

    /**
     * @inheritdoc
     */
    protected function settingsHtml()
    {
        return Craft::$app->getView()->renderTemplate('recipe/settings', [
            'settings' => $this->settings
        ]);
    }
}
