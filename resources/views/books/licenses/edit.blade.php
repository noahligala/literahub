<x-layouts.dashboard>

    <div class="page-shell">

        <div class="page-header">

            <div>

                <span class="eyebrow">
                    Distribution
                </span>

                <h1>
                    Edit Licence
                </h1>

                <p>
                    {{ $license->license_number }}
                </p>

            </div>


            <a
                href="{{ route(
                    'book-licenses.show',
                    $license
                ) }}"
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
            action="{{ route(
                'book-licenses.update',
                $license
            ) }}"
        >

            @csrf
            @method('PUT')


            <x-forms.book-license
                :license="$license"
                :schools="$schools"
                :books="$books"
                :publishers="$publishers"
                :authors="$authors"
            />

        </form>

    </div>

</x-layouts.dashboard>