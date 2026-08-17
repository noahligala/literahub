<?php

use App\Http\Controllers\Reader\BookPageController;
use App\Http\Controllers\Reader\ReaderBookmarkController;
use App\Http\Controllers\Reader\ReaderSessionController;
use App\Http\Controllers\Reader\SecureReaderController;
use Illuminate\Support\Facades\Route;


/*
|--------------------------------------------------------------------------
| LiteraHub Secure Reader
|--------------------------------------------------------------------------
|
| These routes are intentionally kept separate during the migration from
| PDF streaming to page-by-page secure rendering.
|
*/

Route::middleware([
    'auth',
])->group(function () {

    Route::get(
        '/secure-reader/{book}',
        [
            SecureReaderController::class,
            'show',
        ]
    )
        ->name(
            'secure-reader.show'
        );


    Route::post(
        '/secure-reader/{book}/session',
        [
            ReaderSessionController::class,
            'store',
        ]
    )
        ->name(
            'secure-reader.sessions.store'
        );


    Route::delete(
        '/secure-reader/sessions/{readerSession}',
        [
            ReaderSessionController::class,
            'destroy',
        ]
    )
        ->name(
            'secure-reader.sessions.destroy'
        );


    Route::get(
        '/secure-reader/{book}/pages/{page}',
        [
            BookPageController::class,
            'show',
        ]
    )
        ->whereNumber('page')
        ->name(
            'secure-reader.pages.show'
        );


    Route::post(
        '/secure-reader/{book}/bookmarks',
        [
            ReaderBookmarkController::class,
            'store',
        ]
    )
        ->name(
            'secure-reader.bookmarks.store'
        );

});
