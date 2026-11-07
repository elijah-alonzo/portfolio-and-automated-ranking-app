import { Plus } from 'lucide-react';
import { useState } from 'react';
import IssueDialog from '@/components/sections/certificates/IssueDialog';
import CertificateList from '@/components/shared/CertificateList';
import PageHeader from '@/components/shared/PageHeader';
import Seo from '@/components/shared/Seo';
import Button from '@/components/ui/Button';
import Panel from '@/components/ui/Panel';
import AppLayout from '@/layouts/AppLayout';
import type { StudentOption } from '@/types/councils';
import type { Certificate } from '@/types/portfolio';

type CertificatesProps = {
    certificates: Certificate[];
    canIssue: boolean;
    students: StudentOption[];
    maxFileKb: number;
};

export default function Certificates({ certificates, canIssue, students, maxFileKb }: CertificatesProps) {
    const [issuing, setIssuing] = useState(false);

    return (
        <AppLayout>
            <Seo title="Certificates" description="Leadership certificates issued as PDFs." />
            <PageHeader
                title="Certificates"
                description={
                    canIssue
                        ? 'Certificates you and others issued to students in your department.'
                        : 'Certificates issued to you, newest first.'
                }
                actions={
                    canIssue && (
                        <Button onClick={() => setIssuing(true)}>
                            <Plus size={16} aria-hidden />
                            Issue certificate
                        </Button>
                    )
                }
            />
            <Panel flush>
                <CertificateList
                    certificates={certificates}
                    showStudent={canIssue}
                    empty={
                        canIssue
                            ? {
                                  title: 'No certificates issued yet',
                                  body: 'Issue one to give students a PDF they can keep in their portfolio.',
                              }
                            : {
                                  title: 'No certificates yet',
                                  body: 'When an adviser issues you a certificate, it appears here and in your portfolio.',
                              }
                    }
                />
            </Panel>
            {canIssue && (
                <IssueDialog
                    open={issuing}
                    onClose={() => setIssuing(false)}
                    students={students}
                    maxFileKb={maxFileKb}
                />
            )}
        </AppLayout>
    );
}
