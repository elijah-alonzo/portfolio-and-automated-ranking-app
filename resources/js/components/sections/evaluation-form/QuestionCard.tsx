import clsx from 'clsx';
import type { Question } from '@/types/councils';

/** One rubric question: its text and the four ratings, 3 down to 0, as choosable cards. */
export default function QuestionCard({
    question,
    value,
    disabled,
    onChange,
}: {
    question: Question;
    value: number | undefined;
    disabled: boolean;
    onChange: (score: number) => void;
}) {
    return (
        <fieldset className="py-5 first:pt-0 last:pb-0">
            <legend className="flex gap-3 text-sm leading-relaxed">
                <span className="shrink-0 font-semibold text-brand tabular-nums">{question.number}</span>
                <span>{question.text}</span>
            </legend>
            <div className="mt-3 grid gap-2 sm:grid-cols-2 xl:grid-cols-4">
                {question.criteria.map((criterion) => {
                    const checked = value === criterion.score;

                    return (
                        <label
                            key={criterion.score}
                            className={clsx(
                                'relative flex gap-3 rounded-lg p-3 text-sm ring-1 transition',
                                checked ? 'bg-brand-tint ring-2 ring-brand' : 'bg-surface ring-gray-950/10',
                                disabled ? 'cursor-default' : 'cursor-pointer hover:ring-brand/50',
                                disabled && !checked && 'opacity-60'
                            )}
                        >
                            <input
                                type="radio"
                                name={question.key}
                                value={criterion.score}
                                checked={checked}
                                disabled={disabled}
                                onChange={() => onChange(criterion.score)}
                                className="sr-only"
                            />
                            <span
                                aria-hidden
                                className={clsx(
                                    'grid size-7 shrink-0 place-items-center rounded-md text-sm font-bold tabular-nums',
                                    checked ? 'bg-brand text-on-brand' : 'bg-paper text-muted'
                                )}
                            >
                                {criterion.score}
                            </span>
                            <span className="leading-snug">
                                <span className="sr-only">{criterion.score} points: </span>
                                {criterion.label}
                            </span>
                        </label>
                    );
                })}
            </div>
        </fieldset>
    );
}
