import { Link, router } from '@inertiajs/react';
import { ArrowLeft, Download } from 'lucide-react';
import { useState } from 'react';
import QuestionCard from '@/components/sections/evaluation-form/QuestionCard';
import ServiceNote from '@/components/sections/evaluation-form/ServiceNote';
import Avatar from '@/components/shared/Avatar';
import PageHeader from '@/components/shared/PageHeader';
import Seo from '@/components/shared/Seo';
import { FormBadge } from '@/components/shared/StatusBadge';
import Button from '@/components/ui/Button';
import Dialog from '@/components/ui/Dialog';
import Panel from '@/components/ui/Panel';
import { buttonClass, card } from '@/components/ui/styles';
import { evaluatorLabel } from '@/lib/evaluations';
import AppLayout from '@/layouts/AppLayout';
import type { Domain, EvaluatorType, FormStatus } from '@/types/councils';
import { show as council } from '@/actions/App/Features/Evaluations/Controllers/CouncilController';
import { store } from '@/actions/App/Features/Evaluations/Controllers/EvaluationFormController';
import { exportMethod } from '@/actions/App/Features/Evaluations/Controllers/EvaluationExportController';

type EvaluationFormProps = {
    council: { id: number; name: string; academicYear: string };
    evaluatee: { id: number; name: string; avatar: string | null; position: string | null };
    type: EvaluatorType;
    weight: number;
    status: FormStatus;
    isEditable: boolean;
    canExport: boolean;
    domains: Domain[];
    answers: Record<string, number>;
    lengthOfService: { key: string; years: number; score: number; awardType: string | null } | null;
};

export default function EvaluationForm(props: EvaluationFormProps) {
    const { council: c, evaluatee, type, weight, status, isEditable, canExport, domains, lengthOfService } = props;
    const [answers, setAnswers] = useState<Record<string, number>>(props.answers);
    const [confirming, setConfirming] = useState(false);
    const [busy, setBusy] = useState(false);

    const questions = domains.flatMap((d) => d.strands.flatMap((s) => s.questions));
    const rated = questions.filter((q) => q.key !== lengthOfService?.key);
    const answered = rated.filter((q) => answers[q.key] !== undefined).length;
    const isComplete = answered === rated.length;

    const save = (action: 'draft' | 'submitted') =>
        router.post(
            store.url({ evaluation: c.id, user: evaluatee.id, type }),
            { answers, submission_action: action },
            {
                preserveScroll: true,
                onStart: () => setBusy(true),
                onFinish: () => {
                    setBusy(false);
                    setConfirming(false);
                },
            }
        );

    const title = type === 'self' ? 'Self evaluation' : `${evaluatorLabel[type]} for ${evaluatee.name}`;
    const exportUrl = (format: 'csv' | 'pdf') =>
        exportMethod.url({ evaluation: c.id, user: evaluatee.id, type, format });

    return (
        <AppLayout>
            <Seo title={title} description={`${title}, ${c.name} ${c.academicYear}.`} />
            <PageHeader
                eyebrow={
                    <Link
                        href={council.url(c.id)}
                        className="inline-flex items-center gap-1 font-medium text-muted hover:text-ink"
                    >
                        <ArrowLeft size={14} aria-hidden />
                        {c.name} {c.academicYear}
                    </Link>
                }
                title={title}
                description={`Counts for ${Math.round(weight * 100)}% of ${type === 'self' ? 'your' : `${evaluatee.name}'s`} final score. Rate each statement from 3 (best) to 0.`}
                actions={
                    <>
                        <FormBadge status={status} />
                        {canExport && (
                            <>
                                <a href={exportUrl('pdf')} className={buttonClass('secondary')}>
                                    <Download size={15} aria-hidden />
                                    PDF
                                </a>
                                <a href={exportUrl('csv')} className={buttonClass('secondary')}>
                                    <Download size={15} aria-hidden />
                                    CSV
                                </a>
                            </>
                        )}
                    </>
                }
            />

            <div className={`${card} mb-6 flex items-center gap-3 p-4`}>
                <Avatar name={evaluatee.name} src={evaluatee.avatar} />
                <div>
                    <p className="text-sm font-medium">{evaluatee.name}</p>
                    <p className="text-xs text-muted">{evaluatee.position ?? 'Officer'}</p>
                </div>
                {!isEditable && (
                    <p className="ml-auto text-sm text-muted">
                        {status === 'submitted' ? 'Submitted. This form is read-only.' : 'Read-only.'}
                    </p>
                )}
            </div>

            <div className="space-y-6">
                {domains.map((domain) => (
                    <Panel key={domain.key} title={domain.title} description={domain.description}>
                        <div className="space-y-8">
                            {domain.strands.map((strand) => (
                                <div key={strand.title}>
                                    {domain.strands.length > 1 || strand.title !== domain.title ? (
                                        <h3 className="mb-4 text-sm font-semibold text-brand-strong">{strand.title}</h3>
                                    ) : null}
                                    <div className="divide-y divide-line">
                                        {strand.questions.map((question) =>
                                            question.key === lengthOfService?.key ? (
                                                <ServiceNote
                                                    key={question.key}
                                                    years={lengthOfService.years}
                                                    score={lengthOfService.score}
                                                    awardType={lengthOfService.awardType}
                                                />
                                            ) : (
                                                <QuestionCard
                                                    key={question.key}
                                                    question={question}
                                                    value={answers[question.key]}
                                                    disabled={!isEditable}
                                                    onChange={(score) =>
                                                        setAnswers((current) => ({ ...current, [question.key]: score }))
                                                    }
                                                />
                                            )
                                        )}
                                    </div>
                                </div>
                            ))}
                        </div>
                    </Panel>
                ))}
            </div>

            {isEditable && (
                <div className="sticky bottom-4 z-10 mt-8">
                    <div className={`${card} flex flex-wrap items-center gap-4 p-4 shadow-lg`}>
                        <div className="min-w-40 flex-1">
                            <p className="text-sm font-medium tabular-nums">
                                {answered} of {rated.length} answered
                            </p>
                            <div className="mt-1.5 h-1.5 overflow-hidden rounded-full bg-paper">
                                <div
                                    className="h-full rounded-full bg-brand transition-all"
                                    style={{ width: `${(answered / Math.max(rated.length, 1)) * 100}%` }}
                                />
                            </div>
                        </div>
                        <Button variant="secondary" disabled={busy} onClick={() => save('draft')}>
                            Save draft
                        </Button>
                        <Button disabled={busy || !isComplete} onClick={() => setConfirming(true)}>
                            Submit
                        </Button>
                    </div>
                </div>
            )}

            <Dialog
                open={confirming}
                onClose={() => setConfirming(false)}
                title="Submit this form?"
                description="Once submitted, the form is final and counts toward the score. You can't edit it afterwards."
            >
                <div className="flex justify-end gap-2">
                    <Button variant="secondary" onClick={() => setConfirming(false)}>
                        Keep editing
                    </Button>
                    <Button disabled={busy} onClick={() => save('submitted')}>
                        Submit
                    </Button>
                </div>
            </Dialog>
        </AppLayout>
    );
}
