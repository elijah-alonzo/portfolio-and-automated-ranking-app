<x-filament-panels::page>
    @component ('Help.HelpLayout', [
        'title' => 'My Councils',
    ])
        <p>The <strong>My Councils</strong> tab is where users can view all the council they have participated in and access their own evaluations. It is also where advisers can manage their councils and control the flow and status of the evaluation process for their councils.</p>
        <figure class="help-figure">
            <img
                class="help-image"
                src="{{ asset('assets/my-council/my-council-table.png') }}"
                alt="Dashboard overview"
            />
        </figure>
        <div class="help-section">
            <h2 class="help-section-title">Council Evaluations</h2>
            <p>When a user clicks on a council, they are taken to a page showing the council’s details. This is also where advisers manage the evaluation process for their councils. The evaluation has three phases, and each phase is handled by the adviser.</p>

            <ul class="help-list">
                <li>
                    <strong>Closed Phase:</strong> Advisers can add students to
                    the council and assign peer evaluators to them.
                </li>
                <li>
                    <strong>Open Phase:</strong> Evaluations are open for
                    advisers and students to fill out and submit.
                </li>
                <li>
                    <strong>Completed Phase:</strong> When all evaluations are
                    submitted, the adviser can mark it as completed to end the
                    evaluation.
                </li>
            </ul>

            <figure class="help-figure">
                <img
                    class="help-image"
                    src="{{ asset('assets/my-council/my-council-view.png') }}"
                    alt="Council evaluations"
                />
                <figcaption class="help-caption">
                    Use the three dot icon on each row to perform actions.
                </figcaption>
            </figure>
        </div>
        <div class="help-section">
            <h2 class="help-section-title">Evaluation Form</h2>
            <p>The evaluation form is where users can fill out and submit their respective evaluations. The evaluation form can be accessed during the <strong>Open Phase</strong>.</p>
            <figure class="help-figure">
                <img
                    class="help-image"
                    src="{{ asset('assets/my-council/my-council-eval.png') }}"
                    alt="Evaluation form"
                />
            </figure>
        </div>
    @endcomponent
</x-filament-panels::page>
