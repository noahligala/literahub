<?php

namespace App\Http\Controllers\School;

use App\Http\Controllers\Controller;
use App\Models\School;
use App\Models\SchoolClass;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ClassController extends Controller
{
    private function school(Request $request): School
    {
        return $request
            ->user()
            ->schools()
            ->firstOrFail();
    }

    private function schoolClass(
        School $school,
        int|string $id
    ): SchoolClass {
        return $school
            ->classes()
            ->whereKey($id)
            ->firstOrFail();
    }

    public function index(Request $request): View
    {
        $school = $this->school($request);

        $classes = $school
            ->classes()
            ->withCount([
                'students',
                'teachers',
                'streams',
                'assignments',
            ])
            ->orderBy('name')
            ->paginate(20);

        return view(
            'school.classes.index',
            compact(
                'school',
                'classes'
            )
        );
    }

    public function create(Request $request): View
    {
        $school = $this->school($request);

        return view(
            'school.classes.create',
            compact('school')
        );
    }

    public function store(
        Request $request
    ): RedirectResponse {
        $school = $this->school($request);

        $validated = $this->validateClass(
            $request
        );

        $class = $school
            ->classes()
            ->create($validated);

        return redirect()
            ->route(
                'school.classes.show',
                $class
            )
            ->with(
                'success',
                'Class created successfully.'
            );
    }

    public function show(
        Request $request,
        int $class
    ): View {
        $school = $this->school($request);

        $class = $this->schoolClass(
            $school,
            $class
        );

        $class->load([
            'streams.teacher',
            'students',
            'teachers',
        ]);

        $class->loadCount([
            'students',
            'teachers',
            'streams',
            'assignments',
        ]);

        return view(
            'school.classes.show',
            compact(
                'school',
                'class'
            )
        );
    }

    public function edit(
        Request $request,
        int $class
    ): View {
        $school = $this->school($request);

        $class = $this->schoolClass(
            $school,
            $class
        );

        return view(
            'school.classes.edit',
            compact(
                'school',
                'class'
            )
        );
    }

    public function update(
        Request $request,
        int $class
    ): RedirectResponse {
        $school = $this->school($request);

        $class = $this->schoolClass(
            $school,
            $class
        );

        $class->update(
            $this->validateClass(
                $request
            )
        );

        return redirect()
            ->route(
                'school.classes.show',
                $class
            )
            ->with(
                'success',
                'Class updated successfully.'
            );
    }

    public function destroy(
        Request $request,
        int $class
    ): RedirectResponse {
        $school = $this->school($request);

        $class = $this->schoolClass(
            $school,
            $class
        );

        $class->update([
            'status' => 'inactive',
        ]);

        return redirect()
            ->route('school.classes.index')
            ->with(
                'success',
                'Class archived.'
            );
    }

    private function validateClass(
        Request $request
    ): array {
        return $request->validate([
            'name' => [
                'required',
                'string',
                'max:150',
            ],

            'code' => [
                'nullable',
                'string',
                'max:50',
            ],

            'level' => [
                'nullable',
                'string',
                'max:100',
            ],

            'academic_year' => [
                'nullable',
                'string',
                'max:30',
            ],

            'status' => [
                'required',
                Rule::in([
                    'active',
                    'inactive',
                ]),
            ],
        ]);
    }
}