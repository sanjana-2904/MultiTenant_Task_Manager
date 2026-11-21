<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CompanyRegistrationController;

Route::get('/', function () {
    return view('welcome');
});

// Company Registration Routes
Route::get('/company/register', [CompanyRegistrationController::class, 'create'])
    ->name('company.register');

Route::post('/company/register', [CompanyRegistrationController::class, 'store'])
    ->name('company.register.store');
