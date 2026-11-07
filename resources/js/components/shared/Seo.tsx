import { Head, usePage } from '@inertiajs/react';

type SeoProps = {
    title?: string;
    description: string;
    /** Only public pages (sign-in) should be indexed; the signed-in app is private. */
    index?: boolean;
};

export default function Seo({ title, description, index = false }: SeoProps) {
    const page = usePage();
    const { appName, appUrl } = page.props;
    const base = appUrl.replace(/\/$/, '');
    const fullTitle = title ? `${title} · ${appName}` : appName;
    const canonical = `${base}${page.url.split('?')[0]}`;
    const image = `${base}/logo-01.png`;

    return (
        <Head title={fullTitle}>
            <meta name="description" content={description} />
            <meta name="robots" content={index ? 'index, follow' : 'noindex, nofollow'} />
            <link rel="canonical" href={canonical} />
            <meta property="og:site_name" content={appName} />
            <meta property="og:title" content={fullTitle} />
            <meta property="og:description" content={description} />
            <meta property="og:type" content="website" />
            <meta property="og:url" content={canonical} />
            <meta property="og:image" content={image} />
            <meta name="twitter:card" content="summary" />
            <meta name="twitter:image" content={image} />
        </Head>
    );
}
