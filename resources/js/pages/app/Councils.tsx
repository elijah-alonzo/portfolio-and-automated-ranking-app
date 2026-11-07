import { Link } from '@inertiajs/react';
import { ChevronRight } from 'lucide-react';
import EmptyState from '@/components/shared/EmptyState';
import PageHeader from '@/components/shared/PageHeader';
import Seo from '@/components/shared/Seo';
import { EvaluationBadge } from '@/components/shared/StatusBadge';
import Panel from '@/components/ui/Panel';
import AppLayout from '@/layouts/AppLayout';
import type { CouncilListItem } from '@/types/councils';
import { show } from '@/actions/App/Features/Evaluations/Controllers/CouncilController';

export default function Councils({ councils }: { councils: CouncilListItem[] }) {
    return (
        <AppLayout>
            <Seo title="My Councils" description="The council evaluations you advise or serve in." />
            <PageHeader
                title="My Councils"
                description="Each council evaluation you advise or serve in, newest year first."
            />
            <Panel flush>
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
                                    className="grid grid-cols-[1fr_auto] items-center gap-x-4 gap-y-1 px-5 py-4 transition-colors hover:bg-paper focus-visible:outline-2 focus-visible:-outline-offset-2 focus-visible:outline-brand sm:grid-cols-[1fr_8rem_7rem_auto_auto]"
                                >
                                    <div className="min-w-0">
                                        <p className="truncate font-medium">{council.name}</p>
                                        <p className="truncate text-sm text-muted">
                                            {council.academicYear}
                                            {council.adviser && ` · Adviser: ${council.adviser}`}
                                        </p>
                                    </div>
                                    <p className="hidden text-sm text-muted sm:block">
                                        {council.officers} officer{council.officers === 1 ? '' : 's'}
                                    </p>
                                    <p className="hidden text-sm sm:block">
                                        You: <span className="font-medium">{council.role}</span>
                                    </p>
                                    <EvaluationBadge status={council.status} />
                                    <ChevronRight size={16} aria-hidden className="hidden text-muted sm:block" />
                                </Link>
                            </li>
                        ))}
                    </ul>
                )}
            </Panel>
        </AppLayout>
    );
}
