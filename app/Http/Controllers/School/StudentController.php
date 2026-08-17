<?php

namespace App\Http\Controllers\School;

use App\Http\Controllers\Controller;
use App\Models\School;
use App\Models\Stream;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class StudentController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Resolve School
    |--------------------------------------------------------------------------
    */

    private function school(Request $request): School
    {
        return $request
            ->user()
            ->schools()
            ->firstOrFail();
    }


    /*
    |--------------------------------------------------------------------------
    | Resolve School Student
    |--------------------------------------------------------------------------
    |
    | Ensures the requested student actually belongs to the authenticated
    | school and has a student membership within that institution.
    |
    */

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


    /*
    |--------------------------------------------------------------------------
    | Students Directory
    |--------------------------------------------------------------------------
    */

    public function index(Request $request): View
    {
        $school = $this->school($request);

        $search = trim(
            (string) $request->query(
                'search',
                ''
            )
        );


        /*
        |--------------------------------------------------------------------------
        | Base Query
        |--------------------------------------------------------------------------
        */

        $studentQuery = $school
            ->users()
            ->wherePivot('role', 'student');


        /*
        |--------------------------------------------------------------------------
        | Metrics
        |--------------------------------------------------------------------------
        */

        $totalStudents = (clone $studentQuery)
            ->count();

        $activeStudents = (clone $studentQuery)
            ->wherePivot('status', 'active')
            ->count();

        $inactiveStudents = (clone $studentQuery)
            ->whereIn(
                'school_user.status',
                [
                    'inactive',
                    'suspended',
                ]
            )
            ->count();


        /*
        |--------------------------------------------------------------------------
        | Student Listing
        |--------------------------------------------------------------------------
        */

        $students = $school
            ->users()
            ->wherePivot('role', 'student')
            ->with([
                'studentClasses.streams',
            ])
            ->when(
                $search !== '',
                function ($query) use ($search) {
                    $query->where(
                        function ($query) use ($search) {
                            $query
                                ->where(
                                    'users.name',
                                    'like',
                                    "%{$search}%"
                                )
                                ->orWhere(
                                    'users.email',
                                    'like',
                                    "%{$search}%"
                                )
                                ->orWhere(
                                    'users.phone',
                                    'like',
                                    "%{$search}%"
                                )
                                ->orWhere(
                                    'school_user.reference_number',
                                    'like',
                                    "%{$search}%"
                                );
                        }
                    );
                }
            )
            ->orderBy('users.name')
            ->paginate(20)
            ->withQueryString();


        return view(
            'school.students.index',
            compact(
                'school',
                'students',
                'totalStudents',
                'activeStudents',
                'inactiveStudents'
            )
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Create Student
    |--------------------------------------------------------------------------
    */

    public function create(Request $request): View
    {
        $school = $this->school($request);

        $classes = $school
            ->classes()
            ->where('status', 'active')
            ->with([
                'streams' => fn ($query) =>
                    $query
                        ->where('status', 'active')
                        ->orderBy('name'),
            ])
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


    /*
    |--------------------------------------------------------------------------
    | Store Student
    |--------------------------------------------------------------------------
    */

    public function store(
        Request $request
    ): RedirectResponse {
        $school = $this->school($request);


        /*
        |--------------------------------------------------------------------------
        | Validation
        |--------------------------------------------------------------------------
        */

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

            'stream_id' => [
                'nullable',
                Rule::exists(
                    'streams',
                    'id'
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


        /*
        |--------------------------------------------------------------------------
        | Resolve Stream / Class
        |--------------------------------------------------------------------------
        |
        | A stream always belongs to a class. Therefore when a stream is
        | supplied we resolve its class server-side instead of trusting the
        | class ID submitted by the browser.
        |
        */

        $validated = $this->resolveClassAndStream(
            $school,
            $validated
        );


        /*
        |--------------------------------------------------------------------------
        | Create Student
        |--------------------------------------------------------------------------
        */

        $student = DB::transaction(
            function () use (
                $validated,
                $school
            ) {
                /*
                |--------------------------------------------------------------------------
                | User Account
                |--------------------------------------------------------------------------
                */

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


                /*
                |--------------------------------------------------------------------------
                | Global LiteraHub Role
                |--------------------------------------------------------------------------
                */

                $student->assignRole(
                    'student'
                );


                /*
                |--------------------------------------------------------------------------
                | School Membership
                |--------------------------------------------------------------------------
                */

                $school
                    ->users()
                    ->attach(
                        $student->id,
                        [
                            'role' =>
                                'student',

                            'status' =>
                                $validated['status'],

                            'reference_number' =>
                                $validated[
                                    'admission_number'
                                ],
                        ]
                    );


                /*
                |--------------------------------------------------------------------------
                | Class / Stream Membership
                |--------------------------------------------------------------------------
                */

                $this->syncStudentPlacement(
                    $student,
                    $validated
                );


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


    /*
    |--------------------------------------------------------------------------
    | Show Student
    |--------------------------------------------------------------------------
    */

    public function show(
        Request $request,
        int $student
    ): View {
        $school = $this->school($request);

        $student = $this->student(
            $school,
            $student
        );


        /*
        |--------------------------------------------------------------------------
        | Relationships
        |--------------------------------------------------------------------------
        */

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


    /*
    |--------------------------------------------------------------------------
    | Edit Student
    |--------------------------------------------------------------------------
    */

    public function edit(
        Request $request,
        int $student
    ): View {
        $school = $this->school($request);

        $student = $this->student(
            $school,
            $student
        );


        /*
        |--------------------------------------------------------------------------
        | Existing Student Placement
        |--------------------------------------------------------------------------
        |
        | Important because the Blade component reads stream_id from the
        | class_student pivot.
        |
        */

        $student->load([
            'studentClasses',
        ]);


        /*
        |--------------------------------------------------------------------------
        | Available Classes / Streams
        |--------------------------------------------------------------------------
        */

        $classes = $school
            ->classes()
            ->with([
                'streams' => fn ($query) =>
                    $query->orderBy('name'),
            ])
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


    /*
    |--------------------------------------------------------------------------
    | Update Student
    |--------------------------------------------------------------------------
    */

    public function update(
        Request $request,
        int $student
    ): RedirectResponse {
        $school = $this->school($request);

        $student = $this->student(
            $school,
            $student
        );


        /*
        |--------------------------------------------------------------------------
        | Validation
        |--------------------------------------------------------------------------
        */

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
                )->ignore(
                    $student->id
                ),
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

            'stream_id' => [
                'nullable',

                Rule::exists(
                    'streams',
                    'id'
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


        /*
        |--------------------------------------------------------------------------
        | Resolve Stream / Class
        |--------------------------------------------------------------------------
        */

        $validated = $this->resolveClassAndStream(
            $school,
            $validated
        );


        /*
        |--------------------------------------------------------------------------
        | Update Student
        |--------------------------------------------------------------------------
        */

        DB::transaction(
            function () use (
                $student,
                $school,
                $validated
            ) {
                /*
                |--------------------------------------------------------------------------
                | User Account
                |--------------------------------------------------------------------------
                */

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


                /*
                |--------------------------------------------------------------------------
                | Password
                |--------------------------------------------------------------------------
                */

                if (
                    !empty(
                        $validated['password']
                    )
                ) {
                    $data['password'] =
                        $validated['password'];
                }


                $student->update(
                    $data
                );


                /*
                |--------------------------------------------------------------------------
                | School Membership
                |--------------------------------------------------------------------------
                */

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


                /*
                |--------------------------------------------------------------------------
                | Class / Stream Placement
                |--------------------------------------------------------------------------
                */

                $this->syncStudentPlacement(
                    $student,
                    $validated
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


    /*
    |--------------------------------------------------------------------------
    | Deactivate Student
    |--------------------------------------------------------------------------
    */

    public function destroy(
        Request $request,
        int $student
    ): RedirectResponse {
        $school = $this->school($request);

        $student = $this->student(
            $school,
            $student
        );


        DB::transaction(
            function () use (
                $student,
                $school
            ) {
                /*
                |--------------------------------------------------------------------------
                | School Membership
                |--------------------------------------------------------------------------
                */

                $school
                    ->users()
                    ->updateExistingPivot(
                        $student->id,
                        [
                            'status' =>
                                'inactive',
                        ]
                    );


                /*
                |--------------------------------------------------------------------------
                | User Account
                |--------------------------------------------------------------------------
                */

                $student->update([
                    'status' =>
                        'inactive',
                ]);
            }
        );


        return redirect()
            ->route(
                'school.students.index'
            )
            ->with(
                'success',
                'Student account deactivated.'
            );
    }


    /*
    |--------------------------------------------------------------------------
    | Resolve Class and Stream
    |--------------------------------------------------------------------------
    |
    | Rules:
    |
    | 1. Student may have no class.
    | 2. Student may belong to a class without a stream.
    | 3. Student may belong to a class + stream.
    | 4. A submitted stream must belong to this school.
    | 5. A stream automatically determines its parent class.
    |
    */

    private function resolveClassAndStream(
        School $school,
        array $validated
    ): array {
        if (
            empty(
                $validated['stream_id']
            )
        ) {
            /*
             * No stream selected.
             *
             * Keep the submitted class as-is.
             */
            $validated['stream_id'] =
                null;

            return $validated;
        }


        /*
        |--------------------------------------------------------------------------
        | Resolve Stream
        |--------------------------------------------------------------------------
        */

        $stream = Stream::query()
            ->whereKey(
                $validated['stream_id']
            )
            ->whereHas(
                'schoolClass',
                function ($query) use ($school) {
                    $query->where(
                        'school_id',
                        $school->id
                    );
                }
            )
            ->firstOrFail();


        /*
        |--------------------------------------------------------------------------
        | Stream Determines Class
        |--------------------------------------------------------------------------
        */

        $validated['school_class_id'] =
            $stream->school_class_id;


        return $validated;
    }


    /*
    |--------------------------------------------------------------------------
    | Synchronize Student Placement
    |--------------------------------------------------------------------------
    |
    | The class_student table represents the student's current academic
    | placement:
    |
    | school_class_id -> required when assigned
    | stream_id       -> optional
    |
    */

    private function syncStudentPlacement(
        User $student,
        array $validated
    ): void {
        /*
        |--------------------------------------------------------------------------
        | No Class
        |--------------------------------------------------------------------------
        */

        if (
            empty(
                $validated[
                    'school_class_id'
                ]
            )
        ) {
            $student
                ->studentClasses()
                ->detach();

            return;
        }


        /*
        |--------------------------------------------------------------------------
        | Class / Optional Stream
        |--------------------------------------------------------------------------
        */

        $student
            ->studentClasses()
            ->sync([
                $validated[
                    'school_class_id'
                ] => [
                    'stream_id' =>
                        $validated[
                            'stream_id'
                        ] ?? null,
                ],
            ]);
    }
}