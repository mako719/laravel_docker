<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/publishers', [App\Http\Controllers\publisherAction::class, 'create']);

// バッチ処理仮受信API
Route::post('/import-orders', function (Request $request) {
    $json = $request->getContent();
    file_put_contents('/tmp/orders', $json);

    return response('ok');
});
