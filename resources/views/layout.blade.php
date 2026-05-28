<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>
        @yield ('title', 'Paulinian Student Government E-Portfolio and Ranking System')
    </title>

    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
        href="https://fonts.googleapis.com/css2?family=Figtree:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet"
    />

    <style>
        :root {
            --brand-green: #036635;
            --brand-green-dark: #024427;
            --brand-green-soft: #e6f2ec;
            --ink: #0f1a14;
            --muted: #4b5a52;
            --paper: #ffffff;
            --bg: #f4f6f2;
            --hero-image: url("/sys-bg.jpg");
            --nav-height: 64px;
        }

        * { box-sizing: border-box; }

        body {
            margin: 0;
            font-family: "Figtree", "Trebuchet MS", sans-serif;
            color: var(--ink);
            background: var(--bg);
        }

        a { color: inherit; text-decoration: none; }

        .container {
            width: min(1200px, 92vw);
            margin: 0 auto;
            padding: 0 24px;
        }

        /* ── Nav ── */
        header {
            background: var(--paper);
            border-bottom: 1px solid rgba(15, 26, 20, 0.08);
            position: sticky;
            top: 0;
            z-index: 10;
        }

        .nav {
            display: flex;
            align-items: center;
            justify-content: space-between;
            min-height: var(--nav-height);
            gap: 24px;
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 12px;
            font-weight: 600;
            letter-spacing: 0.4px;
        }

        .logo-image {
            width: auto;
            height: 40px;
            object-fit: contain;
        }

        .nav-links {
            display: flex;
            align-items: center;
            gap: 24px;
            font-size: 15px;
        }

        .nav-links a {
            color: var(--muted);
            font-weight: 500;
        }

        .nav-actions {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        /* ── Buttons ── */
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 12px 20px;
            border-radius: 999px;
            font-weight: 600;
            border: 1px solid transparent;
            transition: transform 0.2s ease, box-shadow 0.2s ease, color 0.2s ease;
        }

        .btn:hover {
            transform: scale(1.05);
            box-shadow: 0 8px 18px rgba(15, 26, 20, 0.12);
        }

        .btn-primary {
            background: var(--brand-green);
            color: white;
        }

        .btn-outline {
            border-color: rgba(15, 26, 20, 0.15);
            color: var(--ink);
            background: white;
        }

        .nav-actions .btn-primary {
            background: var(--brand-green-dark);
            color: #ffffff;
        }

        /* ── Hero ── */
        .hero {
            position: relative;
            padding: 72px 0 88px;
            min-height: calc(100vh - var(--nav-height));
            display: flex;
            align-items: center;
            color: #f7fbf8;
            overflow: hidden;
        }

        .hero::before {
            content: "";
            position: absolute;
            inset: 0;
            background-image: var(--hero-image);
            background-size: cover;
            background-position: center;
            filter: contrast(0.80) brightness(0.4);
        }

        .hero::after {
            content: "";
            position: absolute;
            inset: 0;
            background: rgba(2, 68, 39, 0.9);
        }

        .hero-grid {
            position: relative;
            z-index: 1;
            display: grid;
            grid-template-columns: minmax(0, 1fr) minmax(0, 0.8fr);
            align-items: center;
            gap: 24px;
        }

        .hero-text { max-width: 680px; }

        .hero-brand {
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .hero-brand img {
            width: min(360px, 80vw);
            max-height: 360px;
            object-fit: contain;
            filter: drop-shadow(0 16px 32px rgba(2, 68, 39, 0.5));
        }

        .hero-kicker {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            background: rgba(230, 242, 236, 0.16);
            color: #e8f6ef;
            padding: 6px 14px;
            border-radius: 999px;
            font-size: 13px;
            font-weight: 600;
            letter-spacing: 0.4px;
            text-transform: uppercase;
        }

        .hero-title {
            font-size: clamp(1.8rem, 2vw + 1.2rem, 2.6rem);
            line-height: 1.2;
            margin: 0 0 16px;
        }

        .hero-description {
            font-size: 1rem;
            line-height: 1.7;
            color: rgba(247, 251, 248, 0.82);
            max-width: 560px;
        }

        .hero-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            margin: 28px 0 18px;
        }

        .hero-divider {
            height: 1px;
            width: 100%;
            background: rgba(255, 255, 255, 0.2);
            margin: 10px 0 18px;
        }

        .hero-meta {
            display: flex;
            gap: 18px;
            flex-wrap: wrap;
            color: rgba(247, 251, 248, 0.72);
            font-size: 14px;
        }

        .hero-meta span {
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .hero-counters {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 16px;
            margin-top: 22px;
        }

        .hero-counter {
            background: rgba(255, 255, 255, 0.08);
            border: 1px solid rgba(255, 255, 255, 0.16);
            border-radius: 16px;
            padding: 14px 16px;
            backdrop-filter: blur(6px);
        }

        .hero-counter strong {
            display: block;
            font-size: 30px;
            margin-bottom: 6px;
            color: #f7fbf8;
        }

        .hero-counter span {
            margin: 0;
            font-size: 13px;
            color: white;
            line-height: 1.6;
        }

        .hero .btn-outline {
            border-color: rgba(255, 255, 255, 0.55);
            color: #f7fbf8;
            background: transparent;
        }

        .hero .btn-primary {
            background: #ffffff;
            color: var(--brand-green-dark);
            border-color: rgba(255, 255, 255, 0.6);
        }

        /* ── About ── */
        .about {
            background: var(--paper);
            padding: 72px 0;
        }

        .about-grid {
            display: grid;
            grid-template-columns: 1fr 1.4fr;
            gap: 56px;
            align-items: start;
        }

        .about-intro h2 {
            font-size: clamp(1.8rem, 2vw + 1.2rem, 2.6rem);
            margin: 0 0 16px;
            line-height: 1.2;
        }

        .about-intro p {
            color: var(--muted);
            line-height: 1.7;
            font-size: 1rem;
            margin: 0 0 14px;
        }

        /* ── Step cards ── */
        .steps {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 16px;
        }

        .step-card {
            background: rgba(2, 68, 39, 0.08);
            border: 1px solid rgba(2, 68, 39, 0.14);
            border-radius: 16px;
            padding: 18px;
            min-height: 120px;
        }

        .step-card span {
            display: block;
            font-size: 12px;
            letter-spacing: 0.18em;
            color: rgba(2, 68, 39, 0.7);
            text-transform: uppercase;
            margin-bottom: 8px;
        }

        .step-card strong {
            display: block;
            font-size: 15px;
            margin-bottom: 6px;
            color: var(--brand-green-dark);
        }

        .step-card p {
            margin: 0;
            font-size: 13px;
            color: var(--muted);
            line-height: 1.6;
        }

        /* ── Footer ── */
        footer {
            position: relative;
            background: var(--brand-green-dark) url("/sys-footer.png") center / cover no-repeat;
            color: white;
            padding: 36px 0;
        }

        footer::before {
            content: "";
            position: absolute;
            inset: 0;
            background: rgba(2, 68, 39, 0.9);
        }

        .footer-grid {
            position: relative;
            z-index: 1;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 20px;
            font-size: 14px;
        }

        .footer-brand {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 10px;
        }

        .footer-logo {
            width: 44px;
            height: 44px;
            object-fit: contain;
        }

        .footer-title {
            font-weight: 600;
            margin-bottom: 10px;
        }

        .footer-list {
            display: grid;
            gap: 6px;
            margin: 0;
            padding: 0;
            list-style: none;
        }

        .footer-grid a { color: rgba(255, 255, 255, 0.85); }

        /* ── Responsive ── */
        @media (max-width: 960px)
        {
                   .about-grid { grid-template-columns: 1fr; gap: 32px; }
                   .hero-grid { grid-template-columns: 1fr; }
                   .hero-brand { order: -1; }
                   .hero-counters { grid-template-columns: repeat(2, minmax(0, 1fr)); }
                   .nav-links { display: none; }
               }
        @media (max-width: 640px)
        {
                   .nav { flex-wrap: wrap; }
                   .hero { padding: 40px 0 60px; }
                   .hero-actions { flex-direction: column; align-items: flex-start; }
                   .hero-counters { grid-template-columns: 1fr; }
                   .steps { grid-template-columns: 1fr; }
               }
    </style>
</head>
<body>
    <header>
        <div class="container nav">
            <div class="logo">
                <img
                    class="logo-image"
                    src="/sys-logo.png"
                    alt="Paulinian Student Government logo"
                />
            </div>
            <nav class="nav-links">
                @yield ('nav_links')
            </nav>
            <div class="nav-actions">
                @yield ('nav_actions')
            </div>
        </div>
    </header>

    <main>
        @yield ('content')
    </main>

    @yield ('footer')
</body>
</html>
