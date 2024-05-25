<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Gamecontroller;
use App\Http\Controllers\Testecontroller;

Route::post('/web-api/auth/session/v2/verifyOperatorPlayerSession', [Gamecontroller::class, 'verifySession']);
Route::post('/web-api/auth/session/v2/verifySession', [Gamecontroller::class, 'verifySession']);
Route::post('/web-api/game-proxy/v2/GameName/Get', [Gamecontroller::class, 'getGameJson']);
Route::post('/web-api/game-proxy/v2/Resources/GetByResourcesTypeIds', [Gamecontroller::class, 'getByResourcesTypeIds']);
Route::post('/web-api/game-proxy/v2/GameWallet/Get', [Gamecontroller::class, 'getGameWallet']);
Route::post('/web-api/game-proxy/v2/GameRule/Get', [Gamecontroller::class, 'getGameRule']);
Route::post('', [Gamecontroller::class, '']);


// games get info

Route::post('/game-api/{game}/v2/GameInfo/Get', [Gamecontroller::class, 'getGameInfo']);

// game spin

Route::post('/game-api/{game}/v2/Spin', [Gamecontroller::class, 'spin']);
Route::post('/testealgoritmos', [Testecontroller::class, 'testealgoritmos']);
