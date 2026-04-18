<x-filament-panels::page>
    @component('Help.HelpLayout', [
        'title' => 'Available Councils',
    ])

        <p>
            <strong>Councils</strong> are the student councils that serve the Paulinian community. Every year, councils undergo changes in their composition, with new members joining and others leaving. 
        </p>

            <figure class="help-figure">
                <img
                    class="help-image"
                    src="{{ asset('assets/councils/councils-table.png') }}"
                    alt="Council table"
                >
                <figcaption class="help-caption">Table displaying all councils in the system.</figcaption>
            </figure>

        <div class="help-section">
            <h2 class="help-section-title">Creating a Council</h2>
            <p>
                Councils can be created, edited, and deleted by administrators. When creating a new council, you must select the <strong>Departments</strong> that are allowed to join and <strong>Award Type</strong> that the student leaders can get for serving that council.
            </p>
            <figure class="help-figure">
                <img
                    class="help-image"
                    src="{{ asset('assets/councils/councils-form.png') }}"
                    alt="Council form"
                >
                <figcaption class="help-caption">Form for creating or editing a council.</figcaption>
            </figure>
        </div>

    @endcomponent
</x-filament-panels::page>