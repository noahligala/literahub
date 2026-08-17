@php
    use App\Models\Author;
    use App\Models\Book;
    use App\Models\BookAccessRequest;
    use App\Models\BookBorrowing;
    use App\Models\Publisher;
    use App\Models\School;
    use App\Models\SchoolBookLicense;
    use App\Models\User;

    /*
    |--------------------------------------------------------------------------
    | Platform Metrics
    |--------------------------------------------------------------------------
    */

    $totalSchools = School::query()->count();

    $activeSchools = School::query()
        ->where('status', 'active')
        ->count();

    $totalUsers = User::query()->count();

    $totalStudents = User::role('student')->count();

    $totalTeachers = User::role('teacher')->count();

    $totalAuthors = Author::query()->count();

    $totalPublishers = Publisher::query()->count();

    $totalBooks = Book::query()->count();

    $publishedBooks = Book::query()
        ->where('status', 'published')
        ->count();

    $booksUnderReview = Book::query()
        ->where('status', 'under_review')
        ->count();

    $activeLicenses = SchoolBookLicense::query()
        ->where('status', 'active')
        ->where('starts_at', '<=', now())
        ->where(function ($query) {
            $query
                ->whereNull('expires_at')
                ->orWhere('expires_at', '>', now());
        })
        ->count();

    $pendingLicenses = SchoolBookLicense::query()
        ->where('status', 'pending')
        ->count();

    $activeBorrowings = BookBorrowing::query()
        ->where('status', 'borrowed')
        ->count();

    $pendingAccessRequests = BookAccessRequest::query()
        ->where('status', 'pending')
        ->count();


    /*
    |--------------------------------------------------------------------------
    | Recent Schools
    |--------------------------------------------------------------------------
    */

    $recentSchools = School::query()
        ->latest()
        ->limit(5)
        ->get();


    /*
    |--------------------------------------------------------------------------
    | Books Awaiting Review
    |--------------------------------------------------------------------------
    */

    $reviewQueue = Book::query()
        ->where('status', 'under_review')
        ->with([
            'publisher',
            'authors',
            'uploader',
        ])
        ->latest('submitted_at')
        ->limit(5)
        ->get();


    /*
    |--------------------------------------------------------------------------
    | Recent Licences
    |--------------------------------------------------------------------------
    */

    $recentLicenses = SchoolBookLicense::query()
        ->with([
            'school',
            'book',
            'publisher',
            'author',
        ])
        ->latest()
        ->limit(5)
        ->get();


    /*
    |--------------------------------------------------------------------------
    | Recent Users
    |--------------------------------------------------------------------------
    */

    $recentUsers = User::query()
        ->with('roles')
        ->latest()
        ->limit(5)
        ->get();


    /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    */

    $initials = function (?string $name): string {
        if (! $name) {
            return '--';
        }

        return collect(preg_split('/\s+/', trim($name)))
            ->filter()
            ->take(2)
            ->map(fn ($part) => strtoupper(mb_substr($part, 0, 1)))
            ->implode('');
    };
@endphp


<x-layouts.dashboard>

    <div class="admin-dashboard">

        {{-- ================================================================
             HEADER
        ================================================================= --}}

        <section class="dashboard-hero">

            <div class="dashboard-hero__content">

                <div>
                    <span class="dashboard-eyebrow">
                        Platform Administration
                    </span>

                    <h1>
                        Super Admin Dashboard
                    </h1>

                    <p>
                        Monitor LiteraHub schools, users, digital content,
                        licences, subscriptions, and platform activity from
                        one central workspace.
                    </p>
                </div>


                <div class="dashboard-hero__actions">

                    <a
                        href="{{ Route::has('admin.schools.index') ? route('admin.schools.index') : '#' }}"
                        class="btn btn--secondary"
                    >
                        Manage Schools
                    </a>

                    <a
                        href="{{ route('books.index') }}"
                        class="btn btn--primary"
                    >
                        Manage Library
                    </a>

                </div>

            </div>

        </section>


        {{-- ================================================================
             PRIMARY METRICS
        ================================================================= --}}

        <section class="metric-grid">

            <article class="metric-card">

                <div class="metric-card__icon">

                    <svg viewBox="0 0 24 24">
                        <path d="M3 21h18"/>
                        <path d="M5 21V9l7-5 7 5v12"/>
                        <path d="M9 21v-6h6v6"/>
                    </svg>

                </div>

                <div class="metric-card__body">

                    <span class="metric-card__label">
                        Schools
                    </span>

                    <strong class="metric-card__value">
                        {{ number_format($totalSchools) }}
                    </strong>

                    <span class="metric-card__meta">
                        {{ number_format($activeSchools) }} active
                    </span>

                </div>

            </article>


            <article class="metric-card">

                <div class="metric-card__icon">

                    <svg viewBox="0 0 24 24">
                        <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/>
                        <circle cx="9" cy="7" r="4"/>
                        <path d="M22 21v-2a4 4 0 0 0-3-3.87"/>
                        <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                    </svg>

                </div>

                <div class="metric-card__body">

                    <span class="metric-card__label">
                        Users
                    </span>

                    <strong class="metric-card__value">
                        {{ number_format($totalUsers) }}
                    </strong>

                    <span class="metric-card__meta">
                        {{ number_format($totalStudents) }} students ·
                        {{ number_format($totalTeachers) }} teachers
                    </span>

                </div>

            </article>


            <article class="metric-card">

                <div class="metric-card__icon">

                    <svg viewBox="0 0 24 24">
                        <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/>
                        <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2Z"/>
                    </svg>

                </div>

                <div class="metric-card__body">

                    <span class="metric-card__label">
                        Books
                    </span>

                    <strong class="metric-card__value">
                        {{ number_format($totalBooks) }}
                    </strong>

                    <span class="metric-card__meta">
                        {{ number_format($publishedBooks) }} published
                    </span>

                </div>

            </article>


            <article class="metric-card">

                <div class="metric-card__icon">

                    <svg viewBox="0 0 24 24">
                        <path d="M20 13c0 5-3.5 7-8 9-4.5-2-8-4-8-9V5l8-3 8 3Z"/>
                        <path d="m9 12 2 2 4-4"/>
                    </svg>

                </div>

                <div class="metric-card__body">

                    <span class="metric-card__label">
                        Active Licences
                    </span>

                    <strong class="metric-card__value">
                        {{ number_format($activeLicenses) }}
                    </strong>

                    <span class="metric-card__meta">
                        {{ number_format($pendingLicenses) }} pending
                    </span>

                </div>

            </article>

        </section>


        {{-- ================================================================
             ATTENTION
        ================================================================= --}}

        <section class="dashboard-section">

            <div class="section-heading">

                <div>

                    <span class="dashboard-eyebrow">
                        Needs Attention
                    </span>

                    <h2>
                        Platform queue
                    </h2>

                </div>

            </div>


            <div class="attention-grid">

                <a
                    href="{{ route('book-reviews.index') }}"
                    class="attention-card"
                >

                    <div class="attention-card__icon">

                        <svg viewBox="0 0 24 24">
                            <path d="M12 20h9"/>
                            <path d="M16.5 3.5a2.12 2.12 0 0 1 3 3L7 19l-4 1 1-4Z"/>
                        </svg>

                    </div>

                    <div>
                        <strong>
                            {{ number_format($booksUnderReview) }}
                        </strong>

                        <span>
                            Books awaiting review
                        </span>
                    </div>

                </a>


                <a
                    href="{{ route('book-licenses.index') }}"
                    class="attention-card"
                >

                    <div class="attention-card__icon">

                        <svg viewBox="0 0 24 24">
                            <rect x="3" y="4" width="18" height="16" rx="2"/>
                            <path d="M7 8h10"/>
                            <path d="M7 12h6"/>
                        </svg>

                    </div>

                    <div>

                        <strong>
                            {{ number_format($pendingLicenses) }}
                        </strong>

                        <span>
                            Licence requests pending
                        </span>

                    </div>

                </a>


                <a
                    href="#"
                    class="attention-card"
                >

                    <div class="attention-card__icon">

                        <svg viewBox="0 0 24 24">
                            <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10Z"/>
                            <path d="M9 12h6"/>
                            <path d="M12 9v6"/>
                        </svg>

                    </div>

                    <div>

                        <strong>
                            {{ number_format($pendingAccessRequests) }}
                        </strong>

                        <span>
                            Student access requests
                        </span>

                    </div>

                </a>


                <a
                    href="#"
                    class="attention-card"
                >

                    <div class="attention-card__icon">

                        <svg viewBox="0 0 24 24">
                            <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/>
                            <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2Z"/>
                        </svg>

                    </div>

                    <div>

                        <strong>
                            {{ number_format($activeBorrowings) }}
                        </strong>

                        <span>
                            Active digital loans
                        </span>

                    </div>

                </a>

            </div>

        </section>


        {{-- ================================================================
             MAIN DASHBOARD GRID
        ================================================================= --}}

        <section class="dashboard-main-grid">


            {{-- ============================================================
                 BOOK REVIEW QUEUE
            ============================================================= --}}

            <article class="dashboard-panel dashboard-panel--wide">

                <header class="dashboard-panel__header">

                    <div>

                        <span class="dashboard-eyebrow">
                            Content Workflow
                        </span>

                        <h2>
                            Book review queue
                        </h2>

                    </div>


                    <a
                        href="{{ route('book-reviews.index') }}"
                        class="panel-link"
                    >
                        View all
                    </a>

                </header>


                @if ($reviewQueue->isNotEmpty())

                    <div class="table-wrapper">

                        <table class="table-condensed">

                            <thead>

                                <tr>
                                    <th>Book</th>
                                    <th>Publisher</th>
                                    <th>Submitted</th>
                                    <th>Status</th>
                                    <th></th>
                                </tr>

                            </thead>

                            <tbody>

                                @foreach ($reviewQueue as $book)

                                    <tr>

                                        <td>

                                            <div class="directory-person">

                                                <span class="directory-avatar">
                                                    {{ $initials($book->title) }}
                                                </span>

                                                <div class="directory-person__details">

                                                    <div class="directory-person__name">

                                                        <a
                                                            href="{{ route('book-reviews.show', $book) }}"
                                                        >
                                                            {{ $book->title }}
                                                        </a>

                                                    </div>

                                                    <span class="directory-person__meta">

                                                        {{ $book->authors->pluck('name')->join(', ') ?: 'No author' }}

                                                    </span>

                                                </div>

                                            </div>

                                        </td>


                                        <td>

                                            <span class="table-value">
                                                {{ $book->publisher?->name ?? 'Independent' }}
                                            </span>

                                        </td>


                                        <td>

                                            <span class="table-value">

                                                {{ $book->submitted_at?->format('d M Y') ?? '—' }}

                                            </span>

                                        </td>


                                        <td>

                                            <span class="badge badge--warning">
                                                Under review
                                            </span>

                                        </td>


                                        <td>

                                            <div class="table-icon-actions">

                                                <a
                                                    href="{{ route('book-reviews.show', $book) }}"
                                                    class="table-icon-button"
                                                    title="Review book"
                                                    aria-label="Review book"
                                                >

                                                    <svg viewBox="0 0 24 24">
                                                        <path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7S1 12 1 12Z"/>
                                                        <circle cx="12" cy="12" r="3"/>
                                                    </svg>

                                                </a>

                                            </div>

                                        </td>

                                    </tr>

                                @endforeach

                            </tbody>

                        </table>

                    </div>

                @else

                    <div class="panel-empty">

                        <strong>
                            Review queue is clear
                        </strong>

                        <p>
                            There are currently no books awaiting review.
                        </p>

                    </div>

                @endif

            </article>


            {{-- ============================================================
                 PLATFORM SNAPSHOT
            ============================================================= --}}

            <article class="dashboard-panel">

                <header class="dashboard-panel__header">

                    <div>

                        <span class="dashboard-eyebrow">
                            Ecosystem
                        </span>

                        <h2>
                            Platform snapshot
                        </h2>

                    </div>

                </header>


                <div class="snapshot-list">

                    <div class="snapshot-item">

                        <span>
                            Publishers
                        </span>

                        <strong>
                            {{ number_format($totalPublishers) }}
                        </strong>

                    </div>


                    <div class="snapshot-item">

                        <span>
                            Authors
                        </span>

                        <strong>
                            {{ number_format($totalAuthors) }}
                        </strong>

                    </div>


                    <div class="snapshot-item">

                        <span>
                            Published titles
                        </span>

                        <strong>
                            {{ number_format($publishedBooks) }}
                        </strong>

                    </div>


                    <div class="snapshot-item">

                        <span>
                            Active licences
                        </span>

                        <strong>
                            {{ number_format($activeLicenses) }}
                        </strong>

                    </div>


                    <div class="snapshot-item">

                        <span>
                            Current loans
                        </span>

                        <strong>
                            {{ number_format($activeBorrowings) }}
                        </strong>

                    </div>


                    <div class="snapshot-item">

                        <span>
                            Students
                        </span>

                        <strong>
                            {{ number_format($totalStudents) }}
                        </strong>

                    </div>

                </div>

            </article>


            {{-- ============================================================
                 RECENT SCHOOLS
            ============================================================= --}}

            <article class="dashboard-panel">

                <header class="dashboard-panel__header">

                    <div>

                        <span class="dashboard-eyebrow">
                            Institutions
                        </span>

                        <h2>
                            Recent schools
                        </h2>

                    </div>

                </header>


                <div class="activity-list">

                    @forelse ($recentSchools as $school)

                        <div class="activity-item">

                            <span class="activity-avatar">
                                {{ $initials($school->name) }}
                            </span>

                            <div class="activity-item__body">

                                <strong>
                                    {{ $school->name }}
                                </strong>

                                <span>
                                    Added {{ $school->created_at?->diffForHumans() }}
                                </span>

                            </div>


                            <span
                                class="
                                    badge
                                    {{ $school->status === 'active'
                                        ? 'badge--success'
                                        : 'badge--muted'
                                    }}
                                "
                            >
                                {{ ucfirst($school->status) }}
                            </span>

                        </div>

                    @empty

                        <div class="panel-empty">

                            <p>
                                No schools registered yet.
                            </p>

                        </div>

                    @endforelse

                </div>

            </article>


            {{-- ============================================================
                 RECENT LICENCES
            ============================================================= --}}

            <article class="dashboard-panel dashboard-panel--wide">

                <header class="dashboard-panel__header">

                    <div>

                        <span class="dashboard-eyebrow">
                            Distribution
                        </span>

                        <h2>
                            Recent licences
                        </h2>

                    </div>


                    <a
                        href="{{ route('book-licenses.index') }}"
                        class="panel-link"
                    >
                        View all
                    </a>

                </header>


                @if ($recentLicenses->isNotEmpty())

                    <div class="table-wrapper">

                        <table class="table-condensed">

                            <thead>

                                <tr>
                                    <th>Book</th>
                                    <th>School</th>
                                    <th>Type</th>
                                    <th>Expires</th>
                                    <th>Status</th>
                                    <th></th>
                                </tr>

                            </thead>

                            <tbody>

                                @foreach ($recentLicenses as $license)

                                    <tr>

                                        <td>

                                            <a
                                                href="{{ route('book-licenses.show', $license) }}"
                                                class="table-link"
                                            >
                                                {{ $license->book?->title ?? 'Unknown book' }}
                                            </a>

                                        </td>


                                        <td>

                                            <span class="table-value">
                                                {{ $license->school?->name ?? 'Unknown school' }}
                                            </span>

                                        </td>


                                        <td>

                                            <span class="table-value">
                                                {{ ucfirst($license->license_type) }}
                                            </span>

                                        </td>


                                        <td>

                                            <span class="table-value">

                                                {{ $license->expires_at?->format('d M Y') ?? 'No expiry' }}

                                            </span>

                                        </td>


                                        <td>

                                            @php
                                                $licenseBadge = match ($license->status) {
                                                    'active' => 'badge--success',
                                                    'pending' => 'badge--warning',
                                                    'expired',
                                                    'revoked',
                                                    'suspended' => 'badge--danger',
                                                    default => 'badge--muted',
                                                };
                                            @endphp

                                            <span class="badge {{ $licenseBadge }}">
                                                {{ ucfirst($license->status) }}
                                            </span>

                                        </td>


                                        <td>

                                            <div class="table-icon-actions">

                                                <a
                                                    href="{{ route('book-licenses.show', $license) }}"
                                                    class="table-icon-button"
                                                    title="View licence"
                                                    aria-label="View licence"
                                                >

                                                    <svg viewBox="0 0 24 24">
                                                        <path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7S1 12 1 12Z"/>
                                                        <circle cx="12" cy="12" r="3"/>
                                                    </svg>

                                                </a>

                                            </div>

                                        </td>

                                    </tr>

                                @endforeach

                            </tbody>

                        </table>

                    </div>

                @else

                    <div class="panel-empty">

                        <p>
                            No licences have been issued yet.
                        </p>

                    </div>

                @endif

            </article>


            {{-- ============================================================
                 RECENT USERS
            ============================================================= --}}

            <article class="dashboard-panel">

                <header class="dashboard-panel__header">

                    <div>

                        <span class="dashboard-eyebrow">
                            Accounts
                        </span>

                        <h2>
                            Recent users
                        </h2>

                    </div>

                </header>


                <div class="activity-list">

                    @forelse ($recentUsers as $user)

                        <div class="activity-item">

                            <span class="activity-avatar">
                                {{ $initials($user->name) }}
                            </span>


                            <div class="activity-item__body">

                                <strong>
                                    {{ $user->name }}
                                </strong>

                                <span>
                                    {{ $user->roles->pluck('name')->map(
                                        fn ($role) => str($role)->replace('_', ' ')->title()
                                    )->join(', ') ?: 'No role' }}
                                </span>

                            </div>

                        </div>

                    @empty

                        <div class="panel-empty">

                            <p>
                                No users available.
                            </p>

                        </div>

                    @endforelse

                </div>

            </article>

        </section>


        {{-- ================================================================
             QUICK ACTIONS
        ================================================================= --}}

        <section class="dashboard-section">

            <div class="section-heading">

                <div>

                    <span class="dashboard-eyebrow">
                        Administration
                    </span>

                    <h2>
                        Quick actions
                    </h2>

                </div>

            </div>


            <div class="quick-action-grid">

                <a
                    href="{{ route('publishers.create') }}"
                    class="quick-action-card"
                >

                    <span class="quick-action-card__icon">

                        <svg viewBox="0 0 24 24">
                            <path d="M3 21h18"/>
                            <path d="M6 21V4h12v17"/>
                            <path d="M9 8h2"/>
                            <path d="M13 8h2"/>
                            <path d="M9 12h2"/>
                            <path d="M13 12h2"/>
                        </svg>

                    </span>

                    <strong>
                        Add Publisher
                    </strong>

                    <span>
                        Register a publishing partner.
                    </span>

                </a>


                <a
                    href="{{ route('authors.create') }}"
                    class="quick-action-card"
                >

                    <span class="quick-action-card__icon">

                        <svg viewBox="0 0 24 24">
                            <circle cx="12" cy="8" r="4"/>
                            <path d="M4 21a8 8 0 0 1 16 0"/>
                        </svg>

                    </span>

                    <strong>
                        Add Author
                    </strong>

                    <span>
                        Register and verify an author profile.
                    </span>

                </a>


                <a
                    href="{{ route('books.create') }}"
                    class="quick-action-card"
                >

                    <span class="quick-action-card__icon">

                        <svg viewBox="0 0 24 24">
                            <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/>
                            <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2Z"/>
                            <path d="M12 7v6"/>
                            <path d="M9 10h6"/>
                        </svg>

                    </span>

                    <strong>
                        Upload Book
                    </strong>

                    <span>
                        Add a title to the platform catalogue.
                    </span>

                </a>


                <a
                    href="{{ route('book-licenses.create') }}"
                    class="quick-action-card"
                >

                    <span class="quick-action-card__icon">

                        <svg viewBox="0 0 24 24">
                            <path d="M20 13c0 5-3.5 7-8 9-4.5-2-8-4-8-9V5l8-3 8 3Z"/>
                            <path d="m9 12 2 2 4-4"/>
                        </svg>

                    </span>

                    <strong>
                        Issue Licence
                    </strong>

                    <span>
                        Grant a school access to a published book.
                    </span>

                </a>

            </div>

        </section>

    </div>


    {{-- ====================================================================
         DASHBOARD STYLES
    ===================================================================== --}}

    <style>

        .admin-dashboard {
            display: flex;
            flex-direction: column;
            gap: 22px;
        }


        /*
        |--------------------------------------------------------------------------
        | Hero
        |--------------------------------------------------------------------------
        */

        .dashboard-hero {
            padding: 24px;
            border: 1px solid var(--color-border);
            border-radius: var(--radius-lg);
            background: var(--color-surface);
        }

        .dashboard-hero__content {
            display: flex;
            align-items: flex-end;
            justify-content: space-between;
            gap: 24px;
        }

        .dashboard-hero h1 {
            margin: 4px 0 6px;
            color: var(--color-text);
            font-size: clamp(1.45rem, 2vw, 2rem);
            line-height: 1.15;
        }

        .dashboard-hero p {
            max-width: 720px;
            margin: 0;
            color: var(--color-text-muted);
            font-size: .82rem;
            line-height: 1.6;
        }

        .dashboard-eyebrow {
            color: var(--color-primary);
            font-size: .62rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: .09em;
        }

        .dashboard-hero__actions {
            display: flex;
            align-items: center;
            gap: 8px;
            flex-shrink: 0;
        }


        /*
        |--------------------------------------------------------------------------
        | Metrics
        |--------------------------------------------------------------------------
        */

        .metric-grid {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 12px;
        }

        .metric-card {
            display: flex;
            align-items: center;
            gap: 12px;
            min-width: 0;
            padding: 16px;
            border: 1px solid var(--color-border);
            border-radius: var(--radius-lg);
            background: var(--color-surface);
        }

        .metric-card__icon {
            width: 38px;
            height: 38px;
            flex: 0 0 38px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: var(--radius-md);
            background: var(--color-surface-soft);
            color: var(--color-primary);
        }

        .metric-card__icon svg {
            width: 18px;
            height: 18px;
            fill: none;
            stroke: currentColor;
            stroke-width: 1.7;
            stroke-linecap: round;
            stroke-linejoin: round;
        }

        .metric-card__body {
            min-width: 0;
            display: flex;
            flex-direction: column;
        }

        .metric-card__label {
            color: var(--color-text-muted);
            font-size: .62rem;
            font-weight: 700;
        }

        .metric-card__value {
            margin-top: 1px;
            color: var(--color-text);
            font-size: 1.35rem;
            line-height: 1.2;
        }

        .metric-card__meta {
            margin-top: 2px;
            color: var(--color-text-muted);
            font-size: .58rem;
        }


        /*
        |--------------------------------------------------------------------------
        | Sections
        |--------------------------------------------------------------------------
        */

        .dashboard-section {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .section-heading {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
        }

        .section-heading h2,
        .dashboard-panel__header h2 {
            margin: 2px 0 0;
            color: var(--color-text);
            font-size: .95rem;
            font-weight: 750;
        }


        /*
        |--------------------------------------------------------------------------
        | Attention Cards
        |--------------------------------------------------------------------------
        */

        .attention-grid {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 10px;
        }

        .attention-card {
            display: flex;
            align-items: center;
            gap: 11px;
            padding: 13px;
            border: 1px solid var(--color-border);
            border-radius: var(--radius-md);
            background: var(--color-surface);
            color: inherit;
            text-decoration: none;
            transition:
                border-color var(--transition-fast),
                background var(--transition-fast);
        }

        .attention-card:hover {
            border-color: var(--brand-300);
            background: var(--color-surface-soft);
        }

        .attention-card__icon {
            width: 34px;
            height: 34px;
            flex: 0 0 34px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: var(--radius-md);
            background: var(--color-surface-soft);
            color: var(--color-primary);
        }

        .attention-card__icon svg {
            width: 16px;
            height: 16px;
            fill: none;
            stroke: currentColor;
            stroke-width: 1.7;
            stroke-linecap: round;
            stroke-linejoin: round;
        }

        .attention-card strong {
            display: block;
            color: var(--color-text);
            font-size: 1rem;
        }

        .attention-card span {
            display: block;
            margin-top: 1px;
            color: var(--color-text-muted);
            font-size: .61rem;
        }


        /*
        |--------------------------------------------------------------------------
        | Panels
        |--------------------------------------------------------------------------
        */

        .dashboard-main-grid {
            display: grid;
            grid-template-columns: minmax(0, 2fr) minmax(260px, 1fr);
            gap: 14px;
            align-items: start;
        }

        .dashboard-panel {
            min-width: 0;
            padding: 16px;
            border: 1px solid var(--color-border);
            border-radius: var(--radius-lg);
            background: var(--color-surface);
        }

        .dashboard-panel--wide {
            min-width: 0;
        }

        .dashboard-panel__header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            margin-bottom: 13px;
        }

        .panel-link {
            color: var(--color-primary);
            font-size: .64rem;
            font-weight: 700;
            text-decoration: none;
        }

        .panel-link:hover {
            text-decoration: underline;
        }

        .panel-empty {
            padding: 24px 12px;
            text-align: center;
        }

        .panel-empty strong {
            color: var(--color-text);
            font-size: .78rem;
        }

        .panel-empty p {
            margin: 4px 0 0;
            color: var(--color-text-muted);
            font-size: .65rem;
        }


        /*
        |--------------------------------------------------------------------------
        | Snapshot
        |--------------------------------------------------------------------------
        */

        .snapshot-list {
            display: flex;
            flex-direction: column;
        }

        .snapshot-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 15px;
            padding: 9px 0;
            border-bottom: 1px solid var(--color-border);
        }

        .snapshot-item:last-child {
            border-bottom: 0;
        }

        .snapshot-item span {
            color: var(--color-text-muted);
            font-size: .66rem;
        }

        .snapshot-item strong {
            color: var(--color-text);
            font-size: .72rem;
        }


        /*
        |--------------------------------------------------------------------------
        | Activity
        |--------------------------------------------------------------------------
        */

        .activity-list {
            display: flex;
            flex-direction: column;
        }

        .activity-item {
            display: flex;
            align-items: center;
            gap: 9px;
            padding: 8px 0;
            border-bottom: 1px solid var(--color-border);
        }

        .activity-item:last-child {
            border-bottom: 0;
        }

        .activity-avatar {
            width: 28px;
            height: 28px;
            flex: 0 0 28px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border: 1px solid var(--color-border);
            border-radius: 50%;
            background: var(--color-surface-soft);
            color: var(--color-primary);
            font-size: .55rem;
            font-weight: 800;
        }

        .activity-item__body {
            min-width: 0;
            flex: 1;
            display: flex;
            flex-direction: column;
        }

        .activity-item__body strong {
            overflow: hidden;
            color: var(--color-text);
            font-size: .68rem;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .activity-item__body span {
            overflow: hidden;
            margin-top: 1px;
            color: var(--color-text-muted);
            font-size: .56rem;
            text-overflow: ellipsis;
            white-space: nowrap;
        }


        /*
        |--------------------------------------------------------------------------
        | Quick Actions
        |--------------------------------------------------------------------------
        */

        .quick-action-grid {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 10px;
        }

        .quick-action-card {
            display: flex;
            flex-direction: column;
            align-items: flex-start;
            padding: 15px;
            border: 1px solid var(--color-border);
            border-radius: var(--radius-md);
            background: var(--color-surface);
            color: inherit;
            text-decoration: none;
            transition:
                border-color var(--transition-fast),
                background var(--transition-fast);
        }

        .quick-action-card:hover {
            border-color: var(--brand-300);
            background: var(--color-surface-soft);
        }

        .quick-action-card__icon {
            width: 31px;
            height: 31px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 10px;
            border-radius: var(--radius-md);
            background: var(--color-surface-soft);
            color: var(--color-primary);
        }

        .quick-action-card__icon svg {
            width: 15px;
            height: 15px;
            fill: none;
            stroke: currentColor;
            stroke-width: 1.7;
            stroke-linecap: round;
            stroke-linejoin: round;
        }

        .quick-action-card strong {
            color: var(--color-text);
            font-size: .7rem;
        }

        .quick-action-card > span:last-child {
            margin-top: 3px;
            color: var(--color-text-muted);
            font-size: .58rem;
            line-height: 1.45;
        }


        /*
        |--------------------------------------------------------------------------
        | Responsive
        |--------------------------------------------------------------------------
        */

        @media (max-width: 1100px) {

            .metric-grid,
            .attention-grid,
            .quick-action-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

        }


        @media (max-width: 850px) {

            .dashboard-main-grid {
                grid-template-columns: 1fr;
            }

            .dashboard-hero__content {
                align-items: flex-start;
                flex-direction: column;
            }

        }


        @media (max-width: 600px) {

            .dashboard-hero {
                padding: 17px;
            }

            .metric-grid,
            .attention-grid,
            .quick-action-grid {
                grid-template-columns: 1fr;
            }

            .dashboard-hero__actions {
                width: 100%;
                flex-wrap: wrap;
            }

            .dashboard-hero__actions .btn {
                flex: 1;
                justify-content: center;
            }

        }

    </style>

</x-layouts.dashboard>