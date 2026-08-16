<x-layouts.auth title="Create Account — LiteraHub">

    <span class="eyebrow">
        Join LiteraHub
    </span>

    <h1>
        Create your LiteraHub account
    </h1>

    <p>
        Choose how you want to access LiteraHub.
    </p>

    <section class="cards">

        <article>
            <h2>
                School or Institution
            </h2>

            <p>
                Register your school, college or university
                and manage teachers, students, subscriptions,
                and literature resources.
            </p>

            <a
                href="{{ route('register.school') }}"
                class="button"
            >
                Register Institution
            </a>
        </article>

        <article>
            <h2>
                Individual Student
            </h2>

            <p>
                Subscribe personally and access literature,
                study resources, reading tools,
                assignments, and progress tracking.
            </p>

            <a
                href="{{ route('register.student') }}"
                class="button"
            >
                Register as Student
            </a>
        </article>

    </section>

    <div style="margin-top: 32px;">

        <p>
            Already have an account?
        </p>

        <a
            href="{{ route('login') }}"
            class="button button-secondary"
        >
            Sign In
        </a>

    </div>

</x-layouts.auth>