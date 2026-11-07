import { Link } from '@inertiajs/react';
import EmptyState from '@/components/shared/EmptyState';
import { EvaluationBadge } from '@/components/shared/StatusBadge';
import Panel from '@/components/ui/Panel';
import type { EvaluationStatus } from '@/types/councils';
import { index, show } from '@/actions/App/Features/Evaluations/Controllers/CouncilController';

type Council = {
    id: number;
    name: string;
    academicYear: string;
    status: EvaluationStatus;
    role: string;
};

export default function CouncilShortlist({ councils }: { councils: Council[] }) {
    return (
        <Panel
            title="Your councils"
            actions={
                <Link href={index.url()} className="text-sm font-medium text-brand hover:underline">
                    See all
                </Link>
            }
            flush
        >
            {councils.length === 0 ? (
                <EmptyState title="No councils yet">
                    An adviser adds you to a council when they set up its evaluation.
                </EmptyState>
            ) : (
                <ul className="divide-y divide-line">
                    {councils.map((council) => (
                        <li key={council.id}>
                            <Link
                                href={show.url(council.id)}
                                className="flex items-center gap-3 px-5 py-3.5 transition-colors hover:bg-paper focus-visible:outline-2 focus-visible:-outline-offset-2 focus-visible:outline-brand"
                            >
                                <div className="min-w-0 flex-1">
                                    <p className="truncate text-sm font-medium">{council.name}</p>
                                    <p className="text-xs text-muted">
                                        {council.academicYear} · {council.role}
                                    </p>
                                </div>
                                <EvaluationBadge status={council.status} />
                            </Link>
                        </li>
                    ))}
                </ul>
            )}
        </Panel>
    );
}
