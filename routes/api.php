<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\EventController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Support\Facades\Route;
use LaravelJsonApi\Laravel\Facades\JsonApiRoute;

JsonApiRoute::server('v1')
    ->middleware('auth:api')
//    ->domain(config('app.APP_DOMAIN'))
    ->resources(function ($server) {
        $server->resource('users', UserController::class)
            ->only('index', 'show', 'store', 'update', 'delete')
            ->actions(function ($actions) {
                $actions->get('me');
            });
        $server->resource('events', EventController::class)
            ->only('index', 'show', 'store', 'update', 'delete')
            ->relationships(function ($relations) {
                $relations->hasMany('users');
            })
            ->actions(function ($actions) {
                $actions->withId()->post('add-event-user');
            });
    });


Route::group([
    'prefix' => 'auth'
], function (){
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/currentPassword', [AuthController::class, 'currentPassword']);
    Route::post('/register', [AuthController::class, 'store']);
    Route::post('/refresh', [AuthController::class, 'refresh']);
    Route::post('/password/forgot', [AuthController::class, 'forgotPassword']);
    Route::post('/password/reset', [AuthController::class, 'resetPassword']);
    Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:api');
});
