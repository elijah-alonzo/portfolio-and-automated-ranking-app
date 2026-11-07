import { useForm } from '@inertiajs/react';
import type { FormEvent } from 'react';
import Button from '@/components/ui/Button';
import Field from '@/components/ui/Field';
import Panel from '@/components/ui/Panel';
import { control } from '@/components/ui/styles';
import { password } from '@/actions/App/Features/Users/Controllers/ProfileController';

export default function PasswordForm() {
    const form = useForm({ current_password: '', password: '', password_confirmation: '' });

    const submit = (event: FormEvent) => {
        event.preventDefault();
        form.submit(password(), { preserveScroll: true, onSuccess: () => form.reset() });
    };

    return (
        <Panel title="Change Password" description="Use a long password you don't use anywhere else.">
            <form onSubmit={submit} className="grid gap-4 md:grid-cols-3">
                <Field label="Current Password" error={form.errors.current_password}>
                    <input
                        type="password"
                        autoComplete="current-password"
                        value={form.data.current_password}
                        onChange={(e) => form.setData('current_password', e.target.value)}
                        required
                        className={control}
                    />
                </Field>
                <Field label="New Password" error={form.errors.password}>
                    <input
                        type="password"
                        autoComplete="new-password"
                        value={form.data.password}
                        onChange={(e) => form.setData('password', e.target.value)}
                        required
                        className={control}
                    />
                </Field>
                <Field label="Confirm New Password">
                    <input
                        type="password"
                        autoComplete="new-password"
                        value={form.data.password_confirmation}
                        onChange={(e) => form.setData('password_confirmation', e.target.value)}
                        required
                        className={control}
                    />
                </Field>
                <div className="flex justify-end md:col-span-3">
                    <Button type="submit" disabled={form.processing}>
                        Change Password
                    </Button>
                </div>
            </form>
        </Panel>
    );
}
