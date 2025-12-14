<?php

declare(strict_types=1);

namespace Davox\RealFavicon\Controllers;

use Backend\Classes\Controller;
use Backend\Facades\BackendMenu;
use Cms\Classes\Layout;
use Cms\Classes\Page;
use Cms\Classes\Partial as CmsPartial;
use Cms\Classes\Theme;
use Davox\RealFavicon\Models\FaviconSetting;
use Exception;
use Flash;
use Illuminate\Support\Facades\Redirect;
use System\Classes\SettingsManager;
use Url;

/**
 * Favicon Settings Backend Controller
 *
 * @link https://docs.octobercms.com/3.x/extend/system/controllers.html
 */
class FaviconSettings extends Controller
{
    public $implement = [
        \Backend\Behaviors\FormController::class,
    ];

    /**
     * @var string Configuration file for the form controller.
     */
    public $formConfig = 'config_form.yaml';

    /**
     * @var array Permissions required to access this controller.
     */
    public $requiredPermissions = ['davox.realfavicon.access_favicon_settings'];

    /**
     * Controller constructor.
     */
    public function __construct()
    {
        parent::__construct();

        BackendMenu::setContext('October.System', 'system', 'CATEGORY_CMS');
        SettingsManager::setContext('Davox.RealFavicon', 'davox_realfavicon');
    }

    /**
     * The default action, displays the settings update form.
     * This method internally calls the 'update' action.
     */
    public function index(): void
    {
        // Call our update action with a symbolic ID.
        // This is required to prepare the FormController in the correct context.
        $this->update(1);
    }

    /**
     * Update action, handles the form logic.
     *
     * @param int|null $recordId The record ID to update.
     */
    public function update($recordId = null): void
    {
        $this->asExtension('FormController')->update($recordId);
        $this->pageTitle = __('Favicon Settings');
        $this->pageSize = 750;
    }

    /**
     * Finds the singleton model for the form.
     * This method is essential for the FormController to find the settings model.
     *
     * @param int $recordId The record ID.
     *
     * @return FaviconSetting
     */
    public function formFindModelObject($recordId)
    {
        return FaviconSetting::instance();
    }

    /**
     * Handles the save action from the settings form.
     * This calls the FormController's onSave method for the "update" context.
     */
    public function onSave()
    {
        $this->asExtension('FormController')->update_onSave(1);

        Flash::success(__('Settings successfully saved.'));

        $this->update(1);

        return [
            '#settings-form-container' => $this->makePartial('form'),
        ];
    }

    /**
     * AJAX handler for the "Generate Favicon" button, which redirects to the API.
     *
     * @return \Illuminate\Http\RedirectResponse|void
     */
    public function onGenerate()
    {
        try {
            $settings = FaviconSetting::instance();
            if (empty($settings->api_key)) {
                throw new Exception(__('API Key is missing. Please enter your API key in the settings.'));
            }
            if (! $settings->master_picture) {
                throw new Exception(__('Master picture is missing. Please upload a master picture.'));
            }

            $callbackUrl = Url::to('/davox/realfavicon/callback');

            $json_params = [
                'favicon_generation' => [
                    'api_key' => $settings->api_key,
                    'master_picture' => [
                        'type' => 'url',
                        'url' => $settings->master_picture->getUrl(),
                        'demo' => false,
                    ],
                    'files_location' => [
                        'type' => 'path',
                        'path' => '/storage/app/media/favicon/',
                    ],
                    'callback' => [
                        'type' => 'url',
                        'url' => $callbackUrl,
                        'short_url' => true,
                    ],
                ],
            ];

            $apiUrl = 'https://realfavicongenerator.net/api/favicon_generator';
            $redirectUrl = $apiUrl . '?json_params=' . urlencode(json_encode($json_params));

            return Redirect::to($redirectUrl);
        } catch (Exception $e) {
            Flash::error($e->getMessage());
        }
    }

    /**
     * Scans the active theme for overlapping markup, correctly handling attributes
     * and multi-line comments. This is the final and robust version.
     */
    public function onScanForOverlaps()
    {
        $settings = FaviconSetting::instance();
        $theme = Theme::getActiveTheme();
        $findings = [];
        $foundLines = [];

        $selectorsFromApi = json_decode($settings->overlapping_markups, true) ?: [];

        if (empty($selectorsFromApi)) {
            Flash::info(__('There are no overlapping markups to scan for.'));

            return;
        }

        $templates = array_merge(
            Layout::listInTheme($theme, true)->all(),
            Page::listInTheme($theme, true)->all(),
            CmsPartial::listInTheme($theme, true)->all(),
        );

        foreach ($templates as $template) {
            $fileName = $template->getFileName();
            $content = $template->getContent();

            $commentRanges = [];
            preg_match_all('/|{#.*?#}/s', $content, $commentMatches, PREG_OFFSET_CAPTURE);
            foreach ($commentMatches[0] as $match) {
                $commentRanges[] = ['start' => $match[1], 'end' => $match[1] + strlen($match[0])];
            }

            foreach ($selectorsFromApi as $selector) {
                if (preg_match('/^([a-z0-9]+)(\[[^\]]+\])/i', $selector, $mainPart)) {
                    $simpleSelector = $mainPart[1] . $mainPart[2];

                    if (preg_match('/^([a-z0-9]+)\[([^=]+)="([^"]+)"\]$/i', $simpleSelector, $parts)) {
                        $tagName = $parts[1];
                        $attributeName = $parts[2];
                        $attributeValue = $parts[3];

                        preg_match_all('/<' . preg_quote($tagName, '/') . '[^>]*>/i', $content, $tagMatches, PREG_OFFSET_CAPTURE);

                        foreach ($tagMatches[0] as $tagMatch) {
                            $tag = $tagMatch[0];
                            $offset = $tagMatch[1];

                            if (preg_match('/\s' . preg_quote($attributeName) . '\s*=\s*["\']' . preg_quote($attributeValue) . '["\']/i', $tag)) {
                                $isCommented = false;
                                foreach ($commentRanges as $range) {
                                    if ($offset >= $range['start'] && $offset < $range['end']) {
                                        $isCommented = true;
                                        break;
                                    }
                                }

                                if (! $isCommented) {
                                    $lineNumber = substr_count($content, "\n", 0, (int) $offset) + 1;
                                    $lineKey = $fileName . ':' . $lineNumber;

                                    if (! isset($foundLines[$lineKey])) {
                                        $findings[] = [
                                            'file' => $fileName,
                                            'line' => $lineNumber,
                                            'tag' => trim($tag),
                                        ];
                                        $foundLines[$lineKey] = true;
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }

        $this->vars['scanResults'] = ['findings' => $findings];

        return [
            '#Form-field-FaviconSetting-_scan_results-group' => $this->makePartial('~/plugins/davox/realfavicon/controllers/faviconsettings/_scan_results.php'),
        ];
    }
}
