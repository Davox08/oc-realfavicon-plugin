<?php

namespace Davox\RealFavicon\Components;

use Cms\Classes\ComponentBase;
use Davox\RealFavicon\Models\FaviconSetting;

/**
 * FaviconTags Component
 *
 * Injects the necessary HTML tags for the favicon into the page head.
 */
class FaviconTags extends ComponentBase
{
    /**
     * @var string The HTML code for the favicon tags.
     */
    public $htmlCode;
    public $overlappingMarkups;

    /**
     * Returns details about the component.
     *
     * @return array
     */
    public function componentDetails()
    {
        return [
            'name'        => __('Favicon Tags'),
            'description' => __('Injects the generated favicon HTML tags into your layout.'),
        ];
    }

    /**
     * Executed when the component is run on a page.
     */
    public function onRun()
    {
        $settings = FaviconSetting::instance();
        $this->htmlCode = $settings->html_code;
        $this->overlappingMarkups = json_decode($settings->overlapping_markups);
    }
}
