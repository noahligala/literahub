<x-layouts.dashboard>

    <div class="page-shell">

        <div class="page-header">

            <div>

                <span class="eyebrow">
                    Digital Catalogue
                </span>

                <h1>
                    Upload Book
                </h1>

                <p>
                    Add a new title, define its intellectual-property
                    permissions and submit it for review.
                </p>

            </div>


            <a
                href="{{ route('books.index') }}"
                class="btn btn--secondary"
            >
                Back to Books
            </a>

        </div>


        @if ($errors->any())

            <div class="alert alert-error">

                <strong>
                    The book could not be saved.
                </strong>

                <ul>
                    @foreach ($errors->all() as $error)
                        <li>
                            {{ $error }}
                        </li>
                    @endforeach
                </ul>

            </div>

        @endif


        <form
            method="POST"
            action="{{ route('books.store') }}"
            enctype="multipart/form-data"
        >

            @csrf

            <x-forms.book
                :publishers="$publishers"
                :authors="$authors"
            />

        </form>

    </div>

</x-layouts.dashboard>