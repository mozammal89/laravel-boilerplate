<?php

use App\Http\Controllers\API\v1\AppBannerController;
use App\Http\Controllers\API\v1\AppCategoryController;
use App\Http\Controllers\API\v1\AuthController;
use App\Http\Controllers\API\v1\BillerController;
use App\Http\Controllers\API\v1\MerchantController;
use App\Http\Controllers\API\v1\Payment\CallbackController;
use App\Http\Controllers\API\v1\Payment\PaymentController;
use App\Http\Controllers\API\v1\ProfileController;
use App\Http\Controllers\API\v1\Service\ServiceController;
use App\Http\Controllers\API\v2\Service\ServiceController as ServiceControllerV2;
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

Route::prefix('v1/auth')->group(function () {
    Route::post('/sign-up', [AuthController::class, 'signUp']);
    Route::post('/sign-in', [AuthController::class, 'signIn']);

    Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
    Route::post('/reset-password', [AuthController::class, 'resetPassword']);

    Route::post('/verify-otp', [AuthController::class, 'verifyOTP']);
    Route::post('/resend-otp', [AuthController::class, 'resendOTP']);
});

Route::group(['middleware' => 'auth.jwt'], function ($router) {
    Route::prefix('v1')->group(function () {
        Route::prefix('user')->group(function () {
            Route::get('/get-profile', [ProfileController::class, 'getUserProfile']);
            Route::patch('/update-profile', [ProfileController::class, 'updateUserProfile']);
            Route::delete('/delete-profile', [ProfileController::class, 'deleteUserProfile']);
        });

        Route::prefix('app')->group(function () {
            Route::get('/get-categories', [AppCategoryController::class, 'getAppCategories']);
            Route::get('/get-biller', [AppCategoryController::class, 'getAppCategoryBiller']);

            Route::get('/get-banners', [AppBannerController::class, 'getAppBanners']);
        });

        Route::prefix('biller')->group(function () {
            Route::get('/get-groups', [BillerController::class, 'getBillerGroups']);
            Route::get('/get-list', [BillerController::class, 'getBillerLists']);
        });

//        Route::prefix('service')->group(function () {
//            Route::get('/{identifier}/check', [ServiceController::class, 'isServiceAvailable']);
//            Route::post('/{identifier}/create-service-request', [ServiceController::class, 'createServiceRequest']);
//        });
        Route::prefix('service/{biller_id}')->group(function () {
            Route::get('/check', [ServiceControllerV2::class, 'isServiceAvailable']);
            Route::post('/create-service-request', [ServiceControllerV2::class, 'createServiceRequest']);
        });

        Route::prefix('payment')->group(function () {
            Route::post('/pay-with-saved-card', [PaymentController::class, 'payWithSavedCard']);
            Route::post('/pay-with-another-card', [PaymentController::class, 'payWithAnotherCard']);
            Route::get('/get-transactions', [PaymentController::class, 'getTransactions']);
            Route::get('/get-transaction-details', [PaymentController::class, 'getTransactionDetails']);
            Route::post('/add-new-card', [PaymentController::class, 'addNewCard']);
            Route::get('/get-saved-cards', [PaymentController::class, 'getSavedCards']);
            Route::delete('/delete-saved-card', [PaymentController::class, 'deleteSavedCard']);
        });

        Route::prefix('merchant')->group(function () {
            Route::get('/get-information', [MerchantController::class, 'getMerchantInformation']);
            Route::post('/pay-with-qr', [MerchantController::class, 'payMerchantWithQR']);
        });
    });
});

Route::post('v1/merchant/add-merchant', [MerchantController::class, 'addMerchantAPI'])->middleware('auth.token');
Route::get('v1/merchant/get-merchant-qr', [MerchantController::class, 'getMerchantQR'])->middleware('auth.token');

Route::post('/webxpay/card-data/callback', [CallbackController::class, 'webxpayCallback']);
//Route::post('/webxpay/merchant-info', [MerchantController::class, 'MerchantData']);
