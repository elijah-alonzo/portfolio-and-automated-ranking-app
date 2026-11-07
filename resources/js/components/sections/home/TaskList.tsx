import { Link } from '@inertiajs/react';
import Avatar from '@/components/shared/Avatar';
import EmptyState from '@/components/shared/EmptyState';
import { FormBadge } from '@/components/shared/StatusBadge';
import Panel from '@/components/ui/Panel';
import { buttonClass } from '@/components/ui/styles';
import { evaluatorLabel } from '@/lib/evaluations';
import type { PendingTask } from '@/types/councils';
import { show as form } from '@/actions/App/Features/Evaluations/Controllers/EvaluationFormController';

/** Every form waiting on this person across the evaluations that are open right now. */
export default function TaskList({ tasks }: { tasks: PendingTask[] }) {
    return (
        <Panel title="Waiting on you" description="Forms in evaluations that are open right now." flush>
            {tasks.length === 0 ? (
                <EmptyState title="You're all caught up">
                    When an adviser opens an evaluation you are part of, your forms will show up here.
                </EmptyState>
            ) : (
                <ul className="divide-y divide-line">
                    {tasks.map((task) => (
                        <li
                            key={`${task.evaluationId}-${task.evaluateeId}-${task.type}`}
                            className="flex flex-wrap items-center gap-4 px-5 py-4"
                        >
                            <Avatar name={task.evaluatee} src={task.evaluateeAvatar} className="size-10 text-sm" />
                            <div className="min-w-0 flex-1">
                                <p className="font-medium">
                                    {evaluatorLabel[task.type]}
                                    {task.type !== 'self' && (
                                        <span className="font-normal text-muted"> for {task.evaluatee}</span>
                                    )}
                                </p>
                                <p className="truncate text-sm text-muted">
                                    {task.council}, {task.academicYear}
                                </p>
                            </div>
                            <FormBadge status={task.status} />
                            <Link
                                href={form.url({
                                    evaluation: task.evaluationId,
                                    user: task.evaluateeId,
                                    type: task.type,
                                })}
                                className={buttonClass(task.status === 'draft' ? 'secondary' : 'primary')}
                            >
                                {task.status === 'draft' ? 'Continue' : 'Start'}
                            </Link>
                        </li>
                    ))}
                </ul>
            )}
        </Panel>
    );
}
