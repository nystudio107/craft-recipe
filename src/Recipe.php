<?php
/**
 * Recipe plugin for Craft CMS 3.x
 *
 * A comprehensive recipe FieldType for Craft CMS that includes metric/imperial conversion, portion calculation, and JSON-LD microdata support
 *
 * @link      https://nystudio107.com
 * @copyright Copyright (c) 2017 nystudio107
 */

namespace nystudio107\recipe;

use nystudio107\recipe\fields\Recipe as RecipeField;

use Craft;
use craft\base\Plugin;
use craft\services\Fields;
use craft\events\RegisterComponentTypesEvent;
use yii\base\Event;

/**
 * @author    nystudio107
 * @package   Recipe
 * @since     1.0.0
 */
class Recipe extends Plugin
{
    // Static Properties
    // =========================================================================

    /**
     * @var static
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

        Event::on(
            Fields::className(),
            Fields::EVENT_REGISTER_FIELD_TYPES,
            function (RegisterComponentTypesEvent $event) {
                $event->types[] = RecipeField::className();
            }
        );

        Craft::info('Recipe ' . Craft::t('recipe', 'plugin loaded'), __METHOD__);
    }

    // Protected Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    protected function beforeInstall(): bool
    {
        return true;
    }

    /**
     * @inheritdoc
     */
    protected function afterInstall()
    {
    }

    /**
     * @inheritdoc
     */
    protected function beforeUpdate(): bool
    {
        return true;
    }

    /**
     * @inheritdoc
     */
    protected function afterUpdate()
    {
    }

    /**
     * @inheritdoc
     */
    protected function beforeUninstall(): bool
    {
        return true;
    }

    /**
     * @inheritdoc
     */
    protected function afterUninstall()
    {
    }
}
