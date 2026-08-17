<x-layouts.dashboard>

    <div class="page-shell">

        <div class="page-header">

            <div>

                <span class="eyebrow">
                    Distribution
                </span>

                <h1>
                    Issue Book Licence
                </h1>

                <p>
                    Grant an institution defined access to a published book.
                </p>

            </div>


            <a
                href="{{ route('book-licenses.index') }}"
                class="btn btn--secondary"
            >
                Cancel
            </a>

        </div>


        @if ($errors->any())

            <div class="alert alert-error">

                <strong>
                    Please correct the licence details.
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
            action="{{ route('book-licenses.store') }}"
        >

            @csrf


            <x-forms.book-license
                :schools="$schools"
                :books="$books"
                :publishers="$publishers"
                :authors="$authors"
            />

        </form>

    </div>

</x-layouts.dashboard>