@extends('layouts.landingpage')

{{-- @section('title', $user->name . ' - Service Provider Profile') --}}

@section('content')
    <main id="content" class="mx-auto w-full max-w-6xl flex-1 px-4 py-6 sm:py-8">
        <main>
            <section class="hero" style="display: flex">

                <!-- KIRI -->
                <div class="hero-copy">
                        <div class="eyebrow">Built for campus life</div>

                        <h1>
                            Talent, closer
                            <em>than you think.</em>
                        </h1>

                    <div class="hero-bottom">
                        <div>
                            <p class="hero-lede">
                                Find trusted student talent for the work that moves you
                                forward—from a room away, not a world away.
                            </p>
                        </div>
                    </div>
                </div>
                <!-- KANAN -->
                <div class="hero-visual">

                    <div class="skill-stack">

                        <article class="skill-card">
                            <div class="skill-icon">
                                BK
                            </div>

                            <div>
                                <h3>Design consultation</h3>
                                <p>
                                    Barikh &middot; 0.8 km away &middot; Rp150.000
                                </p>
                            </div>

                            <span class="rating">
                                &#9733; 4.9 (18 jobs)
                            </span>
                        </article>


                        <article class="skill-card">
                            <div class="skill-icon">
                                AR
                            </div>

                            <div>
                                <h3>Peer tutoring</h3>
                                <p>
                                    Aditya &middot; 0.4 km away &middot; Rp75.000
                                </p>
                            </div>

                            <span class="rating">
                                &#9733; 5.0 (24 jobs)
                            </span>
                        </article>


                        <article class="skill-card">
                            <div class="skill-icon">
                                CM
                            </div>

                            <div>
                                <h3>Graphic design</h3>
                                <p>
                                    Clara &middot; 1.2 km away &middot; Rp100.000
                                </p>
                            </div>

                            <span class="rating">
                                &#9733; 4.8 (32 jobs)
                            </span>
                        </article>

                    </div>
                </div>

            </section>
            <div class="ticker" aria-hidden="true">
                <div class="ticker-track">
                    <span>Design</span><span>Peer tutoring</span><span>Presentation slides design</span><span>Graduation and
                        event photography</span><span>Graphic design</span><span>Photocopy
                        services</span><span>Design</span><span>Peer tutoring</span><span>Presentation slides
                        design</span><span>Graduation and event photography</span><span>Graphic design</span><span>Photocopy
                        services</span>
                </div>
            </div>
            <section class="intro" id="impact">
                <div class="shell">
                    <div class="intro-grid reveal">
                        <div class="eyebrow">Opportunity, localised</div>
                        <div class="intro-copy">
                            <h2 class="display">
                                The right skill can change your next move.
                            </h2>
                            <p>
                                LOCALSKILL turns campus know-how into real opportunity. Students
                                get fast, trusted help. Providers build a reputation, a
                                portfolio, and an income—right where they study.
                            </p>
                        </div>
                    </div>
                    <div class="stats reveal">
                        <div class="stat">
                            <strong>2.4k</strong><span>Active student providers</span>
                        </div>
                        <div class="stat">
                            <strong>4.9/5</strong><span>Average service rating</span>
                        </div>
                        <div class="stat">
                            <strong>86%</strong><span>Matched within the same faculty</span>
                        </div>
                    </div>
                </div>
            </section>
            <section class="services" id="services">
                <div class="shell">
                    <div class="section-head reveal">
                        <div>
                            <div class="eyebrow">Popular nearby</div>
                            <h2 class="display" style="margin-top: 25px">
                                Expertise for every ambition.
                            </h2>
                        </div>
                        <a class="link-arrow" href="{{ route('explore.index') }}">Explore all skills <span class="arrow">→</span></a>
                    </div>
                    <div class="services-grid reveal">
                        <div class="service" href="explore.html?category=design"><span class="service-num">01 / CAMPUS
                                SKILLS</span>
                            <div class="service-icon">
                                <i data-lucide="palette" aria-hidden="true"></i>
                            </div>
                            <div>
                                <h3>Design</h3>
                                <p>
                                    A one-hour design consultation with a mood board and visual
                                    direction for your campus project.
                                </p>
                            </div>
                        </div>
                        <div class="service" href="explore.html?category=peer-tutoring"><span class="service-num">02 / CAMPUS
                                SKILLS</span>
                            <div class="service-icon">
                                <i data-lucide="graduation-cap" aria-hidden="true"></i>
                            </div>
                            <div>
                                <h3>Peer tutoring</h3>
                                <p>
                                    A one-hour peer study session to review course concepts and
                                    prepare for exams.
                                </p>
                            </div>
                        </div>
                        <div class="service" href="explore.html?category=presentation-slides-design"><span
                                class="service-num">03 / CAMPUS SKILLS</span>
                            <div class="service-icon">
                                <i data-lucide="presentation" aria-hidden="true"></i>
                            </div>
                            <div>
                                <h3>Presentation slides design</h3>
                                <p>
                                    A ten-slide presentation with clear layouts, consistent
                                    typography, and one revision.
                                </p>
                            </div>
                        </div>
                        <div class="service" href="explore.html?category=graduation-event-photography"><span
                                class="service-num">04 / CAMPUS SKILLS</span>
                            <div class="service-icon">
                                <i data-lucide="camera" aria-hidden="true"></i>
                            </div>
                            <div>
                                <h3>Graduation and event photography</h3>
                                <p>
                                    A one-hour graduation or campus event shoot with 20 edited
                                    photos.
                                </p>
                            </div>
                        </div>
                        <div class="service" href="explore.html?category=graphic-design"><span class="service-num">05 / CAMPUS
                                SKILLS</span>
                            <div class="service-icon">
                                <i data-lucide="pen-tool" aria-hidden="true"></i>
                            </div>
                            <div>
                                <h3>Graphic design</h3>
                                <p>
                                    A campus event poster and matching social media graphic with
                                    one revision.
                                </p>
                            </div>
                        </div>
                        <div class="service" href="explore.html?category=photocopy-services"><span class="service-num">06 /
                                CAMPUS SKILLS</span>
                            <div class="service-icon">
                                <i data-lucide="printer" aria-hidden="true"></i>
                            </div>
                            <div>
                                <h3>Photocopy services</h3>
                                <p>
                                    Nearby printing and photocopying for essays, journals, thesis
                                    pages, and study materials.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
            <section class="intro" id="photocopy" aria-labelledby="photocopy-title">
                <div class="shell intro-grid">
                    <div class="eyebrow">Photocopy services</div>
                    <div class="intro-copy">
                        <h2 class="display" id="photocopy-title">
                            No printer at home? We can help.
                        </h2>
                        <p>
                            Get your essays, journals, thesis pages, and study materials
                            printed or photocopied by a nearby provider. Print from a digital
                            document, or arrange to bring the original pages for photocopying.
                        </p>
                        <p>
                            In your booking brief, mention printing or photocopying, the
                            number of pages and copies, paper size, black-and-white or colour,
                            single- or double-sided printing, and your deadline. Agree on the
                            price, how to share your document or hand over originals, and a
                            campus pickup point before confirming.
                        </p>
                    </div>
                </div>
            </section>
            <section class="how" id="how">
                <div class="shell how-layout">
                    <div class="how-sticky reveal">
                        <div class="eyebrow">Simple by design</div>
                        <h2 class="display">From need to done.</h2>
                        <p>
                            A clear process, campus-verified profiles, and real reviews keep
                            every project moving with confidence.
                        </p>
                    </div>
                    <div class="steps reveal">
                        <article class="step">
                            <span class="step-num">01</span>
                            <div>
                                <h3>Find</h3>
                                <p>
                                    Search by skill, describe your task, and set the timing and
                                    budget that work for you.
                                </p>
                            </div>
                            <span>↗</span>
                        </article>
                        <article class="step">
                            <span class="step-num">02</span>
                            <div>
                                <h3>Match</h3>
                                <p>
                                    Compare verified nearby providers by expertise, availability,
                                    rating, and price.
                                </p>
                            </div>
                            <span>↗</span>
                        </article>
                        <article class="step">
                            <span class="step-num">03</span>
                            <div>
                                <h3>Book</h3>
                                <p>
                                    Agree on the brief, keep communication in one place, and
                                    confirm your booking.
                                </p>
                            </div>
                            <span>↗</span>
                        </article>
                        <article class="step">
                            <span class="step-num">04</span>
                            <div>
                                <h3>Complete</h3>
                                <p>
                                    Check the delivered work against your brief and confirm
                                    completion.
                                </p>
                            </div>
                            <span>↗</span>
                        </article>
                        <article class="step">
                            <span class="step-num">05</span>
                            <div>
                                <h3>Review</h3>
                                <p>
                                    Share your experience and help other students find trusted
                                    campus talent.
                                </p>
                            </div>
                            <span>&#8599;</span>
                        </article>
                    </div>
                </div>
            </section>
            <section class="feature" id="talent">
                <div class="feature-art">
                    <div class="orb one"></div>
                    <div class="orb two"></div>
                    <article class="profile-card">
                        <div class="profile-top">
                            <div class="avatar">BK</div>
                            <div>
                                <h3>Barikh K.</h3>
                                <p>Verified student · 0.8 km away</p>
                            </div>
                        </div>
                        <div class="skill-tags">
                            <span>Design</span><span>Visual direction</span><span>Mood boards</span>
                        </div>
                        <div class="profile-foot">
                            <span><strong>4.9 ★</strong><br />18 completed
                                projects</span><span><strong>Rp150.000</strong><br />starting price</span>
                        </div>
                    </article>
                </div>
                <div class="feature-copy">
                    <div class="eyebrow">Your talent has value</div>
                    <h2 class="display">Build your name while you build your future.</h2>
                    <p>
                        Turn what you already know into experience that counts. Set your
                        offer, choose your hours, and grow through real work with people
                        around you.
                    </p>
                    <a class="btn" href="{{ route('register') }}">Become a provider <span>↗</span></a>
                </div>
            </section>
            <section class="cta">
                <div class="shell reveal">
                    <div class="eyebrow">Start where you are</div>
                    <h2 class="display">Your next opportunity may be across campus.</h2>
                    <div class="cta-actions">
                        <a class="btn btn-green" href="{{ route('register') }}">Join LOCALSKILL</a>
                        <a class="btn btn-outline"
                            href="{{ route('explore.index') }}">Explore services</a>
                    </div>
                </div>
            </section>
        </main>

        <script>
            const observer = new IntersectionObserver(
                (entries) =>
                    entries.forEach((e) => {
                        if (e.isIntersecting) e.target.classList.add("visible");
                    }),
                { threshold: 0.12 },
            );
            document
                .querySelectorAll(".reveal")
                .forEach((el) => observer.observe(el));
            document.getElementById("heroSearch").addEventListener("submit", (e) => {
                e.preventDefault();
                const q = document.getElementById("searchInput").value.trim();
                location.href = q
                    ? "explore.html?q=" + encodeURIComponent(q)
                    : "explore.html";
            });
            const menu = document.querySelector(".menu"),
                links = document.querySelector(".nav-links");
            menu.addEventListener("click", () => {
                const open = menu.getAttribute("aria-expanded") === "true";
                menu.setAttribute("aria-expanded", String(!open));
                links.style.cssText = !open
                    ? "display:flex;position:absolute;left:0;right:0;top:70px;padding:25px 24px;background:#f3f1e8;flex-direction:column;border-bottom:1px solid rgba(11,33,25,.18)"
                    : "";
            });
        </script>

    </main>
@endsection
