<?php

namespace App\Http\Controllers;

use App\Models\Author;
use App\Models\Book;
use App\Models\Publisher;
use App\Rules\ValidIsbn;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class BookController extends Controller
{
    public function index(Request $request): View
    {
        Gate::authorize(
            'viewAny',
            Book::class
        );

        $user = $request->user();

        $search = trim(
            (string) $request->query('search', '')
        );

        $status = $request->query('status');

        $query = Book::query()
            ->with([
                'publisher',
                'authors',
            ]);

        /*
         * Authors see their own works unless they
         * have broader platform/content privileges.
         */
        if (
            $user->hasRole('author')
            && !$user->hasAnyRole([
                'content_manager',
                'platform_admin',
                'super_admin',
            ])
        ) {
            $query->whereHas(
                'authors',
                fn ($query) =>
                    $query->where(
                        'user_id',
                        $user->id
                    )
            );
        }

        $books = $query
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
                                    fn ($query) =>
                                        $query->where(
                                            'name',
                                            'like',
                                            "%{$search}%"
                                        )
                                );
                        }
                    );
                }
            )
            ->when(
                $status,
                fn ($query) =>
                    $query->where(
                        'status',
                        $status
                    )
            )
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view(
            'books.index',
            compact('books')
        );
    }

    public function create(
        Request $request
    ): View {
        Gate::authorize(
            'create',
            Book::class
        );

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
            'books.create',
            compact(
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
            Book::class
        );

        $validated =
            $this->validateBook(
                $request
            );

        $pdf =
            $request->file('pdf');

        $pdfPath =
            $pdf->store(
                'library/books',
                'local'
            );

        $coverPath = null;

        if ($request->hasFile('cover')) {
            $coverPath =
                $request
                    ->file('cover')
                    ->store(
                        'library/covers',
                        'public'
                    );
        }

        try {
            $book = DB::transaction(
                function () use (
                    $validated,
                    $request,
                    $pdf,
                    $pdfPath,
                    $coverPath
                ) {
                    $book = Book::create([
                        'publisher_id' =>
                            $validated[
                                'publisher_id'
                            ] ?? null,

                        'uploaded_by' =>
                            $request->user()->id,

                        'title' =>
                            $validated['title'],

                        'slug' =>
                            $this->uniqueSlug(
                                $validated['title']
                            ),

                        'isbn' =>
                            $this->normalizeIsbn(
                                $validated['isbn']
                            ),

                        'edition' =>
                            $validated['edition']
                            ?? null,

                        'publication_year' =>
                            $validated[
                                'publication_year'
                            ] ?? null,

                        'language' =>
                            $validated['language'],

                        'category' =>
                            $validated['category']
                            ?? null,

                        'description' =>
                            $validated[
                                'description'
                            ] ?? null,

                        'cover_path' =>
                            $coverPath,

                        'pdf_path' =>
                            $pdfPath,

                        'file_size' =>
                            $pdf->getSize(),

                        'file_hash' =>
                            hash_file(
                                'sha256',
                                $pdf->getRealPath()
                            ),

                        'status' =>
                            'under_review',

                        'submitted_at' =>
                            now(),

                        'allow_online_reading' =>
                            $request->boolean(
                                'allow_online_reading',
                                true
                            ),

                        'allow_download' =>
                            $request->boolean(
                                'allow_download'
                            ),

                        'allow_print' =>
                            $request->boolean(
                                'allow_print'
                            ),

                        'allow_teacher_assignment' =>
                            $request->boolean(
                                'allow_teacher_assignment',
                                true
                            ),

                        'allow_student_borrowing' =>
                            $request->boolean(
                                'allow_student_borrowing',
                                true
                            ),

                        'loan_days' =>
                            $validated['loan_days']
                            ?? 14,

                        'max_concurrent_loans' =>
                            $validated[
                                'max_concurrent_loans'
                            ] ?? null,

                        'rights_statement' =>
                            $validated[
                                'rights_statement'
                            ] ?? null,
                    ]);

                    $book
                        ->authors()
                        ->sync(
                            collect(
                                $validated[
                                    'author_ids'
                                ]
                            )
                                ->mapWithKeys(
                                    fn ($id) => [
                                        $id => [
                                            'contribution' =>
                                                'author',
                                        ],
                                    ]
                                )
                                ->all()
                        );

                    return $book;
                }
            );
        } catch (\Throwable $exception) {
            Storage::disk('local')
                ->delete($pdfPath);

            if ($coverPath) {
                Storage::disk('public')
                    ->delete($coverPath);
            }

            throw $exception;
        }

        return redirect()
            ->route(
                'books.show',
                $book
            )
            ->with(
                'success',
                'Book uploaded and submitted for review.'
            );
    }

    public function show(
        Book $book
    ): View {
        Gate::authorize(
            'view',
            $book
        );

        $book->load([
            'publisher',
            'authors',
            'uploader',
            'reviewer',
            'licenses.school',
        ]);

        return view(
            'books.show',
            compact('book')
        );
    }

    public function edit(
        Book $book
    ): View {
        Gate::authorize(
            'update',
            $book
        );

        $book->load('authors');

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
            'books.edit',
            compact(
                'book',
                'publishers',
                'authors'
            )
        );
    }

    public function update(
        Request $request,
        Book $book
    ): RedirectResponse {
        Gate::authorize(
            'update',
            $book
        );

        $validated =
            $this->validateBook(
                $request,
                $book,
                false
            );

        DB::transaction(
            function () use (
                $request,
                $validated,
                $book
            ) {
                $data = [
                    'publisher_id' =>
                        $validated[
                            'publisher_id'
                        ] ?? null,

                    'title' =>
                        $validated['title'],

                    'isbn' =>
                        $this->normalizeIsbn(
                            $validated['isbn']
                        ),

                    'edition' =>
                        $validated['edition']
                        ?? null,

                    'publication_year' =>
                        $validated[
                            'publication_year'
                        ] ?? null,

                    'language' =>
                        $validated['language'],

                    'category' =>
                        $validated['category']
                        ?? null,

                    'description' =>
                        $validated[
                            'description'
                        ] ?? null,

                    'loan_days' =>
                        $validated['loan_days']
                        ?? 14,

                    'max_concurrent_loans' =>
                        $validated[
                            'max_concurrent_loans'
                        ] ?? null,

                    'rights_statement' =>
                        $validated[
                            'rights_statement'
                        ] ?? null,

                    'allow_online_reading' =>
                        $request->boolean(
                            'allow_online_reading'
                        ),

                    'allow_download' =>
                        $request->boolean(
                            'allow_download'
                        ),

                    'allow_print' =>
                        $request->boolean(
                            'allow_print'
                        ),

                    'allow_teacher_assignment' =>
                        $request->boolean(
                            'allow_teacher_assignment'
                        ),

                    'allow_student_borrowing' =>
                        $request->boolean(
                            'allow_student_borrowing'
                        ),
                ];

                if (
                    $validated['title']
                    !== $book->title
                ) {
                    $data['slug'] =
                        $this->uniqueSlug(
                            $validated['title'],
                            $book->id
                        );
                }

                if ($request->hasFile('cover')) {
                    if ($book->cover_path) {
                        Storage::disk('public')
                            ->delete(
                                $book->cover_path
                            );
                    }

                    $data['cover_path'] =
                        $request
                            ->file('cover')
                            ->store(
                                'library/covers',
                                'public'
                            );
                }

                if ($request->hasFile('pdf')) {
                    $oldPath =
                        $book->pdf_path;

                    $file =
                        $request->file('pdf');

                    $newPath =
                        $file->store(
                            'library/books',
                            'local'
                        );

                    $data['pdf_path'] =
                        $newPath;

                    $data['file_size'] =
                        $file->getSize();

                    $data['file_hash'] =
                        hash_file(
                            'sha256',
                            $file->getRealPath()
                        );

                    Storage::disk('local')
                        ->delete($oldPath);
                }

                /*
                 * Any author revision returns the work
                 * to the review queue.
                 */
                $data['status'] =
                    'under_review';

                $data['submitted_at'] =
                    now();

                $data['reviewed_at'] =
                    null;

                $data['reviewed_by'] =
                    null;

                $book->update($data);

                $book
                    ->authors()
                    ->sync(
                        collect(
                            $validated[
                                'author_ids'
                            ]
                        )
                            ->mapWithKeys(
                                fn ($id) => [
                                    $id => [
                                        'contribution' =>
                                            'author',
                                    ],
                                ]
                            )
                            ->all()
                    );
            }
        );

        return redirect()
            ->route(
                'books.show',
                $book
            )
            ->with(
                'success',
                'Book updated and submitted for review.'
            );
    }

    public function destroy(
        Book $book
    ): RedirectResponse {
        Gate::authorize(
            'delete',
            $book
        );

        DB::transaction(
            function () use ($book) {
                Storage::disk('local')
                    ->delete(
                        $book->pdf_path
                    );

                if ($book->cover_path) {
                    Storage::disk('public')
                        ->delete(
                            $book->cover_path
                        );
                }

                $book->delete();
            }
        );

        return redirect()
            ->route('books.index')
            ->with(
                'success',
                'Book deleted successfully.'
            );
    }

    private function validateBook(
        Request $request,
        ?Book $book = null,
        bool $pdfRequired = true
    ): array {
        return $request->validate([
            'publisher_id' => [
                'nullable',
                Rule::exists(
                    'publishers',
                    'id'
                ),
            ],

            'author_ids' => [
                'required',
                'array',
                'min:1',
            ],

            'author_ids.*' => [
                'integer',
                Rule::exists(
                    'authors',
                    'id'
                ),
            ],

            'title' => [
                'required',
                'string',
                'max:255',
            ],

            'isbn' => [
                'required',
                new ValidIsbn(),

                Rule::unique(
                    'books',
                    'isbn'
                )->ignore(
                    $book?->id
                ),
            ],

            'edition' => [
                'nullable',
                'string',
                'max:100',
            ],

            'publication_year' => [
                'nullable',
                'integer',
                'min:1000',
                'max:' . now()->year,
            ],

            'language' => [
                'required',
                'string',
                'max:50',
            ],

            'category' => [
                'nullable',
                'string',
                'max:100',
            ],

            'description' => [
                'nullable',
                'string',
                'max:20000',
            ],

            'cover' => [
                'nullable',
                'image',
                'max:5120',
            ],

            'pdf' => [
                $pdfRequired
                    ? 'required'
                    : 'nullable',

                'file',
                'mimes:pdf',
                'max:102400',
            ],

            'loan_days' => [
                'nullable',
                'integer',
                'min:1',
                'max:365',
            ],

            'max_concurrent_loans' => [
                'nullable',
                'integer',
                'min:1',
            ],

            'rights_statement' => [
                'nullable',
                'string',
                'max:10000',
            ],
        ]);
    }

    private function normalizeIsbn(
        string $isbn
    ): string {
        return strtoupper(
            preg_replace(
                '/[^0-9X]/i',
                '',
                $isbn
            )
        );
    }

    private function uniqueSlug(
        string $title,
        ?int $ignoreId = null
    ): string {
        $base =
            Str::slug($title)
            ?: 'book';

        $slug = $base;

        $counter = 1;

        while (
            Book::query()
                ->where('slug', $slug)
                ->when(
                    $ignoreId,
                    fn ($query) =>
                        $query->where(
                            'id',
                            '!=',
                            $ignoreId
                        )
                )
                ->exists()
        ) {
            $slug =
                $base . '-' . $counter++;
        }

        return $slug;
    }
}