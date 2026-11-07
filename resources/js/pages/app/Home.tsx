import { usePage } from '@inertiajs/react';
import CouncilShortlist from '@/components/sections/home/CouncilShortlist';
import LatestRank from '@/components/sections/home/LatestRank';
import TaskList from '@/components/sections/home/TaskList';
import PageHeader from '@/components/shared/PageHeader';
import Seo from '@/components/shared/Seo';
import AppLayout from '@/layouts/AppLayout';
import type { EvaluationStatus, PendingTask } from '@/types/councils';

type HomeProps = {
    tasks: PendingTask[];
    councils: {
        id: number;
        name: string;
        academicYear: string;
        status: EvaluationStatus;
        role: string;
    }[];
    latestRank: {
        council: string | null;
        academicYear: string;
        finalScore: number;
        rank: string | null;
    } | null;
};

export default function Home({ tasks, councils, latestRank }: HomeProps) {
    const { auth } = usePage().props;
    const count = tasks.length;

    return (
        <AppLayout>
            <Seo title="Home" description="Your open evaluation forms and councils." />
            <PageHeader
                title={`Welcome, ${auth.user?.firstName ?? ''}`}
                description={
                    count === 0
                        ? 'Nothing needs you right now.'
                        : `${count} form${count === 1 ? '' : 's'} waiting on you.`
                }
            />
            <div className="grid gap-6 lg:grid-cols-[1fr_20rem]">
                <TaskList tasks={tasks} />
                <div className="space-y-6">
                    {latestRank && <LatestRank rank={latestRank} />}
                    <CouncilShortlist councils={councils} />
                </div>
            </div>
        </AppLayout>
    );
}
