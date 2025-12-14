<?php

namespace Davox\RealFavicon\Models;

use System\Models\SettingModel;

/**
 * FaviconSetting Model.
 *
 * Manages the settings for the RealFavicon plugin, stored as a singleton
 * in the database. It handles validation and file attachments for the
 * master picture and the generated preview image.
 *
 * @link https://docs.octobercms.com/3.x/extend/system/models.html
 */
class FaviconSetting extends SettingModel
{
    use \October\Rain\Database\Traits\Validation;

    /**
     * @var string The key to identify the settings record in the database.
     */
    public $settingsCode = 'davox_realfavicon_settings';

    /**
     * @var string The file containing the form field definitions.
     */
    public $settingsFields = 'fields.yaml';

    /**
     * @var array Validation rules for the settings.
     */
    public $rules = [
        'api_key'        => 'required',
        'master_picture' => 'required',
    ];

    /**
     * @var array Custom validation messages.
     */
    public $customMessages = [
        'api_key.required'        => 'The API key is required.',
        'master_picture.required' => 'The master picture is required.',
    ];

    /**
     * @var array Relations for file attachments.
     */
    public $attachOne = [
        'master_picture' => \System\Models\File::class,
        'preview_image'  => \System\Models\File::class,
    ];
}
