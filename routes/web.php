<?php

use Illuminate\Support\Facades\Route;
use CranleighSchool\AnnualLeave\Http\Controllers\AnnualLeaveController;

Route::middleware(config('annual_leave.route_middleware', []))
    ->prefix(config('annual_leave.route_prefix', 'annual-leave'))
    ->group(function () {
        Route::get('/', [AnnualLeaveController::class, 'index'])
            ->name('annual-leave.index');

        Route::get('/json', [AnnualLeaveController::class, 'json'])
            ->name('annual-leave.json');
    });
