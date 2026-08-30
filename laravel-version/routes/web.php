<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| The application now serves Blade templates for all pages.
| Static HTML files remain in public/ as fallback.
|
*/

Route::get('/', [\App\Http\Controllers\Frontend\HomeController::class, 'index'])->name('home');

Route::get('/contact', [\App\Http\Controllers\Frontend\ContactController::class, 'index'])->name('contact');
Route::post('/contact', [\App\Http\Controllers\Frontend\ContactController::class, 'send'])->name('contact.send');

Route::get('/booking', [\App\Http\Controllers\Frontend\BookingController::class, 'index'])->name('booking');
Route::post('/booking', [\App\Http\Controllers\Frontend\BookingController::class, 'store'])->name('booking.store');
Route::post('/booking/payment-intent', [\App\Http\Controllers\Frontend\BookingController::class, 'paymentIntent'])->name('booking.payment-intent');
Route::post('/booking/checkout', [\App\Http\Controllers\Frontend\BookingController::class, 'checkout'])->name('booking.checkout');
Route::get('/payment/token', [\App\Http\Controllers\Frontend\BookingController::class, 'token'])->name('booking.token');

Route::get('/paiement/succes', [\App\Http\Controllers\Frontend\BookingController::class, 'success'])->name('booking.success');
Route::get('/paiement/annule', [\App\Http\Controllers\Frontend\BookingController::class, 'cancel'])->name('booking.cancel');
Route::post('/webhook/stripe', [\App\Http\Controllers\Frontend\BookingController::class, 'webhook'])->name('booking.webhook');

Route::get('/pages/{slug}', [\App\Http\Controllers\Frontend\PageController::class, 'show'])->name('pages.show');

Route::get('/services/category/{slug}', [\App\Http\Controllers\Frontend\ServiceController::class, 'category'])->name('services.category');
Route::get('/services/{slug}', [\App\Http\Controllers\Frontend\ServiceController::class, 'show'])->name('service.show');

// Auth routes
Route::get('/login', [\App\Http\Controllers\Auth\AuthenticatedSessionController::class, 'create'])->name('login');
Route::post('/login', [\App\Http\Controllers\Auth\AuthenticatedSessionController::class, 'store']);
Route::post('/logout', [\App\Http\Controllers\Auth\AuthenticatedSessionController::class, 'destroy'])->name('logout');

Route::get('/register', [\App\Http\Controllers\Auth\RegisteredUserController::class, 'create'])->name('register');
Route::post('/register', [\App\Http\Controllers\Auth\RegisteredUserController::class, 'store']);

Route::get('/forgot-password', [\App\Http\Controllers\Auth\PasswordResetLinkController::class, 'create'])->name('password.request');
Route::post('/forgot-password', [\App\Http\Controllers\Auth\PasswordResetLinkController::class, 'store'])->name('password.email');

Route::get('/reset-password/{token}', [\App\Http\Controllers\Auth\NewPasswordController::class, 'create'])->name('password.reset');
Route::post('/reset-password', [\App\Http\Controllers\Auth\NewPasswordController::class, 'store'])->name('password.store');

// Student portal
Route::middleware(['auth', 'role:student|admin'])->prefix('student')->name('student.')->group(function () {
    Route::get('/', [\App\Http\Controllers\Student\StudentController::class, 'dashboard'])->name('dashboard');
    Route::get('/dashboard', [\App\Http\Controllers\Student\StudentController::class, 'dashboard'])->name('dashboard');
    Route::get('/programs', [\App\Http\Controllers\Student\StudentController::class, 'programs'])->name('programs');
    Route::get('/payments', [\App\Http\Controllers\Student\StudentController::class, 'payments'])->name('payments');
    Route::get('/profile', [\App\Http\Controllers\Student\StudentController::class, 'profile'])->name('profile');
    Route::post('/profile', [\App\Http\Controllers\Student\StudentController::class, 'updateProfile'])->name('profile.update');
    Route::get('/calendar', [\App\Http\Controllers\Student\StudentController::class, 'calendar'])->name('calendar');
    Route::get('/level-tests', [\App\Http\Controllers\Student\StudentController::class, 'levelTests'])->name('level-tests');
    Route::get('/support', [\App\Http\Controllers\Student\StudentController::class, 'support'])->name('support');
});

// Teacher portal
Route::middleware(['auth', 'role:teacher|admin'])->prefix('teacher')->name('teacher.')->group(function () {
    Route::get('/', [\App\Http\Controllers\Teacher\TeacherController::class, 'dashboard'])->name('dashboard');
    Route::get('/dashboard', [\App\Http\Controllers\Teacher\TeacherController::class, 'dashboard'])->name('dashboard');
    Route::get('/students', [\App\Http\Controllers\Teacher\TeacherController::class, 'students'])->name('students');
    Route::get('/schedule', [\App\Http\Controllers\Teacher\TeacherController::class, 'schedule'])->name('schedule');
    Route::get('/sessions/{id}', [\App\Http\Controllers\Teacher\TeacherController::class, 'sessionDetails'])->name('session-details');
    Route::get('/profile', [\App\Http\Controllers\Teacher\TeacherController::class, 'profile'])->name('profile');
    Route::post('/profile', [\App\Http\Controllers\Teacher\TeacherController::class, 'updateProfile'])->name('profile.update');
});

// Admin panel
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [\App\Http\Controllers\Admin\AdminController::class, 'index'])->name('dashboard');
    Route::get('/dashboard', [\App\Http\Controllers\Admin\AdminController::class, 'index'])->name('dashboard');

    // Users
    Route::get('/users', [\App\Http\Controllers\Admin\UserController::class, 'index'])->name('users.index');
    Route::get('/users/create', [\App\Http\Controllers\Admin\UserController::class, 'create'])->name('users.create');
    Route::post('/users', [\App\Http\Controllers\Admin\UserController::class, 'store'])->name('users.store');
    Route::get('/users/{user}/edit', [\App\Http\Controllers\Admin\UserController::class, 'edit'])->name('users.edit');
    Route::put('/users/{user}', [\App\Http\Controllers\Admin\UserController::class, 'update'])->name('users.update');
    Route::delete('/users/{user}', [\App\Http\Controllers\Admin\UserController::class, 'destroy'])->name('users.destroy');

    // Services
    Route::get('/services', [\App\Http\Controllers\Admin\ServiceController::class, 'index'])->name('services.index');
    Route::get('/services/create', [\App\Http\Controllers\Admin\ServiceController::class, 'create'])->name('services.create');
    Route::post('/services', [\App\Http\Controllers\Admin\ServiceController::class, 'store'])->name('services.store');
    Route::get('/services/{service}/edit', [\App\Http\Controllers\Admin\ServiceController::class, 'edit'])->name('services.edit');
    Route::put('/services/{service}', [\App\Http\Controllers\Admin\ServiceController::class, 'update'])->name('services.update');
    Route::delete('/services/{service}', [\App\Http\Controllers\Admin\ServiceController::class, 'destroy'])->name('services.destroy');

    // Bookings
    Route::get('/bookings', [\App\Http\Controllers\Admin\BookingController::class, 'index'])->name('bookings.index');
    Route::get('/bookings/{booking}', [\App\Http\Controllers\Admin\BookingController::class, 'show'])->name('bookings.show');
    Route::get('/bookings/{booking}/edit', [\App\Http\Controllers\Admin\BookingController::class, 'edit'])->name('bookings.edit');
    Route::put('/bookings/{booking}', [\App\Http\Controllers\Admin\BookingController::class, 'update'])->name('bookings.update');
    Route::delete('/bookings/{booking}', [\App\Http\Controllers\Admin\BookingController::class, 'destroy'])->name('bookings.destroy');

    // Pages
    Route::get('/pages', [\App\Http\Controllers\Admin\PageController::class, 'index'])->name('pages.index');
    Route::get('/pages/create', [\App\Http\Controllers\Admin\PageController::class, 'create'])->name('pages.create');
    Route::post('/pages', [\App\Http\Controllers\Admin\PageController::class, 'store'])->name('pages.store');
    Route::get('/pages/{page}/edit', [\App\Http\Controllers\Admin\PageController::class, 'edit'])->name('pages.edit');
    Route::put('/pages/{page}', [\App\Http\Controllers\Admin\PageController::class, 'update'])->name('pages.update');
    Route::delete('/pages/{page}', [\App\Http\Controllers\Admin\PageController::class, 'destroy'])->name('pages.destroy');

    // FAQs
    Route::get('/faqs', [\App\Http\Controllers\Admin\FaqController::class, 'index'])->name('faqs.index');
    Route::get('/faqs/create', [\App\Http\Controllers\Admin\FaqController::class, 'create'])->name('faqs.create');
    Route::post('/faqs', [\App\Http\Controllers\Admin\FaqController::class, 'store'])->name('faqs.store');
    Route::get('/faqs/{faq}/edit', [\App\Http\Controllers\Admin\FaqController::class, 'edit'])->name('faqs.edit');
    Route::put('/faqs/{faq}', [\App\Http\Controllers\Admin\FaqController::class, 'update'])->name('faqs.update');
    Route::delete('/faqs/{faq}', [\App\Http\Controllers\Admin\FaqController::class, 'destroy'])->name('faqs.destroy');

    // Testimonials
    Route::get('/testimonials', [\App\Http\Controllers\Admin\TestimonialController::class, 'index'])->name('testimonials.index');
    Route::get('/testimonials/create', [\App\Http\Controllers\Admin\TestimonialController::class, 'create'])->name('testimonials.create');
    Route::post('/testimonials', [\App\Http\Controllers\Admin\TestimonialController::class, 'store'])->name('testimonials.store');
    Route::get('/testimonials/{testimonial}/edit', [\App\Http\Controllers\Admin\TestimonialController::class, 'edit'])->name('testimonials.edit');
    Route::put('/testimonials/{testimonial}', [\App\Http\Controllers\Admin\TestimonialController::class, 'update'])->name('testimonials.update');
    Route::delete('/testimonials/{testimonial}', [\App\Http\Controllers\Admin\TestimonialController::class, 'destroy'])->name('testimonials.destroy');

    // Statistics
    Route::get('/statistics', [\App\Http\Controllers\Admin\StatisticController::class, 'index'])->name('statistics.index');
    Route::get('/statistics/create', [\App\Http\Controllers\Admin\StatisticController::class, 'create'])->name('statistics.create');
    Route::post('/statistics', [\App\Http\Controllers\Admin\StatisticController::class, 'store'])->name('statistics.store');
    Route::get('/statistics/{statistic}/edit', [\App\Http\Controllers\Admin\StatisticController::class, 'edit'])->name('statistics.edit');
    Route::put('/statistics/{statistic}', [\App\Http\Controllers\Admin\StatisticController::class, 'update'])->name('statistics.update');
    Route::delete('/statistics/{statistic}', [\App\Http\Controllers\Admin\StatisticController::class, 'destroy'])->name('statistics.destroy');

    // Navigation
    Route::get('/navigation', [\App\Http\Controllers\Admin\NavigationController::class, 'index'])->name('navigation.index');
    Route::get('/navigation/create', [\App\Http\Controllers\Admin\NavigationController::class, 'create'])->name('navigation.create');
    Route::post('/navigation', [\App\Http\Controllers\Admin\NavigationController::class, 'store'])->name('navigation.store');
    Route::get('/navigation/{navigationItem}/edit', [\App\Http\Controllers\Admin\NavigationController::class, 'edit'])->name('navigation.edit');
    Route::put('/navigation/{navigationItem}', [\App\Http\Controllers\Admin\NavigationController::class, 'update'])->name('navigation.update');
    Route::delete('/navigation/{navigationItem}', [\App\Http\Controllers\Admin\NavigationController::class, 'destroy'])->name('navigation.destroy');

    // Media
    Route::get('/media', [\App\Http\Controllers\Admin\MediaController::class, 'index'])->name('media.index');
    Route::post('/media', [\App\Http\Controllers\Admin\MediaController::class, 'store'])->name('media.store');
    Route::delete('/media/{medium}', [\App\Http\Controllers\Admin\MediaController::class, 'destroy'])->name('media.destroy');

    // Settings
    Route::get('/settings', [\App\Http\Controllers\Admin\SettingController::class, 'index'])->name('settings');
    Route::post('/settings', [\App\Http\Controllers\Admin\SettingController::class, 'update'])->name('settings.update');

    // Analytics
    Route::get('/analytics', [\App\Http\Controllers\Admin\AdminController::class, 'analytics'])->name('analytics');

    // Calendar (programmes & ateliers)
    Route::get('/calendar', [\App\Http\Controllers\Admin\CalendarController::class, 'index'])->name('calendar');
    Route::get('/calendar/create', [\App\Http\Controllers\Admin\CalendarController::class, 'create'])->name('calendar.create');
    Route::post('/calendar', [\App\Http\Controllers\Admin\CalendarController::class, 'store'])->name('calendar.store');
    Route::get('/calendar/{calendarProgram}/edit', [\App\Http\Controllers\Admin\CalendarController::class, 'edit'])->name('calendar.edit');
    Route::put('/calendar/{calendarProgram}', [\App\Http\Controllers\Admin\CalendarController::class, 'update'])->name('calendar.update');
    Route::delete('/calendar/{calendarProgram}', [\App\Http\Controllers\Admin\CalendarController::class, 'destroy'])->name('calendar.destroy');
    Route::post('/calendar/{calendarProgram}/refresh', [\App\Http\Controllers\Admin\CalendarController::class, 'refresh'])->name('calendar.refresh');
    Route::get('/calendar/{calendarProgram}/sessions', [\App\Http\Controllers\Admin\CalendarController::class, 'sessions'])->name('calendar.sessions');
    Route::post('/calendar/{calendarProgram}/sessions', [\App\Http\Controllers\Admin\CalendarController::class, 'sessionsStore'])->name('calendar.sessions.store');
    Route::get('/calendar/{calendarProgram}/sessions/{calendarSession}/edit', [\App\Http\Controllers\Admin\CalendarController::class, 'sessionsEdit'])->name('calendar.sessions.edit');
    Route::put('/calendar/{calendarProgram}/sessions/{calendarSession}', [\App\Http\Controllers\Admin\CalendarController::class, 'sessionsUpdate'])->name('calendar.sessions.update');
    Route::delete('/calendar/{calendarProgram}/sessions/{calendarSession}', [\App\Http\Controllers\Admin\CalendarController::class, 'sessionsDestroy'])->name('calendar.sessions.destroy');
    Route::post('/calendar/{calendarProgram}/sessions/{calendarSession}/meetings', [\App\Http\Controllers\Admin\CalendarController::class, 'meetingsStore'])->name('calendar.meetings.store');
    Route::put('/calendar/{calendarProgram}/sessions/{calendarSession}/meetings/{calendarMeeting}', [\App\Http\Controllers\Admin\CalendarController::class, 'meetingsUpdate'])->name('calendar.meetings.update');
    Route::delete('/calendar/{calendarProgram}/sessions/{calendarSession}/meetings/{calendarMeeting}', [\App\Http\Controllers\Admin\CalendarController::class, 'meetingsDestroy'])->name('calendar.meetings.destroy');
    Route::post('/calendar/import', [\App\Http\Controllers\Admin\CalendarController::class, 'import'])->name('calendar.import');

    // Payments
    Route::get('/payments', [\App\Http\Controllers\Admin\AdminController::class, 'payments'])->name('payments');
});

// Keep browser favicon requests from falling through to the error page.
Route::get('favicon.ico', fn () => response()->noContent());
