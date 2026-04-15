<style>
    /* Main evaluation card - matching portfolio styling */
    .ef-evaluation-card {
        background: #fff;
        margin: 40px auto;
        max-width: 1100px;
        width: 100%;
        padding: 60px 80px;
        box-sizing: border-box;
        box-shadow: 0 2px 12px rgba(0,0,0,0.08);
    }
    
    /* Header styles */
    .ef-evaluation-header { 
        margin-bottom: 32px; 
    }
    
    .ef-evaluation-title { 
        font-size: 2rem; 
        font-weight: 700; 
        margin-bottom: 0.25rem; 
        color: #036635; 
    }
    
    .ef-evaluation-subheading { 
        color: #555; 
        font-size: 1.1rem; 
        margin-bottom: 0; 
    }
    
    /* Domain section styles */
    .ef-domain-section { 
        margin-bottom: 32px; 
        border-radius: 10px; 
        border: 1px solid #f1f1f1; 
        background: #fff; 
    }
    
    .ef-domain-header { 
        border-bottom: 1px solid #f1f1f1; 
        padding: 20px 24px 10px 24px; 
        background: #f0f9f4; 
        border-radius: 10px 10px 0 0;
    }
    
    .ef-domain-title { 
        font-size: 1.25rem; 
        font-weight: 700; 
        color: #036635; 
    }
    
    .ef-domain-description { 
        font-size: 1rem; 
        color: #444; 
        margin-top: 4px; 
    }
    
    /* Strand styles */
    .ef-strand { 
        padding: 16px 24px 0 24px; 
    }
    
    .ef-strand-title { 
        font-size: 1.05rem; 
        font-weight: 600; 
        color: #036635; 
        margin-bottom: 8px; 
    }
    
    /* Question container */
    .ef-questions-container { 
        margin-bottom: 12px; 
    }
    
    .ef-question-item { 
        margin-bottom: 24px; 
        padding-bottom: 12px; 
        border-bottom: 1px solid #f1f1f1; 
    }
    
    .ef-question-text { 
        font-size: 1.08rem; 
        color: #222; 
        margin-bottom: 10px; 
        font-weight: 500; 
    }
    
    /* Rating scale styles */
    .ef-rating-scale { 
        display: flex; 
        gap: 32px; 
        margin-left: 0; 
        margin-top: 4px; 
    }
    
    .ef-rating-option { 
        display: flex; 
        align-items: center; 
        cursor: pointer; 
        font-size: 1rem; 
    }
    
    .ef-rating-option input[type="radio"] { 
        accent-color: #036635; 
        width: 18px; 
        height: 18px; 
        margin-right: 6px; 
    }
    
    .ef-rating-label { 
        display: flex; 
        align-items: center; 
        gap: 4px; 
    }
    
    .ef-rating-value { 
        font-weight: 600; 
        color: #036635; 
        margin-right: 2px; 
    }
    
    .ef-rating-criteria { 
        color: #444; 
        font-size: 0.97rem; 
    }

    .ef-length-service-note {
        background: #f7faf8;
        border: 1px dashed #cfe6d7;
        border-radius: 8px;
        color: #355a44;
        font-size: 0.98rem;
        margin-top: 8px;
        padding: 10px 12px;
    }
    
    /* Form actions */
    .ef-form-actions { 
        display: flex; 
        justify-content: flex-end; 
        gap: 12px; 
        margin-top: 32px; 
    }
    
    .ef-btn { 
        padding: 10px 24px; 
        border-radius: 6px; 
        font-size: 1rem; 
        font-weight: 600; 
        border: none; 
        cursor: pointer; 
        transition: all 0.2s; 
    }
    
    .ef-btn-primary { 
        background: #036635; 
        color: #fff; 
    }
    
    .ef-btn-primary:hover { 
        background: #024d27; 
        box-shadow: 0 4px 12px rgba(3, 102, 53, 0.3); 
    }
    
    /* Locked message */
    .ef-locked-message { 
        background: #f0f9f4; 
        color: #036635; 
        border-left: 4px solid #036635; 
        padding: 12px 18px; 
        margin-bottom: 18px; 
        border-radius: 6px; 
        font-weight: 500; 
    }
    
    /* Tablet and smaller devices */
    @media (max-width: 768px) {
        .ef-evaluation-card {
            padding: 40px 40px;
            margin: 20px auto;
        }
        
        .ef-evaluation-title {
            font-size: 1.75rem;
        }
        
        .ef-evaluation-subheading {
            font-size: 1rem;
        }
        
        .ef-domain-title {
            font-size: 1.15rem;
        }
        
        .ef-strand-title {
            font-size: 1rem;
        }
        
        .ef-question-text {
            font-size: 1rem;
        }
        
        .ef-rating-scale { 
            flex-direction: column; 
            gap: 10px; 
            align-items: flex-start; 
        }
    }
    
    /* Mobile devices */
    @media (max-width: 480px) {
        .ef-evaluation-card {
            padding: 30px 20px;
            margin: 10px auto;
        }
        
        .ef-evaluation-title {
            font-size: 1.5rem;
        }
        
        .ef-evaluation-subheading {
            font-size: 0.95rem;
        }
        
        .ef-domain-header {
            padding: 16px 20px 10px 20px;
        }
        
        .ef-domain-title {
            font-size: 1.1rem;
        }
        
        .ef-strand {
            padding: 12px 20px 0 20px;
        }
        
        .ef-strand-title {
            font-size: 0.95rem;
        }
        
        .ef-question-text {
            font-size: 0.95rem;
        }
        
        .ef-rating-criteria {
            font-size: 0.9rem;
        }
        
        .ef-form-actions {
            margin-top: 24px;
        }
        
        .ef-btn {
            padding: 8px 20px;
            font-size: 0.95rem;
        }
    }
</style>
