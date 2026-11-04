import FleurDeLis from '../shared/FleurDeLis';

const icons = {
    evaluations: (
        <>
            <path d="M9 4h6v3H9z" />
            <path d="M9 5.500H7a2 2 0 0 0-2 2V19a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7.500a2 2 0 0 0-2-2h-2" />
            <path d="m9 14 2 2 4-4" />
        </>
    ),
    councils: <path d="M3 10 12 4l9 6M5 10v9M9.500 10v9M14.500 10v9M19 10v9M3 21h18" />,
    officers: (
        <>
            <circle cx="9" cy="8" r="3.500" />
            <path d="M2.500 20a6.500 6.500 0 0 1 13 0" />
            <path d="M16 4.600a3.500 3.500 0 0 1 0 6.800M18 14.200a6.500 6.500 0 0 1 3.500 5.800" />
        </>
    ),
};

const statItems = [
    ['ongoingEvaluations', 'Ongoing evaluations', 'evaluations'],
    ['activeCouncils', 'Active councils', 'councils'],
    ['studentOfficers', 'Student Officers', 'officers'],
];

function Icon({ children, className }) {
    return (
        <svg
            viewBox="0 0 24 24"
            className={className}
            fill="none"
            stroke="currentColor"
            strokeWidth="1.8"
            strokeLinecap="round"
            strokeLinejoin="round"
            aria-hidden="true"
        >
            {children}
        </svg>
    );
}

export default function HomeHero({ stats: counts }) {
    return (
        <section className="relative flex min-h-[calc(100vh-4rem)] items-center overflow-hidden bg-brand-dark pt-[72px] pb-[88px] text-[#f7fbf8] max-sm:pt-10 max-sm:pb-[60px]">
            <FleurDeLis className="pointer-events-none absolute top-1/2 right-[3%] h-[104%] -translate-y-1/2 text-brand-deep max-lg:right-1/2 max-lg:translate-x-1/2" />
            <div className="container-page relative grid items-center gap-8">
                <div className="max-w-[720px]">
                    <span className="inline-flex items-center gap-3 text-[13px] font-semibold tracking-[0.14em] text-gold uppercase before:h-0.5 before:w-7 before:bg-gold before:content-['']">
                        St. Paul University Philippines
                    </span>
                    <h1 className="my-[18px] text-[clamp(1.8rem,2vw+1.2rem,2.6rem)] leading-[1.2] font-bold tracking-[-0.01em]">
                        Paulinian Student Government <br />
                        E-Portfolio and Ranking System
                    </h1>                   <p className="max-w-[560px] text-base leading-[1.7] text-[#f7fbf8]/80">
                        Empowering student leadership through a smarter and more efficient digital experience — from
                        officer evaluations and leadership rankings to award applications, certificate issuance, and
                        professional e-portfolios, all in one centralized platform designed to streamline recognition,
                        performance tracking, and organizational management.
                    </p>
                    <div className="mt-7 mb-[18px] flex flex-wrap gap-3 max-sm:flex-col max-sm:items-start">
                        <a className="btn group border-white/60 bg-white text-brand-dark" href="/ranking/login">
                            Sign in
                            <Icon className="transition-transform group-hover:translate-x-1">
                                <path d="M5 12h14M13 6l6 6-6 6" />
                            </Icon>
                        </a>
                        <a className="btn border-white/55 text-[#f7fbf8]" href="#about">
                            Explore the system
                            <Icon>
                                <path d="m6 9 6 6 6-6" />
                            </Icon>
                        </a>
                    </div>
                    <div className="mt-10 grid grid-cols-3 border-t border-white/25 max-sm:grid-cols-1">
                        {statItems.map(([key, label, icon]) => (
                            <div
                                key={key}
                                className="flex items-center gap-3 px-5 pt-5 first:pl-0 not-first:border-l not-first:border-white/20 max-sm:px-0 max-sm:py-4 max-sm:not-first:border-t max-sm:not-first:border-l-0"
                            >
                                <span className="shrink-0 text-gold [&_svg]:block [&_svg]:size-[26px]">
                                    <Icon>{icons[icon]}</Icon>
                                </span>
                                <div>
                                    <strong className="block text-[26px] leading-[1.1]">{counts[key]}</strong>
                                    <span className="mt-0.5 block text-[12.5px] leading-[1.35] text-[#f7fbf8]/80">
                                        {label}
                                    </span>
                                </div>
                            </div>
                        ))}
                    </div>
                </div>
            </div>
        </section>
    );
}
