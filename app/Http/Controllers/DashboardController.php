<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __invoke(Request $request): RedirectResponse
    {
        $user = $request->user();

        if (
            $user->hasAnyRole([
                'super_admin',
                'platform_admin',
            ])
        ) {
            return redirect()->route('admin.dashboard');
        }

        if (
            $user->hasAnyRole([
                'content_manager',
                'author',
                'finance',
                'support',
            ])
        ) {
            return redirect()->route('staff.dashboard');
        }

        if ($user->hasRole('school_admin')) {
            return redirect()->route('school.dashboard');
        }

        if ($user->hasRole('teacher')) {
            return redirect()->route('teacher.dashboard');
        }

        if (
            $user->hasAnyRole([
                'student',
                'individual_subscriber',
            ])
        ) {
            return redirect()->route('library.dashboard');
        }

        abort(
            403,
            'Your account does not have an assigned LiteraHub role.'
        );
    }
}