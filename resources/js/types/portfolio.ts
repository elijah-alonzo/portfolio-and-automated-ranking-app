import type { EvaluationStatus } from '@/types/councils';

export type Person = {
    id: number;
    name: string;
    email: string;
    contactNumber: string | null;
    bio: string | null;
    avatar: string | null;
    department: string | null;
};

export type Experience = {
    id: number;
    council: string;
    academicYear: string;
    position: string | null;
    status: EvaluationStatus;
    finalScore: number | null;
    rank: string | null;
};

export type Certificate = {
    id: number;
    name: string;
    student?: string | null;
    studentAvatar?: string | null;
    dateIssued: string | null;
    url: string | null;
};

export type AwardType = { id: number; name: string; description: string | null };

export type AwardState = {
    application: { status: string; awardType: string | null } | null;
    blockedReason: string | null;
    awardTypes: AwardType[];
};
