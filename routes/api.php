<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BookController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/



// Route::group([
//     'middleware' => 'api',
//     'prefix' => 'auth'

// ], function ($router) {
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);

Route::group(['middleware' => ['jwt.verify','api']], function() {
    //auth routes
   
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/refresh', [AuthController::class, 'refresh']);
    Route::get('/user-profile', [AuthController::class, 'userProfile']);    
    Route::post('/update-user-profile', [AuthController::class, 'updateUserProfile']);    
    Route::post('/delete-user-profile', [AuthController::class, 'deleteUserProfile']);    

    //books routes
    Route::get('/get-all-books', [BookController::class, 'getBooks']);    
    Route::post('/store-book', [BookController::class, 'storeBook']);    
    Route::post('/update-book', [BookController::class, 'updateBook']);    
    Route::delete('/delete-book/{id}', [BookController::class, 'deleteBook']);    
    Route::post('/rent-book', [BookController::class, 'rentBook']);    
    Route::post('/return-book', [BookController::class, 'returnBook']);    
    Route::post('/get-book-by-user', [BookController::class, 'bookByUser']);    


});