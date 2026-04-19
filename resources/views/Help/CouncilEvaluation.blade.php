<x-filament-panels::page>
    @component ('Help.HelpLayout', [
        'title' => 'Council Evaluations',
    ])
        <p>Every year, councils undergo changes in their composition, with new sets of officers joining. But before a new set of officers can be added, the previous set must be evaluated. This is managed by the <strong>Admin</strong>, who can create a council evaluation for each council.</p>
        <figure class="help-figure">
            <img
                class="help-image"
                src="{{ asset('assets/council-eval/council-eval-table.png') }}"
                alt="Council table"
            />
            <figcaption class="help-caption">
                Table displaying all evaluations in the system.
            </figcaption>
        </figure>
        <div class="help-section">
            <h2 class="help-section-title">
                Viewing Council Evaluation Details
            </h2>
            <p>Only users with the <strong>Admin</strong> role can monitor the progress of each evaluation and view the results of each evaluation, including the scores for each evaluation criterion, the total evaluation score, the <strong>Rank</strong> aswell as the reccomendation for evaluation scores.</p>
            <figure class="help-figure">
                <img
                    class="help-image"
                    src="{{ asset('assets/council-eval/council-eval-view.png') }}"
                    alt="Council table"
                />
                <figcaption class="help-caption">
                    Table displaying evaluations details of a council.
                </figcaption>
            </figure>
        </div>
        <div class="help-section">
            <h2 class="help-section-title">Viewing Council Evaluation Forms</h2>
            <p>
                <strong>Admins</strong> can also view the evaluation forms
                submitted by evaluators by clicking the scores under each
                evaluation criterion. Admins can also download the evaluation
                forms as <strong>PDF</strong> or <strong>CSV</strong> files.
            </p>
            <figure class="help-figure">
                <img
                    class="help-image"
                    src="{{ asset('assets/council-eval/admin-eval.png') }}"
                    alt="Council table"
                />
            </figure>
        </div>
        <div class="help-section">
            <h2 class="help-section-title">Creating a Council Evaluation</h2>
            <p>Evaluations can be created, edited, and deleted by administrators. When creating an evaluation, you must select the <strong>Council</strong> that is being evaluated, <strong>Academic Year</strong> for that evaluation, and the <strong>Adviser</strong> that will manage the evaluation process for that council.</p>
            <figure class="help-figure">
                <img
                    class="help-image"
                    src="{{ asset('assets/council-eval/council-eval-form.png') }}"
                    alt="Council form"
                />
                <figcaption class="help-caption">
                    Form for creating or editing a council evaluation.
                </figcaption>
            </figure>
        </div>

    @endcomponent
</x-filament-panels::page>
