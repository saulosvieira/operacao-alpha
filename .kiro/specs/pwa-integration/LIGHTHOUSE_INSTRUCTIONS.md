# Lighthouse Audit Instructions - Task 24.2

## Quick Start

This guide will walk you through running a Lighthouse audit on the Alfa Quest PWA application.

## Prerequisites Check

Run the validation script first:
```bash
./.kiro/specs/pwa-integration/validate-pwa.sh
```

This will verify:
- ✅ Application is running
- ✅ Manifest.json is accessible
- ✅ Service Worker is registered
- ✅ All PWA icons exist
- ✅ Required meta tags are present

## Step-by-Step Lighthouse Audit

### 1. Prepare the Application

**Option A: Development Mode (Current)**
- Application is already running at http://localhost:8090
- Using Vite dev server for hot reload
- Good for: Quick testing and development

**Option B: Production Build (Recommended for accurate scores)**
```bash
# Build the production assets
cd laravel
npm run build

# Restart the application
docker-compose restart simulados-app simulados-webserver
```

### 2. Open Chrome DevTools

1. Open Google Chrome
2. Navigate to: http://localhost:8090
3. Press `F12` or right-click and select "Inspect"
4. Click on the **"Lighthouse"** tab
   - If you don't see it, click the `>>` icon and select "Lighthouse"

### 3. Configure Lighthouse

Select the following options:

**Categories to audit:**
- ✅ Performance
- ✅ Accessibility  
- ✅ Best Practices
- ✅ SEO
- ✅ Progressive Web App

**Device:**
- Start with **Desktop**
- Then repeat for **Mobile**

**Mode:**
- Navigation (default)

### 4. Run the Audit

1. Click **"Analyze page load"**
2. Wait 30-60 seconds for the audit to complete
3. Review the results


## Understanding the Results

### Performance Score (Target: >90)

**Key Metrics to Check:**
- **First Contentful Paint (FCP)**: Should be < 1.8s
  - When the first text/image appears
- **Largest Contentful Paint (LCP)**: Should be < 2.5s
  - When the main content is visible
- **Total Blocking Time (TBT)**: Should be < 200ms
  - Time the page is blocked from user interaction
- **Cumulative Layout Shift (CLS)**: Should be < 0.1
  - Visual stability (elements shouldn't jump around)
- **Speed Index**: Should be < 3.4s
  - How quickly content is visually displayed

**Common Issues:**
- Large JavaScript bundles
- Unoptimized images
- Render-blocking resources
- No text compression

### Accessibility Score (Target: >90)

**What Lighthouse Checks:**
- Color contrast ratios (WCAG AA standard)
- Form labels and ARIA attributes
- Alt text on images
- Keyboard navigation
- Focus indicators

**Current Status:**
- ✅ All images have alt text
- ✅ Form inputs have labels
- ✅ Using shadcn/ui with proper ARIA
- ⚠️ May need to check custom components

### Best Practices Score (Target: >90)

**What Lighthouse Checks:**
- HTTPS usage (will be HTTP in dev, HTTPS in prod)
- No browser errors in console
- Updated libraries
- Secure cookies
- No deprecated APIs

**Current Status:**
- ✅ Modern React 18
- ✅ Latest dependencies
- ⚠️ HTTP in development (expected)

### SEO Score (Target: >90)

**What Lighthouse Checks:**
- Meta description
- Viewport configuration
- Page titles
- Semantic HTML
- Crawlable links

**Current Status:**
- ✅ Meta description present
- ✅ Viewport configured
- ✅ Open Graph tags
- ⚠️ Dynamic titles may need improvement


### PWA Score (Target: >90)

**What Lighthouse Checks:**
- Valid web app manifest
- Service Worker registered
- Works offline
- Installable
- Configured for mobile
- Icons in multiple sizes

**Current Status:**
- ✅ Manifest.json with all required fields
- ✅ Service Worker at /sw.js
- ✅ 8 icon sizes (72x72 to 512x512)
- ✅ Theme color configured
- ✅ Apple touch icons
- ✅ Offline fallback

## Recording Results

### Desktop Audit Results

After running the Desktop audit, record the scores:

```
Date: [Fill in date]
URL: http://localhost:8090
Mode: Desktop

Scores:
- Performance: ___ / 100
- Accessibility: ___ / 100
- Best Practices: ___ / 100
- SEO: ___ / 100
- PWA: ___ / 100

Key Metrics:
- FCP: ___ s
- LCP: ___ s
- TBT: ___ ms
- CLS: ___
- Speed Index: ___ s
```

### Mobile Audit Results

After running the Mobile audit, record the scores:

```
Date: [Fill in date]
URL: http://localhost:8090
Mode: Mobile

Scores:
- Performance: ___ / 100
- Accessibility: ___ / 100
- Best Practices: ___ / 100
- SEO: ___ / 100
- PWA: ___ / 100

Key Metrics:
- FCP: ___ s
- LCP: ___ s
- TBT: ___ ms
- CLS: ___
- Speed Index: ___ s
```


## Analyzing Issues

### If Performance < 90

**Check the "Opportunities" section:**
- Eliminate render-blocking resources
- Properly size images
- Minify CSS/JavaScript
- Enable text compression
- Reduce unused JavaScript

**Check the "Diagnostics" section:**
- Avoid enormous network payloads
- Minimize main-thread work
- Reduce JavaScript execution time
- Avoid large layout shifts

### If Accessibility < 90

**Common issues:**
- Background and foreground colors do not have sufficient contrast
- Form elements do not have associated labels
- Image elements do not have [alt] attributes
- Links do not have a discernible name
- Buttons do not have an accessible name

### If Best Practices < 90

**Common issues:**
- Does not use HTTPS (expected in dev)
- Browser errors logged to console
- Uses deprecated APIs
- Includes front-end JavaScript libraries with known security vulnerabilities

### If SEO < 90

**Common issues:**
- Document does not have a meta description
- Document does not have a valid `rel=canonical`
- Links are not crawlable
- Page is blocked from indexing

### If PWA < 90

**Common issues:**
- Does not register a service worker
- Web app manifest does not meet installability requirements
- Is not configured for a custom splash screen
- Does not set a theme color for the address bar
- Content is not sized correctly for the viewport

## Taking Action

### 1. Document Issues

For each score < 90, document:
- The specific issue
- The impact (Critical/High/Medium/Low)
- Suggested fix
- Estimated effort

### 2. Create Issues (if needed)

For critical problems, create GitHub issues:
```bash
# Example
Title: [Lighthouse] Performance: Large JavaScript bundle size
Description: Lighthouse reports main bundle is 2.5MB, should be < 500KB
Priority: High
Labels: performance, lighthouse
```

### 3. Implement Fixes

Prioritize fixes based on:
1. **Critical**: Prevents PWA installation or major functionality
2. **High**: Significantly impacts user experience
3. **Medium**: Noticeable but not blocking
4. **Low**: Minor improvements

### 4. Re-run Audit

After implementing fixes:
1. Rebuild the application (if needed)
2. Clear browser cache
3. Run Lighthouse again
4. Compare scores
5. Repeat until all scores > 90


## Quick Fixes for Common Issues

### Performance Improvements

**1. Optimize Images**
```bash
# Install image optimization tool
npm install -D imagemin imagemin-pngquant

# Optimize icons
cd laravel/public/icons
# Use online tools or imagemin to compress
```

**2. Review Bundle Sizes**
```bash
cd laravel
npm run build

# Check output for bundle sizes
# Look for files > 500KB
```

**3. Add Preload for Critical Resources**
```html
<!-- In app.blade.php -->
<link rel="preload" href="/path/to/critical.css" as="style">
<link rel="preload" href="/path/to/critical.js" as="script">
```

### Accessibility Improvements

**1. Add ARIA Labels to Icon Buttons**
```tsx
<Button aria-label="Close menu">
  <X className="h-4 w-4" />
</Button>
```

**2. Ensure Sufficient Color Contrast**
```tsx
// Check text colors against backgrounds
// Use tools like: https://webaim.org/resources/contrastchecker/
```

**3. Add Skip Links**
```tsx
<a href="#main-content" className="sr-only focus:not-sr-only">
  Skip to main content
</a>
```

### SEO Improvements

**1. Add Dynamic Page Titles**
```tsx
// In each page component
useEffect(() => {
  document.title = 'Page Name - Alfa Quest';
}, []);
```

**2. Add Canonical URLs**
```html
<!-- In app.blade.php -->
<link rel="canonical" href="{{ url()->current() }}" />
```

### PWA Improvements

**1. Improve Offline Support**
```javascript
// In sw.js, expand cached URLs
const urlsToCache = [
  '/',
  '/manifest.json',
  '/offline.html', // Create this page
  // Add more critical pages
];
```

**2. Add Splash Screens**
```html
<!-- In app.blade.php -->
<link rel="apple-touch-startup-image" href="/splash-640x1136.png">
```


## Verification Checklist

Before marking task as complete, verify:

### Pre-Audit
- [ ] Application is running at http://localhost:8090
- [ ] No console errors visible
- [ ] Service Worker is registered (check DevTools > Application)
- [ ] Manifest loads correctly (check DevTools > Application > Manifest)
- [ ] All icons are accessible (check Network tab)

### Audit Execution
- [ ] Ran Lighthouse audit in Desktop mode
- [ ] Ran Lighthouse audit in Mobile mode
- [ ] Saved/exported both reports
- [ ] Documented scores in LIGHTHOUSE_VALIDATION.md

### Score Verification
- [ ] Performance score documented
- [ ] Accessibility score documented
- [ ] Best Practices score documented
- [ ] SEO score documented
- [ ] PWA score documented

### Issue Management
- [ ] All issues < 90 documented
- [ ] Critical issues identified
- [ ] GitHub issues created (if needed)
- [ ] Fixes prioritized

### Post-Fix (if fixes were needed)
- [ ] Fixes implemented
- [ ] Re-ran Lighthouse audit
- [ ] Verified improvements
- [ ] Updated documentation

## Expected Results

Based on current implementation, expected scores:

**Desktop:**
- Performance: 85-95 (may vary based on build)
- Accessibility: 90-100 (good implementation)
- Best Practices: 85-95 (HTTP in dev affects score)
- SEO: 90-100 (good meta tags)
- PWA: 90-100 (complete implementation)

**Mobile:**
- Performance: 70-85 (mobile typically lower)
- Accessibility: 90-100 (same as desktop)
- Best Practices: 85-95 (same as desktop)
- SEO: 90-100 (same as desktop)
- PWA: 90-100 (same as desktop)

## Completion Criteria

Task 24.2 is complete when:
1. ✅ Lighthouse audit run for both Desktop and Mobile
2. ✅ All scores documented in LIGHTHOUSE_VALIDATION.md
3. ✅ Critical issues (if any) documented
4. ✅ Action plan created for scores < 90
5. ✅ Task marked as complete in tasks.md

## Next Steps

After completing this task:
1. Review task 24.3 (Integration tests - optional)
2. Proceed to task 24.5 (Test PWA installation on real devices)
3. Complete final documentation

## Resources

- [Lighthouse Documentation](https://developers.google.com/web/tools/lighthouse)
- [Web.dev Lighthouse Guide](https://web.dev/lighthouse-performance/)
- [PWA Checklist](https://web.dev/pwa-checklist/)
- [Chrome DevTools](https://developer.chrome.com/docs/devtools/)

