<?php

namespace App\Http\Controllers\School;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\BookBookmark;
use App\Models\BookBorrowing;
use App\Models\School;
use App\Services\Library\BookAccessService;
use App\Services\Library\BookLicenseService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class LibraryController extends Controller
{
    public function __construct(
        private readonly BookAccessService $access,
        private readonly BookLicenseService $licenses
    ) {
    }

    private function school(Request $request): School
    {
        return $request
            ->user()
            ->schools()
            ->firstOrFail();
    }

    /*
    |--------------------------------------------------------------------------
    | School Library
    |--------------------------------------------------------------------------
    */

    public function index(Request $request): View
    {
        $user = $request->user();

        $school = $this->school($request);

        $search = trim(
            (string) $request->query('search', '')
        );

        $category = trim(
            (string) $request->query('category', '')
        );

        /*
         * The base query already restricts books to
         * active licences belonging to this school.
         */
        $query = $this->licenses
            ->licensedBooksQuery($school)
            ->with([
                'publisher',
                'authors',
            ]);

        /*
         * Students may only catalogue published titles
         * assigned to their class or individually approved.
         */
        if ($user->hasRole('student')) {
            $classIds = $user
                ->studentClasses()
                ->pluck('school_classes.id');

            $query
                ->where('status', 'published')
                ->where(function ($query) use (
                    $classIds,
                    $user,
                    $school
                ) {
                    $query
                        ->whereHas(
                            'classes',
                            function ($query) use ($classIds) {
                                $query->whereIn(
                                    'school_classes.id',
                                    $classIds
                                );
                            }
                        )
                        ->orWhereHas(
                            'accessRequests',
                            function ($query) use (
                                $user,
                                $school
                            ) {
                                $query
                                    ->where(
                                        'student_id',
                                        $user->id
                                    )
                                    ->where(
                                        'school_id',
                                        $school->id
                                    )
                                    ->where(
                                        'status',
                                        'approved'
                                    )
                                    ->where(
                                        function ($query) {
                                            $query
                                                ->whereNull('expires_at')
                                                ->orWhere(
                                                    'expires_at',
                                                    '>',
                                                    now()
                                                );
                                        }
                                    );
                            }
                        );
                });
        } else {
            /*
             * Teachers can see licensed books that are
             * published or still in the academic review workflow.
             */
            $query->whereIn(
                'status',
                [
                    'under_review',
                    'approved',
                    'published',
                ]
            );
        }

        $query
            ->when(
                $search !== '',
                function ($query) use ($search) {
                    $query->where(
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
                                )
                                ->orWhereHas(
                                    'authors',
                                    function ($query) use ($search) {
                                        $query->where(
                                            'name',
                                            'like',
                                            "%{$search}%"
                                        );
                                    }
                                )
                                ->orWhereHas(
                                    'publisher',
                                    function ($query) use ($search) {
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
            )
            ->when(
                $category !== '',
                fn ($query) =>
                    $query->where(
                        'category',
                        $category
                    )
            );

        $resources = $query
            ->orderBy('title')
            ->paginate(20)
            ->withQueryString();

        $categories = $this->licenses
            ->licensedBooksQuery($school)
            ->whereNotNull('category')
            ->where('category', '!=', '')
            ->distinct()
            ->orderBy('category')
            ->pluck('category');

        return view(
            'school.library.index',
            compact(
                'school',
                'resources',
                'categories'
            )
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Book Details
    |--------------------------------------------------------------------------
    */

    public function show(
        Request $request,
        Book $book
    ): View {
        $user = $request->user();

        $school = $this->school($request);

        abort_unless(
            $this->access->canView(
                $user,
                $book,
                $school
            ),
            403,
            'You do not have access to this title.'
        );

        $book->load([
            'authors',
            'publisher',
        ]);

        $license = $this->licenses
            ->activeLicense(
                $school,
                $book
            );

        $activeBorrowing = BookBorrowing::query()
            ->where('book_id', $book->id)
            ->where('user_id', $user->id)
            ->where('school_id', $school->id)
            ->where('status', 'borrowed')
            ->first();

        $bookmarks = BookBookmark::query()
            ->where('book_id', $book->id)
            ->where('user_id', $user->id)
            ->orderBy('page')
            ->get();

        $canRead = $this->access->canRead(
            $user,
            $book,
            $school
        );

        $canBorrow = $this->access->canBorrow(
            $user,
            $book,
            $school
        );

        $canDownload = $this->access->canDownload(
            $user,
            $book,
            $school
        );

        $canPrint = $this->access->canPrint(
            $user,
            $book,
            $school
        );

        $canAssign = $this->access->canAssign(
            $user,
            $book,
            $school
        );

        return view(
            'school.library.show',
            compact(
                'school',
                'book',
                'license',
                'activeBorrowing',
                'bookmarks',
                'canRead',
                'canBorrow',
                'canDownload',
                'canPrint',
                'canAssign'
            )
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Borrow
    |--------------------------------------------------------------------------
    */

    public function borrow(
        Request $request,
        Book $book
    ): RedirectResponse {
        $student = $request->user();

        $school = $this->school($request);

        abort_unless(
            $student->hasRole('student'),
            403
        );

        abort_unless(
            $this->access->canBorrow(
                $student,
                $book,
                $school
            ),
            403,
            'You are not permitted to borrow this title.'
        );

        DB::transaction(
            function () use (
                $student,
                $book,
                $school
            ) {
                $alreadyBorrowed = BookBorrowing::query()
                    ->where('book_id', $book->id)
                    ->where('user_id', $student->id)
                    ->where('school_id', $school->id)
                    ->where('status', 'borrowed')
                    ->lockForUpdate()
                    ->exists();

                if ($alreadyBorrowed) {
                    throw ValidationException::withMessages([
                        'book' =>
                            'You have already borrowed this book.',
                    ]);
                }

                if ($book->max_concurrent_loans) {
                    $activeLoans = BookBorrowing::query()
                        ->where('book_id', $book->id)
                        ->where('status', 'borrowed')
                        ->lockForUpdate()
                        ->count();

                    if (
                        $activeLoans
                        >= $book->max_concurrent_loans
                    ) {
                        throw ValidationException::withMessages([
                            'book' =>
                                'All available digital copies are currently in use.',
                        ]);
                    }
                }

                BookBorrowing::create([
                    'book_id' =>
                        $book->id,

                    'user_id' =>
                        $student->id,

                    'school_id' =>
                        $school->id,

                    'borrowed_at' =>
                        now(),

                    'due_at' =>
                        now()->addDays(
                            max(
                                1,
                                (int) $book->loan_days
                            )
                        ),

                    'status' =>
                        'borrowed',
                ]);
            }
        );

        return redirect()
            ->route(
                'school.library.show',
                $book
            )
            ->with(
                'success',
                'Book borrowed successfully.'
            );
    }

    /*
    |--------------------------------------------------------------------------
    | Return
    |--------------------------------------------------------------------------
    */

    public function returnBook(
        Request $request,
        Book $book
    ): RedirectResponse {
        $school = $this->school($request);

        $borrowing = BookBorrowing::query()
            ->where('book_id', $book->id)
            ->where(
                'user_id',
                $request->user()->id
            )
            ->where(
                'school_id',
                $school->id
            )
            ->where('status', 'borrowed')
            ->firstOrFail();

        $borrowing->update([
            'status' =>
                'returned',

            'returned_at' =>
                now(),
        ]);

        return redirect()
            ->route(
                'school.library.show',
                $book
            )
            ->with(
                'success',
                'Book returned successfully.'
            );
    }

    /*
    |--------------------------------------------------------------------------
    | Bookmark
    |--------------------------------------------------------------------------
    */

    public function bookmark(
        Request $request,
        Book $book
    ): RedirectResponse {
        $school = $this->school($request);

        abort_unless(
            $this->access->canRead(
                $request->user(),
                $book,
                $school
            ),
            403
        );

        $validated = $request->validate([
            'page' => [
                'required',
                'integer',
                'min:1',
            ],

            'label' => [
                'nullable',
                'string',
                'max:150',
            ],

            'note' => [
                'nullable',
                'string',
                'max:2000',
            ],
        ]);

        if (
            $book->page_count
            && $validated['page'] > $book->page_count
        ) {
            throw ValidationException::withMessages([
                'page' =>
                    'The selected page exceeds the number of pages in the book.',
            ]);
        }

        BookBookmark::updateOrCreate(
            [
                'book_id' =>
                    $book->id,

                'user_id' =>
                    $request->user()->id,

                'page' =>
                    $validated['page'],
            ],
            [
                'label' =>
                    $validated['label'] ?? null,

                'note' =>
                    $validated['note'] ?? null,
            ]
        );

        return back()->with(
            'success',
            'Bookmark saved.'
        );
    }
}