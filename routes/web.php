<?php

use App\Http\Controllers\Auth\RegistrationController;
use App\Http\Controllers\DashboardController;

use App\Http\Controllers\AuthorController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\BookLicenseController;
use App\Http\Controllers\BookReviewController;
use App\Http\Controllers\PublisherController;
use App\Http\Controllers\ReaderController;

use App\Http\Controllers\School\AssignmentController;
use App\Http\Controllers\School\BookAccessRequestController;
use App\Http\Controllers\School\BookLicenseController as SchoolBookLicenseController;
use App\Http\Controllers\School\ClassController;
use App\Http\Controllers\School\LibraryController;
use App\Http\Controllers\School\StreamController;
use App\Http\Controllers\School\StudentController;
use App\Http\Controllers\School\TeacherController;

use Illuminate\Support\Facades\Route;


/*
|--------------------------------------------------------------------------
| Public
|--------------------------------------------------------------------------
*/

Route::view('/', 'welcome')
    ->name('home');

Route::view('/pricing', 'pricing')
    ->name('pricing');


/*
|--------------------------------------------------------------------------
| Registration
|--------------------------------------------------------------------------
*/

Route::middleware('guest')
    ->group(function () {

        Route::get(
            '/register',
            [
                RegistrationController::class,
                'choose',
            ]
        )->name('register');


        Route::get(
            '/register/school',
            [
                RegistrationController::class,
                'school',
            ]
        )->name(
            'register.school'
        );


        Route::post(
            '/register/school',
            [
                RegistrationController::class,
                'storeSchool',
            ]
        )
            ->middleware('throttle:10,1')
            ->name(
                'register.school.store'
            );


        Route::get(
            '/register/student',
            [
                RegistrationController::class,
                'student',
            ]
        )->name(
            'register.student'
        );


        Route::post(
            '/register/student',
            [
                RegistrationController::class,
                'storeStudent',
            ]
        )
            ->middleware('throttle:10,1')
            ->name(
                'register.student.store'
            );
    });


/*
|--------------------------------------------------------------------------
| Central Dashboard Router
|--------------------------------------------------------------------------
|
| All authenticated users are sent here after login.
| DashboardController determines the correct portal.
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
| Super Administrator
|--------------------------------------------------------------------------
*/

Route::middleware([
    'auth',
    'role:super_admin',
])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        Route::view(
            '/',
            'dashboards.superadmin'
        )->name('dashboard');
    });


/*
|--------------------------------------------------------------------------
| Platform Administrator
|--------------------------------------------------------------------------
|
| Important:
| Platform admin no longer shares /admin with super admin.
|
*/

Route::middleware([
    'auth',
    'role:platform_admin',
])
    ->prefix('platform')
    ->name('platform.')
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
| Global Catalogue Management
|--------------------------------------------------------------------------
*/

Route::middleware([
    'auth',
    'role:super_admin|platform_admin|content_manager|author',
])
    ->group(function () {

        Route::resource(
            'publishers',
            PublisherController::class
        );

        Route::resource(
            'authors',
            AuthorController::class
        );

        Route::resource(
            'books',
            BookController::class
        );
    });


/*
|--------------------------------------------------------------------------
| Book Review Workflow
|--------------------------------------------------------------------------
*/

Route::middleware([
    'auth',
    'role:super_admin|platform_admin|content_manager',
])
    ->prefix('book-reviews')
    ->name('book-reviews.')
    ->group(function () {

        Route::get(
            '/',
            [
                BookReviewController::class,
                'index',
            ]
        )->name('index');


        Route::get(
            '/{book}',
            [
                BookReviewController::class,
                'show',
            ]
        )->name('show');


        Route::patch(
            '/{book}/approve',
            [
                BookReviewController::class,
                'approve',
            ]
        )->name('approve');


        Route::patch(
            '/{book}/publish',
            [
                BookReviewController::class,
                'publish',
            ]
        )->name('publish');


        Route::patch(
            '/{book}/request-changes',
            [
                BookReviewController::class,
                'requestChanges',
            ]
        )->name(
            'request-changes'
        );


        Route::patch(
            '/{book}/reject',
            [
                BookReviewController::class,
                'reject',
            ]
        )->name('reject');
    });


/*
|--------------------------------------------------------------------------
| Book Licence Administration
|--------------------------------------------------------------------------
*/

Route::middleware([
    'auth',
    'role:super_admin|platform_admin|content_manager|author|finance',
])
    ->group(function () {

        Route::resource(
            'book-licenses',
            BookLicenseController::class
        )->except([
            'destroy',
        ]);


        Route::patch(
            '/book-licenses/{bookLicense}/revoke',
            [
                BookLicenseController::class,
                'revoke',
            ]
        )->name(
            'book-licenses.revoke'
        );


        Route::post(
            '/book-licenses/{bookLicense}/renew',
            [
                BookLicenseController::class,
                'renew',
            ]
        )->name(
            'book-licenses.renew'
        );
    });


/*
|--------------------------------------------------------------------------
| Protected Reader
|--------------------------------------------------------------------------
*/

Route::middleware('auth')
    ->prefix('reader')
    ->name('reader.')
    ->group(function () {

        Route::get(
            '/{book}',
            [
                ReaderController::class,
                'show',
            ]
        )->name('show');


        Route::get(
            '/{book}/stream',
            [
                ReaderController::class,
                'stream',
            ]
        )
            ->middleware('throttle:120,1')
            ->name('stream');


        Route::get(
            '/{book}/download',
            [
                ReaderController::class,
                'download',
            ]
        )
            ->middleware('throttle:20,1')
            ->name('download');


        Route::get(
            '/{book}/print',
            [
                ReaderController::class,
                'printSource',
            ]
        )
            ->middleware('throttle:20,1')
            ->name('print');
    });


/*
|--------------------------------------------------------------------------
| Shared School Library
|--------------------------------------------------------------------------
|
| School administrators, teachers and students may enter the institutional
| library, while school management functions remain school_admin only.
|
*/

Route::middleware([
    'auth',
    'role:school_admin|teacher|student',
])
    ->prefix('school/library')
    ->name('school.library.')
    ->group(function () {

        /*
        |--------------------------------------------------------------------------
        | Catalogue
        |--------------------------------------------------------------------------
        */

        Route::get(
            '/',
            [
                LibraryController::class,
                'index',
            ]
        )->name('index');


        /*
        |--------------------------------------------------------------------------
        | Access Requests
        |--------------------------------------------------------------------------
        */

        Route::get(
            '/requests',
            [
                BookAccessRequestController::class,
                'index',
            ]
        )->name(
            'requests.index'
        );


        Route::get(
            '/requests/{accessRequest}',
            [
                BookAccessRequestController::class,
                'show',
            ]
        )->name(
            'requests.show'
        );


        Route::patch(
            '/requests/{accessRequest}/approve',
            [
                BookAccessRequestController::class,
                'approve',
            ]
        )->name(
            'requests.approve'
        );


        Route::patch(
            '/requests/{accessRequest}/reject',
            [
                BookAccessRequestController::class,
                'reject',
            ]
        )->name(
            'requests.reject'
        );


        Route::delete(
            '/requests/{accessRequest}',
            [
                BookAccessRequestController::class,
                'destroy',
            ]
        )->name(
            'requests.destroy'
        );


        /*
        |--------------------------------------------------------------------------
        | Book
        |--------------------------------------------------------------------------
        */

        Route::get(
            '/books/{book}',
            [
                LibraryController::class,
                'show',
            ]
        )->name('show');


        Route::post(
            '/books/{book}/borrow',
            [
                LibraryController::class,
                'borrow',
            ]
        )->name('borrow');


        Route::patch(
            '/books/{book}/return',
            [
                LibraryController::class,
                'returnBook',
            ]
        )->name('return');


        Route::post(
            '/books/{book}/bookmarks',
            [
                LibraryController::class,
                'bookmark',
            ]
        )->name(
            'bookmarks.store'
        );


        Route::post(
            '/books/{book}/request-access',
            [
                BookAccessRequestController::class,
                'store',
            ]
        )->name(
            'requests.store'
        );
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
        | Dashboard
        |--------------------------------------------------------------------------
        */

        Route::view(
            '/',
            'dashboards.school'
        )->name('dashboard');


        /*
        |--------------------------------------------------------------------------
        | Institution
        |--------------------------------------------------------------------------
        */

        Route::view(
            '/profile',
            'school.profile.show'
        )->name(
            'profile.show'
        );


        Route::view(
            '/profile/edit',
            'school.profile.edit'
        )->name(
            'profile.edit'
        );


        /*
        |--------------------------------------------------------------------------
        | Students
        |--------------------------------------------------------------------------
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
        */

        Route::resource(
            'classes',
            ClassController::class
        );


        /*
        |--------------------------------------------------------------------------
        | Streams
        |--------------------------------------------------------------------------
        */

        Route::get(
            '/classes/{class}/streams/create',
            [
                StreamController::class,
                'create',
            ]
        )->name(
            'streams.create'
        );


        Route::post(
            '/classes/{class}/streams',
            [
                StreamController::class,
                'store',
            ]
        )->name(
            'streams.store'
        );


        Route::get(
            '/streams/{stream}/edit',
            [
                StreamController::class,
                'edit',
            ]
        )->name(
            'streams.edit'
        );


        Route::put(
            '/streams/{stream}',
            [
                StreamController::class,
                'update',
            ]
        )->name(
            'streams.update'
        );


        Route::patch(
            '/streams/{stream}',
            [
                StreamController::class,
                'update',
            ]
        )->name(
            'streams.patch'
        );


        Route::delete(
            '/streams/{stream}',
            [
                StreamController::class,
                'destroy',
            ]
        )->name(
            'streams.destroy'
        );


        /*
        |--------------------------------------------------------------------------
        | School Book Licences
        |--------------------------------------------------------------------------
        */

        Route::get(
            '/library/licenses',
            [
                SchoolBookLicenseController::class,
                'index',
            ]
        )->name(
            'library.licenses.index'
        );


        /*
         * Must remain before /licenses/{license}.
         */
        Route::get(
            '/library/licenses/catalogue',
            [
                SchoolBookLicenseController::class,
                'catalogue',
            ]
        )->name(
            'library.licenses.catalogue'
        );


        Route::post(
            '/library/licenses/request/{book}',
            [
                SchoolBookLicenseController::class,
                'requestLicense',
            ]
        )->name(
            'library.licenses.request'
        );


        Route::get(
            '/library/licenses/{license}',
            [
                SchoolBookLicenseController::class,
                'show',
            ]
        )->name(
            'library.licenses.show'
        );


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
        | Subscription
        |--------------------------------------------------------------------------
        */

        Route::view(
            '/subscription',
            'school.subscription.index'
        )->name(
            'subscription.index'
        );


        Route::view(
            '/subscription/plans',
            'school.subscription.plans'
        )->name(
            'subscription.plans'
        );


        Route::view(
            '/subscription/checkout',
            'school.subscription.checkout'
        )->name(
            'subscription.checkout'
        );


        Route::view(
            '/subscription/payments',
            'school.subscription.payments'
        )->name(
            'subscription.payments'
        );


        Route::view(
            '/subscription/invoice',
            'school.subscription.invoice'
        )->name(
            'subscription.invoice'
        );


        /*
        |--------------------------------------------------------------------------
        | Reports
        |--------------------------------------------------------------------------
        */

        Route::view(
            '/reports',
            'school.reports.index'
        )->name(
            'reports.index'
        );


        Route::view(
            '/reports/students',
            'school.reports.students'
        )->name(
            'reports.students'
        );


        Route::view(
            '/reports/classes',
            'school.reports.classes'
        )->name(
            'reports.classes'
        );


        Route::view(
            '/reports/resources',
            'school.reports.resources'
        )->name(
            'reports.resources'
        );


        Route::view(
            '/reports/assignments',
            'school.reports.assignments'
        )->name(
            'reports.assignments'
        );


        Route::view(
            '/reports/licences',
            'school.reports.licences'
        )->name(
            'reports.licences'
        );
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

        Route::view(
            '/',
            'dashboards.teacher'
        )->name('dashboard');


        Route::view(
            '/classes',
            'teacher.classes.index'
        )->name(
            'classes.index'
        );


        /*
         * Teacher library shares the institutional library.
         */

        Route::redirect(
            '/library',
            '/school/library'
        )->name(
            'library.index'
        );


        Route::redirect(
            '/library/requests',
            '/school/library/requests'
        )->name(
            'library.requests'
        );


        Route::view(
            '/reading-lists',
            'teacher.reading-lists.index'
        )->name(
            'reading-lists.index'
        );


        Route::view(
            '/assignments',
            'teacher.assignments.index'
        )->name(
            'assignments.index'
        );


        Route::view(
            '/students',
            'teacher.students.index'
        )->name(
            'students.index'
        );


        Route::view(
            '/performance',
            'teacher.performance.index'
        )->name(
            'performance.index'
        );
    });


/*
|--------------------------------------------------------------------------
| Learner / Individual Subscriber Portal
|--------------------------------------------------------------------------
|
| Individual subscribers use this portal directly.
|
| School students are redirected to /school/library by DashboardController.
|
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


        Route::view(
            '/browse',
            'library.browse'
        )->name('browse');


        Route::view(
            '/continue-reading',
            'library.continue-reading'
        )->name(
            'continue-reading'
        );


        Route::view(
            '/bookmarks',
            'library.bookmarks'
        )->name(
            'bookmarks'
        );


        Route::view(
            '/notes',
            'library.notes'
        )->name(
            'notes'
        );


        Route::view(
            '/assignments',
            'library.assignments'
        )->name(
            'assignments'
        );


        Route::view(
            '/progress',
            'library.progress'
        )->name(
            'progress'
        );


        Route::view(
            '/subscription',
            'library.subscription'
        )->name(
            'subscription'
        );


        Route::view(
            '/profile',
            'library.profile'
        )->name(
            'profile'
        );
    });