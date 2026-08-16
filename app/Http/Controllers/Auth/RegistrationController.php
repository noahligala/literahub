<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\StoreIndividualRegistrationRequest;
use App\Http\Requests\Auth\StoreSchoolRegistrationRequest;
use App\Models\School;
use App\Models\StudentProfile;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;

class RegistrationController extends Controller
{
    public function choose(): View
    {
        return view('auth.register');
    }

    public function school(): View
    {
        return view('auth.register-school');
    }

    public function student(): View
    {
        return view('auth.register-student');
    }

    public function storeSchool(
        StoreSchoolRegistrationRequest $request
    ): RedirectResponse {
        $validated = $request->validated();

        $user = DB::transaction(function () use ($validated) {
            $slug = $this->generateSchoolSlug(
                $validated['school_name']
            );

            $school = School::create([
                'name' => $validated['school_name'],

                'slug' => $slug,

                'registration_number' =>
                    $validated['registration_number'] ?? null,

                'type' => $validated['school_type'],

                'county' => $validated['county'] ?? null,

                'town' => $validated['town'] ?? null,

                'email' => $validated['school_email'] ?? null,

                'phone' => $validated['school_phone'] ?? null,

                /*
                 * New institutions should be reviewed by the
                 * platform before receiving institutional access.
                 */
                'status' => 'pending',
            ]);

            $user = User::create([
                'name' => $validated['name'],
                'email' => strtolower($validated['email']),
                'phone' => $validated['phone'] ?? null,
                'password' => $validated['password'],
                'status' => 'active',
            ]);

            $user->assignRole('school_admin');

            $school->users()->attach($user->id, [
                'role' => 'school_admin',
                'status' => 'active',
            ]);

            return $user;
        });

        Auth::login($user);

        $request->session()->regenerate();

        return redirect()
            ->route('dashboard')
            ->with(
                'success',
                'Your institution account has been created successfully.'
            );
    }

    public function storeStudent(
        StoreIndividualRegistrationRequest $request
    ): RedirectResponse {
        $validated = $request->validated();

        $user = DB::transaction(function () use ($validated) {
            $user = User::create([
                'name' => $validated['name'],
                'email' => strtolower($validated['email']),
                'phone' => $validated['phone'] ?? null,
                'password' => $validated['password'],
                'status' => 'active',
            ]);

            $user->assignRole('individual_subscriber');

            StudentProfile::create([
                'user_id' => $user->id,

                'education_level' =>
                    $validated['education_level'],

                'institution_name' =>
                    $validated['institution_name'] ?? null,

                'county' =>
                    $validated['county'] ?? null,

                'town' =>
                    $validated['town'] ?? null,

                'date_of_birth' =>
                    $validated['date_of_birth'] ?? null,
            ]);

            return $user;
        });

        Auth::login($user);

        $request->session()->regenerate();

        return redirect()
            ->route('dashboard')
            ->with(
                'success',
                'Your LiteraHub account has been created successfully.'
            );
    }

    private function generateSchoolSlug(string $name): string
    {
        $baseSlug = Str::slug($name);

        if ($baseSlug === '') {
            $baseSlug = 'institution';
        }

        $slug = $baseSlug;

        $counter = 2;

        while (
            School::query()
                ->where('slug', $slug)
                ->exists()
        ) {
            $slug = $baseSlug.'-'.$counter;

            $counter++;
        }

        return $slug;
    }
}