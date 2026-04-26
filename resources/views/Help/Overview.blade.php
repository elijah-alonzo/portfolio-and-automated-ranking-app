<x-filament-panels::page>
    @component ('Help.HelpLayout', [
        'title' => 'Introduction',
    ])
        <p>This system was developed to provide a digital portfolio for Paulinian student leaders that automatically grows as they contribute more to the Paulinian community. It also enables administrators and advisers to manage and automate the evaluation and ranking of Paulinian student leaders through a built-in decision support system.</p>
        <div class="help-section">
            <h2 class="help-section-title">Dashboard</h2>
            <p>Each user is provided with a dashboard that provides an overview of their activities and provides a quick access to information.</p>
            <figure class="help-figure">
                <img
                    class="help-image"
                    src="{{ asset('assets/dashboard.png') }}"
                    alt="Dashboard overview"
                />
                <figcaption class="help-caption">
                    Main dashboard view of the system.
                </figcaption>
            </figure>
        </div>
        <div class="help-section">
            <h2 class="help-section-title">Profile Management</h2>
            <p>Users can also manage their information such as personal details and passwords by clicking on their username at the bottom left corner and selecting <strong>Profile</strong>.</p>
            <figure class="help-figure">
                <img
                    class="help-image"
                    src="{{ asset('assets/profile.png') }}"
                    alt="Profile management"
                />
                <figcaption class="help-caption">
                    Once clicked, the user is redirected to a page where they
                    can edit their profile information.
                </figcaption>
            </figure>
        </div>
        <div class="help-section">
            <h2 class="help-section-title">About This Application</h2>
            <p>This application was developed as a capstone project in partial fulfillment of academic requirements. It aims to demonstrate the application of modern web technologies in building an integrated student leadership management system.</p>
        </div>
    @endcomponent
</x-filament-panels::page>
