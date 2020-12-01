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

use nystudio107\recipe\fields\Recipe as RecipeField;
use nystudio107\recipe\integrations\RecipeFeedMeField;

use Craft;
use craft\base\Plugin;
use craft\services\Fields;
use craft\services\Plugins;
use craft\events\RegisterComponentTypesEvent;
use craft\events\PluginEvent;
use craft\helpers\UrlHelper;
use yii\base\Event;

use craft\feedme\events\RegisterFeedMeFieldsEvent;
use craft\feedme\services\Fields as FeedMeFields;

/**
 * Class Recipe
 *
 * @author    nystudio107
 * @package   Recipe
 * @since     1.0.0
 */
class Recipe extends Plugin
{
    // Static Properties
    // =========================================================================

    /**
     * @var Recipe
     */
    public static $plugin;

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

        if (class_exists(FeedMeFields::class)) {
            Event::on(FeedMeFields::class, FeedMeFields::EVENT_REGISTER_FEED_ME_FIELDS, function(RegisterFeedMeFieldsEvent $e) {
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
}
