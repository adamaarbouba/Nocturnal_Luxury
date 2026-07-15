<?php

use App\Http\Controllers\Api\PublicController;
use Illuminate\Support\Facades\Route;

Route::get('/public/home', [PublicController::class, 'home']);
