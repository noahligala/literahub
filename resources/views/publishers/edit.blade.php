<x-layouts.dashboard>

    <div class="page-shell">

        <div class="page-header">

            <div>
                <span class="eyebrow">
                    Rights Holders
                </span>

                <h1>
                    Edit Publisher
                </h1>

                <p>
                    {{ $publisher->name }}
                </p>
            </div>


            <a
                href="{{ route(
                    'publishers.show',
                    $publisher
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
                'publishers.update',
                $publisher
            ) }}"
            enctype="multipart/form-data"
        >

            @csrf
            @method('PUT')

            <x-forms.publisher
                :publisher="$publisher"
            />

        </form>

    </div>

</x-layouts.dashboard>