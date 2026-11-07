import { card } from '@/components/ui/styles';
import { formatScore } from '@/lib/format';

type Rank = {
    council: string | null;
    academicYear: string;
    finalScore: number;
    rank: string | null;
};

/** The officer's most recent final score, read against the 3.00 scale. */
export default function LatestRank({ rank }: { rank: Rank }) {
    const share = Math.min(rank.finalScore / 3, 1) * 100;

    return (
        <section className={`${card} overflow-hidden`}>
            <div className="bg-brand-deep px-5 py-5 text-white">
                <p className="text-sm text-white/70">Your latest rank</p>
                <p className="mt-1 font-display font-medium text-4xl">
                    {rank.rank && rank.rank !== 'None' ? rank.rank : 'No tier'}
                </p>
                <p className="mt-1 text-sm text-white/70">
                    {rank.council}, {rank.academicYear}
                </p>
            </div>
            <div className="px-5 py-4">
                <div className="flex items-baseline justify-between text-sm">
                    <span className="text-muted">Final score</span>
                    <span className="font-semibold tabular-nums">
                        {formatScore(rank.finalScore)} <span className="font-normal text-muted">/ 3.00</span>
                    </span>
                </div>
                <div
                    className="mt-2 h-2 overflow-hidden rounded-full bg-paper"
                    role="img"
                    aria-label={`${formatScore(rank.finalScore)} out of 3.00`}
                >
                    <div className="h-full rounded-full bg-gold" style={{ width: `${share}%` }} />
                </div>
                <p className="mt-3 text-xs text-muted">Gold from 2.41, Silver from 1.81, Bronze from 1.21.</p>
            </div>
        </section>
    );
}
