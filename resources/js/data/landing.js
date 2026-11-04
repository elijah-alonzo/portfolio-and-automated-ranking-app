export const features = [
    {
        title: 'Online evaluation',
        description:
            'Self, peer and adviser forms replace paper. Scores are weighted 10 / 25 / 65 and tallied the moment the last form comes in.',
        image: '/assets/my-eval/eval.png',
    },
    {
        title: 'Automated ranking',
        description:
            'Final scores map to Gold, Silver and Bronze tiers automatically, so councils review results instead of calculating them.',
        image: '/assets/council-eval/view.png',
    },
    {
        title: 'Award applications',
        description:
            'Students apply for leadership awards online, and the council decides from one portfolio view with rank and certificates attached.',
        image: '/assets/portfolio/apply.png',
    },
];

export const councils = [
    {
        type: 'Campus council',
        name: 'Paulinian Student Government',
        description:
            'Campus-wide initiatives, representation and leadership oversight.',
    },
    {
        type: 'Department council',
        name: 'SITE',
        description: 'School of Information Technology and Engineering.',
    },
    {
        type: 'Department council',
        name: 'SASTE',
        description: 'School of Art Sciences and Teacher Education.',
    },
    {
        type: 'Department council',
        name: 'SBAHM',
        description: 'School of Business, Accountancy, and Hospitality Management.',
    },
    {
        type: 'Department council',
        name: 'SNAHS',
        description: 'School of Nursing and Allied Health Services.',
    },
];

export const weights = [
    { label: 'Adviser', value: 65 },
    { label: 'Peer', value: 25 },
    { label: 'Self', value: 10 },
];
