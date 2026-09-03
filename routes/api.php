<?php

use App\Http\Controllers\Api\Admin\DashboardController;
use App\Http\Controllers\Api\Admin\InformationCategoryController as AdminInformationCategoryController;
use App\Http\Controllers\Api\Admin\InformationRequestController as AdminInformationRequestController;
use App\Http\Controllers\Api\Admin\NewsController as AdminNewsController;
use App\Http\Controllers\Api\Admin\ObjectionController as AdminObjectionController;
use App\Http\Controllers\Api\Admin\PageController as AdminPageController;
use App\Http\Controllers\Api\Admin\PublicInformationController as AdminPublicInformationController;
use App\Http\Controllers\Api\Admin\ReportController;
use App\Http\Controllers\Api\Admin\RoleController;
use App\Http\Controllers\Api\Admin\SettingController;
use App\Http\Controllers\Api\Admin\UserController as AdminUserController;
use App\Http\Controllers\Api\Admin\WorkUnitController as AdminWorkUnitController;
use App\Http\Controllers\Api\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Api\Auth\CurrentUserController;
use App\Http\Controllers\Api\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\Api\Auth\NewPasswordController;
use App\Http\Controllers\Api\Auth\PasswordResetLinkController;
use App\Http\Controllers\Api\Auth\RegisteredUserController;
use App\Http\Controllers\Api\Auth\VerifyEmailController;
use App\Http\Controllers\Api\InformationRequestController;
use App\Http\Controllers\Api\MediaController;
use App\Http\Controllers\Api\ObjectionController;
use App\Http\Controllers\Api\Public\InformationCategoryController;
use App\Http\Controllers\Api\Public\NewsController;
use App\Http\Controllers\Api\Public\PageController;
use App\Http\Controllers\Api\Public\PublicInformationController;
use App\Http\Controllers\Api\Public\SearchController;
use App\Http\Controllers\Api\Public\SettingController as PublicSettingController;
use App\Http\Controllers\Api\Public\WorkUnitController;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->group(function () {
    Route::post('register', [RegisteredUserController::class, 'store'])
        ->middleware('throttle:6,1');
    Route::post('login', [AuthenticatedSessionController::class, 'store'])
        ->middleware('throttle:login');
    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])
        ->middleware('auth:sanctum');
    Route::post('forgot-password', [PasswordResetLinkController::class, 'store'])
        ->middleware('throttle:6,1');
    Route::post('reset-password', [NewPasswordController::class, 'store']);
    Route::get('me', CurrentUserController::class)->middleware('auth:sanctum');

    Route::middleware('auth:sanctum')->group(function () {
        Route::get('email/verify/{id}/{hash}', VerifyEmailController::class)
            ->middleware('signed')
            ->name('verification.verify');
        Route::post('email/verification-notification', EmailVerificationNotificationController::class)
            ->middleware('throttle:6,1');
    });
});

Route::get('informations', [PublicInformationController::class, 'index']);
Route::get('informations/type/{type}', [PublicInformationController::class, 'byType']);
Route::get('informations/{information:slug}', [PublicInformationController::class, 'show'])->name('informations.show');
Route::get('categories', [InformationCategoryController::class, 'index']);
Route::get('news', [NewsController::class, 'index']);
Route::get('news/{news:slug}', [NewsController::class, 'show'])->name('news.show');
Route::get('work-units', [WorkUnitController::class, 'index']);
Route::get('pages/{page:slug}', [PageController::class, 'show']);
Route::get('search', [SearchController::class, 'index']);
Route::get('settings', [PublicSettingController::class, 'show']);
Route::get('requests/track/{informationRequest:request_number}', [InformationRequestController::class, 'track']);
Route::post('requests', [InformationRequestController::class, 'store'])->middleware('throttle:10,1');

Route::middleware('auth:sanctum')->group(function () {
    Route::get('requests', [InformationRequestController::class, 'index']);
    Route::get('requests/{informationRequest:request_number}', [InformationRequestController::class, 'show']);
    Route::get('media/{media}', [MediaController::class, 'show'])->name('media.show');

    Route::middleware('verified')->group(function () {
        Route::post('requests/{informationRequest:request_number}/objection', [ObjectionController::class, 'store']);
    });

    Route::prefix('admin')->middleware('role:super_admin|admin_ppid_utama|admin_ppid_pembantu|pimpinan')->group(function () {
        Route::get('dashboard', [DashboardController::class, 'index']);

        Route::middleware('permission:manage-requests')->group(function () {
            Route::get('requests', [AdminInformationRequestController::class, 'index']);
            Route::get('requests/{informationRequest:request_number}', [AdminInformationRequestController::class, 'show']);
            Route::put('requests/{informationRequest:request_number}/status', [AdminInformationRequestController::class, 'updateStatus']);
            Route::post('requests/{informationRequest:request_number}/respond', [AdminInformationRequestController::class, 'respond']);
        });

        Route::middleware('permission:manage-informations')->group(function () {
            Route::get('informations', [AdminPublicInformationController::class, 'index']);
            Route::get('informations/{information}', [AdminPublicInformationController::class, 'show'])->name('admin.informations.show');
            Route::post('informations', [AdminPublicInformationController::class, 'store']);
            Route::put('informations/{information}', [AdminPublicInformationController::class, 'update']);
            Route::delete('informations/{information}', [AdminPublicInformationController::class, 'destroy']);

            Route::apiResource('work-units', AdminWorkUnitController::class)
                ->parameters(['work-units' => 'workUnit'])
                ->except(['show']);

            Route::apiResource('categories', AdminInformationCategoryController::class)
                ->parameters(['categories' => 'category'])
                ->except(['show']);
        });

        Route::middleware('permission:manage-news')->group(function () {
            Route::get('news', [AdminNewsController::class, 'index']);
            Route::get('news/{news:slug}', [AdminNewsController::class, 'show'])->name('admin.news.show');
            Route::post('news', [AdminNewsController::class, 'store']);
            Route::put('news/{news:slug}', [AdminNewsController::class, 'update']);
            Route::delete('news/{news:slug}', [AdminNewsController::class, 'destroy']);
        });

        Route::middleware('permission:approve-objections')->group(function () {
            Route::get('objections', [AdminObjectionController::class, 'index']);
            Route::get('objections/{objection}', [AdminObjectionController::class, 'show']);
            Route::post('objections/{objection}/respond', [AdminObjectionController::class, 'respond']);
        });

        Route::middleware('permission:view-reports')->group(function () {
            Route::get('reports', [ReportController::class, 'index']);
        });

        Route::middleware('permission:manage-users')->group(function () {
            Route::get('users', [AdminUserController::class, 'index']);
            Route::post('users', [AdminUserController::class, 'store']);
            Route::put('users/{user}', [AdminUserController::class, 'update']);
            Route::delete('users/{user}', [AdminUserController::class, 'destroy']);
            Route::post('users/{user}/reset-password', [AdminUserController::class, 'resetPassword']);
            Route::get('roles', [RoleController::class, 'index']);
        });

        Route::middleware('permission:manage-settings')->group(function () {
            Route::get('settings', [SettingController::class, 'show']);
            Route::put('settings', [SettingController::class, 'update']);
        });

        Route::middleware('permission:manage-pages')->group(function () {
            Route::get('pages', [AdminPageController::class, 'index']);
            Route::get('pages/{page:slug}', [AdminPageController::class, 'show'])->name('admin.pages.show');
            Route::put('pages/{page:slug}', [AdminPageController::class, 'update']);
        });
    });
});
