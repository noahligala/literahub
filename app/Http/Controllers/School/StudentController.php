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

class StudentController extends Controller
{
    private function school(Request $request): School
    {
        return $request
            ->user()
            ->schools()
            ->firstOrFail();
    }

    private function student(
        School $school,
        int|string $id
    ): User {
        return $school
            ->users()
            ->where('users.id', $id)
            ->wherePivot('role', 'student')
            ->firstOrFail();
    }

    public function index(Request $request): View
    {
        $school = $this->school($request);

        $students = $school
            ->users()
            ->wherePivot('role', 'student')
            ->with('studentClasses')
            ->orderBy('users.name')
            ->paginate(20);

        return view(
            'school.students.index',
            compact(
                'school',
                'students'
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
            'school.students.create',
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
            'name' => [
                'required',
                'string',
                'max:255',
            ],

            'email' => [
                'required',
                'email',
                'max:255',
                'unique:users,email',
            ],

            'phone' => [
                'nullable',
                'string',
                'max:30',
            ],

            'password' => [
                'required',
                'string',
                'min:8',
                'confirmed',
            ],

            'admission_number' => [
                'required',
                'string',
                'max:100',
            ],

            'school_class_id' => [
                'nullable',
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

            'status' => [
                'required',
                Rule::in([
                    'active',
                    'inactive',
                    'suspended',
                ]),
            ],
        ]);

        $student = DB::transaction(
            function () use (
                $validated,
                $school
            ) {
                $student = User::create([
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

                $student->assignRole('student');

                $school
                    ->users()
                    ->attach(
                        $student->id,
                        [
                            'role' => 'student',

                            'status' =>
                                $validated['status'],

                            'reference_number' =>
                                $validated[
                                    'admission_number'
                                ],
                        ]
                    );

                if (
                    !empty(
                        $validated[
                            'school_class_id'
                        ]
                    )
                ) {
                    $student
                        ->studentClasses()
                        ->attach(
                            $validated[
                                'school_class_id'
                            ]
                        );
                }

                return $student;
            }
        );

        return redirect()
            ->route(
                'school.students.show',
                $student
            )
            ->with(
                'success',
                'Student created successfully.'
            );
    }

    public function show(
        Request $request,
        int $student
    ): View {
        $school = $this->school($request);

        $student = $this->student(
            $school,
            $student
        );

        $student->load([
            'studentClasses.streams',
        ]);

        return view(
            'school.students.show',
            compact(
                'school',
                'student'
            )
        );
    }

    public function edit(
        Request $request,
        int $student
    ): View {
        $school = $this->school($request);

        $student = $this->student(
            $school,
            $student
        );

        $classes = $school
            ->classes()
            ->orderBy('name')
            ->get();

        return view(
            'school.students.edit',
            compact(
                'school',
                'student',
                'classes'
            )
        );
    }

    public function update(
        Request $request,
        int $student
    ): RedirectResponse {
        $school = $this->school($request);

        $student = $this->student(
            $school,
            $student
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
                'max:255',
                Rule::unique(
                    'users',
                    'email'
                )->ignore($student->id),
            ],

            'phone' => [
                'nullable',
                'string',
                'max:30',
            ],

            'password' => [
                'nullable',
                'string',
                'min:8',
                'confirmed',
            ],

            'admission_number' => [
                'required',
                'string',
                'max:100',
            ],

            'school_class_id' => [
                'nullable',
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

            'status' => [
                'required',
                Rule::in([
                    'active',
                    'inactive',
                    'suspended',
                ]),
            ],
        ]);

        DB::transaction(
            function () use (
                $student,
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

                $student->update($data);

                $school
                    ->users()
                    ->updateExistingPivot(
                        $student->id,
                        [
                            'status' =>
                                $validated['status'],

                            'reference_number' =>
                                $validated[
                                    'admission_number'
                                ],
                        ]
                    );

                $student
                    ->studentClasses()
                    ->sync(
                        !empty(
                            $validated[
                                'school_class_id'
                            ]
                        )
                            ? [
                                $validated[
                                    'school_class_id'
                                ],
                            ]
                            : []
                    );
            }
        );

        return redirect()
            ->route(
                'school.students.show',
                $student
            )
            ->with(
                'success',
                'Student updated successfully.'
            );
    }

    public function destroy(
        Request $request,
        int $student
    ): RedirectResponse {
        $school = $this->school($request);

        $student = $this->student(
            $school,
            $student
        );

        $school
            ->users()
            ->updateExistingPivot(
                $student->id,
                [
                    'status' => 'inactive',
                ]
            );

        $student->update([
            'status' => 'inactive',
        ]);

        return redirect()
            ->route('school.students.index')
            ->with(
                'success',
                'Student account deactivated.'
            );
    }
}