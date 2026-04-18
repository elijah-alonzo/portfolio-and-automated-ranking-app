<x-filament-panels::page>
    @component('Help.HelpLayout', [
        'title' => 'Departments',
    ])

        <p>
            Departments are used to organize users and councils within the system. Each department has a unique name and description.
        </p>

        <div class="help-section">
            <h2 class="help-section-title">Department Management</h2>
            <p>
                Departments can be created, edited, and deleted by administrators. When creating a new department, you must provide a unique name and a description.
            </p>
            <figure class="help-figure">
                <img
                    class="help-image"
                    src="{{ asset('assets/departments/departments-table.png') }}"
                    alt="Department table"
                >
                <figcaption class="help-caption">Table displaying all departments in the system.</figcaption>
            </figure>
        </div>

    @endcomponent
</x-filament-panels::page>