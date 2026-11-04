import { Head } from '@inertiajs/react';

import HomeAbout from '../components/sections/HomeAbout';
import HomeHero from '../components/sections/HomeHero';
import PublicLayout from '../components/shared/PublicLayout';

export default function Home({ stats }) {
    return (
        <PublicLayout>
            <Head title="Home" />
            <HomeHero stats={stats} />
            <HomeAbout />
        </PublicLayout>
    );
}
