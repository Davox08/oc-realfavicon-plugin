<?php

declare(strict_types=1);

/**
 * Registers the API callback route for the RealFavicon plugin.
 */

use Illuminate\Support\Facades\Route;

Route::get('/davox/realfavicon/callback', 'Davox\RealFavicon\Controllers\Generator@onCallback')
    ->name('davox.realfavicon.callback')
;
