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
                            <form
    action="{{ route('explore.index') }}"
    method="GET"
    class="hero-search"
    id="heroSearch"
>
    <input
        id="searchInput"
        name="q"
        type="search"
        value="{{ request('q') }}"
        placeholder="What skill do you need?"
        aria-label="Search skills"
    >

    <button class="btn btn-green" type="submit">
        Find talent <span aria-hidden="true">↗</span>
    </button>
</form>
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

            <a class="link-arrow" href="{{ route('explore.index') }}">
                Explore all skills <span class="arrow">→</span>
            </a>
        </div>

        <div class="services-grid reveal">
            <a
                class="service"
                href="{{ route('explore.index', ['q' => 'Design']) }}"
                style="color: inherit; text-decoration: none;"
            >
                <span class="service-num">01 / CAMPUS SKILLS</span>
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
            </a>

            <a
                class="service"
                href="{{ route('explore.index', ['q' => 'Peer tutoring']) }}"
                style="color: inherit; text-decoration: none;"
            >
                <span class="service-num">02 / CAMPUS SKILLS</span>
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
            </a>

            <a
                class="service"
                href="{{ route('explore.index', ['q' => 'Presentation slides design']) }}"
                style="color: inherit; text-decoration: none;"
            >
                <span class="service-num">03 / CAMPUS SKILLS</span>
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
            </a>

            <a
                class="service"
                href="{{ route('explore.index', ['q' => 'Graduation and event photography']) }}"
                style="color: inherit; text-decoration: none;"
            >
                <span class="service-num">04 / CAMPUS SKILLS</span>
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
            </a>

            <a
                class="service"
                href="{{ route('explore.index', ['q' => 'Graphic design']) }}"
                style="color: inherit; text-decoration: none;"
            >
                <span class="service-num">05 / CAMPUS SKILLS</span>
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
            </a>

            <a
                class="service"
                href="{{ route('explore.index', ['q' => 'Photocopy services']) }}"
                style="color: inherit; text-decoration: none;"
            >
                <span class="service-num">06 / CAMPUS SKILLS</span>
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
            </a>
        </div>
    </div>
</section>            <section class="intro" id="photocopy" aria-labelledby="photocopy-title">
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
<section class="w-full overflow-hidden py-12 sm:py-16 md:py-20 lg:py-28" id="how">
    <div class="mx-auto w-full max-w-7xl px-4 sm:px-6 lg:px-8">

        <div class="grid grid-cols-1 items-start gap-8 sm:gap-10 md:gap-12 lg:grid-cols-[0.8fr_1.2fr] lg:gap-16 xl:gap-24">

            {{-- ================= LEFT CONTENT ================= --}}
            <div class="reveal lg:sticky lg:top-24">

                {{-- Eyebrow --}}
                <div class="mb-2 text-xs font-semibold uppercase tracking-[0.15em]
                            text-emerald-600
                            sm:mb-3 sm:text-sm
                            md:text-base">
                    Simple by design
                </div>

                {{-- Heading --}}
                <h2 class="max-w-xl
                           text-3xl font-bold leading-[1]
                           tracking-tight text-slate-900
                           sm:text-4xl
                           md:text-5xl
                           lg:text-6xl
                           xl:text-7xl
                           dark:text-white">
                    From need to done.
                </h2>

                {{-- Description --}}
                <p class="mt-4 max-w-xl
                          text-xs leading-5
                          text-slate-500
                          sm:mt-5 sm:text-sm sm:leading-6
                          md:text-base md:leading-7
                          lg:text-lg
                          dark:text-slate-400">
                    A clear process, campus-verified profiles, and real reviews keep
                    every project moving with confidence.
                </p>

            </div>


            {{-- ================= STEPS ================= --}}
            <div class="reveal flex min-w-0 flex-col gap-2.5 sm:gap-3">

                {{-- Step 01 --}}
                <article
                    class="group grid w-full min-w-0
                           grid-cols-[32px_minmax(0,1fr)_20px]
                           items-start gap-2.5
                           rounded-xl border border-slate-200
                           bg-white p-3
                           transition-all duration-300
                           hover:-translate-y-0.5
                           hover:border-slate-300
                           hover:shadow-md

                           sm:grid-cols-[38px_minmax(0,1fr)_24px]
                           sm:gap-3 sm:rounded-2xl sm:p-4

                           md:grid-cols-[44px_minmax(0,1fr)_28px]
                           md:gap-4 md:p-5

                           lg:p-6

                           dark:border-slate-800
                           dark:bg-slate-900"
                >

                    <span
                        class="flex h-7 w-7 shrink-0 items-center justify-center
                               rounded-full bg-emerald-50
                               text-[10px] font-bold text-emerald-700
                               sm:h-8 sm:w-8 sm:text-xs
                               md:h-10 md:w-10 md:text-sm
                               dark:bg-emerald-950/50 dark:text-emerald-400"
                    >
                        01
                    </span>

                    <div class="min-w-0">
                        <h3 class="text-sm font-semibold text-slate-900
                                   sm:text-base
                                   md:text-lg
                                   dark:text-white">
                            Find
                        </h3>

                        <p class="mt-1 text-[11px] leading-5 text-slate-500
                                  sm:text-xs sm:leading-5
                                  md:text-sm md:leading-6
                                  lg:text-base
                                  dark:text-slate-400">
                            Search by skill, describe your task, and set the timing and
                            budget that work for you.
                        </p>
                    </div>

                    <span
                        class="flex h-6 w-6 shrink-0 items-center justify-center
                               text-sm text-slate-400
                               transition-transform duration-300
                               group-hover:-translate-y-0.5
                               group-hover:translate-x-0.5
                               sm:text-base
                               md:text-lg"
                    >
                        ↗
                    </span>
                </article>


                {{-- Step 02 --}}
                <article
                    class="group grid w-full min-w-0
                           grid-cols-[32px_minmax(0,1fr)_20px]
                           items-start gap-2.5
                           rounded-xl border border-slate-200
                           bg-white p-3
                           transition-all duration-300
                           hover:-translate-y-0.5
                           hover:border-slate-300
                           hover:shadow-md
                           sm:grid-cols-[38px_minmax(0,1fr)_24px]
                           sm:gap-3 sm:rounded-2xl sm:p-4
                           md:grid-cols-[44px_minmax(0,1fr)_28px]
                           md:gap-4 md:p-5
                           lg:p-6
                           dark:border-slate-800
                           dark:bg-slate-900"
                >

                    <span
                        class="flex h-7 w-7 shrink-0 items-center justify-center
                               rounded-full bg-emerald-50
                               text-[10px] font-bold text-emerald-700
                               sm:h-8 sm:w-8 sm:text-xs
                               md:h-10 md:w-10 md:text-sm
                               dark:bg-emerald-950/50 dark:text-emerald-400"
                    >
                        02
                    </span>

                    <div class="min-w-0">
                        <h3 class="text-sm font-semibold text-slate-900
                                   sm:text-base md:text-lg
                                   dark:text-white">
                            Match
                        </h3>

                        <p class="mt-1 text-[11px] leading-5 text-slate-500
                                  sm:text-xs sm:leading-5
                                  md:text-sm md:leading-6
                                  lg:text-base
                                  dark:text-slate-400">
                            Compare verified nearby providers by expertise, availability,
                            rating, and price.
                        </p>
                    </div>

                    <span
                        class="flex h-6 w-6 shrink-0 items-center justify-center
                               text-sm text-slate-400
                               transition-transform duration-300
                               group-hover:-translate-y-0.5
                               group-hover:translate-x-0.5
                               sm:text-base md:text-lg"
                    >
                        ↗
                    </span>
                </article>


                {{-- Step 03 --}}
                <article
                    class="group grid w-full min-w-0
                           grid-cols-[32px_minmax(0,1fr)_20px]
                           items-start gap-2.5
                           rounded-xl border border-slate-200
                           bg-white p-3
                           transition-all duration-300
                           hover:-translate-y-0.5
                           hover:border-slate-300
                           hover:shadow-md
                           sm:grid-cols-[38px_minmax(0,1fr)_24px]
                           sm:gap-3 sm:rounded-2xl sm:p-4
                           md:grid-cols-[44px_minmax(0,1fr)_28px]
                           md:gap-4 md:p-5
                           lg:p-6
                           dark:border-slate-800
                           dark:bg-slate-900"
                >

                    <span
                        class="flex h-7 w-7 shrink-0 items-center justify-center
                               rounded-full bg-emerald-50
                               text-[10px] font-bold text-emerald-700
                               sm:h-8 sm:w-8 sm:text-xs
                               md:h-10 md:w-10 md:text-sm
                               dark:bg-emerald-950/50 dark:text-emerald-400"
                    >
                        03
                    </span>

                    <div class="min-w-0">
                        <h3 class="text-sm font-semibold text-slate-900
                                   sm:text-base md:text-lg
                                   dark:text-white">
                            Book
                        </h3>

                        <p class="mt-1 text-[11px] leading-5 text-slate-500
                                  sm:text-xs sm:leading-5
                                  md:text-sm md:leading-6
                                  lg:text-base
                                  dark:text-slate-400">
                            Agree on the brief, keep communication in one place, and
                            confirm your booking.
                        </p>
                    </div>

                    <span
                        class="flex h-6 w-6 shrink-0 items-center justify-center
                               text-sm text-slate-400
                               transition-transform duration-300
                               group-hover:-translate-y-0.5
                               group-hover:translate-x-0.5
                               sm:text-base md:text-lg"
                    >
                        ↗
                    </span>
                </article>


                {{-- Step 04 --}}
                <article
                    class="group grid w-full min-w-0
                           grid-cols-[32px_minmax(0,1fr)_20px]
                           items-start gap-2.5
                           rounded-xl border border-slate-200
                           bg-white p-3
                           transition-all duration-300
                           hover:-translate-y-0.5
                           hover:border-slate-300
                           hover:shadow-md
                           sm:grid-cols-[38px_minmax(0,1fr)_24px]
                           sm:gap-3 sm:rounded-2xl sm:p-4
                           md:grid-cols-[44px_minmax(0,1fr)_28px]
                           md:gap-4 md:p-5
                           lg:p-6
                           dark:border-slate-800
                           dark:bg-slate-900"
                >

                    <span
                        class="flex h-7 w-7 shrink-0 items-center justify-center
                               rounded-full bg-emerald-50
                               text-[10px] font-bold text-emerald-700
                               sm:h-8 sm:w-8 sm:text-xs
                               md:h-10 md:w-10 md:text-sm
                               dark:bg-emerald-950/50 dark:text-emerald-400"
                    >
                        04
                    </span>

                    <div class="min-w-0">
                        <h3 class="text-sm font-semibold text-slate-900
                                   sm:text-base md:text-lg
                                   dark:text-white">
                            Complete
                        </h3>

                        <p class="mt-1 text-[11px] leading-5 text-slate-500
                                  sm:text-xs sm:leading-5
                                  md:text-sm md:leading-6
                                  lg:text-base
                                  dark:text-slate-400">
                            Check the delivered work against your brief and confirm
                            completion.
                        </p>
                    </div>

                    <span
                        class="flex h-6 w-6 shrink-0 items-center justify-center
                               text-sm text-slate-400
                               transition-transform duration-300
                               group-hover:-translate-y-0.5
                               group-hover:translate-x-0.5
                               sm:text-base md:text-lg"
                    >
                        ↗
                    </span>
                </article>


                {{-- Step 05 --}}
                <article
                    class="group grid w-full min-w-0
                           grid-cols-[32px_minmax(0,1fr)_20px]
                           items-start gap-2.5
                           rounded-xl border border-slate-200
                           bg-white p-3
                           transition-all duration-300
                           hover:-translate-y-0.5
                           hover:border-slate-300
                           hover:shadow-md
                           sm:grid-cols-[38px_minmax(0,1fr)_24px]
                           sm:gap-3 sm:rounded-2xl sm:p-4
                           md:grid-cols-[44px_minmax(0,1fr)_28px]
                           md:gap-4 md:p-5
                           lg:p-6
                           dark:border-slate-800
                           dark:bg-slate-900"
                >

                    <span
                        class="flex h-7 w-7 shrink-0 items-center justify-center
                               rounded-full bg-emerald-50
                               text-[10px] font-bold text-emerald-700
                               sm:h-8 sm:w-8 sm:text-xs
                               md:h-10 md:w-10 md:text-sm
                               dark:bg-emerald-950/50 dark:text-emerald-400"
                    >
                        05
                    </span>

                    <div class="min-w-0">
                        <h3 class="text-sm font-semibold text-slate-900
                                   sm:text-base md:text-lg
                                   dark:text-white">
                            Review
                        </h3>

                        <p class="mt-1 text-[11px] leading-5 text-slate-500
                                  sm:text-xs sm:leading-5
                                  md:text-sm md:leading-6
                                  lg:text-base
                                  dark:text-slate-400">
                            Share your experience and help other students find trusted
                            campus talent.
                        </p>
                    </div>

                    <span
                        class="flex h-6 w-6 shrink-0 items-center justify-center
                               text-sm text-slate-400
                               transition-transform duration-300
                               group-hover:-translate-y-0.5
                               group-hover:translate-x-0.5
                               sm:text-base md:text-lg"
                    >
                        ↗
                    </span>
                </article>

            </div>
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
  <form action="{{ route('user.profile.register-provider') }}" method="POST" onsubmit="return confirm('Do you want to switch your account to Provider status?')">
                        @csrf
                        <button type="submit" class="btn">
                            Register as Provider
                        </button>
                    </form>                </div>
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
