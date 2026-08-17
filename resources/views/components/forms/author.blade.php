@props([
    'author' => null,
    'publishers' => collect(),
])

@php
    $editing = filled($author);
@endphp


<div class="entity-form-layout">

    <div class="entity-form-main">

        {{-- Identity --}}
        <section class="card entity-form-section">

            <div class="entity-form-section__header">

                <span class="eyebrow">
                    Creator
                </span>

                <h2>
                    Author Information
                </h2>

            </div>


            <div class="entity-form-grid entity-form-grid--2">

                <div class="form-group entity-form-full">

                    <label for="name">
                        Author Name
                    </label>

                    <input
                        id="name"
                        type="text"
                        name="name"
                        value="{{ old(
                            'name',
                            $author?->name
                        ) }}"
                        required
                    >

                </div>


                <div class="form-group">

                    <label for="publisher_id">
                        Publisher
                    </label>

                    <select
                        id="publisher_id"
                        name="publisher_id"
                    >

                        <option value="">
                            Independent Author
                        </option>

                        @foreach ($publishers as $publisher)

                            <option
                                value="{{ $publisher->id }}"
                                @selected(
                                    (string) old(
                                        'publisher_id',
                                        $author?->publisher_id
                                        ?? request('publisher')
                                    )
                                    ===
                                    (string) $publisher->id
                                )
                            >
                                {{ $publisher->name }}
                            </option>

                        @endforeach

                    </select>

                </div>


                <div class="form-group">

                    <label for="status">
                        Status
                    </label>

                    <select
                        id="status"
                        name="status"
                        required
                    >

                        @foreach ([
                            'pending' => 'Pending Verification',
                            'verified' => 'Verified',
                            'suspended' => 'Suspended',
                        ] as $value => $label)

                            <option
                                value="{{ $value }}"
                                @selected(
                                    old(
                                        'status',
                                        $author?->status ?? 'pending'
                                    ) === $value
                                )
                            >
                                {{ $label }}
                            </option>

                        @endforeach

                    </select>

                </div>

            </div>

        </section>


        {{-- Biography --}}
        <section class="card entity-form-section">

            <div class="entity-form-section__header">

                <span class="eyebrow">
                    Profile
                </span>

                <h2>
                    Biography
                </h2>

            </div>


            <div class="form-group">

                <label for="biography">
                    Author Biography
                </label>

                <textarea
                    id="biography"
                    name="biography"
                    rows="8"
                    placeholder="Author background, literary work and professional profile..."
                >{{ old(
                    'biography',
                    $author?->biography
                ) }}</textarea>

            </div>

        </section>


        {{-- Photo --}}
        <section class="card entity-form-section">

            <div class="entity-form-section__header">

                <span class="eyebrow">
                    Media
                </span>

                <h2>
                    Author Photo
                </h2>

            </div>


            <div class="form-group">

                <label for="photo">
                    Profile Photo
                </label>

                <input
                    id="photo"
                    type="file"
                    name="photo"
                    accept="image/png,image/jpeg,image/webp"
                >


                @if (
                    $editing
                    && $author->photo_path
                )

                    <small>
                        An author photo is already stored.
                        Upload another only to replace it.
                    </small>

                @endif

            </div>

        </section>

    </div>


    <aside class="entity-form-sidebar">

        <section class="card entity-submit-card">

            <span class="eyebrow">
                Author
            </span>

            <h2>
                {{ $editing
                    ? 'Save Profile'
                    : 'Register Author'
                }}
            </h2>

            <p>
                Verified authors may upload works and participate
                in rights and licensing workflows.
            </p>


            <button
                type="submit"
                class="btn btn--primary btn--block"
            >
                {{ $editing
                    ? 'Save Author'
                    : 'Create Author'
                }}
            </button>


            <a
                href="{{ $editing
                    ? route(
                        'authors.show',
                        $author
                    )
                    : route(
                        'authors.index'
                    )
                }}"
                class="btn btn--secondary btn--block"
            >
                Cancel
            </a>

        </section>


        <section class="card entity-help-card">

            <strong>
                Verification
            </strong>

            <p>
                Verification confirms an author profile for LiteraHub
                catalogue administration. Distribution rights should still
                follow the applicable rights-holder agreement.
            </p>

        </section>

    </aside>

</div>


<style>
    .entity-form-layout {
        display: grid;
        grid-template-columns: minmax(0, 1fr) 280px;
        gap: 16px;
        align-items: start;
    }

    .entity-form-main {
        display: flex;
        flex-direction: column;
        gap: 14px;
    }

    .entity-form-sidebar {
        position: sticky;
        top: 88px;
        display: flex;
        flex-direction: column;
        gap: 12px;
    }

    .entity-form-section,
    .entity-submit-card,
    .entity-help-card {
        padding: 18px;
    }

    .entity-form-section__header {
        margin-bottom: 15px;
    }

    .entity-form-section__header h2,
    .entity-submit-card h2 {
        margin: 2px 0 0;
        font-size: .88rem;
    }

    .entity-form-grid {
        display: grid;
        gap: 12px;
    }

    .entity-form-grid--2 {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }

    .entity-form-full {
        grid-column: 1 / -1;
    }

    .entity-submit-card p,
    .entity-help-card p {
        color: var(--color-text-muted);
        font-size: .58rem;
        line-height: 1.55;
    }

    .entity-submit-card .btn {
        margin-top: 7px;
    }

    .entity-help-card strong {
        font-size: .63rem;
    }

    .btn--block {
        width: 100%;
        justify-content: center;
    }

    @media (max-width: 900px) {
        .entity-form-layout {
            grid-template-columns: 1fr;
        }

        .entity-form-sidebar {
            position: static;
        }
    }

    @media (max-width: 580px) {
        .entity-form-grid--2 {
            grid-template-columns: 1fr;
        }

        .entity-form-full {
            grid-column: auto;
        }
    }
</style>