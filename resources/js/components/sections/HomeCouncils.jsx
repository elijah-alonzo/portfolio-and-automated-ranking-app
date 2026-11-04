import { councils } from '../../data/landing';
import useReveal from '../../hooks/useReveal';

export default function HomeCouncils() {
    const ref = useReveal();

    return (
        <section id="councils" className="mx-auto max-w-6xl px-6 py-20">
            <h2 className="font-serif text-4xl md:text-5xl">One campus, five councils</h2>
            <div
                ref={ref}
                className="reveal mt-12 grid gap-4 sm:grid-cols-2 lg:grid-cols-3"
            >
                {councils.map((council) => (
                    <article
                        key={council.name}
                        className="rounded-xl border border-line bg-white p-6 transition hover:-translate-y-1 hover:border-brand-400"
                    >
                        <span className="text-xs font-semibold tracking-wide text-brand-600 uppercase">
                            {council.type}
                        </span>
                        <h3 className="mt-2 text-lg font-semibold">{council.name}</h3>
                        <p className="mt-2 text-sm text-muted">{council.description}</p>
                    </article>
                ))}
            </div>
        </section>
    );
}
