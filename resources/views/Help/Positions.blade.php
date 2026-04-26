<x-filament-panels::page>
    @component ('Help.HelpLayout', [
        'title' => 'Council Positions',
    ])
        <p>
            <strong>Council Positions</strong> are used to define the roles of
            student officers within each council. This feature can only be
            performed by users with the <strong>Admin</strong> role.
        </p>
        <figure class="help-figure">
            <img
                class="help-image"
                src="{{ asset('assets/positions/list.png') }}"
                alt="Position table"
            />
            <figcaption class="help-caption">
                Table displaying all council positions in the system.
            </figcaption>
        </figure>
        <div class="help-section">
            <h2 class="help-section-title">Creating a Council Position</h2>
            <p>Council positions can be created, edited, and deleted by administrators. When creating a new position, you must provide a unique name, select a <strong>Council</strong> to which the position is available in, select a <strong>Branch</strong> to which the position belongs, set a <strong>Hierarchy</strong> level for the position, and the <strong>Maximum Slots</strong> for how many students can occupy the position.</p>
            <figure class="help-figure">
                <img
                    class="help-image"
                    src="{{ asset('assets/positions/form.png') }}"
                    alt="Position form"
                />
                <figcaption class="help-caption">
                    Form for creating or editing a council position.
                </figcaption>
            </figure>
        </div>

    @endcomponent
</x-filament-panels::page>
