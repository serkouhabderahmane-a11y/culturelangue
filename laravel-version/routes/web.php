<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| The static frontend is served directly from the public/ directory
| (index.html, admin/, teacher/, student/, booking.html, paiement.html).
| This route only serves the SPA shell at the root if index.html exists.
|
*/

Route::get('/', function () {
    $path = public_path('index.html');

    if (is_file($path)) {
        return response()->file($path);
    }

    return response()->json([
        'success' => true,
        'message' => 'Cultulangues API is running.',
        'api' => url('/api/v1'),
    ]);
});

// Named contact route so error/legacy Blade views (layouts.public) can build the
// link to the static contact page without crashing.
Route::redirect('/contact', '/pages/contact.html')->name('contact');

// Keep browser favicon requests from falling through to the error page.
Route::get('favicon.ico', fn () => response()->noContent());
