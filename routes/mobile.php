<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;




Route::group(['middleware' => ['auth:api']], function () { 


});