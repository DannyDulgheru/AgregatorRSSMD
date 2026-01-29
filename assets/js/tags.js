/**
 * Tags Page JavaScript - Preview on Hover
 */

(function() {
    'use strict';
    
    const previewTooltip = document.getElementById('tagPreview');
    if (!previewTooltip) return;
    
    const previewTitle = previewTooltip.querySelector('.tag-preview-title');
    const previewCount = previewTooltip.querySelector('.tag-preview-count');
    const articlesList = previewTooltip.querySelector('.tag-articles-list');
    
    let isPreviewVisible = false;
    let currentTag = null;
    
    function showTagPreview(tagCard, event) {
        const tagName = tagCard.dataset.tagName;
        const tagCount = tagCard.dataset.tagCount;
        const articlesData = tagCard.dataset.tagArticles;
        
        let articles = [];
        try {
            articles = JSON.parse(articlesData) || [];
        } catch(e) {
            articles = [];
        }
        
        // Update preview content
        previewTitle.textContent = tagName;
        previewCount.textContent = `${tagCount} articole`;
        
        // Clear and populate articles list
        articlesList.innerHTML = '';
        
        if (articles.length > 0) {
            articles.forEach(title => {
                const li = document.createElement('li');
                li.textContent = title;
                articlesList.appendChild(li);
            });
        } else {
            const li = document.createElement('li');
            li.textContent = 'Nu există articole disponibile';
            li.style.fontStyle = 'italic';
            li.style.color = 'var(--text-tertiary)';
            articlesList.appendChild(li);
        }
        
        // Position and show
        updatePreviewPosition(event);
        previewTooltip.classList.add('visible');
        isPreviewVisible = true;
        currentTag = tagCard;
    }
    
    function hideTagPreview() {
        previewTooltip.classList.remove('visible');
        isPreviewVisible = false;
        currentTag = null;
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
    
    // Attach event listeners to tag cards
    document.querySelectorAll('.tag-card-modern').forEach(tagCard => {
        tagCard.addEventListener('mouseenter', function(e) {
            showTagPreview(this, e);
        });
        
        tagCard.addEventListener('mousemove', function(e) {
            if (isPreviewVisible && currentTag === this) {
                updatePreviewPosition(e);
            }
        });
        
        tagCard.addEventListener('mouseleave', function() {
            hideTagPreview();
        });
    });
    
})();
