<x-layouts.dashboard title="Library — LiteraHub">

    <div class="dashboard-heading">

        <div>

            <span class="eyebrow">
                Digital Library
            </span>

            <h1>
                School Library
            </h1>

            <p>
                Browse literature and learning resources
                available under your institution's subscription.
            </p>

        </div>

    </div>

    <div class="card">

        <div class="row">

            <div style="flex: 1;">
                <input
                    type="search"
                    placeholder="Search books, authors or topics..."
                >
            </div>

            <div style="width: min(100%, 180px);">
                <select>
                    <option value="">
                        All Categories
                    </option>

                    <option>Literature</option>
                    <option>Poetry</option>
                    <option>Drama</option>
                    <option>Novels</option>
                    <option>Study Guides</option>
                    <option>Reference</option>
                </select>
            </div>

        </div>

    </div>

    <div style="height: 14px;"></div>

    <div class="metric-grid">

        <article>
            <strong>0</strong>
            <span>Available Titles</span>
        </article>

        <article>
            <strong>0</strong>
            <span>Study Guides</span>
        </article>

        <article>
            <strong>0</strong>
            <span>Recently Added</span>
        </article>

    </div>

    <div style="height: 14px;"></div>

    <section class="cards">

        <article>

            <span class="eyebrow">
                Literature
            </span>

            <h3>
                Browse Books
            </h3>

            <p>
                Explore approved literary works
                available to your institution.
            </p>

            <button class="button button-secondary button-small">
                Browse
            </button>

        </article>

        <article>

            <span class="eyebrow">
                Academic Support
            </span>

            <h3>
                Study Guides
            </h3>

            <p>
                Access study notes, analysis,
                summaries and revision materials.
            </p>

            <button class="button button-secondary button-small">
                View Guides
            </button>

        </article>

        <article>

            <span class="eyebrow">
                Teaching
            </span>

            <h3>
                Teacher Resources
            </h3>

            <p>
                Discover classroom resources and
                supporting teaching material.
            </p>

            <button class="button button-secondary button-small">
                View Resources
            </button>

        </article>

        <article>

            <span class="eyebrow">
                New Content
            </span>

            <h3>
                Recently Added
            </h3>

            <p>
                Review newly published resources
                available to your school.
            </p>

            <button class="button button-secondary button-small">
                Explore
            </button>

        </article>

    </section>

</x-layouts.dashboard>