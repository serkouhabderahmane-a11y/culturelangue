<?php

use App\Http\Controllers\Api\AdminController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BookingController;
use App\Http\Controllers\Api\PublicController;
use App\Http\Controllers\Api\StaffDocumentController;
use App\Http\Controllers\Api\StudentController;
use App\Http\Controllers\Api\TeacherController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

Route::prefix('v1')->middleware('force-json')->group(function () {

    // Public content
    Route::get('home', [PublicController::class, 'home']);
    Route::get('services', [PublicController::class, 'services']);
    Route::get('services/{slug}', [PublicController::class, 'service']);
    Route::get('categories', [PublicController::class, 'categories']);
    Route::get('faqs', [PublicController::class, 'faqs']);
    Route::get('testimonials', [PublicController::class, 'testimonials']);
    Route::get('statistics', [PublicController::class, 'statistics']);
    Route::get('pages', [PublicController::class, 'pages']);
    Route::get('pages/{slug}', [PublicController::class, 'page']);
    Route::get('settings', [PublicController::class, 'settings']);
    Route::get('navigation', [PublicController::class, 'navigation']);
    Route::get('contact-info', [PublicController::class, 'contactInfo']);
    Route::post('contact', [PublicController::class, 'contact']);
    Route::get('lessons', [PublicController::class, 'lessons']);

    // Public booking
    Route::get('bookings', [BookingController::class, 'index']);
    Route::post('bookings', [BookingController::class, 'store'])->middleware('optional-auth');

    // Auth
    Route::post('auth/register', [AuthController::class, 'register']);
    Route::post('auth/login', [AuthController::class, 'login']);

    // Authenticated
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('auth/logout', [AuthController::class, 'logout']);
        Route::get('me', [AuthController::class, 'me']);
        Route::put('me', [AuthController::class, 'updateProfile']);

        // Student portal
        Route::prefix('student')->middleware('role:student|admin')->group(function () {
            Route::get('dashboard', [StudentController::class, 'dashboard']);
            Route::get('enrollments', [StudentController::class, 'enrollments']);
            Route::get('schedule', [StudentController::class, 'schedule']);
            Route::get('attendance', [StudentController::class, 'attendance']);
            Route::get('tests', [StudentController::class, 'tests']);
            Route::get('grades', [StudentController::class, 'grades']);
            Route::get('payments', [StudentController::class, 'payments']);
            Route::get('bookings', [StudentController::class, 'bookings']);
            Route::get('calendar', [StudentController::class, 'calendar']);
            Route::get('notifications', [StudentController::class, 'notifications']);
            Route::post('notifications/{id}/read', [StudentController::class, 'markNotificationRead']);
            Route::post('notifications/read-all', [StudentController::class, 'markAllNotificationsRead']);
            Route::get('tickets', [StudentController::class, 'tickets']);
            Route::post('tickets', [StudentController::class, 'storeTicket']);
            Route::post('tickets/{id}/reply', [StudentController::class, 'replyToTicket']);
        });

        // Staff documents (admin & teacher only)
        Route::prefix('staff')->group(function () {
            Route::get('documents/placement-test-corrige', [StaffDocumentController::class, 'placementTestCorrige'])
                ->middleware('role:admin|teacher');
        });

        // Teacher portal
        Route::prefix('teacher')->middleware('role:teacher|admin')->group(function () {
            Route::get('dashboard', [TeacherController::class, 'dashboard']);
            Route::get('lessons', [TeacherController::class, 'lessons']);
            Route::get('lessons/{id}', [TeacherController::class, 'lesson']);
            Route::get('students', [TeacherController::class, 'students']);
            Route::post('lessons/{id}/attendance', [TeacherController::class, 'markAttendance']);
            Route::get('grades', [TeacherController::class, 'grades']);
            Route::post('grades', [TeacherController::class, 'enterGrade']);
            Route::get('tests', [TeacherController::class, 'tests']);
            Route::post('tests', [TeacherController::class, 'createTest']);
            Route::get('sessions', [TeacherController::class, 'sessions']);
            Route::get('calendar', [TeacherController::class, 'calendar']);
            Route::get('availability', [TeacherController::class, 'availability']);
            Route::put('availability', [TeacherController::class, 'updateAvailability']);
            Route::get('specialties', [TeacherController::class, 'specialties']);
        });

        // Admin portal
        Route::prefix('admin')->middleware('role:admin')->group(function () {
            Route::get('dashboard', [AdminController::class, 'dashboard']);
            Route::get('users', [AdminController::class, 'users']);
            Route::post('users', [AdminController::class, 'storeUser']);
            Route::put('users/{id}', [AdminController::class, 'updateUser']);
            Route::delete('users/{id}', [AdminController::class, 'deleteUser']);
            Route::get('bookings', [AdminController::class, 'bookings']);
            Route::put('bookings/{id}/status', [AdminController::class, 'updateBookingStatus']);
            Route::get('payments', [AdminController::class, 'payments']);
            Route::post('payments/{id}/refund', [AdminController::class, 'refundPayment']);
            Route::put('payments/{id}/status', [AdminController::class, 'updatePaymentStatus']);
            Route::get('invoices', [AdminController::class, 'invoices']);
            Route::get('services', [AdminController::class, 'services']);
            Route::post('services', [AdminController::class, 'storeService']);
            Route::put('services/{id}', [AdminController::class, 'updateService']);
            Route::delete('services/{id}', [AdminController::class, 'deleteService']);
            Route::get('categories', [AdminController::class, 'categories']);
            Route::get('lessons', [AdminController::class, 'lessons']);
            Route::post('lessons', [AdminController::class, 'storeLesson']);
            Route::put('lessons/{id}', [AdminController::class, 'updateLesson']);
            Route::delete('lessons/{id}', [AdminController::class, 'deleteLesson']);
            Route::get('sessions', [AdminController::class, 'sessions']);
            Route::post('sessions', [AdminController::class, 'storeSession']);
            Route::put('sessions/{id}', [AdminController::class, 'updateSession']);
            Route::delete('sessions/{id}', [AdminController::class, 'deleteSession']);
            Route::get('analytics', [AdminController::class, 'analytics']);
            Route::get('calendar', [AdminController::class, 'calendar']);
            Route::get('support-tickets', [AdminController::class, 'supportTickets']);
            Route::post('support-tickets/{id}/reply', [AdminController::class, 'replyToTicket']);
            Route::post('support-tickets/{id}/close', [AdminController::class, 'closeTicket']);
            Route::get('contact-messages', [AdminController::class, 'contactMessages']);
            Route::put('contact-messages/{id}', [AdminController::class, 'updateContactMessage']);
            Route::get('payrolls', [AdminController::class, 'payrolls']);
        });
    });
});
