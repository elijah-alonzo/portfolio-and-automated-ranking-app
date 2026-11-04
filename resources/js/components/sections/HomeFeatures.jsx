import { features } from '../../data/landing';
import useReveal from '../../hooks/useReveal';

function Feature({ feature, index }) {
    const ref = useReveal();
    const flipped = index % 2 === 1;

    return (
        <article
            ref={ref}
            className="reveal grid items-center gap-10 md:grid-cols-2"
        >
            <div className={flipped ? 'md:order-2' : ''}>
                <span className="font-serif text-brand-500">
                    0{index + 1}
                </span>
                <h3 className="mt-2 text-3xl font-semibold tracking-tight">
                    {feature.title}
                </h3>
                <p className="mt-4 text-muted">{feature.description}</p>
            </div>
            <img
                src={feature.image}
                alt={feature.title}
                loading="lazy"
                className="w-full rounded-xl border border-line shadow-lg shadow-brand-900/5"
            />
        </article>
    );
}

export default function HomeFeatures() {
    return (
        <section id="features" className="mx-auto max-w-6xl px-6 py-20">
            <h2 className="font-serif text-4xl md:text-5xl">
                Everything the council needs
            </h2>
            <div className="mt-16 space-y-24">
                {features.map((feature, index) => (
                    <Feature key={feature.title} feature={feature} index={index} />
                ))}
            </div>
        </section>
    );
}
