<x-layouts.app title="LiteraHub — Literature for every learner">

    <header class="site-header">
        <div class="container public-nav">

            <a
                class="brand"
                href="{{ route('home') }}"
            >
                LiteraHub
            </a>

            <nav class="public-nav__links">
                <a href="#features">
                    Features
                </a>

                <a href="{{ route('pricing') }}">
                    Pricing
                </a>

                <a
                    class="button button-small"
                    href="{{ route('register') }}"
                >
                    Get Started
                </a>
            </nav>

        </div>
    </header>

    <main>

        <section class="hero">

            <div class="container hero-grid">

                <div>

                    <span class="eyebrow">
                        Digital literature platform
                    </span>

                    <h1>
                        Books, study resources and learning tools
                        for every learner.
                    </h1>

                    <p class="hero-copy">
                        Give schools and individual learners secure
                        online access to literature, academic resources,
                        assignments and reading progress through active
                        subscriptions.
                    </p>

                    <div class="actions">

                        <a
                            class="button button-large"
                            href="{{ route('pricing') }}"
                        >
                            View Plans
                        </a>

                        <a
                            class="button button-secondary button-large"
                            href="#features"
                        >
                            Explore Features
                        </a>

                    </div>

                    <div class="hero-trust">

                        <div>
                            <strong>Schools</strong>
                            <span>Institutional access</span>
                        </div>

                        <div>
                            <strong>Students</strong>
                            <span>Personal learning</span>
                        </div>

                        <div>
                            <strong>Authors</strong>
                            <span>Content insights</span>
                        </div>

                    </div>

                </div>

                <div class="hero-preview">

                    <div class="dashboard-card">

                        <div class="preview-header">

                            <div>
                                <span class="eyebrow">
                                    Learning overview
                                </span>

                                <h3>
                                    Student engagement
                                </h3>
                            </div>

                            <span class="badge badge-success">
                                Active
                            </span>

                        </div>

                        <div class="metric-grid">

                            <article>
                                <strong>1,250</strong>
                                <span>Students</span>
                            </article>

                            <article>
                                <strong>85%</strong>
                                <span>Completion</span>
                            </article>

                            <article>
                                <strong>3,450</strong>
                                <span>Resources opened</span>
                            </article>

                        </div>

                        <div class="book-list">

                            <h3>
                                Continue reading
                            </h3>

                            <div class="book">

                                <div class="cover">
                                    LU
                                </div>

                                <div class="book-details">

                                    <strong>
                                        Voices Unheard
                                    </strong>

                                    <p>
                                        Chapter 4 · 68% complete
                                    </p>

                                </div>

                            </div>

                            <div class="progress">
                                <span style="width: 68%"></span>
                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </section>

        <section
            id="features"
            class="section section-lg"
        >

            <div class="container">

                <div class="section-heading">

                    <span class="eyebrow">
                        Built for education
                    </span>

                    <h2>
                        One platform, multiple learning journeys
                    </h2>

                    <p>
                        LiteraHub connects institutions, educators,
                        learners and authors through one secure
                        literature platform.
                    </p>

                </div>

                <div class="cards feature-grid">

                    <article>

                        <span class="feature-number">
                            01
                        </span>

                        <h3>
                            School subscriptions
                        </h3>

                        <p>
                            Manage teachers, students, licences,
                            classes, assignments and institutional
                            payments.
                        </p>

                    </article>

                    <article>

                        <span class="feature-number">
                            02
                        </span>

                        <h3>
                            Individual access
                        </h3>

                        <p>
                            Students subscribe directly and build
                            a personal library with notes, bookmarks
                            and reading progress.
                        </p>

                    </article>

                    <article>

                        <span class="feature-number">
                            03
                        </span>

                        <h3>
                            Protected resources
                        </h3>

                        <p>
                            Private storage, signed links,
                            watermarks, audit logs and configurable
                            content-access controls.
                        </p>

                    </article>

                    <article>

                        <span class="feature-number">
                            04
                        </span>

                        <h3>
                            Author analytics
                        </h3>

                        <p>
                            Measure title adoption, reader engagement
                            and completion without exposing private
                            learner information.
                        </p>

                    </article>

                </div>

            </div>

        </section>

        <section class="section">

            <div class="container">

                <div class="cta-card">

                    <div>

                        <span class="eyebrow">
                            Start learning
                        </span>

                        <h2>
                            Bring literature closer to every learner.
                        </h2>

                        <p>
                            Register your institution or create an
                            individual student account today.
                        </p>

                    </div>

                    <div class="actions">

                        <a
                            href="{{ route('register') }}"
                            class="button button-large"
                        >
                            Create Account
                        </a>

                        <a
                            href="{{ route('pricing') }}"
                            class="button button-secondary button-large"
                        >
                            View Pricing
                        </a>

                    </div>

                </div>

            </div>

        </section>

    </main>

    <footer class="site-footer">

        <div class="container footer-inner">

            <div>
                <strong class="brand">
                    LiteraHub
                </strong>

                <p>
                    Literature that inspires.
                    Resources that educate.
                </p>
            </div>

            <div class="footer-copy">
                © {{ date('Y') }} Ligco Technologies.
                All rights reserved.
            </div>

        </div>

    </footer>

</x-layouts.app>