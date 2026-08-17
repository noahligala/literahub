<?php

namespace App\Http\Controllers;

use App\Models\Author;
use App\Models\Book;
use App\Models\Publisher;
use App\Models\School;
use App\Models\SchoolBookLicense;
use App\Services\Library\BookLicenseService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class BookLicenseController extends Controller
{
    public function __construct(
        private readonly BookLicenseService $licenses
    ) {
    }

    public function index(
        Request $request
    ): View {
        Gate::authorize(
            'viewAny',
            SchoolBookLicense::class
        );

        $query =
            SchoolBookLicense::query()
                ->with([
                    'school',
                    'book.authors',
                    'publisher',
                    'author',
                    'creator',
                ]);

        if (
            $request
                ->user()
                ->hasRole('author')
        ) {
            $authorId =
                $request
                    ->user()
                    ->authorProfile()
                    ->value('id');

            $query->where(
                'author_id',
                $authorId
            );
        }

        $licenses = $query
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view(
            'books.licenses.index',
            compact('licenses')
        );
    }

    public function create(
        Request $request
    ): View {
        Gate::authorize(
            'create',
            SchoolBookLicense::class
        );

        $schools =
            School::query()
                ->orderBy('name')
                ->get();

        $books =
            Book::query()
                ->where('status', 'published')
                ->with([
                    'publisher',
                    'authors',
                ])
                ->orderBy('title')
                ->get();

        $publishers =
            Publisher::query()
                ->where('status', 'active')
                ->orderBy('name')
                ->get();

        $authors =
            Author::query()
                ->where('status', 'verified')
                ->orderBy('name')
                ->get();

        return view(
            'books.licenses.create',
            compact(
                'schools',
                'books',
                'publishers',
                'authors'
            )
        );
    }

    public function store(
        Request $request
    ): RedirectResponse {
        Gate::authorize(
            'create',
            SchoolBookLicense::class
        );

        $validated =
            $this->validateLicense(
                $request
            );

        $book =
            Book::query()
                ->with([
                    'authors',
                    'publisher',
                ])
                ->findOrFail(
                    $validated['book_id']
                );

        $this->validateLicensor(
            $book,
            $validated
        );

        $license =
            SchoolBookLicense::create([
                ...$validated,

                'license_number' =>
                    $this->licenseNumber(),

                'created_by' =>
                    $request->user()->id,
            ]);

        return redirect()
            ->route(
                'book-licenses.show',
                $license
            )
            ->with(
                'success',
                'Book licence created successfully.'
            );
    }

    public function show(
        SchoolBookLicense $bookLicense
    ): View {
        Gate::authorize(
            'view',
            $bookLicense
        );

        $bookLicense->load([
            'school',
            'book.authors',
            'book.publisher',
            'publisher',
            'author',
            'creator',
            'revokedBy',
        ]);

        return view(
            'books.licenses.show',
            [
                'license' =>
                    $bookLicense,
            ]
        );
    }

    public function edit(
        SchoolBookLicense $bookLicense
    ): View {
        Gate::authorize(
            'update',
            $bookLicense
        );

        $schools =
            School::query()
                ->orderBy('name')
                ->get();

        $books =
            Book::query()
                ->where('status', 'published')
                ->orderBy('title')
                ->get();

        $publishers =
            Publisher::query()
                ->where('status', 'active')
                ->orderBy('name')
                ->get();

        $authors =
            Author::query()
                ->where('status', 'verified')
                ->orderBy('name')
                ->get();

        return view(
            'books.licenses.edit',
            [
                'license' =>
                    $bookLicense,

                'schools' =>
                    $schools,

                'books' =>
                    $books,

                'publishers' =>
                    $publishers,

                'authors' =>
                    $authors,
            ]
        );
    }

    public function update(
        Request $request,
        SchoolBookLicense $bookLicense
    ): RedirectResponse {
        Gate::authorize(
            'update',
            $bookLicense
        );

        $validated =
            $this->validateLicense(
                $request
            );

        $book =
            Book::query()
                ->with([
                    'authors',
                    'publisher',
                ])
                ->findOrFail(
                    $validated['book_id']
                );

        $this->validateLicensor(
            $book,
            $validated
        );

        unset(
            $validated['license_number']
        );

        $bookLicense->update(
            $validated
        );

        return redirect()
            ->route(
                'book-licenses.show',
                $bookLicense
            )
            ->with(
                'success',
                'Licence updated successfully.'
            );
    }

    public function revoke(
        Request $request,
        SchoolBookLicense $bookLicense
    ): RedirectResponse {
        Gate::authorize(
            'revoke',
            $bookLicense
        );

        $this->licenses->revoke(
            $bookLicense,
            $request->user()->id
        );

        return back()->with(
            'success',
            'Licence revoked successfully.'
        );
    }

    public function renew(
        Request $request,
        SchoolBookLicense $bookLicense
    ): RedirectResponse {
        Gate::authorize(
            'renew',
            $bookLicense
        );

        $validated =
            $request->validate([
                'starts_at' => [
                    'required',
                    'date',
                ],

                'expires_at' => [
                    'nullable',
                    'date',
                    'after:starts_at',
                ],
            ]);

        $newLicense =
            $bookLicense->replicate([
                'license_number',
                'status',
                'starts_at',
                'expires_at',
                'revoked_at',
                'revoked_by',
                'created_by',
            ]);

        $newLicense->fill([
            'license_number' =>
                $this->licenseNumber(),

            'status' =>
                'active',

            'starts_at' =>
                $validated['starts_at'],

            'expires_at' =>
                $validated[
                    'expires_at'
                ] ?? null,

            'created_by' =>
                $request->user()->id,

            'revoked_at' =>
                null,

            'revoked_by' =>
                null,
        ]);

        $newLicense->save();

        return redirect()
            ->route(
                'book-licenses.show',
                $newLicense
            )
            ->with(
                'success',
                'Licence renewed successfully.'
            );
    }

    private function validateLicense(
        Request $request
    ): array {
        return $request->validate([
            'school_id' => [
                'required',
                Rule::exists(
                    'schools',
                    'id'
                ),
            ],

            'book_id' => [
                'required',
                Rule::exists(
                    'books',
                    'id'
                ),
            ],

            'publisher_id' => [
                'nullable',
                Rule::exists(
                    'publishers',
                    'id'
                ),
            ],

            'author_id' => [
                'nullable',
                Rule::exists(
                    'authors',
                    'id'
                ),
            ],

            'license_type' => [
                'required',

                Rule::in([
                    'lease',
                    'subscription',
                    'perpetual',
                    'trial',
                ]),
            ],

            'starts_at' => [
                'required',
                'date',
            ],

            'expires_at' => [
                'nullable',
                'date',
                'after:starts_at',
            ],

            'seat_limit' => [
                'nullable',
                'integer',
                'min:1',
            ],

            'concurrent_reader_limit' => [
                'nullable',
                'integer',
                'min:1',
            ],

            'status' => [
                'required',

                Rule::in([
                    'pending',
                    'active',
                    'expired',
                    'suspended',
                    'revoked',
                ]),
            ],

            'price_minor' => [
                'nullable',
                'integer',
                'min:0',
            ],

            'currency' => [
                'required',
                'string',
                'size:3',
            ],

            'terms' => [
                'nullable',
                'string',
            ],

            'notes' => [
                'nullable',
                'string',
            ],

            'allow_student_reading' => [
                'boolean',
            ],

            'allow_teacher_reading' => [
                'boolean',
            ],

            'allow_teacher_assignment' => [
                'boolean',
            ],

            'allow_student_borrowing' => [
                'boolean',
            ],

            'allow_print' => [
                'boolean',
            ],

            'allow_download' => [
                'boolean',
            ],
        ]);
    }

    private function validateLicensor(
        Book $book,
        array $validated
    ): void {
        $publisherId =
            $validated['publisher_id']
            ?? null;

        $authorId =
            $validated['author_id']
            ?? null;

        if (
            !$publisherId
            && !$authorId
        ) {
            throw ValidationException::withMessages([
                'publisher_id' =>
                    'A publisher or author must issue the licence.',
            ]);
        }

        if (
            $publisherId
            && $authorId
        ) {
            throw ValidationException::withMessages([
                'publisher_id' =>
                    'Choose either the publisher or the author as licensor, not both.',
            ]);
        }

        if (
            $publisherId
            && (int) $book->publisher_id
                !== (int) $publisherId
        ) {
            throw ValidationException::withMessages([
                'publisher_id' =>
                    'The selected publisher does not publish this book.',
            ]);
        }

        if (
            $authorId
            && !$book
                ->authors()
                ->where(
                    'authors.id',
                    $authorId
                )
                ->exists()
        ) {
            throw ValidationException::withMessages([
                'author_id' =>
                    'The selected author is not associated with this book.',
            ]);
        }
    }

    private function licenseNumber(): string
    {
        do {
            $number =
                'LIT-LIC-'
                . now()->format('Y')
                . '-'
                . strtoupper(
                    Str::random(8)
                );
        } while (
            SchoolBookLicense::query()
                ->where(
                    'license_number',
                    $number
                )
                ->exists()
        );

        return $number;
    }
}