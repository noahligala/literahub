<?php

namespace App\Http\Controllers\School;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\School;
use App\Models\SchoolBookLicense;
use App\Services\Library\BookLicenseService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class BookLicenseController extends Controller
{
    public function __construct(
        private readonly BookLicenseService $licenses
    ) {
    }

    private function school(
        Request $request
    ): School {
        return $request
            ->user()
            ->schools()
            ->firstOrFail();
    }

    public function index(
        Request $request
    ): View {
        $school =
            $this->school($request);

        $licenses =
            SchoolBookLicense::query()
                ->where(
                    'school_id',
                    $school->id
                )
                ->with([
                    'book.authors',
                    'book.publisher',
                    'publisher',
                    'author',
                ])
                ->latest()
                ->paginate(20)
                ->withQueryString();

        return view(
            'school.library.licenses.index',
            compact(
                'school',
                'licenses'
            )
        );
    }

    public function show(
        Request $request,
        int $license
    ): View {
        $school =
            $this->school($request);

        $license =
            SchoolBookLicense::query()
                ->whereKey($license)
                ->where(
                    'school_id',
                    $school->id
                )
                ->with([
                    'book.authors',
                    'book.publisher',
                    'publisher',
                    'author',
                ])
                ->firstOrFail();

        return view(
            'school.library.licenses.show',
            compact(
                'school',
                'license'
            )
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Available Global Titles
    |--------------------------------------------------------------------------
    */

    public function catalogue(
        Request $request
    ): View {
        $school =
            $this->school($request);

        abort_unless(
            $request
                ->user()
                ->hasRole(
                    'school_admin'
                ),
            403
        );

        $bookIds =
            SchoolBookLicense::query()
                ->where(
                    'school_id',
                    $school->id
                )
                ->whereIn(
                    'status',
                    [
                        'pending',
                        'active',
                    ]
                )
                ->pluck('book_id');

        $books = Book::query()
            ->where(
                'status',
                'published'
            )
            ->whereNotIn(
                'id',
                $bookIds
            )
            ->with([
                'publisher',
                'authors',
            ])
            ->orderBy('title')
            ->paginate(20)
            ->withQueryString();

        return view(
            'school.library.licenses.catalogue',
            compact(
                'school',
                'books'
            )
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Request Licence
    |--------------------------------------------------------------------------
    */

    public function requestLicense(
        Request $request,
        Book $book
    ): RedirectResponse {
        $school =
            $this->school($request);

        abort_unless(
            $request
                ->user()
                ->hasRole(
                    'school_admin'
                ),
            403
        );

        abort_unless(
            $book->status
                === 'published',
            422
        );

        $existing =
            SchoolBookLicense::query()
                ->where(
                    'school_id',
                    $school->id
                )
                ->where(
                    'book_id',
                    $book->id
                )
                ->whereIn(
                    'status',
                    [
                        'pending',
                        'active',
                    ]
                )
                ->exists();

        if ($existing) {
            return back()->with(
                'info',
                'A current licence or licence request already exists for this title.'
            );
        }

        $book->load('authors');

        $license =
            SchoolBookLicense::create([
                'school_id' =>
                    $school->id,

                'book_id' =>
                    $book->id,

                'publisher_id' =>
                    $book->publisher_id,

                /*
                 * If no publisher controls the work,
                 * default to the first author for review.
                 */
                'author_id' =>
                    $book->publisher_id
                        ? null
                        : $book
                            ->authors
                            ->first()?->id,

                'license_number' =>
                    'REQ-'
                    . now()->format('Ymd')
                    . '-'
                    . strtoupper(
                        Str::random(8)
                    ),

                'license_type' =>
                    'lease',

                'starts_at' =>
                    now(),

                'expires_at' =>
                    null,

                'status' =>
                    'pending',

                'currency' =>
                    'KES',

                'created_by' =>
                    $request->user()->id,

                /*
                 * Rights remain conservative until
                 * licensor approves the request.
                 */
                'allow_student_reading' =>
                    false,

                'allow_teacher_reading' =>
                    false,

                'allow_teacher_assignment' =>
                    false,

                'allow_student_borrowing' =>
                    false,

                'allow_print' =>
                    false,

                'allow_download' =>
                    false,
            ]);

        return redirect()
            ->route(
                'school.library.licenses.show',
                $license
            )
            ->with(
                'success',
                'Licence request submitted.'
            );
    }
}