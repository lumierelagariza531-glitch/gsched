<?php

use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\AppointmentWorkflowController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AvailabilityController;
use App\Http\Controllers\CalendarController;
use App\Http\Controllers\FeedbackController;
use App\Http\Controllers\GuidanceAppointmentController;
use App\Http\Controllers\GuidanceDashboardController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\StudentAppointmentController;
use App\Http\Controllers\StudentDashboardController;
use App\Http\Controllers\StudentIdProofController;
use App\Http\Controllers\SystemSettingController;
use App\Http\Controllers\UserManagementController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
})->name('home');

// Authentication Routes
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Password Change Routes
Route::middleware('auth')->group(function () {
    Route::get('/profile', [AuthController::class, 'showProfile'])->name('profile');
    Route::get('/student-id-proofs/{user}/{side}', [StudentIdProofController::class, 'showForUser'])->name('student-id-proofs.show');
    Route::put('/profile', [AuthController::class, 'updateProfile'])->name('profile.update');
    Route::get('/password/change', [AuthController::class, 'showChangePassword'])->name('password.change');
    Route::post('/password/change', [AuthController::class, 'changePassword'])->name('password.update');
    Route::get('/notifications/{notification}', [NotificationController::class, 'show'])->name('notifications.show');
});

// Student Routes
Route::middleware(['auth', 'role:student'])->prefix('student')->name('student.')->group(function () {
    Route::get('/dashboard', [StudentDashboardController::class, 'index'])->name('dashboard');

    Route::get('/schedules', [StudentAppointmentController::class, 'schedules'])->name('schedules');
    Route::get('/schedules/{date}/slots', [StudentAppointmentController::class, 'getSlots'])->name('schedules.slots');

    Route::get('/appointments', [StudentAppointmentController::class, 'index'])->name('appointments.index');
    Route::get('/appointments/create', [StudentAppointmentController::class, 'create'])->name('appointments.create');
    Route::post('/appointments', [StudentAppointmentController::class, 'store'])->name('appointments.store');
    Route::get('/appointments/{appointment}', [StudentAppointmentController::class, 'show'])->name('appointments.show');
    Route::post('/appointments/{appointment}/cancel', [StudentAppointmentController::class, 'cancel'])->name('appointments.cancel');
    Route::get('/appointments/{appointment}/reschedule', [StudentAppointmentController::class, 'rescheduleForm'])->name('appointments.reschedule');
    Route::post('/appointments/{appointment}/reschedule', [StudentAppointmentController::class, 'reschedule'])->name('appointments.reschedule.submit');

    Route::get('/notifications', [NotificationController::class, 'studentIndex'])->name('notifications');
    Route::post('/notifications/{notification}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllAsRead'])->name('notifications.read-all');

    Route::get('/feedback', [FeedbackController::class, 'studentIndex'])->name('feedback.index');
    Route::get('/feedback/{appointment}/create', [FeedbackController::class, 'create'])->name('feedback.create');
    Route::post('/feedback', [FeedbackController::class, 'store'])->name('feedback.store');
});

// Guidance Associate Routes
Route::middleware(['auth', 'role:guidance_associate'])->prefix('guidance')->name('guidance.')->group(function () {
    Route::get('/dashboard', [GuidanceDashboardController::class, 'index'])->name('dashboard');

    Route::get('/requests', [GuidanceAppointmentController::class, 'requests'])->name('requests');
    Route::get('/requests/{appointment}', [GuidanceAppointmentController::class, 'showRequest'])->name('requests.show');
    Route::post('/requests/{appointment}/approve', [AppointmentWorkflowController::class, 'approve'])->name('requests.approve');
    Route::post('/requests/{appointment}/meeting-link', [AppointmentWorkflowController::class, 'updateMeetingUrl'])->name('requests.meeting-link');
    Route::post('/requests/{appointment}/reject', [GuidanceAppointmentController::class, 'reject'])->name('requests.reject');
    Route::get('/requests/{appointment}/reschedule', [GuidanceAppointmentController::class, 'rescheduleForm'])->name('requests.reschedule');
    Route::post('/requests/{appointment}/reschedule', [GuidanceAppointmentController::class, 'reschedule'])->name('requests.reschedule.submit');
    Route::post('/requests/{appointment}/cancel', [GuidanceAppointmentController::class, 'cancel'])->name('requests.cancel');
    Route::post('/requests/{appointment}/complete', [GuidanceAppointmentController::class, 'complete'])->name('requests.complete');
    Route::post('/requests/{appointment}/remind', [GuidanceAppointmentController::class, 'sendReminder'])->name('requests.remind');
    Route::post('/requests/{appointment}/severity', [GuidanceAppointmentController::class, 'assignSeverity'])->name('requests.severity');
    Route::post('/requests/{appointment}/remind', [GuidanceAppointmentController::class, 'sendReminder'])->name('requests.remind');

    Route::get('/appointments', [GuidanceAppointmentController::class, 'appointments'])->name('appointments');
    Route::get('/appointments/{appointment}', [GuidanceAppointmentController::class, 'show'])->name('appointments.show');
    Route::get('/appointments/{appointment}/student-id/{side}', [StudentIdProofController::class, 'showForAppointment'])->name('appointments.id-proof');

    Route::get('/availability', [AvailabilityController::class, 'index'])->name('availability');
    Route::post('/availability', [AvailabilityController::class, 'store'])->name('availability.store');
    Route::get('/availability/{availability}/edit', [AvailabilityController::class, 'edit'])->name('availability.edit');
    Route::put('/availability/{availability}', [AvailabilityController::class, 'update'])->name('availability.update');
    Route::delete('/availability/{availability}', [AvailabilityController::class, 'destroy'])->name('availability.destroy');

    Route::get('/calendar', [CalendarController::class, 'index'])->name('calendar');

    Route::get('/history', [GuidanceAppointmentController::class, 'history'])->name('history');

    Route::get('/notifications', [NotificationController::class, 'guidanceIndex'])->name('notifications');
    Route::post('/notifications/{notification}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllAsRead'])->name('notifications.read-all');
});

// Admin Routes
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

    Route::get('/users', [UserManagementController::class, 'index'])->name('users.index');
    Route::get('/users/create', [UserManagementController::class, 'create'])->name('users.create');
    Route::post('/users', [UserManagementController::class, 'store'])->name('users.store');
    Route::get('/users/{user}/edit', [UserManagementController::class, 'edit'])->name('users.edit');
    Route::put('/users/{user}', [UserManagementController::class, 'update'])->name('users.update');
    Route::delete('/users/{user}', [UserManagementController::class, 'destroy'])->name('users.destroy');
    Route::post('/users/{user}/reset-password', [UserManagementController::class, 'resetPassword'])->name('users.reset-password');
    Route::post('/users/{user}/toggle-status', [UserManagementController::class, 'toggleStatus'])->name('users.toggle-status');

    Route::get('/appointments', [AdminDashboardController::class, 'appointments'])->name('appointments');
    Route::get('/appointments/{appointment}', [AdminDashboardController::class, 'showAppointment'])->name('appointments.show');
    Route::post('/appointments/{appointment}/approve', [AppointmentWorkflowController::class, 'approve'])->name('appointments.approve');
    Route::post('/appointments/{appointment}/meeting-link', [AppointmentWorkflowController::class, 'updateMeetingUrl'])->name('appointments.meeting-link');
    Route::post('/appointments/{appointment}/complete', [AppointmentWorkflowController::class, 'complete'])->name('appointments.complete');

    Route::get('/schedules', [SystemSettingController::class, 'schedules'])->name('schedules');

    Route::get('/reports', [ReportController::class, 'index'])->name('reports');
    Route::get('/reports/appointments', [ReportController::class, 'appointments'])->name('reports.appointments');
    Route::get('/reports/students', [ReportController::class, 'students'])->name('reports.students');
    Route::get('/reports/guidance-associates', [ReportController::class, 'guidanceAssociates'])->name('reports.guidance-associates');
    Route::get('/reports/status', [ReportController::class, 'status'])->name('reports.status');
    Route::get('/reports/monthly', [ReportController::class, 'monthly'])->name('reports.monthly');
    Route::get('/reports/cancellations', [ReportController::class, 'cancellations'])->name('reports.cancellations');
    Route::get('/reports/feedback', [ReportController::class, 'feedback'])->name('reports.feedback');
    Route::get('/reports/export/{type}', [ReportController::class, 'export'])->name('reports.export');

    Route::get('/logs', [ActivityLogController::class, 'index'])->name('logs');

    Route::get('/notifications', [NotificationController::class, 'adminIndex'])->name('notifications');
    Route::post('/notifications/{notification}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllAsRead'])->name('notifications.read-all');

    Route::get('/settings', [SystemSettingController::class, 'index'])->name('settings');
    Route::post('/settings', [SystemSettingController::class, 'update'])->name('settings.update');

    Route::get('/availability', [AvailabilityController::class, 'adminIndex'])->name('availability');
    Route::post('/availability', [AvailabilityController::class, 'adminStore'])->name('availability.store');
    Route::get('/availability/{availability}/edit', [AvailabilityController::class, 'adminEdit'])->name('availability.edit');
    Route::put('/availability/{availability}', [AvailabilityController::class, 'adminUpdate'])->name('availability.update');
    Route::delete('/availability/{availability}', [AvailabilityController::class, 'adminDestroy'])->name('availability.destroy');
});
