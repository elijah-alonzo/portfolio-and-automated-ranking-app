import { Link } from '@inertiajs/react';
import clsx from 'clsx';
import { Check, Clock, PenLine } from 'lucide-react';
import type { EvaluatorType, FormState } from '@/types/councils';
import { show as form } from '@/actions/App/Features/Evaluations/Controllers/EvaluationFormController';

const order: EvaluatorType[] = ['self', 'peer', 'adviser'];
const names: Record<EvaluatorType, string> = { self: 'Self', peer: 'Peer', adviser: 'Adviser' };

const tone = {
    pending: 'bg-paper text-muted ring-line',
    draft: 'bg-wait-tint text-wait ring-wait/30',
    submitted: 'bg-ok-tint text-ok ring-ok/30',
} as const;

const icon = { pending: Clock, draft: PenLine, submitted: Check } as const;
const statusText = { pending: 'not started', draft: 'draft', submitted: 'submitted' } as const;

/** An officer's three forms. Each one the viewer may open is a link; theirs to fill is ringed in gold. */
export default function FormPills({
    councilId,
    officerId,
    forms,
}: {
    councilId: number;
    officerId: number;
    forms: Record<EvaluatorType, FormState>;
}) {
    return (
        <ul className="flex flex-wrap gap-1.5" aria-label="Forms">
            {order.map((type) => {
                const state = forms[type];
                const Icon = icon[state.status];
                const className = clsx(
                    'inline-flex items-center gap-1 rounded-md px-2 py-1 text-xs font-medium ring-1 ring-inset',
                    tone[state.status],
                    state.isMine && 'ring-2 ring-gold'
                );
                const content = (
                    <>
                        <Icon size={12} aria-hidden />
                        {names[type]}
                        {state.isMine && <span className="font-semibold text-brand-deep">· you</span>}
                        <span className="sr-only">: {statusText[state.status]}</span>
                    </>
                );

                return (
                    <li key={type}>
                        {state.canOpen ? (
                            <Link
                                href={form.url({ evaluation: councilId, user: officerId, type })}
                                className={clsx(
                                    className,
                                    'transition hover:brightness-95 focus-visible:outline-2 focus-visible:outline-brand'
                                )}
                            >
                                {content}
                            </Link>
                        ) : (
                            <span className={className}>{content}</span>
                        )}
                    </li>
                );
            })}
        </ul>
    );
}
