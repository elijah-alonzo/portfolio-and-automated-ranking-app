<x-filament-panels::page>
    @component('Help.HelpLayout', [
        'title' => 'Portfolio',
    ])

        <p>
            <strong>Portfolio</strong> is a collection of your leadership and extracurricular achievements. 
            Your portfolio grows the more you partake in Councils and contribute to the Paulinian community.
        </p>
            <figure class="help-figure">
                <img
                    class="help-image"
                    src="{{ asset('assets/portfolio/portfolio-view.png') }}"
                    alt="Portfolio"
                >
            </figure>

        <div class="help-section">
            <h2 class="help-section-title">Applying for Leadership Awards</h2>
            <p>
                When applying for Leadership Awards,you must select the <strong>Award Type</strong> you are applying for, and confirm that you are graduating. <strong>Application for leadership awards is only for graduating students.</strong>
            <figure class="help-figure">
                <img
                    class="help-image"
                    src="{{ asset('assets/portfolio/portfolio-application.png') }}"
                    alt="Applying for leadership awards"
                >
            </figure>
        </div>

        <div class="help-section">
            <h2 class="help-section-title">Viewing issued Certificates</h2>
            <p>
                Certificates issued to you are automatically reflected in your portfolio. You can view and download the certificates you have received for your leadership and extracurricular achievements by clicking on the <strong>Issued Certificates</strong> button.
            </p>
            <figure class="help-figure">
                <img
                    class="help-image"
                    src="{{ asset('assets/portfolio/portfolio-cert.png') }}"
                >
            </figure>
        </div>

    @endcomponent
</x-filament-panels::page>