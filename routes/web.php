<?php

use App\Livewire\AboutPage;
use App\Livewire\Auth\AccountActivationPage;
use App\Livewire\Auth\AccountVerification;
use App\Livewire\Auth\ForgotPasswordPage;
use App\Livewire\Auth\LoginPage;
use App\Livewire\Auth\RegisterPage;
use App\Livewire\Auth\ResendVerificationPage;
use App\Livewire\Auth\ResetPasswordPage;
use App\Livewire\Client\AppointmentPage;
use App\Livewire\Client\TermsAndCondition;
use App\Livewire\ContactPage;
use App\Livewire\HomePage;
use App\Livewire\ServicePage;
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
Route::get('/', HomePage::class)->name('user.home');
Route::get('/about-us', AboutPage::class)->name('user.about');
Route::get('/service-page', ServicePage::class)->name('user.services');
Route::get('/contact-page', ContactPage::class)->name('user.contact');

Route::middleware('guest')->group(function () {
    // AUTH
    Route::get('/register', RegisterPage::class)->name('register');
    Route::get('/login', LoginPage::class)->name('login');
    Route::get('/forgot', ForgotPasswordPage::class)->name('password.request');
    Route::get('/reset/{token}', ResetPasswordPage::class)->name('password.reset');
    Route::get('/activate-account/{token}', AccountActivationPage::class)->name('activate-account');
    Route::get('/account-verification/{user_id}', AccountVerification::class)->name('account.verify');
    Route::get('/account/resend-verification', ResendVerificationPage::class)->name('account.resend-verification');
});

Route::middleware('auth')->group(function (){
    Route::get('/logout', function (){
        auth()->logout();
        return redirect('/');
    });

    // CLIENT
    Route::prefix('client')->group(function () {
        Route::get('/appointments', AppointmentPage::class)->name('client.appointment');
    });
});
