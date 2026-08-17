<?php

namespace App\Http\Controllers;

use App\Models\Author;
use App\Models\Publisher;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AuthorController extends Controller
{
    /**
     * Display a listing of authors.
     */
    public function index(
        Request $request
    ): View {
        Gate::authorize(
            'viewAny',
            Author::class
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

        $authors = Author::query()
            ->with([
                'publisher',
                'user',
            ])
            ->withCount([
                'books',
                'schoolBookLicenses',
            ])
            ->when(
                $search !== '',
                function ($query) use ($search) {
                    $query->where(
                        function ($query) use ($search) {
                            $query
                                ->where(
                                    'name',
                                    'like',
                                    "%{$search}%"
                                )
                                ->orWhereHas(
                                    'publisher',
                                    fn ($query) =>
                                        $query->where(
                                            'name',
                                            'like',
                                            "%{$search}%"
                                        )
                                )
                                ->orWhereHas(
                                    'user',
                                    fn ($query) =>
                                        $query->where(
                                            'email',
                                            'like',
                                            "%{$search}%"
                                        )
                                );
                        }
                    );
                }
            )
            ->when(
                in_array(
                    $status,
                    [
                        'pending',
                        'verified',
                        'suspended',
                    ],
                    true
                ),
                fn ($query) =>
                    $query->where(
                        'status',
                        $status
                    )
            )
            ->orderBy('name')
            ->paginate(20)
            ->withQueryString();

        return view(
            'authors.index',
            compact('authors')
        );
    }


    /**
     * Show the form for creating an author.
     */
    public function create(): View
    {
        Gate::authorize(
            'create',
            Author::class
        );

        $publishers = Publisher::query()
            ->where(
                'status',
                'active'
            )
            ->orderBy('name')
            ->get();

        return view(
            'authors.create',
            compact('publishers')
        );
    }


    /**
     * Store a newly created author.
     */
    public function store(
        Request $request
    ): RedirectResponse {
        Gate::authorize(
            'create',
            Author::class
        );

        $validated =
            $this->validateAuthor(
                $request
            );

        $validated['slug'] =
            $this->uniqueSlug(
                $validated['name']
            );

        if (
            $request->hasFile('photo')
        ) {
            $validated['photo_path'] =
                $request
                    ->file('photo')
                    ->store(
                        'library/authors',
                        'public'
                    );
        }

        unset(
            $validated['photo']
        );

        $author = Author::query()
            ->create(
                $validated
            );

        return redirect()
            ->route(
                'authors.show',
                $author
            )
            ->with(
                'status',
                'Author created successfully.'
            );
    }


    /**
     * Display the specified author.
     */
    public function show(
        Author $author
    ): View {
        Gate::authorize(
            'view',
            $author
        );

        $author->load([
            'user',

            'publisher',

            'books' => fn ($query) =>
                $query
                    ->with('publisher')
                    ->latest(),

            'schoolBookLicenses' => fn ($query) =>
                $query
                    ->with([
                        'school',
                        'book',
                    ])
                    ->latest(),
        ]);

        return view(
            'authors.show',
            compact('author')
        );
    }


    /**
     * Show the form for editing an author.
     */
    public function edit(
        Author $author
    ): View {
        Gate::authorize(
            'update',
            $author
        );

        $publishers = Publisher::query()
            ->where(
                'status',
                'active'
            )
            ->orderBy('name')
            ->get();

        return view(
            'authors.edit',
            compact(
                'author',
                'publishers'
            )
        );
    }


    /**
     * Update the specified author.
     */
    public function update(
        Request $request,
        Author $author
    ): RedirectResponse {
        Gate::authorize(
            'update',
            $author
        );

        $validated =
            $this->validateAuthor(
                $request,
                $author
            );


        /*
        |--------------------------------------------------------------------------
        | Slug
        |--------------------------------------------------------------------------
        */

        if (
            $validated['name']
            !== $author->name
        ) {
            $validated['slug'] =
                $this->uniqueSlug(
                    $validated['name'],
                    $author->id
                );
        }


        /*
        |--------------------------------------------------------------------------
        | Photo Replacement
        |--------------------------------------------------------------------------
        |
        | Store the replacement first, then delete the old image.
        |
        */

        if (
            $request->hasFile('photo')
        ) {
            $newPhoto =
                $request
                    ->file('photo')
                    ->store(
                        'library/authors',
                        'public'
                    );

            if (
                $author->photo_path
            ) {
                Storage::disk('public')
                    ->delete(
                        $author->photo_path
                    );
            }

            $validated['photo_path'] =
                $newPhoto;
        }

        unset(
            $validated['photo']
        );

        $author->update(
            $validated
        );

        return redirect()
            ->route(
                'authors.show',
                $author
            )
            ->with(
                'status',
                'Author updated successfully.'
            );
    }


    /**
     * Remove the specified author.
     */
    public function destroy(
        Author $author
    ): RedirectResponse {
        Gate::authorize(
            'delete',
            $author
        );


        /*
        |--------------------------------------------------------------------------
        | Integrity Guard
        |--------------------------------------------------------------------------
        |
        | Catalogue history should not disappear simply because an author
        | profile is being removed.
        |
        */

        if (
            $author
                ->books()
                ->exists()
        ) {
            return redirect()
                ->route(
                    'authors.show',
                    $author
                )
                ->withErrors([
                    'author' =>
                        'This author cannot be deleted because catalogue books are associated with the profile.',
                ]);
        }


        if (
            $author
                ->schoolBookLicenses()
                ->exists()
        ) {
            return redirect()
                ->route(
                    'authors.show',
                    $author
                )
                ->withErrors([
                    'author' =>
                        'This author cannot be deleted because licence records are associated with the profile.',
                ]);
        }


        if (
            $author->photo_path
        ) {
            Storage::disk('public')
                ->delete(
                    $author->photo_path
                );
        }

        $author->delete();

        return redirect()
            ->route(
                'authors.index'
            )
            ->with(
                'status',
                'Author deleted successfully.'
            );
    }


    /**
     * Validate author input.
     */
    private function validateAuthor(
        Request $request,
        ?Author $author = null
    ): array {
        return $request->validate([
            'user_id' => [
                'nullable',

                Rule::exists(
                    'users',
                    'id'
                ),
            ],

            'publisher_id' => [
                'nullable',

                Rule::exists(
                    'publishers',
                    'id'
                )->where(
                    fn ($query) =>
                        $query->where(
                            'status',
                            'active'
                        )
                ),
            ],

            'name' => [
                'required',
                'string',
                'max:255',
            ],

            'biography' => [
                'nullable',
                'string',
                'max:10000',
            ],

            'photo' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:4096',
            ],

            'status' => [
                'required',

                Rule::in([
                    'pending',
                    'verified',
                    'suspended',
                ]),
            ],
        ]);
    }


    /**
     * Generate a unique author slug.
     */
    private function uniqueSlug(
        string $name,
        ?int $ignoreId = null
    ): string {
        $base =
            Str::slug($name)
            ?: 'author';

        $slug = $base;
        $counter = 2;

        while (
            Author::query()
                ->where(
                    'slug',
                    $slug
                )
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
                $base
                . '-'
                . $counter;

            $counter++;
        }

        return $slug;
    }
}