<?php

use App\Http\Controllers\API\V1\authController;
use App\Http\Controllers\API\V1\categoriesController;
use App\Http\Controllers\API\V1\postsController;
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

Route::group(['prefix' => 'v1'], function() {


    // Auth
    Route::group(['prefix' => 'auth'], function() {
        Route::post('register', [authController::class, 'register']);
        Route::post('login', [authController::class, 'login']);
        Route::group(['middleware' => 'auth:sanctum'], function() {
            Route::post('logout', [authController::class, 'logout']);
        });
    });

    Route::group(['middleware' => 'auth:sanctum'], function() {
        // Categories
        Route::group(['prefix' => 'categories'], function() {
            Route::get('/', [categoriesController::class, 'index']);
            Route::post('create', [categoriesController::class, 'create']);
            Route::post('update', [categoriesController::class, 'update']);
            Route::delete('delete/{categoryId}', [categoriesController::class, 'delete']);
        });

        // Posts
        Route::group(['prefix' => 'posts'], function() {
            Route::post('/create', [postsController::class, 'create']);
            Route::post('/update', [postsController::class, 'updatePost']);
            Route::post('/', [postsController::class, 'index']);
            Route::post('/{postId}', [postsController::class, 'getPost']);
            // Comments
            Route::group(['prefix' => 'comments'], function() {
                Route::get('/{postId}', [postsController::class, 'getComments']);
                Route::post('create', [postsController::class, 'createComment']);
            });
        });
    });




});

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
