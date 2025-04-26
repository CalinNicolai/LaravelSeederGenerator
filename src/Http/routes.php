<?php

use CalinNicolai\Seedergen\Http\Controllers\SeederGenController;
use Illuminate\Support\Facades\Route;

Route::group([
    'prefix' => config('seedergen.web_route_prefix')], function () {
    Route::get('/', [SeederGenController::class, 'index'])->name('seeder-generator.index');
    Route::post('/{table}', [SeederGenController::class, 'store'])->name('seeder-generator.store');
});
