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

namespace nystudio107\recipe\console\controllers;

use Craft;
use craft\console\Controller;
use craft\elements\Entry;
use craft\helpers\Console;
use nystudio107\recipe\Recipe;
use yii\console\ExitCode;
use yii\helpers\BaseConsole;

/**
 * @author    nystudio107
 * @package   Recipe
 * @since     1.3.0
 */
class NutritionApiController extends Controller
{
    /**
     * @var ?string The handle of the section.
     */
    public ?string $section = null;

    /**
     * @var ?string The handle of the recipe field.
     */
    public ?string $field = null;

    /**
     * @inheritdoc
     */
    public function options($actionID): array
    {
        $options = parent::options($actionID);
        $options[] = 'section';
        $options[] = 'field';

        return $options;
    }

    /**
     * Generates nutritional information for all entries in a section provided using --section.
     */
    public function actionGenerate(): int
    {
        if (!Recipe::$plugin->getSettings()->hasApiCredentials()) {
            $this->stderr(Craft::t('recipe', 'API credentials do not exist in plugin settings.') . PHP_EOL, BaseConsole::FG_RED);

            return ExitCode::OK;
        }

        if ($this->section === null) {
            $this->stderr(Craft::t('recipe', 'A section handle must be provided using --section.') . PHP_EOL, BaseConsole::FG_RED);

            return ExitCode::OK;
        }

        if ($this->field === null) {
            $this->stderr(Craft::t('recipe', 'A field handle must be provided using --field.') . PHP_EOL, BaseConsole::FG_RED);

            return ExitCode::OK;
        }

        $entries = Entry::find()->section($this->section)->all();

        if (empty($entries)) {
            $this->stderr(Craft::t('recipe', 'No entries found in the section with handle `{handle}`.', ['handle' => $this->section]) . PHP_EOL, BaseConsole::FG_RED);

            return ExitCode::OK;
        }

        $total = count($entries);
        $count = 0;
        $failed = 0;

        $this->stdout(Craft::t('recipe', 'Generating nutritional information for {count} entries...', ['count' => $total]) . PHP_EOL, BaseConsole::FG_YELLOW);

        Console::startProgress($count, $total, '', 0.8);

        foreach ($entries as $entry) {
            $field = $entry->{$this->field};
            $ingredients = $field->ingredients;

            foreach ($ingredients as $key => $value) {
                $ingredients[$key] = implode(' ', $value);
            }

            $nutritionalInfo = Recipe::$plugin->nutritionApi->getNutritionalInfo($ingredients, $field->serves);

            if (empty($nutritionalInfo['error'])) {
                $recipe = $entry->{$this->field};

                foreach ($nutritionalInfo as $fieldHandle => $value) {
                    $recipe[$fieldHandle] = $value;
                }

                $entry->setFieldValue($this->field, $recipe);

                if (!Craft::$app->getElements()->saveElement($entry)) {
                    ++$failed;
                }
            } else {
                ++$failed;
            }

            ++$count;

            Console::updateProgress($count, $total);
        }

        Console::endProgress();

        $succeeded = $count - $failed;

        $this->stdout(Craft::t('recipe', 'Successfully generated nutritional information for {count} entries.', ['count' => $succeeded]) . PHP_EOL, BaseConsole::FG_GREEN);

        if ($failed > 0) {
            $this->stderr(Craft::t('recipe', 'Failed to generate nutritional information for {count} entries.', ['count' => $failed]) . PHP_EOL, BaseConsole::FG_RED);
        }

        return ExitCode::OK;
    }
}
