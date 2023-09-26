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

Route::any('/', function () {
    return response()->json([
     'error' => true,
     'message' => 'rota nao informada corretamente'
    ]); 
 });
 
 
 Route::post('/register',[UserControler::class, 'RegisterUser'])->name('register.RegisterUser');
 
 Route::post('/login',[UserControler::class, 'loginUser'])->name('login.loginUser');

 Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/getuser', [UserControler::class, 'GetUser'])->name('getuser.GetUser');

    Route::get('disconect', [UserControler::class, 'DisconectUser'])->name('disconect.DisconectUser');

    Route::delete('/delete', [UserControler::class, 'DestroyUser'])->name('delete.DestroyUser');

    Route::put('/update', [UserControler::class, 'UpdateUser'])->name('update.UpdateUser');
});