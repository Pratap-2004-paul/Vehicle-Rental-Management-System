<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\VehicleController;
use App\Http\Controllers\Api\BookingController;
use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\Api\AdminController;
use App\Http\Controllers\Api\ContactController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ForgotPasswordController;

// ---- Public routes ----
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/contact', [ContactController::class, 'store']);

Route::get('/vehicles', [VehicleController::class, 'index']);
Route::get('/vehicles/{id}', [VehicleController::class, 'show']);

// ---- Logged-in users (customer or admin) ----
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);
    Route::put('/profile', [AuthController::class, 'updateProfile']);
    Route::put('/change-password', [AuthController::class, 'changePassword']);

    Route::get('/bookings', [BookingController::class, 'index']);
    Route::post('/bookings', [BookingController::class, 'store']);
    Route::get('/bookings/{id}', [BookingController::class, 'show']);
    Route::delete('/bookings/{id}', [BookingController::class, 'destroy']);

    Route::get('/bookings/{bookingId}/payment', [PaymentController::class, 'show']);
    Route::post('/bookings/{bookingId}/payment', [PaymentController::class, 'store']);
    
});

// ---- Admin only ----
Route::middleware(['auth:sanctum', 'admin'])->group(function () {
    Route::post('/vehicles', [VehicleController::class, 'store']);
    Route::put('/vehicles/{id}', [VehicleController::class, 'update']);
    Route::delete('/vehicles/{id}', [VehicleController::class, 'destroy']);

    Route::put('/bookings/{id}/status', [BookingController::class, 'updateStatus']);

    Route::get('/admin/stats', [AdminController::class, 'stats']);
    Route::get('/admin/customers', [AdminController::class, 'customers']);
    Route::put('/admin/customers/{id}/status', [AdminController::class, 'updateCustomerStatus']);
    Route::get('/admin/contact-messages', [ContactController::class, 'index']);
    Route::get('/admin/payments', [AdminController::class, 'payments']);
    
});
// ---- Forgot password (rate-limited: 3 attempts per minute per IP, to prevent OTP-spam abuse) ----
Route::middleware('throttle:3,1')->group(function () {
    Route::post('/forgot-password/send-otp', [ForgotPasswordController::class, 'sendOtp']);
    Route::post('/forgot-password/reset-password', [ForgotPasswordController::class, 'resetPassword']);
});

