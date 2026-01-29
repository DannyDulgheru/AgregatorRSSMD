/**
 * ZEN Mode JavaScript - Preview on Hover
 */

(function() {
    'use strict';
    
    const previewTooltip = document.getElementById('zenPreview');
    if (!previewTooltip) return;
    
    const previewImage = previewTooltip.querySelector('.zen-preview-image');
    const previewTitle = previewTooltip.querySelector('.zen-preview-title');
    const previewText = previewTooltip.querySelector('.zen-preview-text');
    
    let isPreviewVisible = false;
    let currentArticle = null;
    
    function showPreview(article, event) {
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
    
    function attachPreviewListeners() {
        document.querySelectorAll('.zen-article-item').forEach(article => {
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
    
    // Reattach after refresh
    window.reattachPreviewListeners = attachPreviewListeners;
    
    // Simple Refresh functionality
    const refreshBtn = document.getElementById('zenRefresh');
    
    if (refreshBtn) {
        refreshBtn.addEventListener('click', function() {
            window.location.reload();
        });
    }
    
})();
