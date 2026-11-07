import { Link } from '@inertiajs/react';
import { ArrowLeft } from 'lucide-react';
import AwardPanel from '@/components/sections/portfolio/AwardPanel';
import ExperienceList from '@/components/sections/portfolio/ExperienceList';
import ProfileCard from '@/components/sections/portfolio/ProfileCard';
import CertificateList from '@/components/shared/CertificateList';
import PageHeader from '@/components/shared/PageHeader';
import Seo from '@/components/shared/Seo';
import Panel from '@/components/ui/Panel';
import AppLayout from '@/layouts/AppLayout';
import type { AwardState, Certificate, Experience, Person } from '@/types/portfolio';
import { index as students } from '@/actions/App/Features/Users/Controllers/StudentController';

type PortfolioProps = {
    person: Person;
    experience: Experience[];
    certificates: Certificate[];
    /** Only on your own portfolio. */
    award: AwardState | null;
};

export default function Portfolio({ person, experience, certificates, award }: PortfolioProps) {
    const isOwn = award !== null;

    return (
        <AppLayout>
            <Seo title={isOwn ? 'My Portfolio' : person.name} description={`${person.name}'s leadership portfolio.`} />
            <PageHeader
                eyebrow={
                    !isOwn && (
                        <Link
                            href={students.url()}
                            className="inline-flex items-center gap-1 font-medium text-muted hover:text-ink"
                        >
                            <ArrowLeft size={14} aria-hidden />
                            Students
                        </Link>
                    )
                }
                title={isOwn ? 'My Portfolio' : `${person.name}'s Portfolio`}
                description="Leadership record: council terms, final ranks and certificates."
            />
            <div className="grid gap-6 lg:grid-cols-[20rem_1fr]">
                <div className="space-y-6">
                    <ProfileCard person={person} />
                    {award && <AwardPanel award={award} />}
                </div>
                <div className="space-y-6">
                    <ExperienceList experience={experience} />
                    <Panel title="Certificates" flush>
                        <CertificateList
                            certificates={certificates}
                            empty={{
                                title: 'No certificates yet',
                                body: 'Certificates issued by an adviser appear here.',
                            }}
                        />
                    </Panel>
                </div>
            </div>
        </AppLayout>
    );
}
