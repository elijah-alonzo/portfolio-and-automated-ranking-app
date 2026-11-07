export type EvaluationStatus = 'closed' | 'ongoing' | 'completed';
export type FormStatus = 'pending' | 'draft' | 'submitted';
export type EvaluatorType = 'self' | 'peer' | 'adviser';

export type CouncilSummary = {
    id: number;
    name: string;
    code: string | null;
    academicYear: string;
    status: EvaluationStatus;
    adviser: string | null;
    adviserAvatar: string | null;
};

export type CouncilListItem = CouncilSummary & {
    officers: number;
    role: 'Adviser' | 'Officer';
};

export type CouncilDetail = CouncilSummary & {
    canManage: boolean;
    canComplete: boolean;
};

export type FormState = {
    status: FormStatus;
    /** The viewer is the one who fills this form. */
    isMine: boolean;
    canOpen: boolean;
};

export type Officer = {
    /** The position slot id. */
    id: number;
    position: string;
    recommendation: string | null;
    student: {
        id: number;
        name: string;
        avatar: string | null;
        department: string | null;
    } | null;
    peer: { id: number; name: string } | null;
    forms: Record<EvaluatorType, FormState> | null;
    score: { final: number; rank: string | null } | null;
};

export type StudentOption = {
    id: number;
    name: string;
    department?: string | null;
};

export type PendingTask = {
    evaluationId: number;
    council: string;
    academicYear: string;
    evaluateeId: number;
    evaluatee: string;
    evaluateeAvatar: string | null;
    type: EvaluatorType;
    status: FormStatus;
};

export type Criterion = { score: number; label: string };

export type Question = {
    key: string;
    number: string;
    text: string;
    criteria: Criterion[];
};

export type Domain = {
    key: string;
    title: string;
    description: string | null;
    strands: { title: string; questions: Question[] }[];
};
