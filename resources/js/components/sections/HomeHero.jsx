const statLabels = [
    ['ongoingEvaluations', 'Ongoing evaluations'],
    ['activeCouncils', 'Active councils'],
    ['studentOfficers', 'Student officers'],
];

export default function HomeHero({ stats }) {
    return (
        <section className="relative overflow-hidden">
            <div
                aria-hidden="true"
                className="absolute inset-x-0 top-0 -z-10 h-[34rem] bg-[radial-gradient(60rem_28rem_at_50%_-10%,var(--color-brand-100),transparent)]"
            />
            <div className="mx-auto max-w-6xl px-6 pt-20 pb-16 text-center md:pt-28">
                <p className="mx-auto mb-6 w-fit rounded-full border border-brand-200 bg-brand-50 px-4 py-1 text-xs font-semibold tracking-wide text-brand-700 uppercase">
                    St. Paul University Philippines
                </p>
                <h1 className="mx-auto max-w-3xl font-serif text-5xl leading-[1.05] tracking-tight md:text-7xl">
                    Student leadership,{' '}
                    <em className="text-brand-600">measured fairly.</em>
                </h1>
                <p className="mx-auto mt-6 max-w-2xl text-lg text-muted">
                    Evaluations, automated rankings, award applications and
                    digital certificates for the Paulinian Student Government,
                    in one platform.
                </p>
                <div className="mt-9 flex flex-wrap justify-center gap-3">
                    <a href="/ranking/login" className="btn btn-primary">
                        Sign in
                    </a>
                    <a href="#features" className="btn btn-ghost">
                        Explore the system
                    </a>
                </div>
                <dl className="mx-auto mt-16 grid max-w-2xl grid-cols-3 gap-6 border-t border-line pt-8">
                    {statLabels.map(([key, label]) => (
                        <div key={key}>
                            <dd className="font-serif text-4xl">{stats[key]}</dd>
                            <dt className="mt-1 text-xs text-muted">{label}</dt>
                        </div>
                    ))}
                </dl>
            </div>
            <div className="mx-auto max-w-5xl px-6 pb-20">
                <img
                    src="/assets/landing-page.png"
                    alt="Screenshot of the ranking dashboard"
                    className="w-full rounded-2xl border border-line shadow-2xl shadow-brand-900/10"
                />
            </div>
        </section>
    );
}
