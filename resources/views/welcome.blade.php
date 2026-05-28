@extends ('Layout')

@section ('nav_links')
    <a href="#about">About</a>
    <a href="#features">Features</a>
    <a href="#contact">Contact</a>
@endsection

@section ('nav_actions')
    <a class="btn btn-outline" href="#about">Learn more</a>
    <a class="btn btn-primary" href="/ranking/login">Login</a>
@endsection

@section ('content')
    <section class="hero">
        <div class="container hero-grid">
            <div class="hero-text">
                <h1 class="hero-title">
                    Paulinian Student Government <br />
                    E-Portfolio and Ranking System
                </h1>
                <p class="hero-description">Empowering student leadership through a smarter and more efficient digital experience — from officer evaluations and leadership rankings to award applications, certificate issuance, and professional e-portfolios, all in one centralized platform designed to streamline recognition, performance tracking, and organizational management.</p>
                <div class="hero-actions">
                    <a class="btn btn-primary" href="/ranking/login">Sign in</a>
                    <a class="btn btn-outline" href="#about"
                        >Explore the system</a
                    >
                </div>
                <div class="hero-divider" aria-hidden="true"></div>
                <div class="hero-counters">
                    <div class="hero-counter">
                        <strong>5</strong>
                        <span>Ongoing evaluations</span>
                    </div>
                    <div class="hero-counter">
                        <strong>5</strong>
                        <span>Active councils</span>
                    </div>
                    <div class="hero-counter">
                        <strong>67</strong>
                        <span>Student Officers</span>
                    </div>
                </div>
            </div>
            <div class="hero-brand" aria-hidden="true">
                <img src="/sys-brand.png" alt="" />
            </div>
        </div>
    </section>
    <section id="about" class="about">
        <div class="container">
            <div class="about-grid">
                <div class="about-intro">
                    <h2>About the Paulinian Student Government</h2>
                    <p>The Paulinian Student Government of St. Paul University Philippines represents student leaders across campus and departmental councils. It fosters leadership, service, and accountability through transparent governance and community-focused initiatives.</p>
                    <p>This system supports those efforts by organizing portfolios, evaluations, and rankings for each council, making achievements and leadership growth visible and measurable.</p>
                </div>
                <div class="steps" id="features">
                    <div class="step-card">
                        <span>Campus council</span>
                        <strong>Paulinian Student Government</strong>
                        <p>The primary student governing body with campus-wide initiatives, representation, and leadership oversight.</p>
                    </div>
                    <div class="step-card">
                        <span>Department council</span>
                        <strong>PSG - SITE</strong>
                        <p>School of Information Technology and Engineering leadership council coordinating departmental programs.</p>
                    </div>
                    <div class="step-card">
                        <span>Department council</span>
                        <strong>PSG - SASTE</strong>
                        <p>School of Art Sciences and Teacher Education council supporting leadership and student initiatives.</p>
                    </div>
                    <div class="step-card">
                        <span>Department council</span>
                        <strong>PSG - SBAHM</strong>
                        <p>School of Business, Accountancy, and Hospitality Management student governance council.</p>
                    </div>
                    <div class="step-card">
                        <span>Department council</span>
                        <strong>PSG - SNAHS</strong>
                        <p>School of Nursing and Allied Health Services council guiding leadership and service projects.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@section ('footer')
    <footer id="contact">
        <div class="container footer-grid">
            <div>
                <div class="footer-brand">
                    <img
                        class="footer-logo"
                        src="/sys-logo.png"
                        alt="Paulinian Student Government logo"
                    />
                    <div>
                        <div class="footer-title">
                            Paulinian Student Government
                        </div>
                        <div>E-Portfolio and Ranking System</div>
                    </div>
                </div>
            </div>
            <div>
                <div class="footer-title">
                    Student Affairs and Academic Services
                </div>
                <ul class="footer-list">
                    <li>St. Paul University Philippines</li>
                    <li><a href="mailto:psg@school.edu">psg@school.edu</a></li>
                </ul>
            </div>
            <div>
                <div class="footer-title">Connect with PSG</div>
                <ul class="footer-list">
                    <li>
                        <a
                            href="https://www.facebook.com/PaulinianStudentGovernment"
                            target="_blank"
                            rel="noreferrer"
                        >
                            Facebook: Paulinian Student Government
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </footer>
@endsection
