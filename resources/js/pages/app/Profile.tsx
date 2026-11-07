import DetailsForm, { type Profile as ProfileData } from '@/components/sections/profile/DetailsForm';
import PasswordForm from '@/components/sections/profile/PasswordForm';
import PageHeader from '@/components/shared/PageHeader';
import Seo from '@/components/shared/Seo';
import AppLayout from '@/layouts/AppLayout';

export default function Profile({ profile, maxAvatarKb }: { profile: ProfileData; maxAvatarKb: number }) {
    return (
        <AppLayout>
            <Seo title="Profile" description="Your details and password." />
            <PageHeader title="Profile" description="Your details, photo and password." />
            <div className="space-y-6">
                <DetailsForm profile={profile} maxAvatarKb={maxAvatarKb} />
                <PasswordForm />
            </div>
        </AppLayout>
    );
}
