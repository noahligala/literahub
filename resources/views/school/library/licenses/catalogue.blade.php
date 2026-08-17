<x-layouts.dashboard>

    <div class="page-shell">

        <div class="page-header">

            <div>
                <span class="eyebrow">
                    Licensing
                </span>

                <h1>
                    Available Titles
                </h1>

                <p>
                    Explore published titles that your institution
                    can request for licensing.
                </p>
            </div>


            <a
                href="{{ route(
                    'school.library.licenses.index'
                ) }}"
                class="btn btn--secondary"
            >
                Current Licences
            </a>

        </div>


        <div class="library-grid">

            @forelse ($books as $book)

                <article class="card" style="padding:15px;">

                    <span class="eyebrow">
                        {{ $book->category ?? 'Literature' }}
                    </span>

                    <h2 style="font-size:.78rem;">
                        {{ $book->title }}
                    </h2>

                    <p style="font-size:.56rem;color:var(--color-text-muted);">
                        {{ $book->authors
                            ->pluck('name')
                            ->join(', ')
                        }}
                    </p>


                    <form
                        method="POST"
                        action="{{ route(
                            'school.library.licenses.request',
                            $book
                        ) }}"
                    >

                        @csrf

                        <button
                            type="submit"
                            class="btn btn--primary btn--block"
                        >
                            Request Licence
                        </button>

                    </form>

                </article>

            @empty

                <div class="card empty-state">

                    <h2>
                        No additional titles available
                    </h2>

                </div>

            @endforelse

        </div>


        <div style="margin-top:18px;">
            {{ $books->links() }}
        </div>

    </div>


    <style>

        .library-grid {
            display: grid;
            grid-template-columns:
                repeat(
                    auto-fill,
                    minmax(220px, 1fr)
                );
            gap: 12px;
        }

        .btn--block {
            width: 100%;
            justify-content: center;
        }

    </style>

</x-layouts.dashboard>