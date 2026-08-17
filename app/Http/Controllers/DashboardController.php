<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __invoke(
        Request $request
    ): RedirectResponse {
        $user = $request->user();


        /*
        |--------------------------------------------------------------------------
        | Super Administrator
        |--------------------------------------------------------------------------
        |
        | Highest-level platform administrator.
        |
        */

        if (
            $user->hasRole(
                'super_admin'
            )
        ) {
            return redirect()
                ->route(
                    'admin.dashboard'
                );
        }


        /*
        |--------------------------------------------------------------------------
        | Platform Administrator
        |--------------------------------------------------------------------------
        |
        | Platform administrators now use their own dashboard route instead
        | of sharing /admin with the super administrator.
        |
        */

        if (
            $user->hasRole(
                'platform_admin'
            )
        ) {
            return redirect()
                ->route(
                    'platform.dashboard'
                );
        }


        /*
        |--------------------------------------------------------------------------
        | School Administrator
        |--------------------------------------------------------------------------
        */

        if (
            $user->hasRole(
                'school_admin'
            )
        ) {
            return redirect()
                ->route(
                    'school.dashboard'
                );
        }


        /*
        |--------------------------------------------------------------------------
        | Teacher
        |--------------------------------------------------------------------------
        */

        if (
            $user->hasRole(
                'teacher'
            )
        ) {
            return redirect()
                ->route(
                    'teacher.dashboard'
                );
        }


        /*
        |--------------------------------------------------------------------------
        | School Student
        |--------------------------------------------------------------------------
        |
        | Students attached to a school should enter the institution's
        | licensed digital library rather than the individual subscriber
        | portal.
        |
        */

        if (
            $user->hasRole('student')
            && $user
                ->schools()
                ->exists()
        ) {
            return redirect()
                ->route(
                    'school.library.index'
                );
        }


        /*
        |--------------------------------------------------------------------------
        | Individual Student / Subscriber
        |--------------------------------------------------------------------------
        |
        | A student without a school relationship is treated as an individual
        | learner.
        |
        */

        if (
            $user->hasAnyRole([
                'student',
                'individual_subscriber',
            ])
        ) {
            return redirect()
                ->route(
                    'library.dashboard'
                );
        }


        /*
        |--------------------------------------------------------------------------
        | Platform Staff
        |--------------------------------------------------------------------------
        |
        | These roles share the staff portal.
        |
        */

        if (
            $user->hasAnyRole([
                'content_manager',
                'author',
                'finance',
                'support',
            ])
        ) {
            return redirect()
                ->route(
                    'staff.dashboard'
                );
        }


        /*
        |--------------------------------------------------------------------------
        | Unknown Role
        |--------------------------------------------------------------------------
        */

        abort(
            403,
            'Your account does not have an assigned LiteraHub role.'
        );
    }
}