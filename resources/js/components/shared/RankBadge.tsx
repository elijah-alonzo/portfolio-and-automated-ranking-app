import clsx from 'clsx';
import { formatScore } from '@/lib/format';

const tones: Record<string, string> = {
    Gold: 'bg-gold-tint text-[#7a5d00] ring-gold/50',
    Silver: 'bg-slate-100 text-slate-700 ring-slate-300',
    Bronze: 'bg-orange-50 text-orange-800 ring-orange-300',
    None: 'bg-paper text-muted ring-line',
};

/** A final score with its tier: Gold from 2.41, Silver from 1.81, Bronze from 1.21. */
export default function RankBadge({ rank, score }: { rank: string | null; score: number }) {
    const tier = rank ?? 'None';

    return (
        <span
            className={clsx(
                'inline-flex items-center gap-1.5 rounded-md px-2 py-0.5 text-xs font-semibold whitespace-nowrap ring-1 ring-inset',
                tones[tier] ?? tones.None
            )}
        >
            {tier === 'None' ? 'No tier' : tier}
            <span className="font-medium tabular-nums opacity-80">{formatScore(score)}</span>
        </span>
    );
}
