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
    
    // Compact view hover preview
    const previewTooltip = document.getElementById('compactPreview');
    if (previewTooltip) {
        const previewImage = previewTooltip.querySelector('.preview-image');
        const previewTitle = previewTooltip.querySelector('.preview-title');
        const previewText = previewTooltip.querySelector('.preview-text');
        
        let isPreviewVisible = false;
        let currentArticle = null;
        
        function showPreview(article, event) {
            if (articlesContainer.getAttribute('data-view-mode') !== 'compact') {
                return;
            }
            
            const imageUrl = article.dataset.articleImage;
            const title = article.dataset.articleTitle;
            const content = article.dataset.articleContent;
            
            // Update preview content
            if (imageUrl) {
                previewImage.style.backgroundImage = `url('${imageUrl}')`;
                previewTooltip.classList.remove('no-image');
            } else {
                previewImage.style.backgroundImage = '';
                previewTooltip.classList.add('no-image');
            }
            
            previewTitle.textContent = title || '';
            previewText.textContent = content || 'Fără descriere disponibilă';
            
            // Position and show
            updatePreviewPosition(event);
            previewTooltip.classList.add('visible');
            isPreviewVisible = true;
            currentArticle = article;
        }
        
        function hidePreview() {
            previewTooltip.classList.remove('visible');
            isPreviewVisible = false;
            currentArticle = null;
        }
        
        function updatePreviewPosition(event) {
            const offsetX = 20;
            const offsetY = 20;
            const tooltipWidth = previewTooltip.offsetWidth;
            const tooltipHeight = previewTooltip.offsetHeight;
            
            let left = event.clientX + offsetX;
            let top = event.clientY + offsetY;
            
            // Check if tooltip goes off right edge
            if (left + tooltipWidth > window.innerWidth) {
                left = event.clientX - tooltipWidth - offsetX;
            }
            
            // Check if tooltip goes off bottom edge
            if (top + tooltipHeight > window.innerHeight) {
                top = event.clientY - tooltipHeight - offsetY;
            }
            
            // Ensure it doesn't go off left or top edge
            left = Math.max(10, left);
            top = Math.max(10, top);
            
            previewTooltip.style.left = `${left}px`;
            previewTooltip.style.top = `${top}px`;
        }
        
        // Attach event listeners to article cards
        function attachPreviewListeners() {
            document.querySelectorAll('.article-card').forEach(article => {
                article.addEventListener('mouseenter', function(e) {
                    showPreview(this, e);
                });
                
                article.addEventListener('mousemove', function(e) {
                    if (isPreviewVisible && currentArticle === this) {
                        updatePreviewPosition(e);
                    }
                });
                
                article.addEventListener('mouseleave', function() {
                    hidePreview();
                });
            });
        }
        
        // Initial attach
        attachPreviewListeners();
        
        // Re-attach when view mode changes
        viewButtons.forEach(btn => {
            btn.addEventListener('click', function() {
                setTimeout(() => {
                    if (this.dataset.view === 'compact') {
                        attachPreviewListeners();
                    } else {
                        hidePreview();
                    }
                }, 100);
            });
        });
    }
    
})();
