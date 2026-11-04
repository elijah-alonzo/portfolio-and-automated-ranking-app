import { Head } from '@inertiajs/react';

import HomeCouncils from '../components/sections/HomeCouncils';
import HomeFeatures from '../components/sections/HomeFeatures';
import HomeHero from '../components/sections/HomeHero';
import HomeRanking from '../components/sections/HomeRanking';
import PublicLayout from '../components/shared/PublicLayout';

export default function Home({ stats }) {
    return (
        <PublicLayout>
            <Head title="Leadership evaluation and ranking" />
            <HomeHero stats={stats} />
            <HomeFeatures />
            <HomeRanking />
            <HomeCouncils />
        </PublicLayout>
    );
}
