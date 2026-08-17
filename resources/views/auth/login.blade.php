<x-layouts.auth title="Sign In — LiteraHub">

    <div class="auth-shell">

        <div class="auth-intro">

            <span class="eyebrow">
                Welcome Back
            </span>

            <h1>
                Sign in to LiteraHub
            </h1>

            <p>
                Access your library, institution,
                classes and learning resources.
            </p>

        </div>


        <div class="card auth-card">

            {{-- Session status --}}
            @if (session('status'))

                <div
                    class="alert alert-success"
                    data-auto-dismiss="5000"
                >
                    {{ session('status') }}
                </div>

            @endif


            {{-- General authentication error --}}
            @if ($errors->any())

                <div class="alert alert-error">

                    @if ($errors->has('email'))

                        {{ $errors->first('email') }}

                    @elseif ($errors->has('password'))

                        {{ $errors->first('password') }}

                    @else

                        Please review the information entered
                        and try again.

                    @endif

                </div>

            @endif


            <form
                method="POST"
                action="{{ route('login') }}"
            >

                @csrf


                {{-- Email --}}
                <div class="form-group">

                    <label for="email">
                        Email Address
                    </label>

                    <input
                        id="email"
                        type="email"
                        name="email"
                        value="{{ old('email') }}"
                        placeholder="name@example.com"
                        autocomplete="email"
                        autofocus
                        required
                    >

                    @error('email')

                        <div class="field-error">
                            {{ $message }}
                        </div>

                    @enderror

                </div>


                {{-- Password --}}
                <div class="form-group">

                    <div class="row-between">

                        <label for="password">
                            Password
                        </label>

                        @if (Route::has('password.request'))

                            <a
                                href="{{ route('password.request') }}"
                                class="link-muted"
                            >
                                Forgot password?
                            </a>

                        @endif

                    </div>

                    <div class="password-field">

                        <input
                            id="password"
                            type="password"
                            name="password"
                            placeholder="Enter your password"
                            autocomplete="current-password"
                            required
                        >

                        <button
                            type="button"
                            class="password-toggle"
                            aria-label="Show password"
                            aria-controls="password"
                            data-password-toggle
                        >
                            Show
                        </button>

                    </div>

                    @error('password')

                        <div class="field-error">
                            {{ $message }}
                        </div>

                    @enderror

                </div>


                {{-- Remember me --}}
                <label class="checkbox-row">

                    <input
                        type="checkbox"
                        name="remember"
                        value="1"
                        @checked(old('remember'))
                    >

                    <span>
                        Keep me signed in
                    </span>

                </label>


                {{-- Submit --}}
                <div class="actions">

                    <button
                        type="submit"
                        class="button button-block"
                    >
                        Sign In
                    </button>

                </div>

            </form>


            {{-- Registration --}}
            <div class="auth-divider">

                <span>
                    New to LiteraHub?
                </span>

            </div>

            <a
                href="{{ route('register') }}"
                class="button button-secondary button-block"
            >
                Create Account
            </a>


            {{-- Registration options --}}
            <div class="auth-options">

                @if (Route::has('register.school'))

                    <a
                        href="{{ route('register.school') }}"
                        class="link-muted"
                    >
                        Register a School
                    </a>

                @endif

                @if (Route::has('register.student'))

                    <a
                        href="{{ route('register.student') }}"
                        class="link-muted"
                    >
                        Individual Learner
                    </a>

                @endif

            </div>

        </div>


        <div class="auth-footer">

            <p>
                By signing in, you agree to LiteraHub's
                platform policies and acceptable use terms.
            </p>

        </div>

    </div>


    <script>
        document.addEventListener('DOMContentLoaded', () => {

            const toggle =
                document.querySelector(
                    '[data-password-toggle]'
                );

            const password =
                document.getElementById('password');

            if (!toggle || !password) {
                return;
            }

            toggle.addEventListener('click', () => {

                const hidden =
                    password.type === 'password';

                password.type =
                    hidden
                        ? 'text'
                        : 'password';

                toggle.textContent =
                    hidden
                        ? 'Hide'
                        : 'Show';

                toggle.setAttribute(
                    'aria-label',
                    hidden
                        ? 'Hide password'
                        : 'Show password'
                );

            });

        });
    </script>

</x-layouts.auth>