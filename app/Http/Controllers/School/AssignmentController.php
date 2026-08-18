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
    */

    private function assignment(
        Request $request,
        School $school,
        int|string $id
    ): Assignment {
        $query = $school
            ->assignments()
            ->whereKey($id);


        /*
         * Teachers may only manage assignments they created.
         *
         * School admins may manage all assignments in the school.
         */
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
    | Index
    |--------------------------------------------------------------------------
    */

    public function index(
        Request $request
    ): View {
        $user =
            $request->user();

        $school =
            $this->school(
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
            ])
            ->withCount([
                'students',
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
                            );
                    }
                );
            }
        );


        /*
        |--------------------------------------------------------------------------
        | Status
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
    | Create
    |--------------------------------------------------------------------------
    */

    public function create(
        Request $request
    ): View {
        $user =
            $request->user();

        $school =
            $this->school(
                $request
            );


        /*
        |--------------------------------------------------------------------------
        | Classes
        |--------------------------------------------------------------------------
        |
        | School admin:
        |     all active classes
        |
        | Teacher:
        |     only classes assigned to the teacher
        |
        */

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
            $classes = $user
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
        } else {
            $classes = $school
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
        | Licensed Books
        |--------------------------------------------------------------------------
        */

        $books = $this->licenses
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
            ])
            ->orderBy(
                'title'
            )
            ->get();


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
    | Store
    |--------------------------------------------------------------------------
    */

    public function store(
        Request $request
    ): RedirectResponse {
        $school =
            $this->school(
                $request
            );


        $validated =
            $this->validated(
                $request,
                $school
            );


        /*
        |--------------------------------------------------------------------------
        | Verify Class Access
        |--------------------------------------------------------------------------
        */

        $class =
            $this->resolveClass(
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


        $assignment =
            DB::transaction(
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
                     * Snapshot the current learners in the class.
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
    | Show
    |--------------------------------------------------------------------------
    */

    public function show(
        Request $request,
        int $assignment
    ): View {
        $school =
            $this->school(
                $request
            );


        $assignment =
            $this->assignment(
                $request,
                $school,
                $assignment
            );


        $assignment->load([
            'schoolClass',
            'creator',
            'students',
        ]);


        /*
         * Load book manually while resource_id remains
         * the current assignment column.
         */
        $book = null;


        if (
            $assignment->resource_id
        ) {
            $book = Book::query()
                ->with([
                    'authors',
                    'publisher',
                ])
                ->find(
                    $assignment
                        ->resource_id
                );
        }


        return view(
            'school.assignments.show',
            compact(
                'school',
                'assignment',
                'book'
            )
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Edit
    |--------------------------------------------------------------------------
    */

    public function edit(
        Request $request,
        int $assignment
    ): View {
        $user =
            $request->user();

        $school =
            $this->school(
                $request
            );


        $assignment =
            $this->assignment(
                $request,
                $school,
                $assignment
            );


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
            $classes = $user
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
        } else {
            $classes = $school
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


        $books = $this->licenses
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
            ])
            ->orderBy(
                'title'
            )
            ->get();


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
    | Update
    |--------------------------------------------------------------------------
    */

    public function update(
        Request $request,
        int $assignment
    ): RedirectResponse {
        $school =
            $this->school(
                $request
            );


        $assignment =
            $this->assignment(
                $request,
                $school,
                $assignment
            );


        $validated =
            $this->validated(
                $request,
                $school
            );


        $class =
            $this->resolveClass(
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


                $assignment
                    ->students()
                    ->sync(
                        $class
                            ->students()
                            ->pluck(
                                'users.id'
                            )
                            ->all()
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
    | Archive
    |--------------------------------------------------------------------------
    */

    public function destroy(
        Request $request,
        int $assignment
    ): RedirectResponse {
        $school =
            $this->school(
                $request
            );


        $assignment =
            $this->assignment(
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
         * A teacher may only assign work to
         * classes they actually teach.
         */
        if (
            $request
                ->user()
                ->hasRole(
                    'teacher'
                )
            &&
            method_exists(
                $request->user(),
                'teacherClasses'
            )
        ) {
            $allowed =
                $request
                    ->user()
                    ->teacherClasses()
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
        return $request->validate([

            'title' => [
                'required',
                'string',
                'max:255',
            ],


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
             * resource_id currently represents a Book.
             *
             * We should rename this to book_id later.
             */
            'resource_id' => [
                'nullable',
                'integer',

                Rule::exists(
                    'books',
                    'id'
                ),
            ],

            'instructions' => [
                'nullable',
                'string',
                'max:20000',
            ],

            'starts_at' => [
                'nullable',
                'date',
            ],

            'due_at' => [
                'nullable',
                'date',
                'after_or_equal:starts_at',
            ],

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

            'total_marks' => [
                'nullable',
                'integer',
                'min:1',
                'max:1000',
            ],

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
    }
}