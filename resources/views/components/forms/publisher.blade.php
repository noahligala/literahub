@props([
    'publisher' => null,
])

@php
    $editing = filled($publisher);
@endphp


<div class="entity-form-layout">

    <div class="entity-form-main">

        {{-- ================================================================
             ORGANISATION
        ================================================================= --}}

        <section class="card entity-form-section">

            <div class="entity-form-section__header">

                <span class="eyebrow">
                    Organisation
                </span>

                <h2>
                    Publisher Information
                </h2>

            </div>


            <div class="entity-form-grid entity-form-grid--2">

                <div class="form-group entity-form-full">

                    <label for="name">
                        Publisher Name
                    </label>

                    <input
                        id="name"
                        type="text"
                        name="name"
                        value="{{ old(
                            'name',
                            $publisher?->name
                        ) }}"
                        required
                    >

                    @error('name')
                        <div class="field-error">
                            {{ $message }}
                        </div>
                    @enderror

                </div>


                <div class="form-group">

                    <label for="registration_number">
                        Registration Number
                    </label>

                    <input
                        id="registration_number"
                        type="text"
                        name="registration_number"
                        value="{{ old(
                            'registration_number',
                            $publisher?->registration_number
                        ) }}"
                    >

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
                            'pending' => 'Pending',
                            'active' => 'Active',
                            'suspended' => 'Suspended',
                        ] as $value => $label)

                            <option
                                value="{{ $value }}"
                                @selected(
                                    old(
                                        'status',
                                        $publisher?->status ?? 'active'
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


        {{-- ================================================================
             CONTACT
        ================================================================= --}}

        <section class="card entity-form-section">

            <div class="entity-form-section__header">

                <span class="eyebrow">
                    Contact
                </span>

                <h2>
                    Contact Information
                </h2>

            </div>


            <div class="entity-form-grid entity-form-grid--2">

                <div class="form-group">

                    <label for="email">
                        Email
                    </label>

                    <input
                        id="email"
                        type="email"
                        name="email"
                        value="{{ old(
                            'email',
                            $publisher?->email
                        ) }}"
                    >

                </div>


                <div class="form-group">

                    <label for="phone">
                        Phone
                    </label>

                    <input
                        id="phone"
                        type="text"
                        name="phone"
                        value="{{ old(
                            'phone',
                            $publisher?->phone
                        ) }}"
                    >

                </div>


                <div class="form-group entity-form-full">

                    <label for="website">
                        Website
                    </label>

                    <input
                        id="website"
                        type="url"
                        name="website"
                        value="{{ old(
                            'website',
                            $publisher?->website
                        ) }}"
                        placeholder="https://..."
                    >

                </div>


                <div class="form-group entity-form-full">

                    <label for="address">
                        Address
                    </label>

                    <textarea
                        id="address"
                        name="address"
                        rows="4"
                    >{{ old(
                        'address',
                        $publisher?->address
                    ) }}</textarea>

                </div>

            </div>

        </section>


        {{-- ================================================================
             PROFILE
        ================================================================= --}}

        <section class="card entity-form-section">

            <div class="entity-form-section__header">

                <span class="eyebrow">
                    Profile
                </span>

                <h2>
                    Public Information
                </h2>

            </div>


            <div class="entity-form-grid">

                <div class="form-group">

                    <label for="description">
                        Description
                    </label>

                    <textarea
                        id="description"
                        name="description"
                        rows="7"
                        placeholder="Describe the publisher, catalogue focus and areas of specialisation..."
                    >{{ old(
                        'description',
                        $publisher?->description
                    ) }}</textarea>

                </div>


                <div class="form-group">

                    <label for="logo">
                        Publisher Logo
                    </label>

                    <input
                        id="logo"
                        type="file"
                        name="logo"
                        accept="image/png,image/jpeg,image/webp"
                    >

                    @if (
                        $editing
                        && $publisher->logo_path
                    )

                        <small>
                            A logo is already stored. Upload a new image
                            only if you want to replace it.
                        </small>

                    @endif

                </div>

            </div>

        </section>

    </div>


    <aside class="entity-form-sidebar">

        <section class="card entity-submit-card">

            <span class="eyebrow">
                Publisher
            </span>

            <h2>
                {{ $editing
                    ? 'Save Changes'
                    : 'Register Publisher'
                }}
            </h2>

            <p>
                Publisher records connect authors, books and
                institutional licensing rights.
            </p>


            <button
                type="submit"
                class="btn btn--primary btn--block"
            >
                {{ $editing
                    ? 'Save Publisher'
                    : 'Create Publisher'
                }}
            </button>


            <a
                href="{{ $editing
                    ? route(
                        'publishers.show',
                        $publisher
                    )
                    : route(
                        'publishers.index'
                    )
                }}"
                class="btn btn--secondary btn--block"
            >
                Cancel
            </a>

        </section>


        <section class="card entity-help-card">

            <strong>
                Rights Holder Record
            </strong>

            <p>
                Registering a publisher identifies the organisation
                associated with works and distribution licences. It does
                not itself transfer intellectual-property ownership.
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
        color: var(--color-text);
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