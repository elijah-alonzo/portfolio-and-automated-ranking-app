import { useForm } from '@inertiajs/react';
import { Award } from 'lucide-react';
import { useState, type FormEvent } from 'react';
import Button from '@/components/ui/Button';
import Dialog from '@/components/ui/Dialog';
import Field from '@/components/ui/Field';
import { card, control } from '@/components/ui/styles';
import type { AwardState } from '@/types/portfolio';
import { store } from '@/actions/App/Features/Awards/Controllers/AwardApplicationController';

const statusText: Record<string, string> = {
    pending: 'Your application is with the admin for review.',
    accepted: 'Your application was accepted. Congratulations.',
};

/** A graduating officer's leadership award application: apply, or see where it stands. */
export default function AwardPanel({ award }: { award: AwardState }) {
    const [open, setOpen] = useState(false);
    const form = useForm({ award_type_id: '', graduating: false });

    const submit = (event: FormEvent) => {
        event.preventDefault();
        form.submit(store(), { preserveScroll: true, onSuccess: () => setOpen(false) });
    };

    return (
        <section className={`${card} p-5`}>
            <div className="flex items-start gap-3">
                <span className="grid size-10 shrink-0 place-items-center rounded-lg bg-gold text-brand-deep">
                    <Award size={19} aria-hidden />
                </span>
                <div className="min-w-0">
                    <h2 className="font-semibold">Leadership award</h2>
                    {award.application ? (
                        <p className="mt-1 text-sm text-muted">
                            <span className="font-medium text-ink">{award.application.awardType}</span>:{' '}
                            {statusText[award.application.status] ?? award.application.status}
                        </p>
                    ) : (
                        <p className="mt-1 text-sm text-muted">
                            {award.blockedReason ??
                                'Graduating officers can apply once every evaluation they are part of is completed.'}
                        </p>
                    )}
                </div>
            </div>

            {!award.application && !award.blockedReason && (
                <Button className="mt-4 w-full" onClick={() => setOpen(true)}>
                    Apply for an award
                </Button>
            )}

            <Dialog
                open={open}
                onClose={() => setOpen(false)}
                title="Apply for a leadership award"
                description="Choose the award, then confirm you are graduating. The admin reviews every application."
            >
                <form onSubmit={submit} className="space-y-5">
                    <Field label="Award" error={form.errors.award_type_id}>
                        <select
                            value={form.data.award_type_id}
                            onChange={(e) => form.setData('award_type_id', e.target.value)}
                            required
                            className={control}
                        >
                            <option value="" disabled>
                                Choose an award
                            </option>
                            {award.awardTypes.map((type) => (
                                <option key={type.id} value={type.id}>
                                    {type.name}
                                </option>
                            ))}
                        </select>
                    </Field>
                    <label className="flex items-start gap-2 text-sm">
                        <input
                            type="checkbox"
                            checked={form.data.graduating}
                            onChange={(e) => form.setData('graduating', e.target.checked)}
                            className="mt-0.5 size-4 accent-brand"
                        />
                        I confirm that I am graduating this year.
                    </label>
                    {form.errors.graduating && <p className="text-sm text-bad">{form.errors.graduating}</p>}
                    <div className="flex justify-end gap-2">
                        <Button variant="secondary" onClick={() => setOpen(false)}>
                            Cancel
                        </Button>
                        <Button type="submit" disabled={form.processing}>
                            Send application
                        </Button>
                    </div>
                </form>
            </Dialog>
        </section>
    );
}
