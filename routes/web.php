<?php

use App\Http\Controllers\{
    ClientController,
    EstrategiaController,
    HomeController,
};
use App\Http\Controllers\Auth\{
    ExpiredPasswordController,
};
use App\Http\Controllers\Config\{
    UserController,
};


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

    Route::group(['middleware' => ['password_expired']], function () {
        Route::get('/', [HomeController::class, 'index'])->name('home');


    /**
     * Clients
     */

    Route::resource('/clients', ClientController::class);
    Route::post('/clients/search-client', [ClientController::class, 'searchCliente'])->name('clients.searchCliente');
    Route::get('/clients/diseno/{id}', [ClientController::class, 'disenoEstrategia'])->name('clients.diseno');

    Route::post('/clients/probar-consulta', [ClientController::class, 'probarConsulta'])->name('clients.probar-consulta');






    Route::resource('/estrategia', EstrategiaController::class);
    Route::post('/estrategia/save-estrategia', [EstrategiaController::class, 'saveEstrategia'])->name('estrategia.save-estrategia');
    Route::post('/estrategia/run-query', [EstrategiaController::class, 'runQuery'])->name('estrategia.run-query');


    Route::post('/estrategia/is-active', [EstrategiaController::class, 'isActive'])->name('estrategia.is-active');

    route::get('/estrategia/delete-strategy/{id}', [EstrategiaController::class, 'deleteStrategy'])->name('estrategia.delete-strategy');
    route::post('/estrategia/accepted-strategy', [EstrategiaController::class, 'acceptedStrategy'])->name('estrategia.accepted-strategy');
    route::get('/estrategia/stop-strategy/{id}', [EstrategiaController::class, 'stopStrategy'])->name('estrategia.stop-strategy');

    //Probar la estrategia antes 

    route::post('/estrategia/probar-strategy', [EstrategiaController::class, 'probarStrategy'])->name('estrategia.probar-strategy');

    // Filtro
    route::post('/estrategia/filter-strategy', [EstrategiaController::class, 'filterStrategy'])->name('estrategia.filter-strategy');


    //Mantenimientos. 

    //Usuarios

    Route::resource('/mantenice/users', UserController::class);
    });

    


    Route::get('/password/expired', [ExpiredPasswordController::class, 'expired'])->name('password.expired');
    Route::post('password/post_expired', [ExpiredPasswordController::class, 'postExpired'])->name('password.post_expired');
});
