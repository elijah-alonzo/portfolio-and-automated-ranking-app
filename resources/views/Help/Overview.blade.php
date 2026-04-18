<x-filament-panels::page>
    @component('Help.HelpLayout', [
        'title' => 'Introduction',
    ])

        <p>
            This system manages student leadership records, evaluations, and recognition in one centralized platform.
            It helps advisers and administrators track performance and maintain a transparent ranking process.
        </p>

        <div class="help-section">
            <h2 class="help-section-title">Purpose</h2>
            <ul class="help-list">
                <li>Centralize student leadership portfolios across councils and positions.</li>
                <li>Standardize evaluation and ranking workflows.</li>
                <li>Support issuance and tracking of certificates and awards.</li>
            </ul>
        </div>

        <div class="help-section">
            <h2 class="help-section-title">Help Topics</h2>
            <ul class="help-list">
                <li><a href="{{ \App\Filament\Resources\Helps\Pages\MyEvaluationHelp::getUrl() }}">My Evaluation</a></li>
                <li><a href="{{ \App\Filament\Resources\Helps\Pages\UserHelp::getUrl() }}">Users</a></li>
                <li><a href="{{ \App\Filament\Resources\Helps\Pages\DepartmentHelp::getUrl() }}">Departments</a></li>
                <li><a href="{{ \App\Filament\Resources\Helps\Pages\CouncilHelp::getUrl() }}">Councils</a></li>
                <li><a href="{{ \App\Filament\Resources\Helps\Pages\CouncilEvaluationHelp::getUrl() }}">Council Evaluation</a></li>
                <li><a href="{{ \App\Filament\Resources\Helps\Pages\PositionHelp::getUrl() }}">Positions</a></li>
                <li><a href="{{ \App\Filament\Resources\Helps\Pages\CertificateHelp::getUrl() }}">Certificates</a></li>
                <li><a href="{{ \App\Filament\Resources\Helps\Pages\AwardHelp::getUrl() }}">Awards</a></li>
                <li><a href="{{ \App\Filament\Resources\Helps\Pages\ApplicationHelp::getUrl() }}">Applications</a></li>
            </ul>
        </div>

        <div class="help-section">
            <h2 class="help-section-title">Key Features</h2>
            <ul class="help-list">
                <li>Portfolio tracking for students and officers.</li>
                <li>Structured evaluation cycles for advisers and admins.</li>
                <li>Automated records for recognition and certificates.</li>
            </ul>
        </div>

        <div class="help-section">
            <h2 class="help-section-title">User Roles</h2>
            <ul class="help-list">
                <li><strong>Students:</strong> View evaluations and update portfolios.</li>
                <li><strong>Advisers:</strong> Evaluate assigned students and validate records.</li>
                <li><strong>Administrators:</strong> Manage councils, evaluations, and certifications.</li>
            </ul>
        </div>

        <div class="help-section">
            <h2 class="help-section-title">Ranking Process</h2>
            <p>
                Rankings are built from multiple criteria to ensure a balanced assessment of performance.
            </p>
            <ul class="help-list">
                <li><strong>Self evaluation:</strong> Students complete their own assessment.</li>
                <li><strong>Adviser evaluation:</strong> Advisers provide formal ratings and comments.</li>
                <li><strong>Admin evaluation:</strong> Administrators review and finalize scoring where required.</li>
                <li><strong>Length of service:</strong> Service duration is calculated automatically based on council assignments.</li>
            </ul>
            <p>
                Award applications also factor into rank determination by capturing additional achievements and
                leadership contributions tied to specific award criteria.
            </p>
        </div>

        <div class="help-section">
            <h2 class="help-section-title">About This Application</h2>
            <p>
                This application was developed as a capstone project in partial fulfillment of academic requirements.
                It aims to demonstrate the application of modern web technologies in building an integrated
                student leadership management system.
            </p>
        </div>
    @endcomponent
</x-filament-panels::page>