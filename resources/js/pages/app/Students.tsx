import { Link } from '@inertiajs/react';
import { Pencil, Plus } from 'lucide-react';
import { useState } from 'react';
import StudentDialog, { type StudentRow } from '@/components/sections/students/StudentDialog';
import Avatar from '@/components/shared/Avatar';
import EmptyState from '@/components/shared/EmptyState';
import PageHeader from '@/components/shared/PageHeader';
import Seo from '@/components/shared/Seo';
import Button from '@/components/ui/Button';
import Panel from '@/components/ui/Panel';
import AppLayout from '@/layouts/AppLayout';
import { student as portfolio } from '@/actions/App/Features/Users/Controllers/PortfolioController';

export default function Students({ department, students }: { department: string | null; students: StudentRow[] }) {
    const [editing, setEditing] = useState<StudentRow | 'new' | null>(null);

    return (
        <AppLayout>
            <Seo title="Students" description="Student accounts in your department." />
            <PageHeader
                title="Students"
                description={`Student accounts in ${department ?? 'your department'}. Open one to see their portfolio.`}
                actions={
                    <Button onClick={() => setEditing('new')}>
                        <Plus size={16} aria-hidden />
                        Add student
                    </Button>
                }
            />
            <Panel flush>
                {students.length === 0 ? (
                    <EmptyState title="No students yet">
                        Add your department's student officers so you can assign them to councils.
                    </EmptyState>
                ) : (
                    <ul className="divide-y divide-line">
                        {students.map((student) => (
                            <li key={student.id} className="flex items-center gap-4 px-5 py-3.5">
                                <Avatar name={student.name} src={student.avatar} />
                                <Link
                                    href={portfolio.url(student.id)}
                                    className="min-w-0 flex-1 rounded focus-visible:outline-2 focus-visible:outline-brand"
                                >
                                    <p className="truncate text-sm font-medium hover:text-brand">{student.name}</p>
                                    <p className="truncate text-xs text-muted">
                                        {student.email}
                                        {student.contactNumber && ` · ${student.contactNumber}`}
                                    </p>
                                </Link>
                                {!student.isActive && (
                                    <span className="rounded-md bg-paper px-2 py-0.5 text-xs font-medium text-muted ring-1 ring-line ring-inset">
                                        Inactive
                                    </span>
                                )}
                                <Button
                                    variant="quiet"
                                    aria-label={`Edit ${student.name}`}
                                    onClick={() => setEditing(student)}
                                >
                                    <Pencil size={15} aria-hidden />
                                </Button>
                            </li>
                        ))}
                    </ul>
                )}
            </Panel>
            {editing && (
                <StudentDialog
                    key={editing === 'new' ? 'new' : editing.id}
                    student={editing}
                    onClose={() => setEditing(null)}
                />
            )}
        </AppLayout>
    );
}
