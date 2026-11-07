import { router } from '@inertiajs/react';
import { Bell, UserMinus, UserPlus, Users } from 'lucide-react';
import { useState } from 'react';
import FormPills from '@/components/sections/council/FormPills';
import PickPersonDialog from '@/components/sections/council/PickPersonDialog';
import Avatar from '@/components/shared/Avatar';
import EmptyState from '@/components/shared/EmptyState';
import RankBadge from '@/components/shared/RankBadge';
import Button from '@/components/ui/Button';
import Dialog from '@/components/ui/Dialog';
import Panel from '@/components/ui/Panel';
import type { CouncilDetail, Officer, StudentOption } from '@/types/councils';
import { assign, peer, remind, remove } from '@/actions/App/Features/Evaluations/Controllers/OfficerController';

type Picker = { kind: 'student' | 'peer' | 'remove'; officer: Officer } | null;

/**
 * Every position in the council: who holds it, who writes their peer form, where their three forms stand,
 * and their final score once it exists. While the evaluation is closed the adviser fills positions and pairs peers here.
 */
export default function OfficerBoard({
    council,
    officers,
    students,
}: {
    council: CouncilDetail;
    officers: Officer[];
    students: StudentOption[];
}) {
    const [picker, setPicker] = useState<Picker>(null);
    const canArrange = council.canManage && council.status === 'closed';
    const canRemind = council.canManage && council.status === 'ongoing';
    const close = () => setPicker(null);
    const options = { preserveScroll: true, onSuccess: close };

    const eligibleFor = (officer: Officer) => {
        const taken = new Set(officers.filter((o) => o.id !== officer.id && o.student).map((o) => o.student!.id));
        return students.filter((student) => !taken.has(student.id));
    };

    const peersFor = (officer: Officer): StudentOption[] =>
        officers
            .filter((o) => o.student && o.student.id !== officer.student?.id)
            .map((o) => ({ id: o.student!.id, name: o.student!.name, department: o.position }));

    const needsReminder = (officer: Officer) =>
        officer.forms !== null &&
        (officer.forms.self.status !== 'submitted' ||
            (officer.peer !== null && officer.forms.peer.status !== 'submitted'));

    const filled = officers.filter((o) => o.student).length;

    return (
        <Panel
            title="Officers"
            description={
                canArrange
                    ? `${filled} of ${officers.length} positions filled. Fill each position, then pick a peer evaluator for every officer.`
                    : 'Open a form to read it. Forms you fill are ringed in gold.'
            }
            flush
        >
            {officers.length === 0 ? (
                <EmptyState title="No officers yet">
                    {council.canManage
                        ? 'This council has no positions set up. The admin adds positions to the council.'
                        : 'The adviser has not added officers to this evaluation yet.'}
                </EmptyState>
            ) : (
                <ul className="divide-y divide-line">
                    {officers.map((officer) => (
                        <li
                            key={officer.id}
                            className="grid gap-x-6 gap-y-3 px-5 py-4 lg:grid-cols-[11rem_1fr_auto] lg:items-center"
                        >
                            <div>
                                <p className="text-sm font-semibold">{officer.position}</p>
                                {officer.recommendation && (
                                    <p className="text-xs text-muted">{officer.recommendation}</p>
                                )}
                            </div>

                            {officer.student ? (
                                <div className="flex min-w-0 flex-wrap items-center gap-x-6 gap-y-3">
                                    <div className="flex min-w-0 items-center gap-3 sm:w-56">
                                        <Avatar name={officer.student.name} src={officer.student.avatar} />
                                        <div className="min-w-0">
                                            <p className="truncate text-sm font-medium">{officer.student.name}</p>
                                            <p className="truncate text-xs text-muted">
                                                Peer:{' '}
                                                {officer.peer ? (
                                                    officer.peer.name
                                                ) : (
                                                    <span className={canArrange ? 'font-medium text-wait' : undefined}>
                                                        not assigned
                                                    </span>
                                                )}
                                            </p>
                                        </div>
                                    </div>
                                    {officer.forms && council.status !== 'closed' && (
                                        <FormPills
                                            councilId={council.id}
                                            officerId={officer.student.id}
                                            forms={officer.forms}
                                        />
                                    )}
                                    {officer.score && (
                                        <RankBadge rank={officer.score.rank} score={officer.score.final} />
                                    )}
                                </div>
                            ) : (
                                <p className="text-sm text-muted italic">Empty position</p>
                            )}

                            {(canArrange || (canRemind && officer.student)) && (
                                <div className="flex flex-wrap gap-2 lg:justify-end">
                                    {canArrange && (
                                        <Button
                                            variant="secondary"
                                            onClick={() => setPicker({ kind: 'student', officer })}
                                        >
                                            <UserPlus size={15} aria-hidden />
                                            {officer.student ? 'Change' : 'Assign'}
                                        </Button>
                                    )}
                                    {canArrange && officer.student && (
                                        <>
                                            <Button
                                                variant="secondary"
                                                onClick={() => setPicker({ kind: 'peer', officer })}
                                            >
                                                <Users size={15} aria-hidden />
                                                Peer
                                            </Button>
                                            <Button
                                                variant="quiet"
                                                aria-label={`Remove ${officer.student.name}`}
                                                onClick={() => setPicker({ kind: 'remove', officer })}
                                            >
                                                <UserMinus size={15} aria-hidden />
                                            </Button>
                                        </>
                                    )}
                                    {canRemind && officer.student && needsReminder(officer) && (
                                        <Button
                                            variant="secondary"
                                            onClick={() =>
                                                router.post(
                                                    remind.url({
                                                        evaluation: council.id,
                                                        officer: officer.student!.id,
                                                    }),
                                                    {},
                                                    { preserveScroll: true }
                                                )
                                            }
                                        >
                                            <Bell size={15} aria-hidden />
                                            Remind
                                        </Button>
                                    )}
                                </div>
                            )}
                        </li>
                    ))}
                </ul>
            )}

            {picker?.kind === 'student' && (
                <PickPersonDialog
                    open
                    onClose={close}
                    title={`${picker.officer.student ? 'Change' : 'Assign'} ${picker.officer.position}`}
                    description="Students from this council's departments who don't already hold a position here."
                    label="Student"
                    options={eligibleFor(picker.officer)}
                    initial={picker.officer.student?.id}
                    submitLabel="Assign"
                    onPick={(id, done) =>
                        router.put(
                            assign.url({ evaluation: council.id, positionSlot: picker.officer.id }),
                            { student_id: id },
                            { ...options, onFinish: done }
                        )
                    }
                />
            )}

            {picker?.kind === 'peer' && picker.officer.student && (
                <PickPersonDialog
                    open
                    onClose={close}
                    title={`Peer evaluator for ${picker.officer.student.name}`}
                    description="A fellow officer in this council who will write their peer form."
                    label="Peer evaluator"
                    options={peersFor(picker.officer)}
                    initial={picker.officer.peer?.id}
                    submitLabel="Save peer"
                    onPick={(id, done) =>
                        router.put(
                            peer.url({ evaluation: council.id, officer: picker.officer.student!.id }),
                            { evaluator_id: id },
                            { ...options, onFinish: done }
                        )
                    }
                />
            )}

            <Dialog
                open={picker?.kind === 'remove'}
                onClose={close}
                title={`Remove ${picker?.officer.student?.name ?? 'officer'}?`}
                description="The position becomes empty and their peer pairings in this evaluation are cleared."
            >
                <div className="flex justify-end gap-2">
                    <Button variant="secondary" onClick={close}>
                        Cancel
                    </Button>
                    <Button
                        variant="danger"
                        onClick={() =>
                            picker &&
                            router.delete(
                                remove.url({ evaluation: council.id, positionSlot: picker.officer.id }),
                                options
                            )
                        }
                    >
                        Remove
                    </Button>
                </div>
            </Dialog>
        </Panel>
    );
}
