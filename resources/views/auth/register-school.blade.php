<x-layouts.auth title="Register Institution — LiteraHub">

    <span class="eyebrow">
        Institutional Access
    </span>

    <h1>
        Register your institution
    </h1>

    <p>
        Create a LiteraHub institutional account for your
        school, college, university or training institution.
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
            action="{{ route('register.school.store') }}"
        >
            @csrf

            <span class="eyebrow">
                Institution Details
            </span>

            <h2>
                Institution Information
            </h2>

            <label for="school_name">
                Institution Name
            </label>

            <input
                id="school_name"
                type="text"
                name="school_name"
                value="{{ old('school_name') }}"
                required
            >

            <label for="registration_number">
                Registration Number
            </label>

            <input
                id="registration_number"
                type="text"
                name="registration_number"
                value="{{ old('registration_number') }}"
            >

            <label for="school_type">
                Institution Type
            </label>

            <select
                id="school_type"
                name="school_type"
                required
            >
                <option value="">
                    Select institution type
                </option>

                <option
                    value="primary"
                    @selected(old('school_type') === 'primary')
                >
                    Primary School
                </option>

                <option
                    value="secondary"
                    @selected(old('school_type') === 'secondary')
                >
                    Secondary School
                </option>

                <option
                    value="college"
                    @selected(old('school_type') === 'college')
                >
                    College
                </option>

                <option
                    value="university"
                    @selected(old('school_type') === 'university')
                >
                    University
                </option>

                <option
                    value="training_institution"
                    @selected(
                        old('school_type') === 'training_institution'
                    )
                >
                    Training Institution
                </option>

                <option
                    value="other"
                    @selected(old('school_type') === 'other')
                >
                    Other
                </option>
            </select>

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

            <label for="school_email">
                Institution Email
            </label>

            <input
                id="school_email"
                type="email"
                name="school_email"
                value="{{ old('school_email') }}"
            >

            <label for="school_phone">
                Institution Phone
            </label>

            <input
                id="school_phone"
                type="text"
                name="school_phone"
                value="{{ old('school_phone') }}"
            >

            <hr>

            <span class="eyebrow">
                Administrator
            </span>

            <h2>
                Administrator Account
            </h2>

            <p>
                This person will become the initial
                administrator for the institution.
            </p>

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
                Administrator Email
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
                Administrator Phone
            </label>

            <input
                id="phone"
                type="text"
                name="phone"
                value="{{ old('phone') }}"
                autocomplete="tel"
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
                    Create Institution Account
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