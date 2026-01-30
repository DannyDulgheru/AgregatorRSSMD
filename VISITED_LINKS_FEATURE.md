# Visited Links Feature

## Overview
I've added a visited links tracking feature similar to Google's visited links (purple color), so users can easily see which articles they've already opened.

## Changes Made

### 1. CSS Styling (`assets/css/style.css`)

Added purple color styling for visited article links in three locations:

#### Main Article Links
```css
.article-title a.visited,
.article-title a:visited.visited {
    color: #7c3aed;
}

.article-title a.visited:hover {
    color: #6d28d9;
}
```

#### Top News Links
```css
.top-news-title a.visited,
.top-news-title a:visited.visited {
    color: #7c3aed;
}

.top-news-title a.visited:hover {
    color: #6d28d9;
}
```

#### ZEN Mode Links
```css
.zen-article-title a.visited,
.zen-article-title a:visited.visited {
    color: #7c3aed;
}

.zen-article-title a.visited:hover {
    color: #6d28d9;
}
```

### 2. JavaScript Tracking (`assets/js/main.js`)

Added visited links tracking functionality for the main page:

- **Storage**: Uses `localStorage` with key `visited_articles`
- **Tracking**: Stores article IDs when links are clicked
- **Limit**: Keeps only last 1000 visited articles to prevent storage issues
- **Application**: Automatically applies `.visited` class to previously viewed article links
- **Persistence**: Survives page refreshes and browser sessions

### 3. ZEN Mode Tracking (`assets/js/zen.js`)

Added similar functionality for ZEN Mode:

- **Difference**: Tracks external URLs instead of article IDs (since ZEN mode links to source websites)
- **Reattachment**: Automatically reapplies visited state when content is refreshed or dynamically loaded

## How It Works

1. **On Page Load**: 
   - JavaScript checks `localStorage` for previously visited articles
   - Applies `.visited` class to matching article links
   - Links turn purple if they were previously clicked

2. **On Click**:
   - When user clicks an article link, the article ID (or URL for ZEN mode) is saved to `localStorage`
   - The `.visited` class is immediately applied to the link
   - The link color changes to purple

3. **Persistence**:
   - Data is stored in browser's `localStorage`
   - Persists across page refreshes and browser sessions
   - User's visited history stays on their device (privacy-friendly)

## Visual Effect

- **Unvisited links**: Default dark color (`var(--text-primary)`)
- **Visited links**: Purple color (`#7c3aed`) - similar to Google's visited links
- **Hover states**: Slightly darker purple on hover (`#6d28d9`)

## Browser Support

- Works in all modern browsers that support `localStorage`
- Gracefully degrades if `localStorage` is not available (no errors, just won't track)

## Privacy

- All data is stored locally in the user's browser
- No server-side tracking
- User can clear their visited history by clearing browser's `localStorage`

## Testing

The feature is now active. To test:

1. Open http://localhost:8000
2. Click on any article title
3. Go back to the homepage
4. The clicked article should now appear in purple
5. Refresh the page - the purple color should persist

## Future Enhancements

Potential improvements:
- Add a "Clear visited history" button in settings
- Add visual indicator showing total visited articles
- Add date-based expiration for old visited links
- Sync visited links across devices (would require backend)
