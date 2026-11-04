import { councils } from '../../data/councils';

export default function HomeAbout() {
    return (
        <section id="about" className="bg-white py-[72px]">
            <div className="container-page">
                <div className="grid items-start gap-8 min-[961px]:grid-cols-[1fr_1.4fr] min-[961px]:gap-14">
                    <div>
                        <h2 className="mb-4 text-[clamp(1.8rem,2vw+1.2rem,2.6rem)] leading-[1.2] font-bold">
                            About the Paulinian Student Government
                        </h2>
                        <p className="mb-3.5 text-base leading-[1.7] text-muted">
                            The Paulinian Student Government of St. Paul University Philippines represents student leaders
                            across campus and departmental councils. It fosters leadership, service, and accountability
                            through transparent governance and community-focused initiatives.
                        </p>
                        <p className="mb-3.5 text-base leading-[1.7] text-muted">
                            This system supports those efforts by organizing portfolios, evaluations, and rankings for
                            each council, making achievements and leadership growth visible and measurable.
                        </p>
                    </div>
                    <div id="features" className="grid grid-cols-2 gap-4 max-sm:grid-cols-1">
                        {councils.map((council) => (
                            <div
                                key={council.name}
                                className="min-h-[120px] rounded-2xl border border-brand-dark/15 bg-brand-dark/8 p-[18px]"
                            >
                                <span className="mb-2 block text-xs tracking-[0.18em] text-brand-dark/70 uppercase">
                                    {council.type}
                                </span>
                                <strong className="mb-1.5 block text-[15px] text-brand-dark">{council.name}</strong>
                                <p className="text-[13px] leading-[1.6] text-muted">{council.description}</p>
                            </div>
                        ))}
                    </div>
                </div>
            </div>
        </section>
    );
}
