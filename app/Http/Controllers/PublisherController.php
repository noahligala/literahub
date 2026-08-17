<?php

namespace App\Http\Controllers;

use App\Models\Publisher;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class PublisherController extends Controller
{
    /**
     * Display a listing of publishers.
     */
    public function index(
        Request $request
    ): View {
        Gate::authorize(
            'viewAny',
            Publisher::class
        );

        $publishers = Publisher::query()
            ->withCount([
                'authors',
                'books',
                'schoolBookLicenses',
            ])

            ->when(
                $request->filled('search'),
                function ($query) use ($request) {
                    $search = trim(
                        $request->string('search')->toString()
                    );

                    $query->where(
                        function ($query) use ($search) {
                            $query
                                ->where(
                                    'name',
                                    'like',
                                    "%{$search}%"
                                )
                                ->orWhere(
                                    'registration_number',
                                    'like',
                                    "%{$search}%"
                                )
                                ->orWhere(
                                    'email',
                                    'like',
                                    "%{$search}%"
                                )
                                ->orWhere(
                                    'phone',
                                    'like',
                                    "%{$search}%"
                                );
                        }
                    );
                }
            )

            ->when(
                $request->filled('status'),
                fn ($query) =>
                    $query->where(
                        'status',
                        $request->string('status')->toString()
                    )
            )

            ->orderBy('name')

            ->paginate(20)

            ->withQueryString();


        return view(
            'publishers.index',
            compact('publishers')
        );
    }


    /**
     * Show the form for creating a publisher.
     */
    public function create(): View
    {
        Gate::authorize(
            'create',
            Publisher::class
        );

        return view(
            'publishers.create'
        );
    }


    /**
     * Store a newly created publisher.
     */
    public function store(
        Request $request
    ): RedirectResponse {
        Gate::authorize(
            'create',
            Publisher::class
        );

        $validated = $this->validatePublisher(
            $request
        );


        /*
        |--------------------------------------------------------------------------
        | Logo
        |--------------------------------------------------------------------------
        */

        if (
            $request->hasFile('logo')
        ) {
            $validated['logo_path'] =
                $request
                    ->file('logo')
                    ->store(
                        'publishers/logos',
                        'public'
                    );
        }


        /*
        |--------------------------------------------------------------------------
        | Slug
        |--------------------------------------------------------------------------
        */

        $validated['slug'] =
            $this->uniqueSlug(
                $validated['name']
            );


        $publisher =
            Publisher::query()
                ->create(
                    $validated
                );


        return redirect()
            ->route(
                'publishers.show',
                $publisher
            )
            ->with(
                'status',
                'Publisher created successfully.'
            );
    }


    /**
     * Display a publisher.
     */
    public function show(
        Publisher $publisher
    ): View {
        Gate::authorize(
            'view',
            $publisher
        );


        $publisher->load([
            'authors' => fn ($query) =>
                $query->orderBy('name'),

            'books' => fn ($query) =>
                $query
                    ->with('authors')
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
            'publishers.show',
            compact('publisher')
        );
    }


    /**
     * Show the form for editing a publisher.
     */
    public function edit(
        Publisher $publisher
    ): View {
        Gate::authorize(
            'update',
            $publisher
        );


        return view(
            'publishers.edit',
            compact('publisher')
        );
    }


    /**
     * Update the specified publisher.
     */
    public function update(
        Request $request,
        Publisher $publisher
    ): RedirectResponse {
        Gate::authorize(
            'update',
            $publisher
        );


        $validated =
            $this->validatePublisher(
                $request,
                $publisher
            );


        /*
        |--------------------------------------------------------------------------
        | Update Logo
        |--------------------------------------------------------------------------
        */

        if (
            $request->hasFile('logo')
        ) {
            $newLogo =
                $request
                    ->file('logo')
                    ->store(
                        'publishers/logos',
                        'public'
                    );


            /*
             * Store the new file first, then delete the old one.
             */
            if (
                $publisher->logo_path
            ) {
                Storage::disk('public')
                    ->delete(
                        $publisher->logo_path
                    );
            }


            $validated['logo_path'] =
                $newLogo;
        }


        /*
        |--------------------------------------------------------------------------
        | Update slug only if name changed
        |--------------------------------------------------------------------------
        */

        if (
            array_key_exists(
                'name',
                $validated
            )
            &&
            $validated['name']
            !== $publisher->name
        ) {
            $validated['slug'] =
                $this->uniqueSlug(
                    $validated['name'],
                    $publisher
                );
        }


        $publisher->update(
            $validated
        );


        return redirect()
            ->route(
                'publishers.show',
                $publisher
            )
            ->with(
                'status',
                'Publisher updated successfully.'
            );
    }


    /**
     * Remove the specified publisher.
     */
    public function destroy(
        Publisher $publisher
    ): RedirectResponse {
        Gate::authorize(
            'delete',
            $publisher
        );


        /*
        |--------------------------------------------------------------------------
        | Final integrity guard
        |--------------------------------------------------------------------------
        |
        | Policy should already prevent this, but keeping the controller guard
        | protects the database if policy rules change later.
        |
        */

        if (
            $publisher
                ->books()
                ->exists()
            ||
            $publisher
                ->schoolBookLicenses()
                ->exists()
        ) {
            return redirect()
                ->route(
                    'publishers.show',
                    $publisher
                )
                ->withErrors([
                    'publisher' =>
                        'This publisher cannot be deleted because it has books or licence records.',
                ]);
        }


        if (
            $publisher->logo_path
        ) {
            Storage::disk('public')
                ->delete(
                    $publisher->logo_path
                );
        }


        $publisher->delete();


        return redirect()
            ->route(
                'publishers.index'
            )
            ->with(
                'status',
                'Publisher deleted successfully.'
            );
    }


    /**
     * Validate publisher input.
     */
    private function validatePublisher(
        Request $request,
        ?Publisher $publisher = null
    ): array {
        return $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
            ],

            'registration_number' => [
                'nullable',
                'string',
                'max:100',

                Rule::unique(
                    'publishers',
                    'registration_number'
                )->ignore(
                    $publisher?->id
                ),
            ],

            'email' => [
                'nullable',
                'email',
                'max:255',
            ],

            'phone' => [
                'nullable',
                'string',
                'max:50',
            ],

            'website' => [
                'nullable',
                'url',
                'max:255',
            ],

            'address' => [
                'nullable',
                'string',
                'max:1000',
            ],

            'description' => [
                'nullable',
                'string',
                'max:10000',
            ],

            'status' => [
                'required',

                Rule::in([
                    'pending',
                    'active',
                    'suspended',
                ]),
            ],

            'logo' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:5120',
            ],
        ]);
    }


    /**
     * Generate a unique publisher slug.
     */
    private function uniqueSlug(
        string $name,
        ?Publisher $ignore = null
    ): string {
        $base =
            Str::slug($name);

        if (
            blank($base)
        ) {
            $base = 'publisher';
        }


        $slug = $base;
        $counter = 2;


        while (
            Publisher::query()
                ->where(
                    'slug',
                    $slug
                )
                ->when(
                    $ignore,
                    fn ($query) =>
                        $query->whereKeyNot(
                            $ignore->getKey()
                        )
                )
                ->exists()
        ) {
            $slug =
                "{$base}-{$counter}";

            $counter++;
        }


        return $slug;
    }
}