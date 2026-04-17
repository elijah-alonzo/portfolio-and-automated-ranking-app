<x-filament-panels::page>
    @component('Help.HelpLayout', [
        'title' => 'Admin\'s Guide',
    ])
        <div class="help-section">
            <h2 class="help-section-title">Introduction</h2>
            <p>
                This is the admin guide for using the system. Admins have full oversight of the system, managing users, departments, councils, evaluations, certificates, awards, and more. Please read through each section to understand how each module works.
            </p>
        </div>

        <div class="help-section">
            <h2 class="help-section-title">User Management</h2>
            <p class="text-sm text-gray-600">
                Admins can manage all user accounts across the system. Similar to advisers, admins can view and manage student accounts, but admins additionally have the ability to create new student and adviser accounts and assign them to their respective departments.
            </p>
            <figure class="help-figure">
                <img
                    class="help-image"
                    src="{{ asset('assets/admin/user-management.png') }}"
                    alt="User management view"
                >
            </figure>
        </div>

        <div class="help-section">
            <h2 class="help-section-title">Department Management</h2>
            <p class="text-sm text-gray-600">
                Admins can create and manage departments within the system. Departments are organizational units that are assigned to users, grouping students and advisers together under the same department.
            </p>
            <figure class="help-figure">
                <img
                    class="help-image"
                    src="{{ asset('assets/admin/department-management.png') }}"
                    alt="Department management view"
                >
            </figure>
        </div>

        <div class="help-section">
            <h2 class="help-section-title">Councils</h2>
            <p class="text-sm text-gray-600">
                In this tab, admins can create and manage councils within the system. Admins can also configure which departments are eligible to join specific councils, controlling membership access at the department level.
            </p>
            <figure class="help-figure">
                <img
                    class="help-image"
                    src="{{ asset('assets/admin/councils.png') }}"
                    alt="Council management view"
                >
            </figure>
        </div>

        <div class="help-section">
            <h2 class="help-section-title">Council Evaluation</h2>
            <p class="text-sm text-gray-600">
                Admins can create council evaluations and assign them to adviser or admin accounts. Once an evaluation is created and assigned, it will automatically appear in the My Evaluations tab of the assigned adviser. Admins can also monitor all evaluations happening across the system, tracking their progress and viewing results. Individual evaluation forms can be viewed and downloaded directly from this tab.
            </p>
            <figure class="help-figure">
                <img
                    class="help-image"
                    src="{{ asset('assets/admin/council-evaluation.png') }}"
                    alt="Council evaluation management view"
                >
            </figure>
        </div>

        <div class="help-section">
            <h2 class="help-section-title">Council Position</h2>
            <p class="text-sm text-gray-600">
                Admins can create council positions and assign them to specific councils. For each position, a slot limit can be set to define the maximum number of students that can hold that position within a council at any given time.
            </p>
            <figure class="help-figure">
                <img
                    class="help-image"
                    src="{{ asset('assets/admin/council-position.png') }}"
                    alt="Council position management view"
                >
            </figure>
        </div>

        <div class="help-section">
            <h2 class="help-section-title">Certificates</h2>
            <p class="text-sm text-gray-600">
                Admins can issue certificates and distribute them to students directly from this tab. Once a certificate is issued, it is automatically reflected in the recipient's portfolio, where the student can view and download it at any time.
            </p>
            <figure class="help-figure">
                <img
                    class="help-image"
                    src="{{ asset('assets/admin/certificates.png') }}"
                    alt="Certificate management view"
                >
            </figure>
        </div>

        <div class="help-section">
            <h2 class="help-section-title">Award Types</h2>
            <p class="text-sm text-gray-600">
                Admins can create award types that define the kinds of awards a council can offer to its students. These award types serve as the basis for award applications that students may submit upon graduation.
            </p>
            <figure class="help-figure">
                <img
                    class="help-image"
                    src="{{ asset('assets/admin/award-types.png') }}"
                    alt="Award types management view"
                >
            </figure>
        </div>

        <div class="help-section">
            <h2 class="help-section-title">Award Applications</h2>
            <p class="text-sm text-gray-600">
                This tab allows admins to monitor and process award applications submitted by students. Admins can review each application and either approve or reject it. A built-in decision support tool is also available to help admins determine the appropriate rank a student should receive based on their application data.
            </p>
            <figure class="help-figure">
                <img
                    class="help-image"
                    src="{{ asset('assets/admin/award-applications.png') }}"
                    alt="Award applications management view"
                >
            </figure>
        </div>
    @endcomponent
</x-filament-panels::page>