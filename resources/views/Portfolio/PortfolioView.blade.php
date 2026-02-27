<x-filament-panels::page>
    @include('Portfolio.PortfolioLayout')

    <div class="portfolio-container">
        <!-- Row 1: Image | About and Contacts -->
        <div class="portfolio-grid">
            <!-- Image -->
            <div class="profile-image-container">
                @if($record->pfp)
                    <img src="{{ asset('storage/' . $record->pfp) }}" alt="{{ $record->name }}" class="profile-image">
                @else
                    <img src="https://ui-avatars.com/api/?name={{ urlencode($record->name) }}&color=036635&background=E8F5E9&size=250" alt="{{ $record->name }}" class="profile-image">
                @endif
            </div>
            
            <!-- About and Contacts -->
            <div class="profile-details">
                <h2 class="profile-name">{{ $record->name }}</h2>
                
                @if($record->bio)
                    <p class="profile-bio">{{ $record->bio }}</p>
                @else
                    <p class="profile-bio" style="font-style: italic; color: #999;">No biography provided</p>
                @endif
                
                <div class="profile-info-grid">
                    <div class="profile-info-item">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75" />
                        </svg>
                        <span>{{ $record->email }}</span>
                    </div>
                    
                    <div class="profile-info-item">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 0 0 2.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 0 1-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 0 0-1.091-.852H4.5A2.25 2.25 0 0 0 2.25 4.5v2.25Z" />
                        </svg>
                        <span>{{ $record->contact_number ?? 'Not provided' }}</span>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Divider -->
        <div class="profile-divider"></div>
        
        <!-- Row 2: Certificates | Experiences -->
        <div class="portfolio-grid">
            <!-- Certificates -->
            <div class="certificates-section">
                <h2 class="section-header">CERTIFICATES</h2>
                
                @if($record->certificates && $record->certificates->count() > 0)
                    <div class="certificates-list">
                        @foreach($record->certificates as $certificate)
                            <div class="certificate-item">
                                <div class="certificate-date-label">{{ \Carbon\Carbon::parse($certificate->date_issued)->format('F j, Y') }}</div>
                                <h3 class="certificate-name">{{ $certificate->certification_name }}</h3>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="empty-state">
                        <p class="empty-state-text">No certificates uploaded yet.</p>
                    </div>
                @endif
            </div>
            
            <!-- Council Experience -->
            <div class="experience-section">
                <h2 class="section-header">COUNCIL EXPERIENCE</h2>
                
                @if($record->participatingEvaluations && $record->participatingEvaluations->count() > 0)
                    <div class="experience-list">
                        @foreach($record->participatingEvaluations as $evaluation)
                            <div class="experience-item">
                                <div class="experience-content">
                                    <h3 class="experience-position">{{ $evaluation->pivot->position ?? 'Member' }}</h3>
                                    <h4 class="experience-council">{{ $evaluation->council->name ?? 'Unknown Council' }}</h4>
                                    <p class="experience-description">
                                        Successfully fulfilled a comprehensive mandate as {{ $evaluation->pivot->position ?? 'Member' }} for the {{ $evaluation->council->name ?? 'Unknown Council' }}, completing a full term of service characterized by dedicated leadership and active engagement in all council proceedings and initiatives.
                                    </p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="empty-state">
                        <p class="empty-state-text">No leadership experience recorded yet.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-filament-panels::page>
