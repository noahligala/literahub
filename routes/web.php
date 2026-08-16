<?php

use App\Http\Controllers\Auth\RegistrationController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

Route::view('/', 'welcome')
    ->name('home');

Route::view('/pricing', 'pricing')
    ->name('pricing');

/*
|--------------------------------------------------------------------------
| Custom LiteraHub Registration
|--------------------------------------------------------------------------
|
| Fortify will still handle:
| - login
| - logout
| - password reset
| - two-factor authentication
| - passkeys
|
| But public registration is handled by LiteraHub.
|
*/

Route::middleware('guest')->group(function () {

    Route::get(
        '/register',
        [RegistrationController::class, 'choose']
    )->name('register');

    Route::get(
        '/register/school',
        [RegistrationController::class, 'school']
    )->name('register.school');

    Route::post(
        '/register/school',
        [RegistrationController::class, 'storeSchool']
    )
        ->middleware('throttle:10,1')
        ->name('register.school.store');

    Route::get(
        '/register/student',
        [RegistrationController::class, 'student']
    )->name('register.student');

    Route::post(
        '/register/student',
        [RegistrationController::class, 'storeStudent']
    )
        ->middleware('throttle:10,1')
        ->name('register.student.store');
});

/*
|--------------------------------------------------------------------------
| Dashboard Router
|--------------------------------------------------------------------------
|
| After login, users are redirected according to their assigned role.
|
*/

Route::get(
    '/dashboard',
    DashboardController::class
)
    ->middleware('auth')
    ->name('dashboard');

/*
|--------------------------------------------------------------------------
| Platform Administration
|--------------------------------------------------------------------------
*/

Route::middleware([
    'auth',
    'role:super_admin|platform_admin',
])
    ->group(function () {

        Route::view(
            '/admin',
            'dashboards.admin'
        )->name('admin.dashboard');

    });

/*
|--------------------------------------------------------------------------
| Platform Staff
|--------------------------------------------------------------------------
*/

Route::middleware([
    'auth',
    'role:content_manager|author|finance|support',
])
    ->group(function () {

        Route::view(
            '/staff',
            'dashboards.staff'
        )->name('staff.dashboard');

    });

/*
|--------------------------------------------------------------------------
| School Administrator
|--------------------------------------------------------------------------
*/

Route::middleware([
    'auth',
    'role:school_admin',
])
    ->prefix('school')
    ->name('school.')
    ->group(function () {

        /*
        |--------------------------------------------------------------------------
        | School Dashboard
        |--------------------------------------------------------------------------
        */

        Route::view(
            '/',
            'dashboards.school'
        )->name('dashboard');

        /*
        |--------------------------------------------------------------------------
        | Institution Profile
        |--------------------------------------------------------------------------
        */

        Route::view(
            '/profile',
            'school.profile'
        )->name('profile');

        /*
        |--------------------------------------------------------------------------
        | Students
        |--------------------------------------------------------------------------
        */

        Route::view(
            '/students',
            'school.students.index'
        )->name('students.index');

        /*
        |--------------------------------------------------------------------------
        | Teachers
        |--------------------------------------------------------------------------
        */

        Route::view(
            '/teachers',
            'school.teachers.index'
        )->name('teachers.index');

        /*
        |--------------------------------------------------------------------------
        | Classes & Streams
        |--------------------------------------------------------------------------
        */

        Route::view(
            '/classes',
            'school.classes.index'
        )->name('classes.index');

        /*
        |--------------------------------------------------------------------------
        | Digital Library
        |--------------------------------------------------------------------------
        */

        Route::view(
            '/library',
            'school.library.index'
        )->name('library.index');

        /*
        |--------------------------------------------------------------------------
        | Assignments
        |--------------------------------------------------------------------------
        */

        Route::view(
            '/assignments',
            'school.assignments.index'
        )->name('assignments.index');

        /*
        |--------------------------------------------------------------------------
        | Subscription & Billing
        |--------------------------------------------------------------------------
        */

        Route::view(
            '/subscription',
            'school.subscription.index'
        )->name('subscription.index');

        /*
        |--------------------------------------------------------------------------
        | Reports
        |--------------------------------------------------------------------------
        */

        Route::view(
            '/reports',
            'school.reports.index'
        )->name('reports.index');

    });

/*
|--------------------------------------------------------------------------
| Teacher
|--------------------------------------------------------------------------
*/

Route::middleware([
    'auth',
    'role:teacher',
])
    ->prefix('teacher')
    ->name('teacher.')
    ->group(function () {

        Route::view(
            '/',
            'dashboards.teacher'
        )->name('dashboard');

    });

/*
|--------------------------------------------------------------------------
| Learner / Individual Subscriber
|--------------------------------------------------------------------------
*/

Route::middleware([
    'auth',
    'role:student|individual_subscriber',
])
    ->prefix('library')
    ->name('library.')
    ->group(function () {

        Route::view(
            '/',
            'dashboards.library'
        )->name('dashboard');

    });