@props([
    'resource',
])

<article class="library-resource-card">

    <div class="library-resource-card__cover">

        @if(!empty($resource->cover_url))

            <img
                src="{{ $resource->cover_url }}"
                alt="{{ $resource->title }}"
            >

        @else

            <div class="library-resource-placeholder">
                {{ strtoupper(
                    substr(
                        $resource->title,
                        0,
                        1
                    )
                ) }}
            </div>

        @endif

    </div>


    <div class="library-resource-card__body">

        <div class="library-resource-card__meta">

            <span class="badge badge-primary">
                {{ $resource->category
                    ?? 'Literature'
                }}
            </span>

        </div>


        <h3>
            {{ $resource->title }}
        </h3>


        <p>
            {{ $resource->author
                ?? 'Unknown Author'
            }}
        </p>


        @if(!empty($resource->description))

            <p class="resource-description">
                {{ \Illuminate\Support\Str::limit(
                    $resource->description,
                    110
                ) }}
            </p>

        @endif


        <div class="library-resource-card__actions">

            <a
                href="{{ route(
                    'school.library.show',
                    $resource
                ) }}"
                class="button button-secondary button-small"
            >
                View Resource
            </a>

        </div>

    </div>

</article>