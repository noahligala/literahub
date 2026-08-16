<x-layouts.auth title="Student Registration — LiteraHub">

    <span class="eyebrow">
        Individual Access
    </span>

    <h1>
        Create your student account
    </h1>

    <p>
        Create your personal LiteraHub account to access
        literature resources, reading tools and learning materials.
    </p>

    @if($errors->any())
        <div class="alert alert-error">
            <strong>
                Please correct the following:
            </strong>

            <ul>
                @foreach($errors->all() as $error)
                    <li>
                        {{ $error }}
                    </li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="dashboard-card">

        <form
            method="POST"
            action="{{ route('register.student.store') }}"
        >
            @csrf

            <label for="name">
                Full Name
            </label>

            <input
                id="name"
                type="text"
                name="name"
                value="{{ old('name') }}"
                autocomplete="name"
                required
            >

            <label for="email">
                Email Address
            </label>

            <input
                id="email"
                type="email"
                name="email"
                value="{{ old('email') }}"
                autocomplete="email"
                required
            >

            <label for="phone">
                Phone Number
            </label>

            <input
                id="phone"
                type="text"
                name="phone"
                value="{{ old('phone') }}"
                autocomplete="tel"
            >

            <label for="education_level">
                Education Level
            </label>

            <select
                id="education_level"
                name="education_level"
                required
            >
                <option value="">
                    Select your education level
                </option>

                <option
                    value="primary"
                    @selected(old('education_level') === 'primary')
                >
                    Primary
                </option>

                <option
                    value="secondary"
                    @selected(old('education_level') === 'secondary')
                >
                    Secondary
                </option>

                <option
                    value="college"
                    @selected(old('education_level') === 'college')
                >
                    College
                </option>

                <option
                    value="university"
                    @selected(old('education_level') === 'university')
                >
                    University
                </option>

                <option
                    value="postgraduate"
                    @selected(old('education_level') === 'postgraduate')
                >
                    Postgraduate
                </option>

                <option
                    value="professional"
                    @selected(old('education_level') === 'professional')
                >
                    Professional
                </option>

                <option
                    value="other"
                    @selected(old('education_level') === 'other')
                >
                    Other
                </option>
            </select>

            <label for="institution_name">
                Institution
            </label>

            <input
                id="institution_name"
                type="text"
                name="institution_name"
                value="{{ old('institution_name') }}"
            >

            <label for="county">
                County
            </label>

            <input
                id="county"
                type="text"
                name="county"
                value="{{ old('county') }}"
            >

            <label for="town">
                Town
            </label>

            <input
                id="town"
                type="text"
                name="town"
                value="{{ old('town') }}"
            >

            <label for="date_of_birth">
                Date of Birth
            </label>

            <input
                id="date_of_birth"
                type="date"
                name="date_of_birth"
                value="{{ old('date_of_birth') }}"
            >

            <label for="password">
                Password
            </label>

            <input
                id="password"
                type="password"
                name="password"
                autocomplete="new-password"
                required
            >

            <label for="password_confirmation">
                Confirm Password
            </label>

            <input
                id="password_confirmation"
                type="password"
                name="password_confirmation"
                autocomplete="new-password"
                required
            >

            <label>
                <input
                    type="checkbox"
                    name="terms"
                    value="1"
                    @checked(old('terms'))
                    required
                >

                I agree to the terms and privacy policy.
            </label>

            <div class="actions">

                <button
                    type="submit"
                    class="button"
                >
                    Create Student Account
                </button>

                <a
                    href="{{ route('register') }}"
                    class="button button-secondary"
                >
                    Back
                </a>

            </div>

        </form>

    </div>

</x-layouts.auth>