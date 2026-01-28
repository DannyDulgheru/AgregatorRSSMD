/**
 * Main JavaScript for News Aggregator
 */

(function() {
    'use strict';
    
    // View mode management
    const viewModeKey = 'view_mode';
    const articlesContainer = document.getElementById('articlesContainer');
    const viewButtons = document.querySelectorAll('.view-btn');
    
    if (!articlesContainer) return;
    
    // Get saved view mode or default to grid
    let currentViewMode = localStorage.getItem(viewModeKey) || 'grid';
    
    // Apply view mode
    function applyViewMode(mode) {
        articlesContainer.setAttribute('data-view-mode', mode);
        const articleCards = articlesContainer.querySelectorAll('.article-card');
        articleCards.forEach(card => {
            card.setAttribute('data-view-mode', mode);
        });
        
        // Update active button
        viewButtons.forEach(btn => {
            if (btn.dataset.view === mode) {
                btn.classList.add('active');
            } else {
                btn.classList.remove('active');
            }
        });
        
        // Save to localStorage
        localStorage.setItem(viewModeKey, mode);
        
        // Set cookie for server-side fallback
        document.cookie = `view_mode=${mode}; path=/; max-age=31536000`; // 1 year
    }
    
    // Initialize view mode
    applyViewMode(currentViewMode);
    
    // Handle view button clicks (both top and bottom)
    document.querySelectorAll('.view-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const mode = this.dataset.view;
            applyViewMode(mode);
            // Update all view buttons
            document.querySelectorAll('.view-btn').forEach(b => {
                if (b.dataset.view === mode) {
                    b.classList.add('active');
                } else {
                    b.classList.remove('active');
                }
            });
        });
    });
    
    // Sync search inputs
    const searchTop = document.getElementById('search');
    const searchBottom = document.getElementById('search-bottom');
    if (searchTop && searchBottom) {
        searchTop.addEventListener('input', function() {
            searchBottom.value = this.value;
        });
        searchBottom.addEventListener('input', function() {
            searchTop.value = this.value;
        });
    }
    
    // Lazy loading images
    if ('IntersectionObserver' in window) {
        const imageObserver = new IntersectionObserver((entries, observer) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const img = entry.target;
                    if (img.dataset.src) {
                        img.src = img.dataset.src;
                        img.removeAttribute('data-src');
                        observer.unobserve(img);
                    }
                }
            });
        });
        
        document.querySelectorAll('img[data-src]').forEach(img => {
            imageObserver.observe(img);
        });
    }
    
    // Smooth scroll for pagination links
    document.querySelectorAll('.pagination-btn').forEach(link => {
        link.addEventListener('click', function(e) {
            // Allow normal navigation, just add smooth scroll
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });
    });
    
})();
