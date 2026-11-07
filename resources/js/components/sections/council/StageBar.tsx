import { router } from '@inertiajs/react';
import clsx from 'clsx';
import { Check } from 'lucide-react';
import { useState } from 'react';
import Button from '@/components/ui/Button';
import Dialog from '@/components/ui/Dialog';
import { card } from '@/components/ui/styles';
import type { CouncilDetail, EvaluationStatus } from '@/types/councils';
import CouncilStatusController from '@/actions/App/Features/Evaluations/Controllers/CouncilStatusController';

const stages: { status: EvaluationStatus; title: string; body: string }[] = [
    {
        status: 'closed',
        title: 'Set up',
        body: 'The adviser fills each position and pairs every officer with a peer evaluator.',
    },
    {
        status: 'ongoing',
        title: 'Open for forms',
        body: 'Officers write their self and peer forms; the adviser writes one for each officer.',
    },
    {
        status: 'completed',
        title: 'Completed',
        body: 'Every form is in, final scores and ranks are set, and nothing can change.',
    },
];

type Move = { to: EvaluationStatus; label: string; confirm: string; variant: 'primary' | 'secondary' };

/** The evaluation's three stages, and for the adviser the button that moves it on. */
export default function StageBar({ council }: { council: CouncilDetail }) {
    const [move, setMove] = useState<Move | null>(null);
    const [busy, setBusy] = useState(false);
    const current = stages.findIndex((stage) => stage.status === council.status);

    const moves: Move[] = !council.canManage
        ? []
        : council.status === 'closed'
          ? [
                {
                    to: 'ongoing',
                    label: 'Open evaluation',
                    variant: 'primary',
                    confirm:
                        'Officers will be notified and can start their forms. Officers and peers can no longer change until you close it again.',
                },
            ]
          : council.status === 'ongoing'
            ? [
                  {
                      to: 'closed',
                      label: 'Close evaluation',
                      variant: 'secondary',
                      confirm:
                          'Forms stop accepting changes until you open it again. Use this to change officers or peers.',
                  },
                  ...(council.canComplete
                      ? [
                            {
                                to: 'completed' as const,
                                label: 'Complete evaluation',
                                variant: 'primary' as const,
                                confirm:
                                    'Final scores become permanent and every form becomes read-only. This cannot be undone.',
                            },
                        ]
                      : []),
              ]
            : [];

    const confirm = () => {
        if (!move) return;
        router.put(
            CouncilStatusController.url(council.id),
            { status: move.to },
            {
                preserveScroll: true,
                onStart: () => setBusy(true),
                onFinish: () => {
                    setBusy(false);
                    setMove(null);
                },
            }
        );
    };

    return (
        <section className={`${card} p-5`}>
            <ol className="grid gap-4 sm:grid-cols-3">
                {stages.map((stage, index) => {
                    const isDone = index < current;
                    const isNow = index === current;

                    return (
                        <li key={stage.status} className="flex gap-3">
                            <span
                                aria-hidden
                                className={clsx(
                                    'mt-0.5 grid size-7 shrink-0 place-items-center rounded-full text-xs font-semibold',
                                    isDone && 'bg-brand text-on-brand',
                                    isNow && 'bg-gold text-brand-deep ring-4 ring-gold-tint',
                                    !isDone && !isNow && 'bg-paper text-muted ring-1 ring-line'
                                )}
                            >
                                {isDone ? <Check size={14} /> : index + 1}
                            </span>
                            <div>
                                <p className={clsx('text-sm font-semibold', !isNow && !isDone && 'text-muted')}>
                                    {stage.title}
                                    {isNow && <span className="sr-only"> (current stage)</span>}
                                </p>
                                <p className="mt-0.5 text-xs leading-relaxed text-muted">{stage.body}</p>
                            </div>
                        </li>
                    );
                })}
            </ol>

            {council.canManage && council.status !== 'completed' && (
                <div className="mt-5 flex flex-wrap items-center justify-end gap-3 border-t border-line pt-4">
                    {council.status === 'ongoing' && !council.canComplete && (
                        <p className="mr-auto text-sm text-muted">
                            You can complete the evaluation once every form is submitted.
                        </p>
                    )}
                    {moves.map((option) => (
                        <Button key={option.to} variant={option.variant} onClick={() => setMove(option)}>
                            {option.label}
                        </Button>
                    ))}
                </div>
            )}

            <Dialog
                open={move !== null}
                onClose={() => setMove(null)}
                title={move?.label ?? ''}
                description={move?.confirm}
            >
                <div className="flex justify-end gap-2">
                    <Button variant="secondary" onClick={() => setMove(null)}>
                        Cancel
                    </Button>
                    <Button onClick={confirm} disabled={busy}>
                        {move?.label}
                    </Button>
                </div>
            </Dialog>
        </section>
    );
}
