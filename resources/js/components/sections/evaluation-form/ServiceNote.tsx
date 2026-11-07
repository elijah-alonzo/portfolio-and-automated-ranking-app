/** Length of service is not rated by hand: it comes from how many years the officer has served. */
export default function ServiceNote({
    years,
    score,
    awardType,
}: {
    years: number;
    score: number;
    awardType: string | null;
}) {
    return (
        <div className="flex flex-wrap items-center gap-4 rounded-lg bg-gold-tint p-4 ring-1 ring-gold/40">
            <span className="grid size-10 place-items-center rounded-md bg-gold font-bold text-brand-deep tabular-nums">
                {score}
            </span>
            <p className="flex-1 text-sm leading-relaxed">
                <span className="font-semibold">Length of service is filled in for you.</span> This officer has served{' '}
                {years} year{years === 1 ? '' : 's'}
                {awardType ? ` in ${awardType} councils` : ''}, which scores {score} of 3.
            </p>
        </div>
    );
}
