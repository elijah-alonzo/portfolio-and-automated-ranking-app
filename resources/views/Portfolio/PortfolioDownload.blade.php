<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Leadership Portfolio - {{ $record->name }}</title>
    
    @include('Portfolio.PortfolioLayout')
    
    <style>
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            background: #fff;
            margin: 0;
            padding: 0;
        }
        
        /* PDF-specific overrides */
        .portfolio-container {
            box-shadow: none;
            margin: 0;
        }
    </style>
</head>
<body>
    <div class="portfolio-container">
        <!-- Row 1: Image | About and Contacts -->
        <div class="portfolio-grid">
            <!-- Image -->
            <div class="profile-image-container">
                @if($record->pfp)
                    <img src="{{ public_path('storage/' . $record->pfp) }}" alt="{{ $record->name }}" class="profile-image">
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
                        <span class="profile-info-label">Email:</span> {{ $record->email }}
                    </div>
                    
                    <div class="profile-info-item">
                        <span class="profile-info-label">Phone:</span> {{ $record->contact_number ?? 'Not provided' }}
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
</body>
</html>
