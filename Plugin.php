<?php

declare(strict_types=1);

namespace Davox\RealFavicon;

use Backend;
use System\Classes\PluginBase;
use System\Classes\SettingsManager;

/**
 * RealFavicon Plugin Information File.
 *
 * @link https://docs.octobercms.com/3.x/extend/system/plugins.html
 */
class Plugin extends PluginBase
{
    /**
     * Returns information about this plugin.
     *
     * @return array
     */
    public function pluginDetails()
    {
        return [
            'name' => 'Real Favicon Generator',
            'description' => 'Integrates RealFaviconGenerator.net API to generate and manage favicons.',
            'author' => 'Davox',
            'icon' => 'ph ph-star',
        ];
    }

    /**
     * Registers any front-end components implemented in this plugin.
     *
     * @return array
     */
    public function registerComponents()
    {
        return [
            Components\FaviconTags::class => 'faviconTags',
        ];
    }

    /**
     * Registers any back-end permissions used by this plugin.
     *
     * @return array
     */
    public function registerPermissions()
    {
        return [
            'davox.realfavicon.access_favicon_settings' => [
                'tab' => 'Real Favicon Generator',
                'label' => 'Access Real Favicon Generator settings',
            ],
        ];
    }

    /**
     * Registers settings items, which are navigation items displayed in the Settings area.
     *
     * @return array
     */
    public function registerSettings()
    {
        return [
            'davox_realfavicon' => [
                'label' => 'Real Favicon Generator',
                'description' => 'Manage favicon generation settings.',
                'category' => SettingsManager::CATEGORY_CMS,
                'icon' => 'ph ph-star',
                'url' => Backend::url('davox/realfavicon/faviconsettings'),
                'order' => 600,
                'keywords' => 'favicon realfavicon generator logo',
                'permissions' => ['davox.realfavicon.access_favicon_settings'],
                'size' => 'large',
            ],
        ];
    }
}
