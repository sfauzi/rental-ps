<?php

use Livewire\Volt\Volt;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\MidtransController;
use App\Livewire\Booking;
use App\Livewire\MyBooking;
use App\Livewire\ServiceTypeResource\ListServiceType;

Route::get('/', function () {
    return view('home');
})->name('home');

Route::middleware(['auth'])->group(function () {
    // User Booking Flow
    Route::get('/booking', Booking::class)->name('booking');
    Route::get('/my-booking', MyBooking::class)->name('my-booking');
});


Route::middleware(['isAdmin'])->group(function () {
    Route::view('dashboard', 'dashboard')
        ->middleware(['auth', 'verified'])
        ->name('dashboard');
    Route::get('services', ListServiceType::class)->name('services');
});


Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Volt::route('settings/profile', 'settings.profile')->name('settings.profile');
    Volt::route('settings/password', 'settings.password')->name('settings.password');
    Volt::route('settings/appearance', 'settings.appearance')->name('settings.appearance');
});

require __DIR__ . '/auth.php';



Route::post('midtrans/callback', [MidtransController::class, 'callback']);
Route::get('midtrans/finish', [MidtransController::class, 'finishRedirect']);
Route::get('midtrans/unfinish', [MidtransController::class, 'unfinishRedirect']);
Route::get('midtrans/failed', [MidtransController::class, 'errorRedirect'])->name('pages.redirect.failed');
Route::get('transaction/not-found', function () {
    return view('pages.confirmations.not-found');
})->name('transaction.not.found');
