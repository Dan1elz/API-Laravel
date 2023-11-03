<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ {
    UserControler,

};
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::fallback(function () {
    return response()->json([
        'error' => true,
        'message' => 'Rota não informada corretamente',
    ], 404);
});
 
// Rotas de Autentificação
 Route::post('/register',[UserControler::class, 'registerUser'])->name('register.registerUser');
 
 Route::post('/login',[UserControler::class, 'loginUser'])->name('login.loginUser');

 // Rotas Protegidas por Autentificação 
 Route::middleware(['auth:sanctum'])->group(function () {

    Route::get('/getuser', [UserControler::class, 'getUser'])->name('getuser.getUser');

    Route::get('/edit', [UserControler::class, 'editUser'])->name('edit.editUser');

    Route::get('disconect', [UserControler::class, 'disconectUser'])->name('disconect.disconectUser');

    Route::delete('/delete', [UserControler::class, 'destroyUser'])->name('delete.destroyUser');

    Route::put('/update', [UserControler::class, 'updateUser'])->name('update.updateUser');
});