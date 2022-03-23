<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\URLController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('index');
})->name('index');

Route::get('/urls', [URLController::class, 'index'])
    ->name('urls.index');

Route::get('/urls/{id}', [URLController::class, 'show'])
    ->name('urls.show');

Route::post('/urls', [URLController::class, 'store'])
    ->name('urls.store');
