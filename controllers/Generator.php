<?php

declare(strict_types=1);

namespace Davox\RealFavicon\Controllers;

use Backend;
use Backend\Classes\Controller;
use Davox\RealFavicon\Models\FaviconSetting;
use Exception;
use File;
use Flash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Redirect;
use System\Models\File as FileModel;
use ZipArchive;

/**
 * Generator Controller
 *
 * Handles the API callback from RealFaviconGenerator.
 */
class Generator extends Controller
{
    /**
     * Handles the callback from the RealFaviconGenerator API.
     * It processes the response, downloads and extracts the favicon package,
     * and saves the generated data.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function onCallback()
    {
        try {
            $jsonResultUrl = request()->input('json_result_url');
            if (! $jsonResultUrl) {
                throw new Exception(__('Invalid callback from API. No JSON result URL found.'));
            }

            $resultJson = Http::get($jsonResultUrl)->body();
            $result = json_decode($resultJson, true);
            $genResult = $result['favicon_generation_result'];

            if ($genResult['result']['status'] === 'error') {
                throw new Exception(__('API Error: :message', ['message' => $genResult['result']['error_message']]));
            }

            $packageUrl = $genResult['favicon']['package_url'];
            $zipContent = Http::get($packageUrl)->body();
            $destinationPath = storage_path('app/media/favicon');
            $zipFilePath = $destinationPath . '/favicon_package.zip';

            if (! File::isDirectory($destinationPath)) {
                File::makeDirectory($destinationPath, 0755, true, true);
            }

            File::put($zipFilePath, $zipContent);

            $zip = new ZipArchive();
            if ($zip->open($zipFilePath) === true) {
                $zip->extractTo($destinationPath);
                $zip->close();
                File::delete($zipFilePath);
            } else {
                throw new Exception(__('Failed to unzip the favicon package.'));
            }
            $settings = FaviconSetting::instance();

            if (isset($genResult['preview_picture_url'])) {
                $previewUrl = $genResult['preview_picture_url'];
                $previewImageContent = Http::get($previewUrl)->body();
                $file = new FileModel();
                $file->fromData($previewImageContent, 'favicon_preview.png');
                $settings->preview_image = $file;
            }
            $settings->html_code = $genResult['favicon']['html_code'];
            $settings->version = $genResult['version'];
            $settings->non_interactive_request = json_encode($genResult['non_interactive_request']);
            if (isset($genResult['favicon']['overlapping_markups'])) {
                $settings->overlapping_markups = json_encode($genResult['favicon']['overlapping_markups'], JSON_PRETTY_PRINT);
            }

            $settings->save();

            Flash::success(__('Favicon generated and saved successfully.'));
        } catch (Exception $e) {
            Flash::error($e->getMessage());
        }

        // Redirect back to the settings page
        return Redirect::to(Backend::url('davox/realfavicon/faviconsettings'));
    }
}
