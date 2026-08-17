<?php

use App\Http\Controllers\Auth\RegistrationController;
use App\Http\Controllers\DashboardController;

use App\Http\Controllers\School\AssignmentController;
use App\Http\Controllers\School\ClassController;
use App\Http\Controllers\School\StreamController;
use App\Http\Controllers\School\StudentController;
use App\Http\Controllers\School\TeacherController;

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
| Fortify continues to handle:
| - login
| - logout
| - password reset
| - two-factor authentication
| - passkeys
|
| Public registration is handled by LiteraHub.
|
*/

Route::middleware('guest')->group(function () {

    /*
    |--------------------------------------------------------------------------
    | Registration Choice
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/register',
        [RegistrationController::class, 'choose']
    )->name('register');


    /*
    |--------------------------------------------------------------------------
    | School Registration
    |--------------------------------------------------------------------------
    */

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


    /*
    |--------------------------------------------------------------------------
    | Individual Student Registration
    |--------------------------------------------------------------------------
    */

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
| After authentication, DashboardController redirects each user to the
| correct portal based on their assigned LiteraHub role.
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
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        Route::view(
            '/',
            'dashboards.admin'
        )->name('dashboard');

    });


/*
|--------------------------------------------------------------------------
| Platform Staff
|--------------------------------------------------------------------------
|
| Used by:
| - content_manager
| - author
| - finance
| - support
|
*/

Route::middleware([
    'auth',
    'role:content_manager|author|finance|support',
])
    ->prefix('staff')
    ->name('staff.')
    ->group(function () {

        Route::view(
            '/',
            'dashboards.staff'
        )->name('dashboard');

    });


/*
|--------------------------------------------------------------------------
| School Administrator Portal
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
        | Dashboard
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
        |
        | These remain view routes temporarily until ProfileController
        | is implemented.
        |
        */

        Route::view(
            '/profile',
            'school.profile.show'
        )->name('profile.show');

        Route::view(
            '/profile/edit',
            'school.profile.edit'
        )->name('profile.edit');


        /*
        |--------------------------------------------------------------------------
        | Students
        |--------------------------------------------------------------------------
        |
        | GET        /school/students
        | GET        /school/students/create
        | POST       /school/students
        | GET        /school/students/{student}
        | GET        /school/students/{student}/edit
        | PUT/PATCH  /school/students/{student}
        | DELETE     /school/students/{student}
        |
        */

        Route::resource(
            'students',
            StudentController::class
        );


        /*
        |--------------------------------------------------------------------------
        | Teachers
        |--------------------------------------------------------------------------
        */

        Route::resource(
            'teachers',
            TeacherController::class
        );


        /*
        |--------------------------------------------------------------------------
        | Classes
        |--------------------------------------------------------------------------
        |
        | Uses SchoolClass model internally while preserving URLs such as:
        | /school/classes/1
        |
        */

        Route::resource(
            'classes',
            ClassController::class
        );


        /*
        |--------------------------------------------------------------------------
        | Class Streams
        |--------------------------------------------------------------------------
        |
        | Streams are managed within an institution's classes.
        |
        */

        Route::get(
            '/classes/{class}/streams/create',
            [StreamController::class, 'create']
        )->name('streams.create');

        Route::post(
            '/classes/{class}/streams',
            [StreamController::class, 'store']
        )->name('streams.store');

        Route::get(
            '/streams/{stream}/edit',
            [StreamController::class, 'edit']
        )->name('streams.edit');

        Route::put(
            '/streams/{stream}',
            [StreamController::class, 'update']
        )->name('streams.update');

        Route::patch(
            '/streams/{stream}',
            [StreamController::class, 'update']
        )->name('streams.patch');

        Route::delete(
            '/streams/{stream}',
            [StreamController::class, 'destroy']
        )->name('streams.destroy');


        /*
        |--------------------------------------------------------------------------
        | Digital Library
        |--------------------------------------------------------------------------
        |
        | Currently presentation routes.
        | These can later be replaced by LibraryController.
        |
        */

        Route::view(
            '/library',
            'school.library.index'
        )->name('library.index');

        Route::view(
            '/library/{resource}',
            'school.library.show'
        )->name('library.show');


        /*
        |--------------------------------------------------------------------------
        | Assignments
        |--------------------------------------------------------------------------
        */

        Route::resource(
            'assignments',
            AssignmentController::class
        );


        /*
        |--------------------------------------------------------------------------
        | Subscription & Billing
        |--------------------------------------------------------------------------
        */

        Route::view(
            '/subscription',
            'school.subscription.index'
        )->name('subscription.index');

        Route::view(
            '/subscription/plans',
            'school.subscription.plans'
        )->name('subscription.plans');

        Route::view(
            '/subscription/checkout',
            'school.subscription.checkout'
        )->name('subscription.checkout');

        Route::view(
            '/subscription/payments',
            'school.subscription.payments'
        )->name('subscription.payments');

        Route::view(
            '/subscription/invoice',
            'school.subscription.invoice'
        )->name('subscription.invoice');


        /*
        |--------------------------------------------------------------------------
        | Reports
        |--------------------------------------------------------------------------
        */

        Route::view(
            '/reports',
            'school.reports.index'
        )->name('reports.index');

        Route::view(
            '/reports/students',
            'school.reports.students'
        )->name('reports.students');

        Route::view(
            '/reports/classes',
            'school.reports.classes'
        )->name('reports.classes');

        Route::view(
            '/reports/resources',
            'school.reports.resources'
        )->name('reports.resources');

        Route::view(
            '/reports/assignments',
            'school.reports.assignments'
        )->name('reports.assignments');

        Route::view(
            '/reports/licences',
            'school.reports.licences'
        )->name('reports.licences');

    });


/*
|--------------------------------------------------------------------------
| Teacher Portal
|--------------------------------------------------------------------------
*/

Route::middleware([
    'auth',
    'role:teacher',
])
    ->prefix('teacher')
    ->name('teacher.')
    ->group(function () {

        /*
        |--------------------------------------------------------------------------
        | Teacher Dashboard
        |--------------------------------------------------------------------------
        */

        Route::view(
            '/',
            'dashboards.teacher'
        )->name('dashboard');


        /*
        |--------------------------------------------------------------------------
        | Teacher Classes
        |--------------------------------------------------------------------------
        |
        | Placeholder pages for now.
        |
        */

        Route::view(
            '/classes',
            'teacher.classes.index'
        )->name('classes.index');


        /*
        |--------------------------------------------------------------------------
        | Teacher Library
        |--------------------------------------------------------------------------
        */

        Route::view(
            '/library',
            'teacher.library.index'
        )->name('library.index');


        /*
        |--------------------------------------------------------------------------
        | Teacher Reading Lists
        |--------------------------------------------------------------------------
        */

        Route::view(
            '/reading-lists',
            'teacher.reading-lists.index'
        )->name('reading-lists.index');


        /*
        |--------------------------------------------------------------------------
        | Teacher Assignments
        |--------------------------------------------------------------------------
        */

        Route::view(
            '/assignments',
            'teacher.assignments.index'
        )->name('assignments.index');


        /*
        |--------------------------------------------------------------------------
        | Teacher Students
        |--------------------------------------------------------------------------
        */

        Route::view(
            '/students',
            'teacher.students.index'
        )->name('students.index');


        /*
        |--------------------------------------------------------------------------
        | Teacher Performance
        |--------------------------------------------------------------------------
        */

        Route::view(
            '/performance',
            'teacher.performance.index'
        )->name('performance.index');

    });


/*
|--------------------------------------------------------------------------
| Learner / Individual Subscriber Portal
|--------------------------------------------------------------------------
*/

Route::middleware([
    'auth',
    'role:student|individual_subscriber',
])
    ->prefix('library')
    ->name('library.')
    ->group(function () {

        /*
        |--------------------------------------------------------------------------
        | Learner Dashboard
        |--------------------------------------------------------------------------
        */

        Route::view(
            '/',
            'dashboards.library'
        )->name('dashboard');


        /*
        |--------------------------------------------------------------------------
        | Browse Library
        |--------------------------------------------------------------------------
        */

        Route::view(
            '/browse',
            'library.browse'
        )->name('browse');


        /*
        |--------------------------------------------------------------------------
        | Continue Reading
        |--------------------------------------------------------------------------
        */

        Route::view(
            '/continue-reading',
            'library.continue-reading'
        )->name('continue-reading');


        /*
        |--------------------------------------------------------------------------
        | Bookmarks
        |--------------------------------------------------------------------------
        */

        Route::view(
            '/bookmarks',
            'library.bookmarks'
        )->name('bookmarks');


        /*
        |--------------------------------------------------------------------------
        | Notes
        |--------------------------------------------------------------------------
        */

        Route::view(
            '/notes',
            'library.notes'
        )->name('notes');


        /*
        |--------------------------------------------------------------------------
        | Learner Assignments
        |--------------------------------------------------------------------------
        */

        Route::view(
            '/assignments',
            'library.assignments'
        )->name('assignments');


        /*
        |--------------------------------------------------------------------------
        | Reading Progress
        |--------------------------------------------------------------------------
        */

        Route::view(
            '/progress',
            'library.progress'
        )->name('progress');


        /*
        |--------------------------------------------------------------------------
        | Subscription
        |--------------------------------------------------------------------------
        */

        Route::view(
            '/subscription',
            'library.subscription'
        )->name('subscription');


        /*
        |--------------------------------------------------------------------------
        | Profile
        |--------------------------------------------------------------------------
        */

        Route::view(
            '/profile',
            'library.profile'
        )->name('profile');

    });