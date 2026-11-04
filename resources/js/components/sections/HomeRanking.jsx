import { weights } from '../../data/landing';
import useReveal from '../../hooks/useReveal';

const tiers = [
    ['Gold', '2.41+', 'bg-amber-400'],
    ['Silver', '1.81+', 'bg-slate-400'],
    ['Bronze', '1.21+', 'bg-orange-500'],
];

export default function HomeRanking() {
    const ref = useReveal();

    return (
        <section id="ranking" className="bg-brand-900 text-white">
            <div
                ref={ref}
                className="reveal mx-auto grid max-w-6xl gap-12 px-6 py-20 md:grid-cols-2"
            >
                <div>
                    <h2 className="font-serif text-4xl md:text-5xl">
                        Scoring you can explain.
                    </h2>
                    <p className="mt-4 max-w-md text-brand-200">
                        Three evaluators, one weighted score, and a tier. No
                        spreadsheets, no manual tallying.
                    </p>
                    <ul className="mt-8 space-y-3">
                        {tiers.map(([name, score, color]) => (
                            <li key={name} className="flex items-center gap-3">
                                <span
                                    className={`h-3 w-3 rounded-full ${color}`}
                                />
                                <span className="font-medium">{name}</span>
                                <span className="text-sm text-brand-200">
                                    {score}
                                </span>
                            </li>
                        ))}
                    </ul>
                </div>
                <div className="space-y-5 self-center">
                    {weights.map((item) => (
                        <div key={item.label}>
                            <div className="mb-1 flex justify-between text-sm">
                                <span>{item.label}</span>
                                <span>{item.value}%</span>
                            </div>
                            <div className="h-2 rounded-full bg-white/10">
                                <div
                                    className="h-2 rounded-full bg-brand-400"
                                    style={{ width: `${item.value}%` }}
                                />
                            </div>
                        </div>
                    ))}
                </div>
            </div>
        </section>
    );
}
