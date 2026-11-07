import { Mail, Phone } from 'lucide-react';
import Avatar from '@/components/shared/Avatar';
import { card } from '@/components/ui/styles';
import type { Person } from '@/types/portfolio';

export default function ProfileCard({ person }: { person: Person }) {
    return (
        <section className={`${card} overflow-hidden`}>
            <div className="h-20 bg-[linear-gradient(120deg,var(--brand-deep),var(--brand))]" />
            <div className="px-6 pb-6">
                <Avatar
                    name={person.name}
                    src={person.avatar}
                    className="-mt-10 size-20 text-2xl ring-4 ring-surface"
                />
                <h2 className="mt-3 font-display font-medium text-2xl text-brand-deep">{person.name}</h2>
                {person.department && <p className="text-sm text-muted">{person.department}</p>}
                <p className="mt-4 text-sm leading-relaxed">
                    {person.bio ?? <span className="text-muted">No bio yet.</span>}
                </p>
                <dl className="mt-5 space-y-2 text-sm">
                    <div className="flex items-center gap-2">
                        <dt>
                            <Mail size={15} aria-label="Email" className="text-muted" />
                        </dt>
                        <dd className="truncate">{person.email}</dd>
                    </div>
                    {person.contactNumber && (
                        <div className="flex items-center gap-2">
                            <dt>
                                <Phone size={15} aria-label="Contact number" className="text-muted" />
                            </dt>
                            <dd>{person.contactNumber}</dd>
                        </div>
                    )}
                </dl>
            </div>
        </section>
    );
}
