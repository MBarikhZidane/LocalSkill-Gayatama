<!DOCTYPE html>

<html lang="en" x-data="themeManager()" x-init="init()" :data-theme="darkMode ? 'dark' : 'light'"
    :class="{ 'dark': darkMode }">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Find, book, and share trusted student skills around your campus.">
    <title>@yield('title', 'LOCALSKILL')</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">

    <style>
        :root {
            --ink: #0b2119;
            --forest: #103c2e;
            --green: #39e37f;
            --mint: #c9f8d9;
            --cream: #f3f1e8;
            --white: #fff;
            --line: rgba(11, 33, 25, 0.18);
            --muted: #5d6d66;
            --max: 1440px;
        }

        * {
            box-sizing: border-box;
        }

        html {
            scroll-behavior: smooth;
        }

        body {
            margin: 0;
            background: var(--cream);
            color: var(--ink);
            font-family: "DM Sans", sans-serif;
            -webkit-font-smoothing: antialiased;
        }

        a {
            color: inherit;
            text-decoration: none;
        }

        button,
        input {
            font: inherit;
        }

        .shell {
            width: min(100% - 48px, var(--max));
            margin: auto;
        }

        .eyebrow {
            display: flex;
            align-items: center;
            gap: 10px;
            font: 700 11px/1 "Manrope";
            letter-spacing: 0.16em;
            text-transform: uppercase;
        }

        .eyebrow:before {
            content: "";
            width: 28px;
            height: 2px;
            background: currentColor;
        }

        .arrow {
            display: inline-flex;
            width: 35px;
            height: 35px;
            border: 1px solid;
            border-radius: 50%;
            align-items: center;
            justify-content: center;
            transition: 0.25s;
        }

        .link-arrow {
            display: inline-flex;
            align-items: center;
            gap: 12px;
            font-weight: 700;
        }

        .link-arrow:hover .arrow {
            background: var(--green);
            border-color: var(--green);
            color: var(--ink);
            transform: translateX(4px);
        }

        .topbar {
            height: 36px;
            background: var(--ink);
            color: #cfddd7;
            font-size: 11px;
            letter-spacing: 0.08em;
            text-transform: uppercase;
        }

        .topbar .shell {
            height: 100%;
            display: flex;
            justify-content: flex-end;
            align-items: center;
            gap: 24px;
        }

        .topbar a:hover {
            color: var(--green);
        }

        header {
            position: sticky;
            top: 0;
            z-index: 20;
            background: rgba(243, 241, 232, 0.94);
            backdrop-filter: blur(12px);
            border-bottom: 1px solid var(--line);
        }

        .nav {
            height: 84px;
            display: grid;
            grid-template-columns: 1fr auto 1fr;
            align-items: center;
        }

        .brand {
            font: 700 23px/1 "Manrope";
            letter-spacing: -0.06em;
        }

        .brand span {
            color: #189b55;
        }

        .nav-links {
            display: flex;
            gap: 36px;
            font-size: 14px;
            font-weight: 600;
        }

        .nav-links a {
            position: relative;
        }

        .nav-links a:after {
            content: "";
            position: absolute;
            left: 0;
            right: 100%;
            bottom: -8px;
            height: 2px;
            background: var(--ink);
            transition: 0.25s;
        }

        .nav-links a:hover:after {
            right: 0;
        }

        .nav-actions {
            display: flex;
            justify-content: flex-end;
            align-items: center;
            gap: 18px;
        }

        .btn {
            border: 0;
            cursor: pointer;
            display: inline-flex;
            justify-content: center;
            align-items: center;
            gap: 10px;
            min-height: 46px;
            padding: 0 22px;
            background: var(--ink);
            color: #fff;
            font-size: 13px;
            font-weight: 700;
            transition: 0.25s;
        }

        .btn:hover {
            background: var(--forest);
            transform: translateY(-2px);
        }

        .btn-green {
            background: var(--green);
            color: var(--ink);
        }

        .btn-green:hover {
            background: #72eea3;
        }

        .menu {
            display: none;
            border: 0;
            background: none;
            padding: 8px;
        }

        .menu svg {
            width: 24px;
        }

        .hero {
            min-height: 680px;
            display: grid;
            grid-template-columns: minmax(0, 1.05fr) minmax(0, 0.95fr);
            background: var(--forest);
            color: #fff;
            overflow: hidden;
        }


        /* LEFT SIDE */
        .hero-copy {
            padding: 80px max(24px, calc((100vw - var(--max)) / 2 + 24px)) 70px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            min-width: 0;
        }

        .hero .eyebrow {
            color: var(--green);
            margin-bottom: 28px;
        }

        .hero h1 {
            max-width: 760px;
            margin: 0;
            font-family: "Manrope", sans-serif;
            font-size: clamp(52px, 6vw, 94px);
            font-weight: 500;
            line-height: 0.94;
            letter-spacing: -0.065em;
        }

        .hero h1 em {
            color: var(--green);
            font-style: normal;
        }

        /* BOTTOM CONTENT */
        .hero-bottom {
            display: grid;
            grid-template-columns: minmax(0, 1fr) 220px;
            gap: 45px;
            align-items: end;
            margin-top: 70px;
        }

        .hero-lede {
            max-width: 500px;
            margin: 0;
            color: #d2ded9;
            font-size: 17px;
            line-height: 1.6;
        }

        /* SEARCH */
        .hero-search {
            width: 100%;
            max-width: 570px;
            margin-top: 25px;
            padding: 7px;
            display: flex;
            align-items: center;
            background: #fff;
            color: var(--ink);
            border-radius: 3px;
        }

        .hero-search input {
            flex: 1;
            min-width: 0;
            height: 50px;
            padding: 0 15px;
            border: 0;
            outline: none;
            background: transparent;
            color: var(--ink);
            font-size: 14px;
        }

        .hero-search input::placeholder {
            color: #87958f;
        }

        .hero-search .btn {
            min-height: 50px;
            padding: 0 20px;
            white-space: nowrap;
        }

        /* NOTE */
        .hero-note {
            padding-left: 20px;
            border-left: 1px solid rgba(255, 255, 255, 0.28);
            color: #b8cbc3;
            font-size: 12px;
            line-height: 1.6;
        }

        .hero-note strong {
            display: block;
            margin-bottom: 5px;
            color: #fff;
            font-family: "Manrope", sans-serif;
            font-size: 25px;
            font-weight: 600;
            letter-spacing: -0.04em;
        }


        /* =========================
   HERO VISUAL
========================= */

        .hero-visual {
            position: relative;
            min-width: 0;
            min-height: 680px;
            overflow: hidden;
            background: red !important;

            display: block;

            background:
                linear-gradient(145deg,
                    #c4f0d2 0%,
                    #79d99b 50%,
                    #4fbd78 100%);
        }

        /* Decorative circles */
        .hero-visual::before {
            content: "";
            position: absolute;
            width: 500px;
            height: 500px;
            top: 45px;
            right: -130px;
            border-radius: 50%;
            background: #8cebb0;
        }

        .hero-visual::after {
            content: "";
            position: absolute;
            width: 300px;
            height: 300px;
            left: -100px;
            bottom: -120px;
            border: 80px solid #143d30;
            border-radius: 50%;
        }

        .live-pill {
            position: absolute;
            top: 25px;
            right: 25px;
            z-index: 5;

            display: inline-flex;
            align-items: center;
            gap: 8px;

            padding: 9px 13px;
            border-radius: 999px;

            background: rgba(243, 241, 232, 0.95);
            color: var(--ink);

            font-size: 10px;
            font-weight: 700;
            letter-spacing: 0.08em;
            text-transform: uppercase;

            box-shadow: 0 10px 25px rgba(11, 33, 25, 0.12);
        }

        .live-pill::before {
            content: "";
            width: 7px;
            height: 7px;
            flex-shrink: 0;
            border-radius: 50%;
            background: #18b45f;
            box-shadow: 0 0 0 4px rgba(24, 180, 95, 0.16);
        }


        /* =========================
   SKILL CARDS
========================= */

        .skill-stack {
            position: absolute;
            z-index: 2;

            top: 100px;
            left: 8%;
            right: 8%;

            display: flex;
            flex-direction: column;
            gap: 16px;

            transform: rotate(-3deg);
        }

        .skill-card {
            position: relative;

            display: grid;
            grid-template-columns: 58px minmax(0, 1fr) auto;
            align-items: center;
            gap: 17px;

            min-height: 105px;
            padding: 18px 20px;

            background: rgba(255, 255, 255, 0.95);
            color: var(--ink);

            box-shadow:
                0 20px 45px rgba(16, 60, 46, 0.18);

            transition:
                transform 0.3s ease,
                box-shadow 0.3s ease;
        }

        .skill-card:hover {
            transform: translateX(-8px);
            box-shadow:
                0 25px 55px rgba(16, 60, 46, 0.25);
        }

        /* Card kedua */
        .skill-card:nth-child(2) {
            margin-left: 12%;
            margin-right: -4%;

            background: var(--ink);
            color: #fff;
        }

        /* Card ketiga */
        .skill-card:nth-child(3) {
            margin-right: 10%;
        }


        /* Avatar */
        .skill-icon {
            width: 58px;
            height: 58px;

            display: grid;
            place-items: center;

            background: var(--mint);
            color: var(--ink);

            font-family: "Manrope", sans-serif;
            font-size: 17px;
            font-weight: 700;
        }

        .skill-card:nth-child(2) .skill-icon {
            background: var(--green);
            color: var(--ink);
        }

        .skill-card h3 {
            margin: 0 0 6px;

            font-family: "Manrope", sans-serif;
            font-size: 15px;
            font-weight: 700;
            line-height: 1.25;
        }

        .skill-card p {
            margin: 0;

            color: #71817a;
            font-size: 11px;
            line-height: 1.5;
        }

        .skill-card:nth-child(2) p {
            color: #aac0b7;
        }

        .rating {
            white-space: nowrap;

            font-size: 11px;
            font-weight: 700;
            color: #56665f;
        }

        .skill-card:nth-child(2) .rating {
            color: #d5e5df;
        }

        .ticker {
            background: var(--green);
            overflow: hidden;
            border-bottom: 1px solid;
        }

        .ticker-track {
            display: flex;
            width: max-content;
            animation: ticker 24s linear infinite;
        }

        .ticker span {
            display: flex;
            align-items: center;
            gap: 28px;
            padding: 18px 30px;
            font: 700 12px "Manrope";
            text-transform: uppercase;
            letter-spacing: 0.13em;
        }

        .ticker span:after {
            content: "✦";
        }

        @keyframes ticker {
            to {
                transform: translateX(-50%);
            }
        }

        .intro {
            padding: 130px 0 110px;
        }

        .intro-grid {
            display: grid;
            grid-template-columns: 1fr 2fr;
            gap: 80px;
        }

        .display {
            margin: 0;
            font: 500 clamp(43px, 5vw, 76px)/1.02 "Manrope";
            letter-spacing: -0.055em;
        }

        .intro-copy {
            max-width: 830px;
        }

        .intro-copy>p {
            max-width: 650px;
            margin: 35px 0 38px;
            color: var(--muted);
            font-size: 18px;
            line-height: 1.65;
        }

        .stats {
            margin-top: 100px;
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            border-top: 1px solid var(--line);
        }

        .stat {
            padding: 35px 30px 0 0;
            border-right: 1px solid var(--line);
        }

        .stat:not(:first-child) {
            padding-left: 30px;
        }

        .stat:last-child {
            border-right: 0;
        }

        .stat strong {
            display: block;
            font: 500 clamp(48px, 5vw, 76px)/1 "Manrope";
            letter-spacing: -0.06em;
        }

        .stat span {
            display: block;
            margin-top: 12px;
            color: var(--muted);
            font-size: 13px;
        }

        .services {
            padding: 115px 0;
            background: var(--ink);
            color: white;
        }

        .section-head {
            display: flex;
            align-items: end;
            justify-content: space-between;
            gap: 30px;
            margin-bottom: 58px;
        }

        .section-head .display {
            max-width: 700px;
        }

        .services-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            border-top: 1px solid #ffffff40;
        }

        .service {
            position: relative;
            min-height: 470px;
            padding: 30px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            border-right: 1px solid #ffffff40;
            overflow: hidden;
        }

        .service:last-child {
            border-right: 0;
        }

        .service:before {
            content: "";
            position: absolute;
            inset: 100% 0 0;
            background: var(--green);
            transition: 0.4s cubic-bezier(0.2, 0.7, 0.2, 1);
        }

        .service:hover:before {
            inset: 0;
        }

        .service>* {
            position: relative;
            z-index: 1;
        }

        .service:hover {
            color: var(--ink);
        }

        .service-num {
            font-size: 12px;
            color: #92a79e;
        }

        .service:hover .service-num {
            color: var(--ink);
        }

        .service-icon {
            align-self: flex-end;
            width: 155px;
            height: 155px;
            border: 1px solid #ffffff52;
            border-radius: 50%;
            display: grid;
            place-items: center;
        }

        .service-icon svg {
            width: 55px;
            height: 55px;
            stroke-width: 1;
        }

        .service:hover .service-icon {
            border-color: var(--ink);
        }

        .service h3 {
            margin: 0 0 13px;
            font: 500 30px/1.1 "Manrope";
            letter-spacing: -0.04em;
        }

        .service p {
            max-width: 310px;
            margin: 0;
            color: #aebeb7;
            line-height: 1.55;
            font-size: 14px;
        }

        .service:hover p {
            color: #254638;
        }

        .how {
            padding: 130px 0;
        }

        .how-layout {
            display: grid;
            grid-template-columns: 0.8fr 1.2fr;
            gap: 100px;
        }

        .how-sticky {
            align-self: start;
            position: sticky;
            top: 135px;
        }

        .how-sticky .display {
            margin: 28px 0;
        }

        .how-sticky p {
            max-width: 430px;
            color: var(--muted);
            line-height: 1.6;
        }

        .steps {
            border-top: 1px solid var(--line);
        }

        .step {
            display: grid;
            grid-template-columns: 70px 1fr auto;
            gap: 24px;
            padding: 34px 0;
            border-bottom: 1px solid var(--line);
            align-items: start;
        }

        .step-num {
            color: #1b8f51;
            font: 600 13px "Manrope";
        }

        .step h3 {
            margin: 0 0 8px;
            font: 600 25px "Manrope";
            letter-spacing: -0.035em;
        }

        .step p {
            margin: 0;
            color: var(--muted);
            font-size: 14px;
            line-height: 1.6;
        }

        .step svg {
            width: 22px;
        }

        .feature {
            display: grid;
            grid-template-columns: 1fr 1fr;
            min-height: 670px;
        }

        .feature-art {
            position: relative;
            overflow: hidden;
            background: #86dbaa;
        }

        .orb {
            position: absolute;
            border-radius: 50%;
            border: 1px solid #0b211973;
        }

        .orb.one {
            width: 580px;
            height: 580px;
            left: -120px;
            top: 45px;
            background: #b9f3cd;
        }

        .orb.two {
            width: 300px;
            height: 300px;
            right: -50px;
            bottom: -30px;
            background: var(--forest);
        }

        .profile-card {
            position: absolute;
            width: min(360px, 70%);
            left: 50%;
            top: 50%;
            transform: translate(-50%, -50%) rotate(3deg);
            background: var(--cream);
            padding: 28px;
            box-shadow: 0 30px 60px #0b211933;
        }

        .profile-top {
            display: flex;
            gap: 16px;
            align-items: center;
        }

        .avatar {
            width: 58px;
            height: 58px;
            border-radius: 50%;
            background: var(--ink);
            color: var(--green);
            display: grid;
            place-items: center;
            font-weight: 700;
        }

        .profile-card h3 {
            margin: 0 0 4px;
            font: 700 18px "Manrope";
        }

        .profile-card p {
            margin: 0;
            color: var(--muted);
            font-size: 12px;
        }

        .skill-tags {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin: 24px 0;
        }

        .skill-tags span {
            border: 1px solid var(--line);
            padding: 7px 10px;
            font-size: 11px;
        }

        .profile-foot {
            border-top: 1px solid var(--line);
            padding-top: 18px;
            display: flex;
            justify-content: space-between;
            font-size: 12px;
        }

        .profile-foot strong {
            font-size: 16px;
        }

        .feature-copy {
            background: var(--green);
            padding: 100px clamp(35px, 7vw, 115px);
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .feature-copy .display {
            margin: 28px 0 34px;
        }

        .feature-copy p {
            max-width: 540px;
            font-size: 17px;
            line-height: 1.6;
        }

        .feature-copy .btn {
            align-self: flex-start;
            margin-top: 22px;
        }

        .cta {
            padding: 120px 0;
            background: var(--forest);
            color: white;
            text-align: center;
        }

        .cta .eyebrow {
            justify-content: center;
            color: var(--green);
        }

        .cta .display {
            max-width: 940px;
            margin: 28px auto 42px;
        }

        .cta-actions {
            display: flex;
            justify-content: center;
            gap: 12px;
        }

        .btn-outline {
            background: transparent;
            border: 1px solid #ffffff73;
        }

        .btn-outline:hover {
            border-color: white;
            background: white;
            color: var(--ink);
        }

        footer {
            background: var(--ink);
            color: white;
            padding: 70px 0 35px;
        }

        .footer-main {
            display: grid;
            grid-template-columns: 2fr repeat(3, 1fr);
            gap: 65px;
            padding-bottom: 75px;
        }

        .footer-brand p {
            max-width: 320px;
            color: #8fa49b;
            font-size: 14px;
            line-height: 1.6;
        }

        .footer-col h4 {
            margin: 0 0 23px;
            color: #81978e;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.14em;
        }

        .footer-col a {
            display: block;
            margin: 13px 0;
            font-size: 13px;
        }

        .footer-col a:hover {
            color: var(--green);
        }

        .footer-bottom {
            border-top: 1px solid #ffffff2b;
            padding-top: 25px;
            display: flex;
            justify-content: space-between;
            color: #7f968c;
            font-size: 11px;
        }

        .reveal {
            opacity: 1;
            transform: none;
            transition:
                opacity 0.7s,
                transform 0.7s;
        }

        .reveal.visible {
            opacity: 1;
            transform: none;
        }

        @media (max-width: 980px) {
            .nav {
                grid-template-columns: 1fr auto;
            }

            .nav-links {
                display: none;
            }

            .nav-actions .login {
                display: none;
            }

            .menu {
                display: block;
            }

            .hero {
                grid-template-columns: 1fr;
            }

            .hero-copy {
                min-height: 650px;
                padding: 75px 24px;
            }

            .hero-visual {
                min-height: 560px;
            }

            .hero-bottom {
                grid-template-columns: 1fr;
            }

            .hero-note {
                display: none;
            }

            .intro-grid,
            .how-layout {
                grid-template-columns: 1fr;
                gap: 45px;
            }

            .stats {
                margin-top: 65px;
            }

            .how-sticky {
                position: static;
            }

            .services-grid {
                grid-template-columns: 1fr;
            }

            .service {
                min-height: 330px;
                border-right: 0;
                border-bottom: 1px solid #ffffff40;
            }

            .service-icon {
                width: 100px;
                height: 100px;
            }

            .feature {
                grid-template-columns: 1fr;
            }

            .feature-art {
                min-height: 570px;
            }

            .footer-main {
                grid-template-columns: 2fr 1fr 1fr;
            }

            .footer-col:last-child {
                display: none;
            }
        }

        @media (max-width: 640px) {
            .shell {
                width: min(100% - 32px, var(--max));
            }

            .topbar {
                display: none;
            }

            .nav {
                height: 70px;
            }

            .nav-actions .btn {
                display: none;
            }

            .hero-copy {
                min-height: 600px;
                padding: 62px 18px;
            }

            .hero h1 {
                font-size: 52px;
                margin: 30px 0;
            }

            .hero-lede {
                font-size: 16px;
            }

            .hero-search {
                display: grid;
            }

            .hero-search input {
                height: 48px;
            }

            .hero-visual {
                min-height: 460px;
            }

            .skill-stack {
                inset: 70px 7% 40px;
            }

            .skill-card {
                grid-template-columns: 50px 1fr;
                padding: 15px;
            }

            .skill-icon {
                width: 50px;
                height: 50px;
            }

            .skill-card .rating {
                display: block;
                grid-column: 2;
                font-size: 12px;
            }

            .intro,
            .how {
                padding: 85px 0;
            }

            .intro-grid {
                gap: 28px;
            }

            .display {
                font-size: 40px;
            }

            .stats {
                grid-template-columns: 1fr;
            }

            .stat,
            .stat:not(:first-child) {
                padding: 24px 0;
                border-right: 0;
                border-bottom: 1px solid var(--line);
            }

            .section-head {
                align-items: start;
                flex-direction: column;
            }

            .services {
                padding: 85px 0;
            }

            .service {
                padding: 25px;
                min-height: 320px;
            }

            .step {
                grid-template-columns: 38px 1fr;
            }

            .step>svg {
                display: none;
            }

            .feature-art {
                min-height: 480px;
            }

            .feature-copy {
                padding: 75px 24px;
            }

            .cta {
                padding: 85px 0;
            }

            .cta-actions {
                flex-direction: column;
            }

            .footer-main {
                grid-template-columns: 1fr 1fr;
                gap: 40px;
            }

            .footer-brand {
                grid-column: 1/-1;
            }

            .footer-bottom {
                flex-direction: column;
                gap: 10px;
            }
        }

        @media (prefers-reduced-motion: reduce) {
            html {
                scroll-behavior: auto;
            }

            .ticker-track {
                animation: none;
            }

            .reveal {
                transition: none;
            }
        }
    </style>

    {{-- DaisyUI --}}
    <link href="https://cdn.jsdelivr.net/npm/daisyui@4.12.10/dist/full.min.css" rel="stylesheet">

    {{-- Tailwind --}}
    <script src="https://cdn.tailwindcss.com"></script>

    {{-- Lucide --}}
    <script defer src="https://unpkg.com/lucide@0.468.0/dist/umd/lucide.min.js"></script>

    {{-- AlpineJS --}}
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    {{-- Tailwind Dark Mode --}}
    <script>
        tailwind.config = {
            darkMode: 'class'
        }
    </script>

    {{-- Dark mode script dijalankan sebelum halaman tampil --}}
    <script>
        function themeManager() {
            return {
                darkMode: false,

                init() {
                    const savedTheme = localStorage.getItem('theme');

                    if (savedTheme) {
                        this.darkMode = savedTheme === 'dark';
                    } else {
                        this.darkMode = window.matchMedia(
                            '(prefers-color-scheme: dark)'
                        ).matches;
                    }

                    this.applyTheme();
                },

                toggleTheme() {
                    this.darkMode = !this.darkMode;

                    localStorage.setItem(
                        'theme',
                        this.darkMode ? 'dark' : 'light'
                    );

                    this.applyTheme();
                },

                applyTheme() {
                    document.documentElement.classList.toggle(
                        'dark',
                        this.darkMode
                    );

                    document.documentElement.setAttribute(
                        'data-theme',
                        this.darkMode ? 'dark' : 'light'
                    );
                }
            }
        }
    </script>

    {{-- x-cloak --}}
    <style>
        [x-cloak] {
            display: none !important;
        }
    </style>


</head>

<body class="flex min-h-screen flex-col
           bg-slate-50 text-slate-800
           dark:bg-slate-950 dark:text-slate-100
           antialiased transition-colors duration-200">


    <a href="#content" class="sr-only focus:not-sr-only focus:p-4">
        Skip to content
    </a>

    {{-- ================= HEADER ================= --}}
    <header class="border-b border-slate-200 bg-white
           dark:border-slate-800 dark:bg-slate-900
           transition-colors duration-200">
        <nav class="mx-auto flex max-w-6xl items-center justify-between px-4 py-4" aria-label="Main navigation">

            {{-- Logo --}}
            <a href="{{ url('/') }}" class="text-xl font-extrabold tracking-tight
                   text-slate-900 dark:text-white">
                LOCAL<span class="text-emerald-600">SKILL</span>
            </a>

            {{-- Nav Menu Kanan --}}
            <div class="flex items-center gap-4">

                {{-- Explore --}}
                <a href="{{ route('explore.index') }}" class="text-sm font-semibold
                       text-slate-700 hover:text-emerald-600
                       dark:text-slate-300 dark:hover:text-emerald-400">
                    Explore
                </a>

                {{-- ================= THEME TOGGLE ================= --}}
                <button type="button" @click="toggleTheme()" class="btn btn-ghost btn-circle
                       text-slate-700
                       dark:text-slate-300" aria-label="Toggle dark mode" title="Toggle dark mode">

                    {{-- Sun --}}
                    <i x-show="darkMode" x-cloak data-lucide="sun" class="h-5 w-5"></i>

                    {{-- Moon --}}
                    <i x-show="!darkMode" x-cloak data-lucide="moon" class="h-5 w-5"></i>

                </button>

                {{-- Workspace / Login / Profile Dropdown --}}
                @auth
                    {{-- Dropdown Profile --}}
                    <div class="dropdown dropdown-end">
                        <div tabindex="0" role="button" class="btn btn-ghost btn-circle avatar">
                            <div class="w-9 rounded-full ring ring-emerald-500 ring-offset-2 ring-offset-base-100">
                                {{-- Mengambil foto profil user atau fallback placeholder --}}
                                <img alt="Profile Avatar"
                                    src="{{ Auth::user()->avatar_url ?? 'https://ui-avatars.com/api/?name=' . urlencode(Auth::user()->name) . '&background=0D9488&color=fff' }}" />
                            </div>
                        </div>

                        <ul tabindex="0"
                            class="menu menu-sm dropdown-content mt-3 z-[50] p-2 shadow-lg bg-white dark:bg-slate-800 rounded-box w-52 border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-200">

                            {{-- Header Info User --}}
                            <li class="menu-title px-4 py-2 border-b border-slate-100 dark:border-slate-700">
                                <span
                                    class="font-bold text-slate-900 dark:text-white truncate block">{{ Auth::user()->name }}</span>
                                <span
                                    class="text-xs font-normal text-slate-500 dark:text-slate-400 truncate block">{{ Auth::user()->email }}</span>
                            </li>

                            {{-- Item Menu --}}
                            <li class="mt-1">
                                <a href="{{ route('myorders.index') }}"
                                    class="py-2 hover:bg-slate-100 dark:hover:bg-slate-700">
                                    <i data-lucide="shopping-bag" class="w-4 h-4 text-emerald-600"></i>
                                    My Orders
                                </a>
                            </li>

                            {{-- Menu My Dashboard hanya muncul jika role user adalah provider --}}
                            @if (Auth::user()->isProvider())
                                <li>
                                    <a href="{{ route('user.dashboarduser') }}"
                                        class="py-2 hover:bg-slate-100 dark:hover:bg-slate-700">
                                        <i data-lucide="layout-dashboard" class="w-4 h-4 text-emerald-600"></i>
                                        My Dashboard
                                    </a>
                                </li>
                            @endif

                            <li>
                                <a href="{{ route('user.notifications.index') }}"
                                    class="py-2 hover:bg-slate-100 dark:hover:bg-slate-700">
                                    <i data-lucide="mail" class="w-4 h-4 text-emerald-600"></i>
                                    Inbox
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('user.profile.edit') }}"
                                    class="py-2 hover:bg-slate-100 dark:hover:bg-slate-700">
                                    <i data-lucide="settings" class="w-4 h-4 text-emerald-600"></i>
                                    Setting
                                </a>
                            </li>

                            <div class="divider my-1"></div>

                            {{-- Logout --}}
                            <li>
                                <form method="POST" action="{{ route('logout') }}" class="p-0">
                                    @csrf
                                    <button type="submit"
                                        class="w-full text-left flex items-center gap-2 py-2 text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-950/30">
                                        <i data-lucide="log-out" class="w-4 h-4"></i>
                                        Logout
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </div>
                @else
                    <a href="{{ route('login') }}" class="btn btn-sm bg-emerald-600 text-white
                                                           hover:bg-emerald-700 border-none">
                        Log in
                    </a>
                @endauth

            </div>

        </nav>
    </header>


    {{-- ================= CONTENT ================= --}}
    <main id="content" class="mx-auto w-full max-w-6xl flex-1 px-4 py-8 sm:py-12">
        @yield('content')
    </main>


    {{-- ================= FOOTER ================= --}}
    <footer class="mt-auto border-t border-slate-200 bg-white
           dark:border-slate-800 dark:bg-slate-900
           transition-colors duration-200">

        <div class="mx-auto flex max-w-6xl flex-wrap
               justify-between gap-3 px-4 py-6 text-sm
               text-slate-600
               dark:text-slate-400">

            <span>
                &copy; {{ date('Y') }} LOCALSKILL
                &middot; Built for campus life.
            </span>

            <a href="#how" class="hover:underline
                   hover:text-emerald-600
                   dark:hover:text-emerald-400">
                Find &rarr; Match &rarr; Book &rarr; Complete &rarr; Review
            </a>

        </div>

    </footer>


    {{-- ================= LUCIDE ================= --}}
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            if (window.lucide) {
                lucide.createIcons();
            }
        });
    </script>


</body>

</html>