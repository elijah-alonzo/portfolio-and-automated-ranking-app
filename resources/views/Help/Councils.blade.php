<x-filament-panels::page>
    @component ('Help.HelpLayout', [
        'title' => 'Available Councils',
    ])
        <p>
            <strong>Councils</strong> are student organizations that serve the
            Paulinian community. Only users with the <strong>Admin</strong> role
            can create councils and manage their details.
        </p>
        <figure class="help-figure">
            <img
                class="help-image"
                src="{{ asset('assets/councils/list.png') }}"
                alt="Council table"
            />
            <figcaption class="help-caption">
                Table displaying all councils in the system.
            </figcaption>
        </figure>
        <div class="help-section">
            <h2 class="help-section-title">Creating a Council</h2>
            <p>Councils can be created, edited, and deleted by administrators. When creating a new council, you must select the <strong>Departments</strong> that are allowed to join and <strong>Award Type</strong> that the student leaders can get for serving that council.</p>
            <figure class="help-figure">
                <img
                    class="help-image"
                    src="{{ asset('assets/councils/form.png') }}"
                    alt="Council form"
                />
                <figcaption class="help-caption">
                    Form for creating or editing a council.
                </figcaption>
            </figure>
        </div>

    @endcomponent
</x-filament-panels::page>
