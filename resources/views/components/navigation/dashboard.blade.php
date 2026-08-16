<nav class="dashboard-nav">

    @auth

        @role('school_admin')

            <a
                href="{{ route('school.dashboard') }}"
                @class([
                    'nav-link',
                    'active' => request()->routeIs(
                        'school.dashboard'
                    ),
                ])
            >
                <span class="nav-link__icon">
                    ◫
                </span>

                <span>
                    Dashboard
                </span>
            </a>

            <a
                href="{{ route('school.students.index') }}"
                @class([
                    'nav-link',
                    'active' => request()->routeIs(
                        'school.students.*'
                    ),
                ])
            >
                <span class="nav-link__icon">
                    ♙
                </span>

                <span>
                    Students
                </span>
            </a>

            <a
                href="{{ route('school.teachers.index') }}"
                @class([
                    'nav-link',
                    'active' => request()->routeIs(
                        'school.teachers.*'
                    ),
                ])
            >
                <span class="nav-link__icon">
                    ◉
                </span>

                <span>
                    Teachers
                </span>
            </a>

            <a
                href="{{ route('school.classes.index') }}"
                @class([
                    'nav-link',
                    'active' => request()->routeIs(
                        'school.classes.*'
                    ),
                ])
            >
                <span class="nav-link__icon">
                    ▦
                </span>

                <span>
                    Classes
                </span>
            </a>

            <a
                href="{{ route('school.library.index') }}"
                @class([
                    'nav-link',
                    'active' => request()->routeIs(
                        'school.library.*'
                    ),
                ])
            >
                <span class="nav-link__icon">
                    ▤
                </span>

                <span>
                    Library
                </span>
            </a>

            <a
                href="{{ route('school.assignments.index') }}"
                @class([
                    'nav-link',
                    'active' => request()->routeIs(
                        'school.assignments.*'
                    ),
                ])
            >
                <span class="nav-link__icon">
                    ✓
                </span>

                <span>
                    Assignments
                </span>
            </a>

            <a
                href="{{ route('school.subscription.index') }}"
                @class([
                    'nav-link',
                    'active' => request()->routeIs(
                        'school.subscription.*'
                    ),
                ])
            >
                <span class="nav-link__icon">
                    ◇
                </span>

                <span>
                    Subscription
                </span>
            </a>

            <a
                href="{{ route('school.reports.index') }}"
                @class([
                    'nav-link',
                    'active' => request()->routeIs(
                        'school.reports.*'
                    ),
                ])
            >
                <span class="nav-link__icon">
                    ◰
                </span>

                <span>
                    Reports
                </span>
            </a>

        @endrole


        @role('teacher')

            <a
                href="{{ route('teacher.dashboard') }}"
                @class([
                    'nav-link',
                    'active' => request()->routeIs(
                        'teacher.*'
                    ),
                ])
            >
                <span class="nav-link__icon">
                    ◫
                </span>

                <span>
                    Dashboard
                </span>
            </a>

        @endrole


        @hasanyrole('student|individual_subscriber')

            <a
                href="{{ route('library.dashboard') }}"
                @class([
                    'nav-link',
                    'active' => request()->routeIs(
                        'library.*'
                    ),
                ])
            >
                <span class="nav-link__icon">
                    ▤
                </span>

                <span>
                    My Library
                </span>
            </a>

        @endhasanyrole


        @hasanyrole('super_admin|platform_admin')

            <a
                href="{{ route('admin.dashboard') }}"
                @class([
                    'nav-link',
                    'active' => request()->routeIs(
                        'admin.*'
                    ),
                ])
            >
                <span class="nav-link__icon">
                    ◫
                </span>

                <span>
                    Administration
                </span>
            </a>

        @endhasanyrole


        @hasanyrole('content_manager|author|finance|support')

            <a
                href="{{ route('staff.dashboard') }}"
                @class([
                    'nav-link',
                    'active' => request()->routeIs(
                        'staff.*'
                    ),
                ])
            >
                <span class="nav-link__icon">
                    ◫
                </span>

                <span>
                    Staff
                </span>
            </a>

        @endhasanyrole

    @endauth


    @guest

        @if(Route::has('login'))

            <a
                href="{{ route('login') }}"
                @class([
                    'nav-link',
                    'active' => request()->routeIs(
                        'login'
                    ),
                ])
            >
                <span class="nav-link__icon">
                    ⇥
                </span>

                <span>
                    Login
                </span>
            </a>

        @endif

        @if(Route::has('register'))

            <a
                href="{{ route('register') }}"
                @class([
                    'nav-link',
                    'active' => request()->routeIs(
                        'register*'
                    ),
                ])
            >
                <span class="nav-link__icon">
                    +
                </span>

                <span>
                    Register
                </span>
            </a>

        @endif

    @endguest

</nav>