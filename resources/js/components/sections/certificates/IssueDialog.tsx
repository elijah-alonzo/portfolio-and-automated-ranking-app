import { useForm } from '@inertiajs/react';
import type { FormEvent } from 'react';
import Button from '@/components/ui/Button';
import Dialog from '@/components/ui/Dialog';
import Field from '@/components/ui/Field';
import { control, label } from '@/components/ui/styles';
import { formatMegabytes } from '@/lib/format';
import type { StudentOption } from '@/types/councils';
import { store } from '@/actions/App/Features/Certificates/Controllers/CertificateController';

/** An adviser issues one PDF certificate to one or more students in their department. */
export default function IssueDialog({
    open,
    onClose,
    students,
    maxFileKb,
}: {
    open: boolean;
    onClose: () => void;
    students: StudentOption[];
    maxFileKb: number;
}) {
    const form = useForm<{ student_ids: number[]; name: string; date_issued: string; file: File | null }>({
        student_ids: [],
        name: '',
        date_issued: new Date().toISOString().slice(0, 10),
        file: null,
    });

    const toggle = (id: number) =>
        form.setData(
            'student_ids',
            form.data.student_ids.includes(id)
                ? form.data.student_ids.filter((s) => s !== id)
                : [...form.data.student_ids, id]
        );

    const submit = (event: FormEvent) => {
        event.preventDefault();
        form.submit(store(), {
            forceFormData: true,
            preserveScroll: true,
            onSuccess: () => {
                form.reset();
                onClose();
            },
        });
    };

    const studentError =
        form.errors.student_ids ?? Object.entries(form.errors).find(([key]) => key.startsWith('student_ids.'))?.[1];

    return (
        <Dialog
            open={open}
            onClose={onClose}
            title="Issue a certificate"
            description="Each student you pick gets the same PDF, and a notification."
        >
            <form onSubmit={submit} className="space-y-5">
                <Field label="Certificate name" error={form.errors.name}>
                    <input
                        value={form.data.name}
                        onChange={(e) => form.setData('name', e.target.value)}
                        required
                        className={control}
                    />
                </Field>
                <Field label="Date issued" error={form.errors.date_issued}>
                    <input
                        type="date"
                        value={form.data.date_issued}
                        onChange={(e) => form.setData('date_issued', e.target.value)}
                        required
                        className={control}
                    />
                </Field>
                <Field
                    label="PDF file"
                    error={form.errors.file}
                    hint={`PDF only, up to ${formatMegabytes(maxFileKb)}.`}
                >
                    <input
                        type="file"
                        accept="application/pdf"
                        onChange={(e) => form.setData('file', e.target.files?.[0] ?? null)}
                        required
                        className="block w-full text-sm file:mr-3 file:rounded-lg file:border-0 file:bg-brand-tint file:px-3 file:py-2 file:text-sm file:font-semibold file:text-brand"
                    />
                </Field>
                <fieldset>
                    <legend className={label}>Students ({form.data.student_ids.length} chosen)</legend>
                    <div className="max-h-48 space-y-1 overflow-y-auto rounded-lg p-2 ring-1 ring-gray-950/10">
                        {students.length === 0 ? (
                            <p className="p-2 text-sm text-muted">
                                Your department has no students yet. Add them on the Students page.
                            </p>
                        ) : (
                            students.map((student) => (
                                <label
                                    key={student.id}
                                    className="flex items-center gap-2 rounded-md px-2 py-1.5 text-sm hover:bg-paper"
                                >
                                    <input
                                        type="checkbox"
                                        checked={form.data.student_ids.includes(student.id)}
                                        onChange={() => toggle(student.id)}
                                        className="size-4 accent-brand"
                                    />
                                    {student.name}
                                </label>
                            ))
                        )}
                    </div>
                    {studentError && <p className="mt-1.5 text-sm text-bad">{studentError}</p>}
                </fieldset>
                <div className="flex justify-end gap-2">
                    <Button variant="secondary" onClick={onClose}>
                        Cancel
                    </Button>
                    <Button type="submit" disabled={form.processing}>
                        Issue certificate
                    </Button>
                </div>
            </form>
        </Dialog>
    );
}
