<x-layouts.dashboard title="{{ $class->name }} — LiteraHub">

    <div class="dashboard-heading">

        <div>

            <span class="eyebrow">
                Class
            </span>

            <h1>
                {{ $class->name }}
            </h1>

            <p>
                {{ $class->academic_year }}
                @if($class->level)
                    · {{ $class->level }}
                @endif
            </p>

        </div>

        <div class="actions">

            <a
                href="{{ route(
                    'school.classes.edit',
                    $class
                ) }}"
                class="button"
            >
                Edit Class
            </a>

            <a
                href="{{ route(
                    'school.streams.create',
                    $class
                ) }}"
                class="button button-secondary"
            >
                + Add Stream
            </a>

        </div>

    </div>

    <div class="metric-grid">

        <article>
            <strong>
                {{ $class->students_count }}
            </strong>

            <span>Students</span>
        </article>

        <article>
            <strong>
                {{ $class->teachers_count }}
            </strong>

            <span>Teachers</span>
        </article>

        <article>
            <strong>
                {{ $class->streams_count }}
            </strong>

            <span>Streams</span>
        </article>

    </div>

    <div style="height:12px;"></div>

    <div class="card">

        <div class="row-between">

            <div>
                <h3>Streams</h3>

                <p>
                    Streams currently configured
                    for this class.
                </p>
            </div>

        </div>

        <div class="table-wrapper">

            <table>

                <thead>
                    <tr>
                        <th>Stream</th>
                        <th>Teacher</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>

                <tbody>

                    @forelse($class->streams as $stream)

                        <tr>

                            <td>
                                {{ $stream->name }}
                            </td>

                            <td>
                                {{ $stream
                                    ->teacher?->name
                                    ?? 'Not assigned'
                                }}
                            </td>

                            <td>
                                <span class="badge badge-success">
                                    {{ ucfirst(
                                        $stream->status
                                    ) }}
                                </span>
                            </td>

                            <td>
                                <a
                                    href="{{ route(
                                        'school.streams.edit',
                                        $stream
                                    ) }}"
                                >
                                    Edit
                                </a>
                            </td>

                        </tr>

                    @empty

                        <tr>
                            <td colspan="4">
                                No streams created.
                            </td>
                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

    </div>

</x-layouts.dashboard>