<x-filament-panels::page>
    @component('Help.HelpLayout', [
        'title' => 'Adviser\'s Guide',
    ])
        <div class="help-section">
            <h2 class="help-section-title">Introduction</h2>
            <p>
                This is the adviser guide for using the system. Please read the documentation to understand how the system works. Advisers can manage their councils, assign peer evaluators to students, and fill out adviser evaluation forms for their students. Advisers can also manage student accounts under their department.
            </p>
        </div>

        <div class="help-section">
            <h2 class="help-section-title">My Evaluations</h2>
            <p class="text-sm text-gray-600">
                This tab shows the evaluations for councils you advise and their current status. As an adviser, you can manage the evaluation cycle by setting the evaluation to one of the following states:
            </p>
            <ul class="help-list">
                <li><span class="font-semibold">Closed:</span> Advisers can add students to their councils and assign peer evaluators to them.</li>
                <li><span class="font-semibold">Open:</span> Student management is disabled and evaluation can be performed.</li>
                <li><span class="font-semibold">Completed:</span> When all evaluation forms are submitted it can be marked as completed, making the record read only.</li>
            </ul>
            <figure class="help-figure">
                <img
                    class="help-image"
                    src="{{ asset('assets/adviser/my-evaluation.png') }}"
                    alt="My evaluations overview"
                >
                <figcaption class="help-caption">
                    Note: Click on the three dot icon to manage or evaluate students.
                </figcaption>
            </figure>
        </div>

        <div class="help-section">
            <h2 class="help-section-title">Adviser Evaluation Form</h2>
            <p class="text-sm text-gray-600">
                When you open an evaluation for a student, you will see the adviser evaluation form with the criteria to be evaluated. Complete the form by providing scores and comments where required, then submit when finished. You can also save a draft and return to it later if needed.
            </p>
            <figure class="help-figure">
                <img
                    class="help-image"
                    src="{{ asset('assets/adviser/adviser-evaluation.png') }}"
                    alt="Adviser Evaluation Form"
                >
            </figure>
        </div>

        <div class="help-section">
            <h2 class="help-section-title">User Management</h2>
            <p class="text-sm text-gray-600">
                Here, advisers can manage student accounts that belong to the same department as the adviser. This includes viewing student information and overseeing their participation across councils.
            </p>
            <figure class="help-figure">
                <img
                    class="help-image"
                    src="{{ asset('assets/adviser/user-management.png') }}"
                    alt="User management view"
                >
            </figure>
        </div>
    @endcomponent
</x-filament-panels::page>