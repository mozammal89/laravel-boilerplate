<?php

use App\Http\Controllers\WEB\ApiSettingsController;
use App\Http\Controllers\WEB\AppBannerController;
use App\Http\Controllers\WEB\AppCategoryController;
use App\Http\Controllers\WEB\AppSettingsController;
use App\Http\Controllers\WEB\AuthController;
use App\Http\Controllers\WEB\BillerGroupController;
use App\Http\Controllers\WEB\BillerListController;
use App\Http\Controllers\WEB\HomeController;
use App\Http\Controllers\WEB\IPGController;
use App\Http\Controllers\WEB\MerchantController;
use App\Http\Controllers\WEB\PaymentSettingsController;
use App\Http\Controllers\WEB\PermissionController;
use App\Http\Controllers\WEB\PolicyController;
use App\Http\Controllers\WEB\RoleController;
use App\Http\Controllers\WEB\SMSController;
use App\Http\Controllers\WEB\TransactionController;
use App\Http\Controllers\WEB\UserController;
use Illuminate\Support\Facades\Route;

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

Route::get('language/{locale}', function ($locale) {
    app()->setLocale($locale);
    session()->put('locale', $locale);
    return redirect()->back();
})->name('set.lang');

Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('admin.home');
    } else {
        return redirect()->route('auth.signin');
    }
})->name('/');

Route::prefix('admin')->group(function () {
    Route::prefix('auth')->group(function () {
        Route::match(['get', 'post'], '/sign-in', [AuthController::class, 'signIn'])->name('auth.signin');
        Route::match(['get'], '/sign-out', [AuthController::class, 'signOut'])->name('auth.signout');
    });

    Route::middleware(['auth'])->group(function () {
        Route::match(['get'], '/home', [HomeController::class, 'home'])->name('admin.home');
        Route::match(['get', 'post'], '/my-profile', [UserController::class, 'myProfile'])->name('admin.my.profile');

        Route::prefix('roles')->group(function () {
            Route::match(['get'], '/index', [RoleController::class, 'index'])->name('admin.roles.index');
            Route::match(['get'], '/create', [RoleController::class, 'create'])->name('admin.roles.create');
            Route::match(['post'], '/store', [RoleController::class, 'store'])->name('admin.roles.store');
            Route::match(['get'], '/edit/{id}', [RoleController::class, 'edit'])->name('admin.roles.edit');
            Route::match(['post'], '/update/{id}', [RoleController::class, 'update'])->name('admin.roles.update');
            Route::match(['get'], '/delete/{id}', [RoleController::class, 'destroy'])->name('admin.roles.destroy');
        });

        Route::prefix('permissions')->group(function () {
            Route::match(['get'], '/index', [PermissionController::class, 'index'])->name('admin.permissions.index');
            Route::match(['post'], '/api/toggle-permission', [PermissionController::class, 'toggle'])->name('admin.permissions.toggle');
        });

        Route::prefix('users')->group(function () {
            Route::match(['get'], '/index', [UserController::class, 'index'])->name('admin.users.index');
            Route::match(['get'], '/profile/edit/{id}', [UserController::class, 'edit'])->name('admin.users.edit');
            Route::match(['post'], '/profile/update/{id}', [UserController::class, 'update'])->name('admin.users.update');
            Route::match(['get'], '/profile/status-toggle/{id}', [UserController::class, 'statusToggle'])->name('admin.users.status.toggle');
            Route::match(['post'], '/profile/change-role/{id}', [UserController::class, 'changeRole'])->name('admin.users.change.role');
            Route::match(['get'], '/profile/delete/{id}', [UserController::class, 'destroy'])->name('admin.users.destroy');
        });

        Route::prefix('app/category')->group(function () {
            Route::match(['get'], '/index', [AppCategoryController::class, 'index'])->name('admin.app.category.index');
            Route::match(['get'], '/create', [AppCategoryController::class, 'create'])->name('admin.app.category.create');
            Route::match(['post'], '/store', [AppCategoryController::class, 'store'])->name('admin.app.category.store');
            Route::match(['get'], '/edit/{id}', [AppCategoryController::class, 'edit'])->name('admin.app.category.edit');
            Route::match(['post'], '/update/{id}', [AppCategoryController::class, 'update'])->name('admin.app.category.update');
            Route::match(['get'], '/delete/{id}', [AppCategoryController::class, 'destroy'])->name('admin.app.category.destroy');
            Route::match(['get'], '/toggle-status/{id}', [AppCategoryController::class, 'toggleStatus'])->name('admin.app.category.toggle.status');
        });

        Route::prefix('app/banners')->group(function () {
            Route::match(['get'], '/index', [AppBannerController::class, 'index'])->name('admin.app.banners.index');
            Route::match(['get'], '/create', [AppBannerController::class, 'create'])->name('admin.app.banners.create');
            Route::match(['post'], '/store', [AppBannerController::class, 'store'])->name('admin.app.banners.store');
            Route::match(['get'], '/edit/{id}', [AppBannerController::class, 'edit'])->name('admin.app.banners.edit');
            Route::match(['post'], '/update/{id}', [AppBannerController::class, 'update'])->name('admin.app.banners.update');
            Route::match(['get'], '/delete/{id}', [AppBannerController::class, 'destroy'])->name('admin.app.banners.destroy');
            Route::match(['get'], '/toggle-status/{id}', [AppBannerController::class, 'toggleStatus'])->name('admin.app.banners.toggle.status');
        });

        Route::prefix('transactions')->group(function () {
            Route::match(['get'], '/index', [TransactionController::class, 'index'])->name('admin.transactions.index');
        });

        Route::prefix('merchants')->group(function () {
            Route::match(['get'], '/index', [MerchantController::class, 'index'])->name('admin.merchants.index');
            Route::match(['get'], '/create', [MerchantController::class, 'create'])->name('admin.merchants.create');
            Route::match(['post'], '/store', [MerchantController::class, 'store'])->name('admin.merchants.store');
            Route::match(['get'], '/edit/{id}', [MerchantController::class, 'edit'])->name('admin.merchants.edit');
            Route::match(['post'], '/update/{id}', [MerchantController::class, 'update'])->name('admin.merchants.update');
            Route::match(['get'], '/delete/{id}', [MerchantController::class, 'destroy'])->name('admin.merchants.destroy');
            Route::match(['get'], '/toggle-status/{id}', [MerchantController::class, 'toggleStatus'])->name('admin.merchants.toggle.status');
            Route::match(['get'], '/print-qr/{id}', [MerchantController::class, 'printQR'])->name('admin.merchants.qr.print');
            Route::match(['get'], '/download-qra/{id}', [MerchantController::class, 'downloadQR'])->name('admin.merchants.qr.download');
        });

        Route::prefix('settings')->group(function () {
            Route::prefix('app')->group(function () {
                Route::match(['get'], '/index', [AppSettingsController::class, 'index'])->name('admin.settings.app');
                Route::match(['post'], '/update', [AppSettingsController::class, 'update'])->name('admin.settings.app.update');
            });

            Route::prefix('sms')->group(function () {
                Route::match(['get'], '/index', [SMSController::class, 'index'])->name('admin.settings.sms');
                Route::match(['post'], '/update', [SMSController::class, 'update'])->name('admin.settings.sms.update');
            });

            Route::prefix('payment')->group(function () {
                Route::match(['get'], '/index', [PaymentSettingsController::class, 'index'])->name('admin.settings.payment');
                Route::match(['post'], '/update', [PaymentSettingsController::class, 'update'])->name('admin.settings.payment.update');
            });

            Route::prefix('api-key')->group(function () {
                Route::match(['get'], '/index', [ApiSettingsController::class, 'index'])->name('admin.settings.api');
                Route::match(['post'], '/update', [ApiSettingsController::class, 'update'])->name('admin.settings.api.update');
            });
        });

        Route::prefix('policy')->group(function () {
            Route::match(['get'], '/edit/{key}', [PolicyController::class, 'edit'])->name('admin.policy.edit');
            Route::match(['post'], '/update/{key}', [PolicyController::class, 'update'])->name('admin.policy.update');
        });
    });
});

Route::prefix('legal')->group(function () {
    Route::match(['get'], '/{key}', [PolicyController::class, 'index'])->name('admin.policy.index');
});

Route::get('/ipg/transaction/{trx_status}', [IPGController::class, 'ipgReturn'])->name('ipg.app.return');
