<x-layouts.dashboard>

    <div class="page-shell">

        <div class="page-header">

            <div>

                <span class="eyebrow">
                    Digital Catalogue
                </span>

                <h1>
                    Edit Book
                </h1>

                <p>
                    Update the book metadata, files,
                    authorship and distribution permissions.
                </p>

            </div>


            <a
                href="{{ route('books.show', $book) }}"
                class="btn btn--secondary"
            >
                Cancel
            </a>

        </div>


        @if ($errors->any())

            <div class="alert alert-error">

                <strong>
                    Please correct the following issues.
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
            action="{{ route('books.update', $book) }}"
            enctype="multipart/form-data"
        >

            @csrf
            @method('PUT')


            <x-forms.book
                :book="$book"
                :publishers="$publishers"
                :authors="$authors"
            />

        </form>

    </div>

</x-layouts.dashboard>