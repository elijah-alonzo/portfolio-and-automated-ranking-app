import { useForm } from '@inertiajs/react';
import { useState, type FormEvent } from 'react';
import Avatar from '@/components/shared/Avatar';
import Button from '@/components/ui/Button';
import Field from '@/components/ui/Field';
import Panel from '@/components/ui/Panel';
import { control } from '@/components/ui/styles';
import { formatMegabytes } from '@/lib/format';
import { update } from '@/actions/App/Features/Users/Controllers/ProfileController';

export type Profile = {
    name: string;
    email: string;
    contactNumber: string | null;
    bio: string | null;
    avatar: string | null;
    role: string;
    department: string | null;
};

export default function DetailsForm({ profile, maxAvatarKb }: { profile: Profile; maxAvatarKb: number }) {
    const [preview, setPreview] = useState<string | null>(profile.avatar);
    const form = useForm<{
        name: string;
        email: string;
        contact_number: string;
        bio: string;
        avatar: File | null;
        remove_avatar: boolean;
    }>({
        name: profile.name,
        email: profile.email,
        contact_number: profile.contactNumber ?? '',
        bio: profile.bio ?? '',
        avatar: null,
        remove_avatar: false,
    });

    const submit = (event: FormEvent) => {
        event.preventDefault();
        form.submit(update(), {
            forceFormData: true,
            preserveScroll: true,
            onSuccess: () => form.setData('avatar', null),
        });
    };

    return (
        <Panel title="Profile Details" description="Shown on your portfolio and to your adviser.">
            <form onSubmit={submit} className="grid gap-6 md:grid-cols-[10rem_1fr]">
                <div className="flex flex-col items-center gap-3 text-center">
                    <Avatar name={form.data.name || profile.name} src={preview} className="size-28 text-3xl" />
                    <label className="cursor-pointer text-sm font-medium text-brand hover:underline">
                        Change Photo
                        <input
                            type="file"
                            accept="image/*"
                            className="sr-only"
                            onChange={(e) => {
                                const file = e.target.files?.[0] ?? null;
                                form.setData((data) => ({ ...data, avatar: file, remove_avatar: false }));
                                setPreview(file ? URL.createObjectURL(file) : profile.avatar);
                            }}
                        />
                    </label>
                    {preview && (
                        <button
                            type="button"
                            className="text-sm text-muted hover:text-bad"
                            onClick={() => {
                                form.setData((data) => ({ ...data, avatar: null, remove_avatar: true }));
                                setPreview(null);
                            }}
                        >
                            Remove Photo
                        </button>
                    )}
                    <p className="text-xs text-muted">Up to {formatMegabytes(maxAvatarKb)}.</p>
                    {form.errors.avatar && <p className="text-xs text-bad">{form.errors.avatar}</p>}
                    <dl className="mt-2 w-full space-y-1 border-t border-line pt-3 text-xs">
                        <div>
                            <dt className="text-muted">Role</dt>
                            <dd className="font-medium">{profile.role}</dd>
                        </div>
                        {profile.department && (
                            <div>
                                <dt className="text-muted">Department</dt>
                                <dd className="font-medium">{profile.department}</dd>
                            </div>
                        )}
                    </dl>
                </div>
                <div className="space-y-4">
                    <Field label="Full Name" error={form.errors.name}>
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
                    <Field label="Contact Number" error={form.errors.contact_number}>
                        <input
                            value={form.data.contact_number}
                            onChange={(e) => form.setData('contact_number', e.target.value)}
                            className={control}
                        />
                    </Field>
                    <Field
                        label="Bio"
                        error={form.errors.bio}
                        hint="A few lines about your leadership, shown on your portfolio."
                    >
                        <textarea
                            rows={4}
                            value={form.data.bio}
                            onChange={(e) => form.setData('bio', e.target.value)}
                            className={control}
                        />
                    </Field>
                    <div className="flex justify-end">
                        <Button type="submit" disabled={form.processing}>
                            Save Details
                        </Button>
                    </div>
                </div>
            </form>
        </Panel>
    );
}
