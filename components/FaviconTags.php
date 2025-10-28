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

    /**
     * Returns details about the component.
     *
     * @return array
     */
    public function componentDetails()
    {
        return [
            'name'        => 'Favicon Tags',
            'description' => 'Injects the generated favicon HTML tags into your layout.',
        ];
    }

    /**
     * Executed when the component is run on a page.
     */
    public function onRun()
    {
        $settings = FaviconSetting::instance();
        $this->htmlCode = $settings->html_code;
    }
}
