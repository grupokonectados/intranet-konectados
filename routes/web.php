<?php

use App\Http\Controllers\{
    ClientController,
    EstrategiaController
};
//use App\Http\Controllers\EstrategiaController;
use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('auth.login');
});

Auth::routes();

Route::get('/home', [HomeController::class, 'index'])->name('home');

Route::group(['middleware' => ['auth']], function () {

    Route::get('/', [HomeController::class, 'index'])->name('home');


    /**
     * Clients
     */
        
     Route::resource('/clients', ClientController::class);
     Route::post('/clients/search-client', [ClientController::class, 'searchCliente'])->name('clients.searchCliente');




     Route::resource('/estrategia', EstrategiaController::class);
     Route::post('/estrategia/save-estrategia', [EstrategiaController::class, 'saveEstrategia'])->name('estrategia.save-estrategia');
     Route::post('/estrategia/run-query', [EstrategiaController::class, 'runQuery'])->name('estrategia.run-query');


     Route::post('/estrategia/is-active', [EstrategiaController::class, 'isActive'])->name('estrategia.is-active');


     
});
