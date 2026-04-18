<x-filament-panels::page>
    @component('Help.HelpLayout', [
        'title' => 'User Management',
    ])

        <p>
            User accounts are created and managed in the <strong>User Management</strong> tab. Each user is assigned to a role that determines their permissions and access within the system. The three main roles are <strong>Student</strong>, <strong>Adviser</strong>, and <strong>Admin</strong>.
        </p>

        <div class="help-section">
            <h2 class="help-section-title">User Roles</h2>
            <ul class="help-list">
                <li><strong>Students</strong> participate in the evaluation process of the councils they are a participating in.</li>
                <li><strong>Advisers</strong> create student accounts and manage students and evaluations for their councils.</li>
                <li><strong>Admins</strong> have full access to manage all aspects of the system, including councils, students, evaluations, and certifications.</li>
            </ul>
        </div>

        <div class="help-section">
            <h2 class="help-section-title">Users Table</h2>
            <p>
                Displays all the users in the system along with their details such as name, email, role, and associated council. If the user is an <strong>Adviser</strong>, they can only manage user accounts with their assigned department.
            </p>
            <figure class="help-figure">
                <img
                    class="help-image"
                    src="{{ asset('assets/users/users-table.png') }}"
                    alt="Users table"
                >
            </figure>
        </div>

        <div class="help-section">
            <h2 class="help-section-title">Users Form</h2>
            <p>
                Creating a user account requires filling out the user form. The <strong>Role</strong> field determines the user's permissions and access within the system. The <strong>Department</strong> field determines the councils a user can be associated with. And the <strong>Is Active</strong> field determines whether the user can log in to the system or not.
            </p>
            <figure class="help-figure">
                <img
                    class="help-image"
                    src="{{ asset('assets/users/users-form.png') }}"
                    alt="Users form"
                >
            </figure>
        </div>

    @endcomponent
</x-filament-panels::page>