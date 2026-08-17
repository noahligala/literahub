<?php

namespace App\Http\Controllers\School;

use App\Http\Controllers\Controller;
use App\Models\School;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class TeacherController extends Controller
{
    private function school(Request $request): School
    {
        return $request
            ->user()
            ->schools()
            ->firstOrFail();
    }

    private function teacher(
        School $school,
        int|string $id
    ): User {
        return $school
            ->users()
            ->where('users.id', $id)
            ->wherePivot('role', 'teacher')
            ->firstOrFail();
    }

    public function index(Request $request): View
    {
        $school = $this->school($request);

        $teachers = $school
            ->users()
            ->wherePivot('role', 'teacher')
            ->with('teachingClasses')
            ->orderBy('users.name')
            ->paginate(20);

        return view(
            'school.teachers.index',
            compact(
                'school',
                'teachers'
            )
        );
    }

    public function create(Request $request): View
    {
        $school = $this->school($request);

        $classes = $school
            ->classes()
            ->where('status', 'active')
            ->orderBy('name')
            ->get();

        return view(
            'school.teachers.create',
            compact(
                'school',
                'classes'
            )
        );
    }

    public function store(
        Request $request
    ): RedirectResponse {
        $school = $this->school($request);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],

            'email' => [
                'required',
                'email',
                'unique:users,email',
            ],

            'phone' => [
                'nullable',
                'string',
                'max:30',
            ],

            'employee_number' => [
                'nullable',
                'string',
                'max:100',
            ],

            'password' => [
                'required',
                'string',
                'min:8',
                'confirmed',
            ],

            'status' => [
                'required',
                Rule::in([
                    'active',
                    'inactive',
                    'suspended',
                ]),
            ],

            'class_ids' => [
                'nullable',
                'array',
            ],

            'class_ids.*' => [
                Rule::exists(
                    'school_classes',
                    'id'
                )->where(
                    fn ($query) =>
                    $query->where(
                        'school_id',
                        $school->id
                    )
                ),
            ],
        ]);

        $teacher = DB::transaction(
            function () use (
                $validated,
                $school
            ) {
                $teacher = User::create([
                    'name' =>
                        $validated['name'],

                    'email' =>
                        $validated['email'],

                    'phone' =>
                        $validated['phone'] ?? null,

                    'password' =>
                        $validated['password'],

                    'status' =>
                        $validated['status'],
                ]);

                $teacher->assignRole('teacher');

                $school
                    ->users()
                    ->attach(
                        $teacher->id,
                        [
                            'role' => 'teacher',

                            'status' =>
                                $validated['status'],

                            'reference_number' =>
                                $validated[
                                    'employee_number'
                                ] ?? null,
                        ]
                    );

                $teacher
                    ->teachingClasses()
                    ->sync(
                        $validated[
                            'class_ids'
                        ] ?? []
                    );

                return $teacher;
            }
        );

        return redirect()
            ->route(
                'school.teachers.show',
                $teacher
            )
            ->with(
                'success',
                'Teacher created successfully.'
            );
    }

    public function show(
        Request $request,
        int $teacher
    ): View {
        $school = $this->school($request);

        $teacher = $this->teacher(
            $school,
            $teacher
        );

        $teacher->load(
            'teachingClasses'
        );

        return view(
            'school.teachers.show',
            compact(
                'school',
                'teacher'
            )
        );
    }

    public function edit(
        Request $request,
        int $teacher
    ): View {
        $school = $this->school($request);

        $teacher = $this->teacher(
            $school,
            $teacher
        );

        $teacher->load(
            'teachingClasses'
        );

        $classes = $school
            ->classes()
            ->orderBy('name')
            ->get();

        return view(
            'school.teachers.edit',
            compact(
                'school',
                'teacher',
                'classes'
            )
        );
    }

    public function update(
        Request $request,
        int $teacher
    ): RedirectResponse {
        $school = $this->school($request);

        $teacher = $this->teacher(
            $school,
            $teacher
        );

        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
            ],

            'email' => [
                'required',
                'email',

                Rule::unique(
                    'users',
                    'email'
                )->ignore($teacher->id),
            ],

            'phone' => [
                'nullable',
                'string',
                'max:30',
            ],

            'employee_number' => [
                'nullable',
                'string',
                'max:100',
            ],

            'password' => [
                'nullable',
                'string',
                'min:8',
                'confirmed',
            ],

            'status' => [
                'required',
                Rule::in([
                    'active',
                    'inactive',
                    'suspended',
                ]),
            ],

            'class_ids' => [
                'nullable',
                'array',
            ],

            'class_ids.*' => [
                Rule::exists(
                    'school_classes',
                    'id'
                )->where(
                    fn ($query) =>
                    $query->where(
                        'school_id',
                        $school->id
                    )
                ),
            ],
        ]);

        DB::transaction(
            function () use (
                $teacher,
                $school,
                $validated
            ) {
                $data = [
                    'name' =>
                        $validated['name'],

                    'email' =>
                        $validated['email'],

                    'phone' =>
                        $validated['phone'] ?? null,

                    'status' =>
                        $validated['status'],
                ];

                if (
                    !empty(
                        $validated['password']
                    )
                ) {
                    $data['password'] =
                        $validated['password'];
                }

                $teacher->update($data);

                $school
                    ->users()
                    ->updateExistingPivot(
                        $teacher->id,
                        [
                            'status' =>
                                $validated['status'],

                            'reference_number' =>
                                $validated[
                                    'employee_number'
                                ] ?? null,
                        ]
                    );

                $teacher
                    ->teachingClasses()
                    ->sync(
                        $validated[
                            'class_ids'
                        ] ?? []
                    );
            }
        );

        return redirect()
            ->route(
                'school.teachers.show',
                $teacher
            )
            ->with(
                'success',
                'Teacher updated successfully.'
            );
    }

    public function destroy(
        Request $request,
        int $teacher
    ): RedirectResponse {
        $school = $this->school($request);

        $teacher = $this->teacher(
            $school,
            $teacher
        );

        $school
            ->users()
            ->updateExistingPivot(
                $teacher->id,
                [
                    'status' => 'inactive',
                ]
            );

        $teacher->update([
            'status' => 'inactive',
        ]);

        return redirect()
            ->route('school.teachers.index')
            ->with(
                'success',
                'Teacher account deactivated.'
            );
    }
}