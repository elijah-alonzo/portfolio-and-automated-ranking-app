<x-filament-panels::page>
    @component ('Help.HelpLayout', [
        'title' => 'Award Applications',
    ])
        <p>Students who apply for <strong>Award Applications</strong> are shown in this page. Here the <strong>Admin</strong> can view the details and review the student's portfolio of the application and either approve or reject it. This system supports <strong>Decision Support</strong> that automatically shows the <strong>Rank</strong> of the student based on the most recent <strong>Evaluation Result</strong> of the <strong>Council</strong> with the <strong>Award Type</strong> the student applied for.</p>
        <figure class="help-figure">
            <img
                class="help-image"
                src="{{ asset('assets/applications/applications-table.png') }}"
                alt="Award table"
            />
            <figcaption class="help-caption">
                Table displaying all award applications in the system. Click on
                the three dot menu to Accept, Reject, or view the student's
                portfolio.
            </figcaption>
        </figure>
        <div class="help-section">
            <h2 class="help-section-title">Reviewing a Portfolio</h2>
            <p>Admins can manually check the student's portfolio to review the student's Paulinian leadership expereince and decide whether to approve or reject the application. The portfolio includes the student's profile, the councils they served in, the <strong>Evaluation Results</strong> for each council the student partook in, and the certificates they were issued.</p>
            <figure class="help-figure">
                <img
                    class="help-image"
                    src="{{ asset('assets/applications/applications-view.png') }}"
                    alt="Student portfolio"
                />
            </figure>
        </div>

    @endcomponent
</x-filament-panels::page>
