import { useForm } from '@inertiajs/react';
import type { FormEvent } from 'react';
import GoogleButton from '@/components/sections/login/GoogleButton';
import Field from '@/components/ui/Field';
import { buttonClass, control } from '@/components/ui/styles';
import { store } from '@/actions/App/Features/Users/Controllers/SessionController';

export default function SignInForm() {
    const form = useForm({ email: '', password: '', remember: false });

    const submit = (event: FormEvent) => {
        event.preventDefault();
        form.submit(store(), { onFinish: () => form.reset('password') });
    };

    return (
        <form onSubmit={submit} className="w-full max-w-sm">
            <h1 className="text-center font-display font-medium text-[2.25rem] leading-tight text-brand-deep">
                Sign in
            </h1>
            <p className="mt-2 mb-8 text-center text-sm leading-relaxed text-muted">
                Use your PSG account to open your portfolio, evaluations and rankings.
            </p>

            <div className="space-y-4">
                <Field label="Email" error={form.errors.email}>
                    <input
                        type="email"
                        name="email"
                        autoComplete="username"
                        required
                        autoFocus
                        value={form.data.email}
                        onChange={(e) => form.setData('email', e.target.value)}
                        aria-invalid={form.errors.email ? true : undefined}
                        className={control}
                    />
                </Field>
                <Field label="Password" error={form.errors.password}>
                    <input
                        type="password"
                        name="password"
                        autoComplete="current-password"
                        required
                        value={form.data.password}
                        onChange={(e) => form.setData('password', e.target.value)}
                        aria-invalid={form.errors.password ? true : undefined}
                        className={control}
                    />
                </Field>
                <label className="flex items-center gap-2 text-sm text-muted">
                    <input
                        type="checkbox"
                        checked={form.data.remember}
                        onChange={(e) => form.setData('remember', e.target.checked)}
                        className="size-4 accent-brand"
                    />
                    Keep me signed in
                </label>
            </div>

            <button type="submit" disabled={form.processing} className={`${buttonClass('primary')} mt-6 w-full py-2.5`}>
                {form.processing ? 'Signing in…' : 'Sign in'}
            </button>

            <div className="my-6 flex items-center gap-3 text-xs text-muted">
                <span className="h-px flex-1 bg-line" />
                or
                <span className="h-px flex-1 bg-line" />
            </div>

            <GoogleButton />
        </form>
    );
}
