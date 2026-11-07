import { FileText } from 'lucide-react';
import Avatar from '@/components/shared/Avatar';
import EmptyState from '@/components/shared/EmptyState';
import { formatDate } from '@/lib/format';
import type { Certificate } from '@/types/portfolio';

/** Certificates as a list of PDFs to open. Shows who each is for when `showStudent` is set. */
export default function CertificateList({
    certificates,
    showStudent = false,
    empty,
}: {
    certificates: Certificate[];
    showStudent?: boolean;
    empty: { title: string; body: string };
}) {
    if (certificates.length === 0) {
        return <EmptyState title={empty.title}>{empty.body}</EmptyState>;
    }

    return (
        <ul className="divide-y divide-line">
            {certificates.map((certificate) => (
                <li key={certificate.id} className="flex items-center gap-4 px-5 py-3.5">
                    {showStudent && certificate.student ? (
                        <Avatar name={certificate.student} src={certificate.studentAvatar ?? null} />
                    ) : (
                        <span className="grid size-9 shrink-0 place-items-center rounded-lg bg-gold-tint text-[#7a5d00]">
                            <FileText size={17} aria-hidden />
                        </span>
                    )}
                    <div className="min-w-0 flex-1">
                        <p className="truncate text-sm font-medium">{certificate.name}</p>
                        <p className="truncate text-xs text-muted">
                            {showStudent && certificate.student ? `${certificate.student} · ` : ''}
                            Issued {formatDate(certificate.dateIssued)}
                        </p>
                    </div>
                    {certificate.url && (
                        <a
                            href={certificate.url}
                            target="_blank"
                            rel="noreferrer"
                            className="text-sm font-medium text-brand hover:underline"
                        >
                            Open PDF
                        </a>
                    )}
                </li>
            ))}
        </ul>
    );
}
