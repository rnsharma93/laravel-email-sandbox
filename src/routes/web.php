<?php
use Illuminate\Support\Facades\Route;
use Ram\EmailSandbox\Http\Controllers\EmailController;
use Ram\EmailSandbox\Http\Middleware\AuthorizeEmailSandbox;

Route::middleware(['web', AuthorizeEmailSandbox::class])
    ->prefix(config('email-sandbox.route_prefix', 'email-sandbox'))
    ->group(function () {
        Route::get('/', [EmailController::class, 'index'])->name('email-sandbox.index');
        Route::delete('/destroy-all', [EmailController::class, 'destroyAll'])->name('email-sandbox.destroy-all');
        Route::get('/{id}', [EmailController::class, 'show'])->whereNumber('id')->name('email-sandbox.show');
        Route::get('/{id}/attachment/{file}', [EmailController::class, 'download'])->whereNumber('id')->name('email-sandbox.download');
        Route::delete('/{id}', [EmailController::class, 'destroy'])->whereNumber('id')->name('email-sandbox.destroy');
    });
