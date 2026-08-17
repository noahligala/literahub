<?php

namespace App\Http\Controllers\School;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\BookAccessRequest;
use App\Models\School;
use App\Models\User;
use App\Services\Library\BookAccessService;
use App\Services\Library\BookLicenseService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class BookAccessRequestController extends Controller
{
    public function __construct(
        private readonly BookAccessService $bookAccess,
        private readonly BookLicenseService $licenses
    ) {
    }

    /*
    |--------------------------------------------------------------------------
    | Resolve Current School
    |--------------------------------------------------------------------------
    */

    private function school(
        Request $request
    ): School {
        return $request
            ->user()
            ->schools()
            ->firstOrFail();
    }

    /*
    |--------------------------------------------------------------------------
    | Resolve School Access Request
    |--------------------------------------------------------------------------
    */

    private function accessRequest(
        School $school,
        int|string $id
    ): BookAccessRequest {
        return BookAccessRequest::query()
            ->whereKey($id)
            ->where(
                'school_id',
                $school->id
            )
            ->firstOrFail();
    }

    /*
    |--------------------------------------------------------------------------
    | Request Directory
    |--------------------------------------------------------------------------
    |
    | Teachers and school administrators see requests for their school.
    |
    | Students see only their own requests.
    |
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

        $status = $request->query(
            'status'
        );

        $query = BookAccessRequest::query()
            ->where(
                'school_id',
                $school->id
            )
            ->with([
                'book.authors',
                'book.publisher',
                'student',
                'teacher',
            ]);

        /*
        |--------------------------------------------------------------------------
        | Student Scope
        |--------------------------------------------------------------------------
        */

        if ($user->hasRole('student')) {
            $query->where(
                'student_id',
                $user->id
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Teacher Scope
        |--------------------------------------------------------------------------
        |
        | A teacher should normally see requests from students they teach.
        |
        | If teacher_id is already assigned, the teacher sees requests routed
        | specifically to them as well.
        |
        */

        if ($user->hasRole('teacher')) {
            $classIds = $user
                ->teachingClasses()
                ->pluck(
                    'school_classes.id'
                );

            $studentIds = User::query()
                ->whereHas(
                    'studentClasses',
                    function ($query) use ($classIds) {
                        $query->whereIn(
                            'school_classes.id',
                            $classIds
                        );
                    }
                )
                ->pluck('users.id');

            $query->where(
                function ($query) use (
                    $user,
                    $studentIds
                ) {
                    $query
                        ->where(
                            'teacher_id',
                            $user->id
                        )
                        ->orWhereIn(
                            'student_id',
                            $studentIds
                        );
                }
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Search
        |--------------------------------------------------------------------------
        */

        $query->when(
            $search !== '',
            function ($query) use ($search) {
                $query->where(
                    function ($query) use ($search) {
                        $query
                            ->whereHas(
                                'book',
                                function ($query) use ($search) {
                                    $query
                                        ->where(
                                            'title',
                                            'like',
                                            "%{$search}%"
                                        )
                                        ->orWhere(
                                            'isbn',
                                            'like',
                                            "%{$search}%"
                                        );
                                }
                            )
                            ->orWhereHas(
                                'student',
                                function ($query) use ($search) {
                                    $query
                                        ->where(
                                            'name',
                                            'like',
                                            "%{$search}%"
                                        )
                                        ->orWhere(
                                            'email',
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
                    'pending',
                    'approved',
                    'rejected',
                    'expired',
                ],
                true
            ),
            fn ($query) =>
                $query->where(
                    'status',
                    $status
                )
        );

        /*
        |--------------------------------------------------------------------------
        | Metrics
        |--------------------------------------------------------------------------
        */

        $metricQuery =
            BookAccessRequest::query()
                ->where(
                    'school_id',
                    $school->id
                );

        if ($user->hasRole('student')) {
            $metricQuery->where(
                'student_id',
                $user->id
            );
        }

        $totalRequests =
            (clone $metricQuery)
                ->count();

        $pendingRequests =
            (clone $metricQuery)
                ->where(
                    'status',
                    'pending'
                )
                ->count();

        $approvedRequests =
            (clone $metricQuery)
                ->where(
                    'status',
                    'approved'
                )
                ->count();

        $requests = $query
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view(
            'school.library.requests.index',
            compact(
                'school',
                'requests',
                'totalRequests',
                'pendingRequests',
                'approvedRequests'
            )
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Show Request
    |--------------------------------------------------------------------------
    */

    public function show(
        Request $request,
        int $accessRequest
    ): View {
        $user = $request->user();

        $school = $this->school(
            $request
        );

        $accessRequest =
            $this->accessRequest(
                $school,
                $accessRequest
            );

        /*
         * Students may only inspect their own requests.
         */
        if (
            $user->hasRole('student')
            && $accessRequest->student_id !== $user->id
        ) {
            abort(403);
        }

        /*
         * Teachers may only inspect requests they are
         * authorised to review.
         */
        if (
            $user->hasRole('teacher')
            && !$this->teacherCanReview(
                $user,
                $accessRequest
            )
        ) {
            abort(403);
        }

        $accessRequest->load([
            'book.authors',
            'book.publisher',
            'student.studentClasses',
            'teacher',
        ]);

        return view(
            'school.library.requests.show',
            compact(
                'school',
                'accessRequest'
            )
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Student Requests Access
    |--------------------------------------------------------------------------
    */

    public function store(
        Request $request,
        Book $book
    ): RedirectResponse {
        $student = $request->user();

        $school = $this->school(
            $request
        );

        abort_unless(
            $student->hasRole('student'),
            403,
            'Only students may request additional book access.'
        );

        /*
        |--------------------------------------------------------------------------
        | Book Must Be Published
        |--------------------------------------------------------------------------
        */

        abort_unless(
            $book->status === 'published',
            403,
            'This book is not currently available for student access.'
        );

        /*
        |--------------------------------------------------------------------------
        | School Must Already Own a Valid Licence
        |--------------------------------------------------------------------------
        |
        | This is deliberately separate from requesting the school to acquire
        | a new title from a publisher.
        |
        */

        $license =
            $this->licenses
                ->activeLicense(
                    $school,
                    $book
                );

        abort_unless(
            $license !== null,
            403,
            'Your school does not currently hold a licence for this book.'
        );

        abort_unless(
            $license->allow_student_reading
            && $book->allow_online_reading,
            403,
            'Student reading is not permitted under the current licence.'
        );

        /*
        |--------------------------------------------------------------------------
        | Don't Request Something Already Accessible
        |--------------------------------------------------------------------------
        */

        if (
            $this->bookAccess
                ->canRead(
                    $student,
                    $book,
                    $school
                )
        ) {
            return redirect()
                ->route(
                    'school.library.show',
                    $book
                )
                ->with(
                    'info',
                    'You already have access to this book.'
                );
        }

        $validated =
            $request->validate([
                'reason' => [
                    'required',
                    'string',
                    'min:5',
                    'max:1000',
                ],

                'teacher_id' => [
                    'nullable',

                    Rule::exists(
                        'users',
                        'id'
                    ),
                ],
            ]);

        /*
        |--------------------------------------------------------------------------
        | Validate Selected Teacher
        |--------------------------------------------------------------------------
        */

        $teacher = null;

        if (
            !empty(
                $validated['teacher_id']
            )
        ) {
            $teacher = $school
                ->users()
                ->where(
                    'users.id',
                    $validated['teacher_id']
                )
                ->wherePivot(
                    'role',
                    'teacher'
                )
                ->wherePivot(
                    'status',
                    'active'
                )
                ->firstOrFail();

            /*
             * The selected teacher should actually teach
             * the requesting student.
             */
            abort_unless(
                $this->teacherTeachesStudent(
                    $teacher,
                    $student
                ),
                422,
                'The selected teacher does not teach your class.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Prevent Duplicate Pending Request
        |--------------------------------------------------------------------------
        */

        $existing =
            BookAccessRequest::query()
                ->where(
                    'school_id',
                    $school->id
                )
                ->where(
                    'book_id',
                    $book->id
                )
                ->where(
                    'student_id',
                    $student->id
                )
                ->where(
                    'status',
                    'pending'
                )
                ->first();

        if ($existing) {
            return redirect()
                ->route(
                    'school.library.requests.show',
                    $existing
                )
                ->with(
                    'info',
                    'You already have a pending request for this book.'
                );
        }

        /*
        |--------------------------------------------------------------------------
        | Create Request
        |--------------------------------------------------------------------------
        */

        $accessRequest =
            BookAccessRequest::create([
                'book_id' =>
                    $book->id,

                'student_id' =>
                    $student->id,

                'school_id' =>
                    $school->id,

                'teacher_id' =>
                    $teacher?->id,

                'reason' =>
                    $validated['reason'],

                'status' =>
                    'pending',

                'reviewed_at' =>
                    null,

                'expires_at' =>
                    null,
            ]);

        return redirect()
            ->route(
                'school.library.requests.show',
                $accessRequest
            )
            ->with(
                'success',
                'Your request has been sent for teacher approval.'
            );
    }

    /*
    |--------------------------------------------------------------------------
    | Approve Request
    |--------------------------------------------------------------------------
    */

    public function approve(
        Request $request,
        int $accessRequest
    ): RedirectResponse {
        $reviewer = $request->user();

        $school = $this->school(
            $request
        );

        $accessRequest =
            $this->accessRequest(
                $school,
                $accessRequest
            );

        $this->ensureCanReview(
            $reviewer,
            $accessRequest
        );

        abort_unless(
            $accessRequest->status === 'pending',
            422,
            'Only pending access requests may be approved.'
        );

        /*
         * Verify that the school licence is still active at
         * approval time.
         */
        $license =
            $this->licenses
                ->activeLicense(
                    $school,
                    $accessRequest->book
                );

        abort_unless(
            $license !== null,
            422,
            'The school licence for this book is no longer active.'
        );

        $validated =
            $request->validate([
                'expires_at' => [
                    'nullable',
                    'date',
                    'after:now',
                ],
            ]);

        DB::transaction(
            function () use (
                $accessRequest,
                $reviewer,
                $validated,
                $license
            ) {
                /*
                 * Do not allow teacher approval beyond
                 * the school licence expiry.
                 */
                $expiresAt =
                    !empty(
                        $validated['expires_at']
                    )
                        ? $validated['expires_at']
                        : $license->expires_at;

                if (
                    $license->expires_at
                    && $expiresAt
                    && now()
                        ->parse($expiresAt)
                        ->greaterThan(
                            $license->expires_at
                        )
                ) {
                    $expiresAt =
                        $license->expires_at;
                }

                $accessRequest->update([
                    'teacher_id' =>
                        $reviewer->id,

                    'status' =>
                        'approved',

                    'reviewed_at' =>
                        now(),

                    'expires_at' =>
                        $expiresAt,
                ]);
            }
        );

        return redirect()
            ->route(
                'school.library.requests.show',
                $accessRequest
            )
            ->with(
                'success',
                'Book access request approved.'
            );
    }

    /*
    |--------------------------------------------------------------------------
    | Reject Request
    |--------------------------------------------------------------------------
    */

    public function reject(
        Request $request,
        int $accessRequest
    ): RedirectResponse {
        $reviewer = $request->user();

        $school = $this->school(
            $request
        );

        $accessRequest =
            $this->accessRequest(
                $school,
                $accessRequest
            );

        $this->ensureCanReview(
            $reviewer,
            $accessRequest
        );

        abort_unless(
            $accessRequest->status === 'pending',
            422,
            'Only pending requests may be rejected.'
        );

        $accessRequest->update([
            'teacher_id' =>
                $reviewer->id,

            'status' =>
                'rejected',

            'reviewed_at' =>
                now(),

            'expires_at' =>
                null,
        ]);

        return redirect()
            ->route(
                'school.library.requests.show',
                $accessRequest
            )
            ->with(
                'success',
                'Book access request rejected.'
            );
    }

    /*
    |--------------------------------------------------------------------------
    | Student Cancels Pending Request
    |--------------------------------------------------------------------------
    */

    public function destroy(
        Request $request,
        int $accessRequest
    ): RedirectResponse {
        $student = $request->user();

        $school = $this->school(
            $request
        );

        $accessRequest =
            $this->accessRequest(
                $school,
                $accessRequest
            );

        abort_unless(
            $student->hasRole('student')
            && $accessRequest->student_id === $student->id,
            403
        );

        abort_unless(
            $accessRequest->status === 'pending',
            422,
            'Only pending access requests may be cancelled.'
        );

        $accessRequest->delete();

        return redirect()
            ->route(
                'school.library.requests.index'
            )
            ->with(
                'success',
                'Access request cancelled.'
            );
    }

    /*
    |--------------------------------------------------------------------------
    | Review Authorization
    |--------------------------------------------------------------------------
    */

    private function ensureCanReview(
        User $user,
        BookAccessRequest $accessRequest
    ): void {
        if (
            $user->hasAnyRole([
                'school_admin',
                'platform_admin',
                'super_admin',
            ])
        ) {
            return;
        }

        if (
            $user->hasRole('teacher')
            && $this->teacherCanReview(
                $user,
                $accessRequest
            )
        ) {
            return;
        }

        abort(
            403,
            'You are not authorised to review this request.'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Teacher Can Review Request
    |--------------------------------------------------------------------------
    */

    private function teacherCanReview(
        User $teacher,
        BookAccessRequest $accessRequest
    ): bool {
        /*
         * Explicitly assigned teacher.
         */
        if (
            $accessRequest->teacher_id
            && $accessRequest->teacher_id === $teacher->id
        ) {
            return true;
        }

        $student = $accessRequest
            ->student()
            ->first();

        if (!$student) {
            return false;
        }

        return $this->teacherTeachesStudent(
            $teacher,
            $student
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Teacher Teaches Student
    |--------------------------------------------------------------------------
    */

    private function teacherTeachesStudent(
        User $teacher,
        User $student
    ): bool {
        $teacherClassIds =
            $teacher
                ->teachingClasses()
                ->pluck(
                    'school_classes.id'
                );

        if ($teacherClassIds->isEmpty()) {
            return false;
        }

        return $student
            ->studentClasses()
            ->whereIn(
                'school_classes.id',
                $teacherClassIds
            )
            ->exists();
    }
}