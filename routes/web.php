<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BillingServiceController;
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

Route::get('/register', [BillingServiceController::class, 'registerPage'])->name('register');
Route::post('/register', [BillingServiceController::class, 'register']);

Route::get('/login', [BillingServiceController::class, 'loginPage'])->name('login');
Route::post('/login', [BillingServiceController::class, 'login']);
Route::get('/logout', [BillingServiceController::class, 'logout']);




Route::middleware('auth:web')->group(function (){
    Route::post('/workspace/create', [BillingServiceController::class, 'createWorkspace']);
    Route::get('/workspace/{workspace:id}', [BillingServiceController::class, 'workspacePage']);
    Route::post('/workspace/{workspace:id}/token/create', [BillingServiceController::class, 'createToken']);
    Route::post('/workspace/token/{token:id}/deactivate', [BillingServiceController::class, 'deactivateToken']);

    Route::post('/workspace/{workspace:id}/create-quota', [BillingServiceController::class, 'createBillingQuota']);
    Route::post('/workspace/{workspace:id}/delete-quota', [BillingServiceController::class, 'deleteBillingQuota']);
    Route::get('/workspace/{workspace:id}/bills', [BillingServiceController::class, 'billsPage']);

    Route::get('/', [BillingServiceController::class, 'homePage']);

});

//Route::get('/', function () {
//    return view('welcome');
//});
