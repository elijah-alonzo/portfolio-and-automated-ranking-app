import { useForm } from '@inertiajs/react';
import type { FormEvent } from 'react';
import Button from '@/components/ui/Button';
import Dialog from '@/components/ui/Dialog';
import Field from '@/components/ui/Field';
import { control } from '@/components/ui/styles';
import { store, update } from '@/actions/App/Features/Users/Controllers/StudentController';

export type StudentRow = {
    id: number;
    name: string;
    email: string;
    contactNumber: string | null;
    isActive: boolean;
    avatar: string | null;
};

/** Add a student to the adviser's department, or edit one. A password is only required for a new account. */
export default function StudentDialog({ student, onClose }: { student: StudentRow | 'new'; onClose: () => void }) {
    const isNew = student === 'new';
    const form = useForm({
        name: isNew ? '' : student.name,
        email: isNew ? '' : student.email,
        contact_number: isNew ? '' : (student.contactNumber ?? ''),
        is_active: isNew ? true : student.isActive,
        password: '',
        password_confirmation: '',
    });

    const submit = (event: FormEvent) => {
        event.preventDefault();
        form.submit(isNew ? store() : update(student.id), { preserveScroll: true, onSuccess: onClose });
    };

    return (
        <Dialog
            open
            onClose={onClose}
            title={isNew ? 'Add a student' : `Edit ${student.name}`}
            description={
                isNew
                    ? 'They join your department and can sign in with this email and password.'
                    : 'Leave the password empty to keep the current one.'
            }
        >
            <form onSubmit={submit} className="space-y-4">
                <Field label="Full name" error={form.errors.name}>
                    <input
                        value={form.data.name}
                        onChange={(e) => form.setData('name', e.target.value)}
                        required
                        className={control}
                    />
                </Field>
                <Field label="Email" error={form.errors.email}>
                    <input
                        type="email"
                        value={form.data.email}
                        onChange={(e) => form.setData('email', e.target.value)}
                        required
                        className={control}
                    />
                </Field>
                <Field label="Contact number" error={form.errors.contact_number}>
                    <input
                        value={form.data.contact_number}
                        onChange={(e) => form.setData('contact_number', e.target.value)}
                        className={control}
                    />
                </Field>
                <div className="grid gap-4 sm:grid-cols-2">
                    <Field label={isNew ? 'Password' : 'New password'} error={form.errors.password}>
                        <input
                            type="password"
                            autoComplete="new-password"
                            value={form.data.password}
                            onChange={(e) => form.setData('password', e.target.value)}
                            required={isNew}
                            className={control}
                        />
                    </Field>
                    <Field label="Confirm password">
                        <input
                            type="password"
                            autoComplete="new-password"
                            value={form.data.password_confirmation}
                            onChange={(e) => form.setData('password_confirmation', e.target.value)}
                            required={isNew}
                            className={control}
                        />
                    </Field>
                </div>
                <label className="flex items-center gap-2 text-sm">
                    <input
                        type="checkbox"
                        checked={form.data.is_active}
                        onChange={(e) => form.setData('is_active', e.target.checked)}
                        className="size-4 accent-brand"
                    />
                    Active account
                </label>
                <div className="flex justify-end gap-2 pt-1">
                    <Button variant="secondary" onClick={onClose}>
                        Cancel
                    </Button>
                    <Button type="submit" disabled={form.processing}>
                        {isNew ? 'Add student' : 'Save changes'}
                    </Button>
                </div>
            </form>
        </Dialog>
    );
}
