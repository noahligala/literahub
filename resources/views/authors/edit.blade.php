<x-layouts.dashboard>

    <div class="page-shell">

        <div class="page-header">

            <div>
                <span class="eyebrow">
                    Creators
                </span>

                <h1>
                    Edit Author
                </h1>

                <p>
                    {{ $author->name }}
                </p>
            </div>


            <a
                href="{{ route(
                    'authors.show',
                    $author
                ) }}"
                class="btn btn--secondary"
            >
                Cancel
            </a>

        </div>


        @if ($errors->any())

            <div class="alert alert-error">

                <strong>
                    Please correct the following information.
                </strong>

                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>

            </div>

        @endif


        <form
            method="POST"
            action="{{ route(
                'authors.update',
                $author
            ) }}"
            enctype="multipart/form-data"
        >

            @csrf
            @method('PUT')

            <x-forms.author
                :author="$author"
                :publishers="$publishers"
            />

        </form>

    </div>

</x-layouts.dashboard>