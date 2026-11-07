import EmptyState from '@/components/shared/EmptyState';
import RankBadge from '@/components/shared/RankBadge';
import { EvaluationBadge } from '@/components/shared/StatusBadge';
import Panel from '@/components/ui/Panel';
import type { Experience } from '@/types/portfolio';

/** Every council term served, newest first, with the final rank once the evaluation is complete. */
export default function ExperienceList({ experience }: { experience: Experience[] }) {
    return (
        <Panel title="Council experience" description="Every council term served, newest first." flush>
            {experience.length === 0 ? (
                <EmptyState title="No council terms yet">
                    Terms appear here once an adviser adds this officer to a council evaluation.
                </EmptyState>
            ) : (
                <ol className="relative px-5 py-2">
                    {experience.map((term) => (
                        <li key={term.id} className="relative border-l-2 border-line py-4 pl-6 last:border-transparent">
                            <span
                                aria-hidden
                                className="absolute top-5 -left-[7px] size-3 rounded-full bg-brand ring-4 ring-brand-tint"
                            />
                            <div className="flex flex-wrap items-start justify-between gap-2">
                                <div>
                                    <p className="font-semibold">{term.position ?? 'Officer'}</p>
                                    <p className="text-sm text-muted">
                                        {term.council}, {term.academicYear}
                                    </p>
                                </div>
                                {term.finalScore !== null && term.status === 'completed' ? (
                                    <RankBadge rank={term.rank} score={term.finalScore} />
                                ) : (
                                    <EvaluationBadge status={term.status} />
                                )}
                            </div>
                        </li>
                    ))}
                </ol>
            )}
        </Panel>
    );
}
