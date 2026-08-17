<x-layouts.dashboard title="Library — LiteraHub">

    <div class="dashboard-heading">

        <div>

            <span class="eyebrow">
                Digital Library
            </span>

            <h1>
                School Library
            </h1>

            <p>
                Browse resources available under your
                institution's subscription.
            </p>

        </div>

    </div>


    <div class="card library-toolbar">

        <form
            method="GET"
            action="{{ route('school.library.index') }}"
        >

            <div class="form-grid">

                <div class="form-group">

                    <label for="search">
                        Search Library
                    </label>

                    <input
                        id="search"
                        type="search"
                        name="search"
                        value="{{ request('search') }}"
                        placeholder="Title, author or keyword..."
                    >

                </div>


                <div class="form-group">

                    <label for="category">
                        Category
                    </label>

                    <select
                        id="category"
                        name="category"
                    >

                        <option value="">
                            All Categories
                        </option>

                        <option>Literature</option>
                        <option>Novel</option>
                        <option>Drama</option>
                        <option>Poetry</option>
                        <option>Study Guide</option>
                        <option>Reference</option>

                    </select>

                </div>

            </div>


            <div class="form-actions">

                <button class="button">
                    Search
                </button>

                <a
                    href="{{ route(
                        'school.library.index'
                    ) }}"
                    class="button button-secondary"
                >
                    Reset
                </a>

            </div>

        </form>

    </div>


    <div style="height: 14px;"></div>


    @if(isset($resources) && $resources->count())

        <div class="library-grid">

            @foreach($resources as $resource)

                <x-library.resource-card
                    :resource="$resource"
                />

            @endforeach

        </div>

    @else

        <div class="empty-state">

            <h3>
                No resources found
            </h3>

            <p>
                Available LiteraHub resources will
                appear here.
            </p>

        </div>

    @endif

</x-layouts.dashboard>