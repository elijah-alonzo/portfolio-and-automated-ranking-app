import type { EvaluatorType } from '@/types/councils';

export const evaluatorLabel: Record<EvaluatorType, string> = {
    self: 'Self evaluation',
    peer: 'Peer evaluation',
    adviser: 'Adviser evaluation',
};

/** Each form's share of the final score. */
export const evaluatorWeight: Record<EvaluatorType, string> = {
    adviser: '65%',
    peer: '25%',
    self: '10%',
};
