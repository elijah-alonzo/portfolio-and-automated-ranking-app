<x-filament-panels::page>
    @component('Help.HelpLayout', [
        'title' => 'Student\'s Guide',
    ])
        <div class="help-section">
            <h2 class="help-section-title">Introduction</h2>
           <p>
                This is the student guide for using the system. Please read the documentation to understand how the system works. Students’ portfolios grow automatically as they participate in councils and receive certificates. Students can also request leadership awards upon graduation. Additionally, they can complete and submit their assigned evaluations here.
            </p>
        </div>

        <div class="help-section">
            <h2 class="help-section-title">Portfolio</h2>
            <p class="text-sm text-gray-600">
                Your portfolio displays your council experiences and certificates in one place. Your portfolio grows the more active you are. You can also check and download certificates issued to you by your advisers by clicking on the Issued Certificates button on the top right corner of your portfolio page.
            </p>
            <figure class="help-figure">
                <img
                    class="help-image"
                    src="{{ asset('assets/student/portfolio.png') }}"
                    alt="Student portfolio view"
                >
                <figcaption class="help-caption">
                    Note: You can access your personal information and account settings by clicking on your name at the bottom of the sidebar and selecting Profile.
                </figcaption>
            </figure>
        </div>

        <div class="help-section">
            <h2 class="help-section-title">My Evaluations</h2>
            <p class="text-sm text-gray-600">
                This tab shows your assigned evaluations for your councils and their status. Evaluations begin when your adviser opens the evaluation cycle for a council you are in. You will see the evaluation listed here with its current status.
            </p>
            <figure class="help-figure">
                <img
                    class="help-image"
                    src="{{ asset('assets/student/my-evaluation.png') }}"
                    alt="My evaluations overview"
                >
                <figcaption class="help-caption">
                    Note: Click on the three dot icon on the row of a user to evaluate them.
                </figcaption>
            </figure>
        </div>

        <div class="help-section">
            <h2 class="help-section-title">Evaluation Form</h2>
            <p class="text-sm text-gray-600">
                When you open an evaluation, you will see the evaluation form with the criteria to be evaluated. Complete the form by providing scores and comments where required, then submit when finished. You can also save a draft and return to it later if needed.
            </p>
            <figure class="help-figure">
                <img
                    class="help-image"
                    src="{{ asset('assets/student/self-evaluation.png') }}"
                    alt="Self evaluation form"
                >
            </figure>
        </div>
    @endcomponent
</x-filament-panels::page>
