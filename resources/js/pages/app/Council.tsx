import { Link } from '@inertiajs/react';
import { ArrowLeft } from 'lucide-react';
import OfficerBoard from '@/components/sections/council/OfficerBoard';
import StageBar from '@/components/sections/council/StageBar';
import Avatar from '@/components/shared/Avatar';
import PageHeader from '@/components/shared/PageHeader';
import Seo from '@/components/shared/Seo';
import { EvaluationBadge } from '@/components/shared/StatusBadge';
import AppLayout from '@/layouts/AppLayout';
import type { CouncilDetail, Officer, StudentOption } from '@/types/councils';
import { index } from '@/actions/App/Features/Evaluations/Controllers/CouncilController';

type CouncilProps = {
    council: CouncilDetail;
    officers: Officer[];
    students: StudentOption[];
};

export default function Council({ council, officers, students }: CouncilProps) {
    return (
        <AppLayout>
            <Seo
                title={`${council.name} ${council.academicYear}`}
                description={`The ${council.name} evaluation for ${council.academicYear}.`}
            />
            <PageHeader
                eyebrow={
                    <Link
                        href={index.url()}
                        className="inline-flex items-center gap-1 font-medium text-muted hover:text-ink"
                    >
                        <ArrowLeft size={14} aria-hidden />
                        My Councils
                    </Link>
                }
                title={council.name}
                description={
                    <span className="inline-flex flex-wrap items-center gap-x-2 gap-y-1">
                        {council.academicYear}
                        {council.adviser && (
                            <>
                                <span aria-hidden>·</span>
                                <Avatar
                                    name={council.adviser}
                                    src={council.adviserAvatar}
                                    className="size-5 text-[0.625rem]"
                                />
                                Adviser: {council.adviser}
                            </>
                        )}
                    </span>
                }
                actions={<EvaluationBadge status={council.status} />}
            />
            <div className="space-y-6">
                <StageBar council={council} />
                <OfficerBoard council={council} officers={officers} students={students} />
            </div>
        </AppLayout>
    );
}
