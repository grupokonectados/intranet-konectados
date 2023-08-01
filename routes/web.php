<?php

use App\Http\Controllers\{
    ClientController,
    EstrategiaController,
    HomeController,
    MailNotifyController,
};
use App\Http\Controllers\Auth\{
    ExpiredPasswordController,
};
use App\Http\Controllers\Config\{
    MailConfigController,
    UserController,
};
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;


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

        // Custom routes clients
        Route::get('/clients/diseno/{id}', [ClientController::class, 'disenoEstrategia'])->name('clients.diseno');
        
        /**
         * End clients
         */

        /**
         * Estrategias
         */

        Route::resource('/estrategia', EstrategiaController::class);

        // Custom routes estrategias

        Route::post('/estrategia/save-estrategia', [EstrategiaController::class, 'saveEstrategia'])->name('estrategia.save-estrategia');
        route::post('/estrategia/accepted-strategy', [EstrategiaController::class, 'acceptedStrategy'])->name('estrategia.accepted-strategy');
        route::post('/estrategia/probar-strategy', [EstrategiaController::class, 'probarStrategy'])->name('estrategia.probar-strategy');
        route::post('/estrategia/filter-strategy', [EstrategiaController::class, 'filterStrategy'])->name('estrategia.filter-strategy');

        /**
         * End estrategias
         */

        /**
         * Mantenimientos
         */

        //Usuarios
        Route::resource('/mantenice/users', UserController::class);
        route::get('/mantenice/users/reset-password/{id}', [UserController::class, 'resetPassword'])->name('users.reset-password');



        //MailConfig

        Route::resource('/mantenice/mail-config', MailConfigController::class);
        route::get('/mail/send_notify', [MailNotifyController::class, 'send_notify'])->name('mail.send_notify');
    
        /**
         * End mantenimientos
         */
    });

    //Verificar el password y que lo cambie
    Route::get('/password/expired', [ExpiredPasswordController::class, 'expired'])->name('password.expired');
    Route::post('password/post_expired', [ExpiredPasswordController::class, 'postExpired'])->name('password.post_expired');
});
