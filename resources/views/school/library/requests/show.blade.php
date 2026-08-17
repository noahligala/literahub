<x-layouts.dashboard>

    <div class="page-shell">

        <div class="page-header">

            <div>
                <span class="eyebrow">
                    Library Access
                </span>

                <h1>
                    Access Request
                </h1>

                <p>
                    {{ $accessRequest->book?->title }}
                </p>
            </div>


            <a
                href="{{ route(
                    'school.library.requests.index'
                ) }}"
                class="btn btn--secondary"
            >
                Back
            </a>

        </div>


        <div class="card" style="padding:18px;">

            <dl class="request-details">

                <div>
                    <dt>Student</dt>
                    <dd>
                        {{ $accessRequest->student?->name }}
                    </dd>
                </div>

                <div>
                    <dt>Book</dt>
                    <dd>
                        {{ $accessRequest->book?->title }}
                    </dd>
                </div>

                <div>
                    <dt>Status</dt>
                    <dd>
                        {{ str(
                            $accessRequest->status
                        )->title() }}
                    </dd>
                </div>

                <div>
                    <dt>Reason</dt>
                    <dd>
                        {{ $accessRequest->reason }}
                    </dd>
                </div>

            </dl>


            @if (
                $accessRequest->status
                === 'pending'
                &&
                auth()->user()
                    ->hasAnyRole([
                        'teacher',
                        'school_admin',
                    ])
            )

                <div class="request-actions">

                    <form
                        method="POST"
                        action="{{ route(
                            'school.library.requests.approve',
                            $accessRequest
                        ) }}"
                    >

                        @csrf
                        @method('PATCH')


                        <div class="form-group">

                            <label for="expires_at">
                                Access Expires
                            </label>

                            <input
                                id="expires_at"
                                type="datetime-local"
                                name="expires_at"
                            >

                        </div>


                        <button
                            type="submit"
                            class="btn btn--primary"
                        >
                            Approve
                        </button>

                    </form>


                    <form
                        method="POST"
                        action="{{ route(
                            'school.library.requests.reject',
                            $accessRequest
                        ) }}"
                    >

                        @csrf
                        @method('PATCH')


                        <button
                            type="submit"
                            class="btn btn--danger"
                        >
                            Reject
                        </button>

                    </form>

                </div>

            @endif

        </div>

    </div>


    <style>

        .request-details {
            margin: 0;
        }

        .request-details > div {
            display: grid;
            grid-template-columns: 160px 1fr;
            gap: 15px;
            padding: 10px 0;
            border-bottom: 1px solid var(--color-border);
        }

        .request-details dt {
            color: var(--color-text-muted);
            font-size: .58rem;
        }

        .request-details dd {
            margin: 0;
            font-size: .62rem;
        }

        .request-actions {
            display: flex;
            align-items: end;
            gap: 10px;
            margin-top: 18px;
        }

    </style>

</x-layouts.dashboard>