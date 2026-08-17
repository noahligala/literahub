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
    public function index(Request $request): View
    {
        Gate::authorize(
            'viewAny',
            Author::class
        );

        $search = trim(
            (string) $request->query('search', '')
        );

        $authors = Author::query()
            ->with('publisher')
            ->withCount('books')
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
                                );
                        }
                    );
                }
            )
            ->orderBy('name')
            ->paginate(20)
            ->withQueryString();

        return view(
            'authors.index',
            compact('authors')
        );
    }

    public function create(): View
    {
        Gate::authorize(
            'create',
            Author::class
        );

        $publishers =
            Publisher::query()
                ->where('status', 'active')
                ->orderBy('name')
                ->get();

        return view(
            'authors.create',
            compact('publishers')
        );
    }

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

        if ($request->hasFile('photo')) {
            $validated['photo_path'] =
                $request
                    ->file('photo')
                    ->store(
                        'library/authors',
                        'public'
                    );
        }

        unset($validated['photo']);

        $author = Author::create(
            $validated
        );

        return redirect()
            ->route(
                'authors.show',
                $author
            )
            ->with(
                'success',
                'Author created successfully.'
            );
    }

    public function show(
        Author $author
    ): View {
        Gate::authorize(
            'view',
            $author
        );

        $author->load([
            'publisher',
            'books.publisher',
        ]);

        return view(
            'authors.show',
            compact('author')
        );
    }

    public function edit(
        Author $author
    ): View {
        Gate::authorize(
            'update',
            $author
        );

        $publishers =
            Publisher::query()
                ->where('status', 'active')
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

        if ($request->hasFile('photo')) {
            if ($author->photo_path) {
                Storage::disk('public')
                    ->delete(
                        $author->photo_path
                    );
            }

            $validated['photo_path'] =
                $request
                    ->file('photo')
                    ->store(
                        'library/authors',
                        'public'
                    );
        }

        unset($validated['photo']);

        $author->update(
            $validated
        );

        return redirect()
            ->route(
                'authors.show',
                $author
            )
            ->with(
                'success',
                'Author updated successfully.'
            );
    }

    public function destroy(
        Author $author
    ): RedirectResponse {
        Gate::authorize(
            'delete',
            $author
        );

        if ($author->photo_path) {
            Storage::disk('public')
                ->delete(
                    $author->photo_path
                );
        }

        $author->delete();

        return redirect()
            ->route('authors.index')
            ->with(
                'success',
                'Author deleted successfully.'
            );
    }

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

    private function uniqueSlug(
        string $name,
        ?int $ignoreId = null
    ): string {
        $base =
            Str::slug($name)
            ?: 'author';

        $slug = $base;
        $counter = 1;

        while (
            Author::query()
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