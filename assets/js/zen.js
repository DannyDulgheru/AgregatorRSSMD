/**
 * ZEN Mode JavaScript - Preview on Hover
 */

(function() {
    'use strict';
    
    // Visited links tracking (shared with main.js)
    const visitedLinksKey = 'visited_articles';
    
    // Get visited articles from localStorage
    function getVisitedArticles() {
        try {
            const visited = localStorage.getItem(visitedLinksKey);
            return visited ? JSON.parse(visited) : [];
        } catch (e) {
            return [];
        }
    }
    
    // Save visited article
    function markArticleAsVisited(url) {
        const visited = getVisitedArticles();
        if (!visited.includes(url)) {
            visited.push(url);
            // Keep only last 1000 visited articles to avoid storage issues
            if (visited.length > 1000) {
                visited.shift();
            }
            try {
                localStorage.setItem(visitedLinksKey, JSON.stringify(visited));
            } catch (e) {
                console.warn('Could not save visited article to localStorage:', e);
            }
        }
    }
    
    // Apply visited state to all article links
    function applyVisitedState() {
        const visited = getVisitedArticles();
        
        // Mark zen article title links as visited
        document.querySelectorAll('.zen-article-title a').forEach(link => {
            const href = link.getAttribute('href');
            if (!href) return;
            
            // For ZEN mode, we track by URL since it goes to external sources
            if (visited.includes(href)) {
                link.classList.add('visited');
            }
        });
    }
    
    // Track clicks on article links
    function trackArticleClicks() {
        document.querySelectorAll('.zen-article-title a').forEach(link => {
            link.addEventListener('click', function() {
                const href = this.getAttribute('href');
                if (href) {
                    markArticleAsVisited(href);
                    this.classList.add('visited');
                }
            });
        });
    }
    
    // Initialize visited links tracking
    applyVisitedState();
    trackArticleClicks();
    
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
        
        // Reapply visited state and tracking after new content is loaded
        applyVisitedState();
        trackArticleClicks();
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
