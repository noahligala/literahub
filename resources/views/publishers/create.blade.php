<x-layouts.dashboard>

    <div class="page-shell">

        <div class="page-header">

            <div>
                <span class="eyebrow">
                    Rights Holders
                </span>

                <h1>
                    Add Publisher
                </h1>

                <p>
                    Register a publishing organisation that can own
                    catalogue titles and issue institutional licences.
                </p>
            </div>


            <a
                href="{{ route('publishers.index') }}"
                class="btn btn--secondary"
            >
                Cancel
            </a>

        </div>


        @if ($errors->any())

            <div class="alert alert-error">

                <strong>
                    The publisher could not be saved.
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
            action="{{ route('publishers.store') }}"
            enctype="multipart/form-data"
        >

            @csrf

            <x-forms.publisher />

        </form>

    </div>

</x-layouts.dashboard>