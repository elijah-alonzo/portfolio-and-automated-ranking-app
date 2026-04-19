<x-filament-panels::page>
    @component ('Help.HelpLayout', [
        'title' => 'Award Types',
    ])
        <p>
            <strong>Award Types</strong> are used to define the categories of
            awards that can be given to students for serving the council. This
            feature can only be performed by users with the
            <strong>Admin</strong> role.
        </p>
        <figure class="help-figure">
            <img
                class="help-image"
                src="{{ asset('assets/awards/awards-table.png') }}"
                alt="Award table"
            />
            <figcaption class="help-caption">
                Table displaying all award types in the system.
            </figcaption>
        </figure>
        <div class="help-section">
            <h2 class="help-section-title">Creating an Award Type</h2>
            <p>Award types can be created, edited, and deleted by administrators. When creating a new award type, you must provide a unique name and a description.</p>
            <figure class="help-figure">
                <img
                    class="help-image"
                    src="{{ asset('assets/awards/awards-form.png') }}"
                    alt="Award form"
                />
                <figcaption class="help-caption">
                    Form for creating or editing an award type.
                </figcaption>
            </figure>
        </div>

    @endcomponent
</x-filament-panels::page>
