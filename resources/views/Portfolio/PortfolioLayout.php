<style>
    .portfolio-container {
        background: #fff;
        margin: 40px auto;
        max-width: 1100px;
        width: 100%;
        padding: 60px 80px;
        box-sizing: border-box;
        box-shadow: 0 2px 12px rgba(0,0,0,0.08);
    }
    
    /* Portfolio Grid Layout */
    .portfolio-grid {
        display: grid;
        grid-template-columns: 1fr 2fr;
        gap: 40px;
        margin-bottom: 40px;
    }
    
    /* Profile section divider */
    .profile-divider {
        border-bottom: 1px solid #ddd;
        margin: 40px 0;
    }
    
    /* Profile Image Container */
    .profile-image-container {
        text-align: center;
    }
    
    .profile-image {
        width: 100%;
        max-width: 250px;
        aspect-ratio: 1;
        border-radius: 50%;
        object-fit: cover;
        border: 4px solid #036635;
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }
    
    .profile-details {
        display: flex;
        flex-direction: column;
        gap: 16px;
    }
    
    .profile-name {
        font-size: 2rem;
        font-weight: 700;
        color: #036635;
        margin: 0;
    }
    
    .profile-bio {
        font-size: 1.1rem;
        color: #444;
        line-height: 1.6;
        margin: 0;
    }
    
    .profile-info-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 16px;
        margin-top: 8px;
    }
    
    .profile-info-item {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 1rem;
        color: #555;
    }
    
    .profile-info-item svg {
        width: 20px;
        height: 20px;
        color: #036635;
    }
    
    .profile-info-label {
        font-weight: 600;
        color: #036635;
    }
    
    /* Two Column Layout */
    .two-column-layout {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 40px;
        margin-top: 40px;
    }
    
    /* Section Headers */
    .section-header {
        font-size: 1.5rem;
        font-weight: 700;
        color: #036635;
        margin-bottom: 24px;
        text-transform: uppercase;
        letter-spacing: 1px;
    }
    
    /* Certificates Column */
    .certificates-section {
        padding-right: 20px;
    }
    
    .certificates-list {
        display: flex;
        flex-direction: column;
        gap: 20px;
    }
    
    .certificate-item {
        margin-bottom: 16px;
    }
    
    .certificate-date-label {
        font-size: 0.9rem;
        color: #666;
        margin-bottom: 4px;
    }
    
    .certificate-name {
        font-size: 1.1rem;
        font-weight: 700;
        color: #222;
        margin: 0;
    }
    
    /* Council Experience Column */
    .experience-section {
        padding-left: 20px;
        border-left: 1px solid #ddd;
    }
    
    .experience-list {
        display: flex;
        flex-direction: column;
        gap: 32px;
    }
    
    .experience-item {
        margin-bottom: 32px;
    }
    
    .experience-content {
        flex: 1;
    }
    
    .experience-position {
        font-size: 1.2rem;
        font-weight: 700;
        color: #222;
        margin: 0 0 4px 0;
    }
    
    .experience-council {
        font-size: 1rem;
        font-weight: 600;
        color: #222;
        margin: 0 0 8px 0;
    }
    
    .experience-description {
        font-size: 0.95rem;
        color: #444;
        line-height: 1.6;
        text-align: justify;
        margin: 0;
    }
    
    /* Empty States */
    .empty-state {
        text-align: center;
        padding: 48px 24px;
        background: #f8f9fa;
        border-radius: 10px;
        border: 2px dashed #dee2e6;
    }
    
    .empty-state svg {
        width: 64px;
        height: 64px;
        color: #adb5bd;
        margin-bottom: 16px;
    }
    
    .empty-state-text {
        font-size: 1.1rem;
        color: #6c757d;
        margin: 0;
    }
    
    @media print {
        .portfolio-container {
            box-shadow: none;
            border: none;
            margin: 0;
            padding: 20px;
        }
    }
    
    /* Tablet and smaller devices */
    @media (max-width: 768px) {
        .portfolio-container {
            padding: 40px 40px;
            margin: 20px auto;
        }
        
        .portfolio-grid {
            grid-template-columns: 1fr;
            gap: 30px;
        }
        
        .profile-name {
            font-size: 1.75rem;
        }
        
        .profile-bio {
            font-size: 1rem;
        }
        
        .profile-info-grid {
            grid-template-columns: 1fr;
        }
        
        .section-header {
            font-size: 1.3rem;
        }
        
        /* Remove vertical divider on small screens */
        .experience-section {
            border-left: none;
            padding-left: 0;
        }
        
        .certificates-section {
            padding-right: 0;
        }
    }
    
    /* Mobile devices */
    @media (max-width: 480px) {
        .portfolio-container {
            padding: 30px 20px;
            margin: 10px auto;
        }
        
        .portfolio-grid {
            gap: 24px;
        }
        
        .profile-image {
            max-width: 180px;
        }
        
        .profile-name {
            font-size: 1.5rem;
        }
        
        .profile-bio {
            font-size: 0.95rem;
        }
        
        .profile-info-item {
            font-size: 0.9rem;
        }
        
        .section-header {
            font-size: 1.2rem;
            margin-bottom: 20px;
        }
        
        .certificate-name {
            font-size: 1rem;
        }
        
        .certificate-date-label {
            font-size: 0.85rem;
        }
        
        .experience-position {
            font-size: 1.1rem;
        }
        
        .experience-council {
            font-size: 0.95rem;
        }
        
        .experience-description {
            font-size: 0.9rem;
        }
        
        .profile-divider {
            margin: 30px 0;
        }
    }
</style>
