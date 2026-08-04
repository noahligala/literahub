<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LiteraHub — Literature for every learner</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
<header class="site-header"><div class="container nav"><a class="brand" href="/">LiteraHub</a><nav><a href="#features">Features</a><a href="/pricing">Pricing</a><a class="button button-small" href="/register">Get started</a></nav></div></header>
<main>
<section class="hero"><div class="container hero-grid"><div><span class="eyebrow">Digital literature platform</span><h1>Books, study resources and learning tools for schools and students.</h1><p>Give institutions and individual learners protected online access to approved literature, university resources, assignments and reading progress through active subscriptions.</p><div class="actions"><a class="button" href="/pricing">View plans</a><a class="button button-secondary" href="#features">Explore features</a></div></div><div class="dashboard-card"><div class="metric-grid"><article><strong>1,250</strong><span>Students</span></article><article><strong>85%</strong><span>Completion</span></article><article><strong>3,450</strong><span>Resources opened</span></article></div><div class="book-list"><h3>Continue reading</h3><div class="book"><div class="cover">LU</div><div><strong>Voices Unheard</strong><p>Chapter 4 · 68% complete</p></div></div><div class="progress"><span style="width:68%"></span></div></div></div></div></section>
<section id="features" class="section"><div class="container"><span class="eyebrow">Built for education</span><h2>One platform, multiple learning journeys</h2><div class="cards"><article><h3>School subscriptions</h3><p>Manage teachers, students, licences, classes, assignments and institutional payments.</p></article><article><h3>Individual access</h3><p>Students subscribe directly and build a personal library with notes, bookmarks and progress.</p></article><article><h3>Protected resources</h3><p>Private storage, signed links, watermarks, audit logs and configurable download controls.</p></article><article><h3>Author analytics</h3><p>Measure title adoption, reader engagement and completion without exposing private learner data.</p></article></div></div></section>
</main>
<footer><div class="container">© {{ date('Y') }} LiteraHub. Literature that inspires. Resources that educate.</div></footer>
</body></html>
