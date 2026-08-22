<?php

namespace App\Http\Controllers\School;

use App\Http\Controllers\Controller;
use App\Models\Assignment;
use App\Models\Book;
use App\Models\School;
use App\Services\Library\BookLicenseService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AssignmentController extends Controller
{
    public function __construct(
        private readonly BookLicenseService $licenses
    ) {
    }


    /*
    |--------------------------------------------------------------------------
    | Resolve Active School
    |--------------------------------------------------------------------------
    */

    private function school(
        Request $request
    ): School {
        return $request
            ->user()
            ->schools()
            ->wherePivot(
                'status',
                'active'
            )
            ->firstOrFail();
    }


    /*
    |--------------------------------------------------------------------------
    | Resolve Assignment
    |--------------------------------------------------------------------------
    |
    | School administrators may access assignments within their school.
    |
    | Teachers may only access assignments they created.
    |
    */

    private function assignment(
        Request $request,
        School $school,
        int|string $id
    ): Assignment {
        $query = $school
            ->assignments()
            ->whereKey($id);


        if (
            $request
                ->user()
                ->hasRole('teacher')
        ) {
            $query->where(
                'creator_id',
                $request->user()->id
            );
        }


        return $query
            ->firstOrFail();
    }


    /*
    |--------------------------------------------------------------------------
    | Assignment Index
    |--------------------------------------------------------------------------
    */

    public function index(
        Request $request
    ): View {
        $user = $request->user();

        $school = $this->school(
            $request
        );


        $search = trim(
            (string) $request->query(
                'search',
                ''
            )
        );


        $status = trim(
            (string) $request->query(
                'status',
                ''
            )
        );


        $query = $school
            ->assignments()
            ->with([
                'schoolClass',
                'creator',
                'book',
            ])
            ->withCount([
                'students',

                'submissions',

                'submissions as submitted_count' =>
                    fn (Builder $query) =>
                        $query->whereIn(
                            'status',
                            [
                                'submitted',
                                'late',
                                'graded',
                            ]
                        ),

                'submissions as graded_count' =>
                    fn (Builder $query) =>
                        $query->where(
                            'status',
                            'graded'
                        ),
            ]);


        /*
        |--------------------------------------------------------------------------
        | Teacher Scope
        |--------------------------------------------------------------------------
        */

        if (
            $user->hasRole(
                'teacher'
            )
        ) {
            $query->where(
                'creator_id',
                $user->id
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Search
        |--------------------------------------------------------------------------
        */

        $query->when(
            $search !== '',
            function (
                Builder $query
            ) use ($search) {
                $query->where(
                    function (
                        Builder $query
                    ) use ($search) {
                        $query
                            ->where(
                                'title',
                                'like',
                                "%{$search}%"
                            )
                            ->orWhere(
                                'instructions',
                                'like',
                                "%{$search}%"
                            )
                            ->orWhereHas(
                                'book',
                                function (
                                    Builder $query
                                ) use ($search) {
                                    $query->where(
                                        'title',
                                        'like',
                                        "%{$search}%"
                                    );
                                }
                            )
                            ->orWhereHas(
                                'schoolClass',
                                function (
                                    Builder $query
                                ) use ($search) {
                                    $query->where(
                                        'name',
                                        'like',
                                        "%{$search}%"
                                    );
                                }
                            );
                    }
                );
            }
        );


        /*
        |--------------------------------------------------------------------------
        | Status Filter
        |--------------------------------------------------------------------------
        */

        $query->when(
            in_array(
                $status,
                [
                    'draft',
                    'published',
                    'closed',
                    'archived',
                ],
                true
            ),
            fn (Builder $query) =>
                $query->where(
                    'status',
                    $status
                )
        );


        $assignments = $query
            ->latest()
            ->paginate(20)
            ->withQueryString();


        return view(
            'school.assignments.index',
            compact(
                'school',
                'assignments'
            )
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Create Assignment
    |--------------------------------------------------------------------------
    */

    public function create(
        Request $request
    ): View {
        $school = $this->school(
            $request
        );


        $classes = $this->availableClasses(
            $request,
            $school
        );


        $books = $this->availableBooks(
            $school
        );


        return view(
            'school.assignments.create',
            compact(
                'school',
                'classes',
                'books'
            )
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Store Assignment
    |--------------------------------------------------------------------------
    */

    public function store(
        Request $request
    ): RedirectResponse {
        $school = $this->school(
            $request
        );


        $validated = $this->validated(
            $request,
            $school
        );


        /*
        |--------------------------------------------------------------------------
        | Verify Class Access
        |--------------------------------------------------------------------------
        */

        $class = $this->resolveClass(
            $request,
            $school,
            $validated[
                'school_class_id'
            ]
        );


        /*
        |--------------------------------------------------------------------------
        | Verify Book Licence
        |--------------------------------------------------------------------------
        */

        if (
            ! empty(
                $validated[
                    'resource_id'
                ]
            )
        ) {
            $this->resolveLicensedBook(
                $school,
                $validated[
                    'resource_id'
                ]
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Create Assignment
        |--------------------------------------------------------------------------
        */

        $assignment = DB::transaction(
            function () use (
                $request,
                $school,
                $validated,
                $class
            ) {
                $assignment = $school
                    ->assignments()
                    ->create([
                        ...$validated,

                        'creator_id' =>
                            $request
                                ->user()
                                ->id,
                    ]);


                /*
                 * Snapshot the current students assigned
                 * to the selected class.
                 */
                $studentIds = $class
                    ->students()
                    ->pluck(
                        'users.id'
                    )
                    ->all();


                $assignment
                    ->students()
                    ->sync(
                        $studentIds
                    );


                return $assignment;
            }
        );


        return redirect()
            ->route(
                'school.assignments.show',
                $assignment
            )
            ->with(
                'success',
                $assignment->status
                    === 'published'
                    ? 'Assignment published successfully.'
                    : 'Assignment saved successfully.'
            );
    }


    /*
    |--------------------------------------------------------------------------
    | Show Assignment
    |--------------------------------------------------------------------------
    */

    public function show(
        Request $request,
        int $assignment
    ): View {
        $school = $this->school(
            $request
        );


        $assignment = $this->assignment(
            $request,
            $school,
            $assignment
        );


        $assignment->load([
            'schoolClass',
            'creator',

            'students' =>
                fn ($query) =>
                    $query->orderBy(
                        'name'
                    ),

            'book.authors',
            'book.publisher',

            'submissions' =>
                fn ($query) =>
                    $query
                        ->with([
                            'student',
                            'grader',
                        ])
                        ->latest(
                            'updated_at'
                        ),
        ]);


        $assignment->loadCount([
            'students',

            'submissions',

            'submissions as submitted_count' =>
                fn ($query) =>
                    $query->whereIn(
                        'status',
                        [
                            'submitted',
                            'late',
                            'graded',
                        ]
                    ),

            'submissions as graded_count' =>
                fn ($query) =>
                    $query->where(
                        'status',
                        'graded'
                    ),
        ]);


        return view(
            'school.assignments.show',
            compact(
                'school',
                'assignment'
            )
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Edit Assignment
    |--------------------------------------------------------------------------
    */

    public function edit(
        Request $request,
        int $assignment
    ): View {
        $school = $this->school(
            $request
        );


        $assignment = $this->assignment(
            $request,
            $school,
            $assignment
        );


        $classes = $this->availableClasses(
            $request,
            $school
        );


        $books = $this->availableBooks(
            $school,
            $assignment->resource_id
        );


        return view(
            'school.assignments.edit',
            compact(
                'school',
                'assignment',
                'classes',
                'books'
            )
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Update Assignment
    |--------------------------------------------------------------------------
    */

    public function update(
        Request $request,
        int $assignment
    ): RedirectResponse {
        $school = $this->school(
            $request
        );


        $assignment = $this->assignment(
            $request,
            $school,
            $assignment
        );


        $validated = $this->validated(
            $request,
            $school
        );


        $class = $this->resolveClass(
            $request,
            $school,
            $validated[
                'school_class_id'
            ]
        );


        if (
            ! empty(
                $validated[
                    'resource_id'
                ]
            )
        ) {
            $this->resolveLicensedBook(
                $school,
                $validated[
                    'resource_id'
                ]
            );
        }


        DB::transaction(
            function () use (
                $assignment,
                $validated,
                $class
            ) {
                $assignment->update(
                    $validated
                );


                /*
                 * Keep assignment recipients synchronized
                 * with the selected class.
                 */
                $studentIds = $class
                    ->students()
                    ->pluck(
                        'users.id'
                    )
                    ->all();


                $assignment
                    ->students()
                    ->sync(
                        $studentIds
                    );
            }
        );


        return redirect()
            ->route(
                'school.assignments.show',
                $assignment
            )
            ->with(
                'success',
                'Assignment updated successfully.'
            );
    }


    /*
    |--------------------------------------------------------------------------
    | Archive Assignment
    |--------------------------------------------------------------------------
    */

    public function destroy(
        Request $request,
        int $assignment
    ): RedirectResponse {
        $school = $this->school(
            $request
        );


        $assignment = $this->assignment(
            $request,
            $school,
            $assignment
        );


        if (
            $assignment->status
            !== 'archived'
        ) {
            $assignment->update([
                'status' =>
                    'archived',
            ]);
        }


        return redirect()
            ->route(
                'school.assignments.index'
            )
            ->with(
                'success',
                'Assignment archived.'
            );
    }


    /*
    |--------------------------------------------------------------------------
    | Available Classes
    |--------------------------------------------------------------------------
    |
    | School admins receive every active class in the school.
    |
    | Teachers receive only active classes they are assigned to.
    |
    */

    private function availableClasses(
        Request $request,
        School $school
    ) {
        $user = $request->user();


        if (
            $user->hasRole(
                'teacher'
            )
            &&
            method_exists(
                $user,
                'teacherClasses'
            )
        ) {
            return $user
                ->teacherClasses()
                ->where(
                    'school_classes.school_id',
                    $school->id
                )
                ->where(
                    'school_classes.status',
                    'active'
                )
                ->orderBy(
                    'school_classes.name'
                )
                ->get();
        }


        return $school
            ->classes()
            ->where(
                'status',
                'active'
            )
            ->orderBy(
                'name'
            )
            ->get();
    }


    /*
    |--------------------------------------------------------------------------
    | Available Books
    |--------------------------------------------------------------------------
    |
    | Only actively licensed, published books that explicitly
    | permit teacher assignment are selectable.
    |
    | When editing an assignment, the currently selected book
    | may be included if required for display consistency.
    |
    */

    private function availableBooks(
        School $school,
        int|string|null $currentBookId = null
    ) {
        $query = $this->licenses
            ->licensedBooksQuery(
                $school
            )
            ->where(
                'status',
                'published'
            )
            ->where(
                'allow_teacher_assignment',
                true
            )
            ->with([
                'authors',
                'publisher',
            ]);


        /*
         * Normally the active licensed catalogue is sufficient.
         *
         * The parameter is retained for future handling of assignments
         * whose book licence expires after the assignment was created.
         */
        if ($currentBookId) {
            // Intentionally no licence bypass here.
            //
            // Existing assignments continue to reference their book,
            // but edit forms only expose books currently valid for
            // assignment.
        }


        return $query
            ->orderBy(
                'title'
            )
            ->get();
    }


    /*
    |--------------------------------------------------------------------------
    | Resolve Class
    |--------------------------------------------------------------------------
    */

    private function resolveClass(
        Request $request,
        School $school,
        int|string $classId
    ) {
        $class = $school
            ->classes()
            ->where(
                'status',
                'active'
            )
            ->whereKey(
                $classId
            )
            ->firstOrFail();


        /*
         * A teacher may only create assignments
         * for classes they actually teach.
         */
        if (
            $request
                ->user()
                ->hasRole(
                    'teacher'
                )
        ) {
            abort_unless(
                method_exists(
                    $request->user(),
                    'teacherClasses'
                ),
                403,
                'Teacher class assignments are not configured.'
            );


            $allowed = $request
                ->user()
                ->teacherClasses()
                ->where(
                    'school_classes.school_id',
                    $school->id
                )
                ->whereKey(
                    $class->id
                )
                ->exists();


            abort_unless(
                $allowed,
                403,
                'You are not assigned to this class.'
            );
        }


        return $class;
    }


    /*
    |--------------------------------------------------------------------------
    | Resolve Licensed Book
    |--------------------------------------------------------------------------
    */

    private function resolveLicensedBook(
        School $school,
        int|string $bookId
    ): Book {
        $book = $this->licenses
            ->licensedBooksQuery(
                $school
            )
            ->whereKey(
                $bookId
            )
            ->where(
                'status',
                'published'
            )
            ->where(
                'allow_teacher_assignment',
                true
            )
            ->first();


        if (! $book) {
            throw ValidationException::withMessages([
                'resource_id' =>
                    'The selected book is not currently licensed for this school or cannot be assigned.',
            ]);
        }


        return $book;
    }


    /*
    |--------------------------------------------------------------------------
    | Validation
    |--------------------------------------------------------------------------
    */

    private function validated(
        Request $request,
        School $school
    ): array {
        $validated = $request->validate([

            /*
            |--------------------------------------------------------------------------
            | Assignment
            |--------------------------------------------------------------------------
            */

            'title' => [
                'required',
                'string',
                'max:255',
            ],


            /*
            |--------------------------------------------------------------------------
            | Class
            |--------------------------------------------------------------------------
            */

            'school_class_id' => [
                'required',
                'integer',

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


            /*
            |--------------------------------------------------------------------------
            | Assigned Book
            |--------------------------------------------------------------------------
            |
            | resource_id currently references books.id.
            |
            | The licence/assignment entitlement is verified again below through
            | BookLicenseService. A raw books.id match is never sufficient.
            |
            */

            'resource_id' => [
                'nullable',
                'integer',

                Rule::exists(
                    'books',
                    'id'
                ),
            ],


            /*
            |--------------------------------------------------------------------------
            | Instructions
            |--------------------------------------------------------------------------
            */

            'instructions' => [
                'nullable',
                'string',
                'max:20000',
            ],


            /*
            |--------------------------------------------------------------------------
            | Availability
            |--------------------------------------------------------------------------
            */

            'starts_at' => [
                'nullable',
                'date',
            ],


            'due_at' => [
                'nullable',
                'date',
                'after_or_equal:starts_at',
            ],


            /*
            |--------------------------------------------------------------------------
            | Reading Range
            |--------------------------------------------------------------------------
            */

            'start_page' => [
                'nullable',
                'integer',
                'min:1',
            ],


            'end_page' => [
                'nullable',
                'integer',
                'min:1',
                'gte:start_page',
            ],


            /*
            |--------------------------------------------------------------------------
            | Marks
            |--------------------------------------------------------------------------
            */

            'total_marks' => [
                'nullable',
                'integer',
                'min:1',
                'max:1000',
            ],


            /*
            |--------------------------------------------------------------------------
            | Late Submission Policy
            |--------------------------------------------------------------------------
            |
            | allow
            |     Accept late work without an automatic penalty.
            |
            | allow_with_penalty
            |     Accept late work and apply the configured penalty when grading.
            |
            | reject
            |     The student submission controller must block final submission
            |     after due_at.
            |
            */

            'late_submission_policy' => [
                'required',

                Rule::in([
                    'allow',
                    'allow_with_penalty',
                    'reject',
                ]),
            ],


            'late_penalty_type' => [
                'nullable',

                Rule::requiredIf(
                    $request->input(
                        'late_submission_policy'
                    ) === 'allow_with_penalty'
                ),

                Rule::in([
                    'percentage',
                    'fixed',
                ]),
            ],


            'late_penalty_value' => [
                'nullable',

                Rule::requiredIf(
                    $request->input(
                        'late_submission_policy'
                    ) === 'allow_with_penalty'
                ),

                'numeric',
                'min:0',
            ],


            /*
            |--------------------------------------------------------------------------
            | Status
            |--------------------------------------------------------------------------
            */

            'status' => [
                'required',

                Rule::in([
                    'draft',
                    'published',
                    'closed',
                    'archived',
                ]),
            ],

        ]);


        /*
        |--------------------------------------------------------------------------
        | Normalize Late Policy
        |--------------------------------------------------------------------------
        |
        | Penalty fields should never retain stale values if the teacher changes
        | the assignment back to ordinary late acceptance or rejects late work.
        |
        */

        if (
            $validated[
                'late_submission_policy'
            ] !== 'allow_with_penalty'
        ) {
            $validated[
                'late_penalty_type'
            ] = null;

            $validated[
                'late_penalty_value'
            ] = null;
        }


        /*
        |--------------------------------------------------------------------------
        | Validate Late Penalty
        |--------------------------------------------------------------------------
        */

        if (
            $validated[
                'late_submission_policy'
            ] === 'allow_with_penalty'
        ) {
            $penaltyType =
                $validated[
                    'late_penalty_type'
                ];

            $penaltyValue =
                (float) $validated[
                    'late_penalty_value'
                ];


            if (
                $penaltyType === 'percentage'
                && $penaltyValue > 100
            ) {
                throw ValidationException::withMessages([
                    'late_penalty_value' =>
                        'A percentage late penalty cannot exceed 100%.',
                ]);
            }


            if (
                $penaltyType === 'fixed'
                && ! empty(
                    $validated[
                        'total_marks'
                    ]
                )
                && $penaltyValue >
                    (float) $validated[
                        'total_marks'
                    ]
            ) {
                throw ValidationException::withMessages([
                    'late_penalty_value' =>
                        'A fixed late penalty cannot exceed the assignment total marks.',
                ]);
            }
        }


        /*
        |--------------------------------------------------------------------------
        | Page Range Validation Against Book
        |--------------------------------------------------------------------------
        |
        | Laravel validation ensures start_page <= end_page.
        |
        | Here we additionally ensure the selected pages do not exceed the known
        | page count of the actively licensed, assignable book.
        |
        */

        if (
            ! empty(
                $validated[
                    'resource_id'
                ]
            )
        ) {
            $book = $this->resolveLicensedBook(
                $school,
                $validated[
                    'resource_id'
                ]
            );


            if (
                $book->page_count
                &&
                ! empty(
                    $validated[
                        'start_page'
                    ]
                )
                &&
                $validated[
                    'start_page'
                ] > $book->page_count
            ) {
                throw ValidationException::withMessages([
                    'start_page' =>
                        "The start page cannot exceed the book's {$book->page_count} pages.",
                ]);
            }


            if (
                $book->page_count
                &&
                ! empty(
                    $validated[
                        'end_page'
                    ]
                )
                &&
                $validated[
                    'end_page'
                ] > $book->page_count
            ) {
                throw ValidationException::withMessages([
                    'end_page' =>
                        "The end page cannot exceed the book's {$book->page_count} pages.",
                ]);
            }
        }


        return $validated;
    }
}