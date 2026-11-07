import { useState, type FormEvent } from 'react';
import Button from '@/components/ui/Button';
import Dialog from '@/components/ui/Dialog';
import Field from '@/components/ui/Field';
import { control } from '@/components/ui/styles';
import type { StudentOption } from '@/types/councils';

/** Choose one person from a list: used to fill a position and to pick a peer evaluator. */
export default function PickPersonDialog({
    open,
    onClose,
    title,
    description,
    label,
    options,
    initial,
    submitLabel,
    onPick,
}: {
    open: boolean;
    onClose: () => void;
    title: string;
    description: string;
    label: string;
    options: StudentOption[];
    initial?: number | null;
    submitLabel: string;
    onPick: (id: number, done: () => void) => void;
}) {
    const [chosen, setChosen] = useState<string>(initial ? String(initial) : '');
    const [busy, setBusy] = useState(false);

    const submit = (event: FormEvent) => {
        event.preventDefault();
        if (!chosen) return;
        setBusy(true);
        onPick(Number(chosen), () => setBusy(false));
    };

    return (
        <Dialog open={open} onClose={onClose} title={title} description={description}>
            {options.length === 0 ? (
                <p className="text-sm text-muted">
                    Nobody is available. Every eligible student already holds a position in this evaluation.
                </p>
            ) : (
                <form onSubmit={submit} className="space-y-5">
                    <Field label={label}>
                        <select
                            value={chosen}
                            onChange={(event) => setChosen(event.target.value)}
                            required
                            className={control}
                        >
                            <option value="" disabled>
                                Choose a person
                            </option>
                            {options.map((option) => (
                                <option key={option.id} value={option.id}>
                                    {option.name}
                                    {option.department ? ` (${option.department})` : ''}
                                </option>
                            ))}
                        </select>
                    </Field>
                    <div className="flex justify-end gap-2">
                        <Button variant="secondary" onClick={onClose}>
                            Cancel
                        </Button>
                        <Button type="submit" disabled={!chosen || busy}>
                            {submitLabel}
                        </Button>
                    </div>
                </form>
            )}
        </Dialog>
    );
}
