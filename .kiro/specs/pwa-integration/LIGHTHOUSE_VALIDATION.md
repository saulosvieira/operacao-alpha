# Lighthouse PWA Validation Guide

## Overview

This document provides a comprehensive guide for validating the Alfa Quest PWA using Google Lighthouse. The application should achieve scores >90 in all categories: Performance, Accessibility, Best Practices, SEO, and PWA.

## Prerequisites

- Application running at http://localhost:8090
- Google Chrome browser (latest version)
- Chrome DevTools

## Running Lighthouse Audit

### Step 1: Open Chrome DevTools

1. Navigate to http://localhost:8090 in Chrome
2. Press F12 or right-click and select "Inspect"
3. Click on the "Lighthouse" tab (may be under >> menu)

### Step 2: Configure Lighthouse

1. Select categories to audit:
   - ✅ Performance
   - ✅ Accessibility
   - ✅ Best Practices
   - ✅ SEO
   - ✅ Progressive Web App

2. Select device:
   - Run audit for both Desktop and Mobile
   - Start with Desktop, then repeat for Mobile

3. Click "Analyze page load"

### Step 3: Wait for Results

Lighthouse will:
- Load the page
- Run various tests
- Generate a comprehensive report
- Provide scores and recommendations

## Expected Scores (Target: >90 for all)


### Performance (Target: >90)

**Key Metrics:**
- First Contentful Paint (FCP): < 1.8s
- Largest Contentful Paint (LCP): < 2.5s
- Total Blocking Time (TBT): < 200ms
- Cumulative Layout Shift (CLS): < 0.1
- Speed Index: < 3.4s

**Current Optimizations:**
- ✅ Code splitting configured in Vite
- ✅ Lazy loading for routes
- ✅ Gzip compression in Nginx
- ✅ Cache busting with hashed filenames
- ✅ Vendor chunks separated (react, ui-vendor)

**Potential Issues:**
- Large bundle sizes (check with `npm run build`)
- Unoptimized images
- Blocking resources
- Unused JavaScript

### Accessibility (Target: >90)

**Key Requirements:**
- ✅ Color contrast ratios meet WCAG AA standards
- ✅ Form labels properly associated
- ✅ Alt text on images
- ✅ ARIA attributes where needed
- ✅ Keyboard navigation support

**Current Implementation:**
- All images have alt text
- shadcn/ui components include proper ARIA attributes
- Form inputs have associated labels
- Focus states visible

**Potential Issues:**
- Missing ARIA labels on custom components
- Insufficient color contrast in some areas
- Missing skip links


### Best Practices (Target: >90)

**Key Requirements:**
- ✅ HTTPS in production (currently HTTP in dev)
- ✅ No console errors
- ✅ Updated libraries
- ✅ Secure cookies
- ✅ No deprecated APIs

**Current Implementation:**
- Modern React 18
- Latest dependencies
- Proper error handling
- Secure authentication with Sanctum

**Potential Issues:**
- HTTP in development (expected, will be HTTPS in production)
- Console warnings from development mode
- Mixed content warnings

### SEO (Target: >90)

**Key Requirements:**
- ✅ Meta description present
- ✅ Viewport meta tag configured
- ✅ Page titles present
- ✅ Semantic HTML
- ✅ Crawlable links

**Current Implementation:**
- Meta tags in app.blade.php:
  - Title: "Operação ALFA - Simulados Militares"
  - Description present
  - Viewport configured
  - Open Graph tags
  - Twitter Card tags
- Semantic HTML structure

**Potential Issues:**
- Dynamic page titles not updating per route
- Missing canonical URLs
- No sitemap.xml


### PWA (Target: >90)

**Key Requirements:**
- ✅ Valid manifest.json
- ✅ Service Worker registered
- ✅ Icons in multiple sizes
- ✅ Theme color configured
- ✅ Installable
- ✅ Works offline (basic)

**Current Implementation:**
- Manifest at /manifest.json with:
  - name, short_name, description
  - start_url: "/"
  - display: "standalone"
  - theme_color: "#1e40af"
  - background_color: "#ffffff"
  - 8 icon sizes (72x72 to 512x512)
  - Shortcuts configured
- Service Worker at /sw.js:
  - Caching strategy
  - Push notification support
  - Offline fallback
- Meta tags:
  - theme-color
  - apple-mobile-web-app-capable
  - Apple touch icons

**Potential Issues:**
- Service Worker caching strategy may need tuning
- Offline functionality limited
- No splash screens configured

## Validation Checklist

### Before Running Lighthouse

- [ ] Application is running at http://localhost:8090
- [ ] Build is up to date (`npm run build`)
- [ ] No console errors visible
- [ ] Service Worker registered (check DevTools > Application > Service Workers)
- [ ] Manifest loads correctly (check DevTools > Application > Manifest)


### During Lighthouse Audit

- [ ] Run audit in Desktop mode
- [ ] Run audit in Mobile mode
- [ ] Save both reports
- [ ] Review all categories
- [ ] Note any scores < 90
- [ ] Review opportunities and diagnostics

### After Lighthouse Audit

- [ ] Document scores in this file
- [ ] Create issues for any critical problems
- [ ] Prioritize fixes based on impact
- [ ] Re-run audit after fixes

## Common Issues and Fixes

### Performance Issues

**Issue: Large JavaScript bundles**
- Solution: Review bundle analyzer, split large dependencies
- Check: `npm run build` output for bundle sizes

**Issue: Unoptimized images**
- Solution: Compress images, use modern formats (WebP)
- Check: Images in /public/icons/

**Issue: Render-blocking resources**
- Solution: Defer non-critical CSS/JS, inline critical CSS
- Check: Vite build configuration

### Accessibility Issues

**Issue: Low contrast ratios**
- Solution: Update colors in tailwind.config.ts
- Check: Text on colored backgrounds

**Issue: Missing ARIA labels**
- Solution: Add aria-label to icon buttons
- Check: Navigation, buttons without text


### PWA Issues

**Issue: Service Worker not registered**
- Solution: Check main.tsx registration code
- Check: Browser console for errors

**Issue: Manifest not valid**
- Solution: Validate manifest.json syntax
- Check: DevTools > Application > Manifest

**Issue: Icons not loading**
- Solution: Verify icon files exist in /public/icons/
- Check: Network tab for 404 errors

## Manual Testing Steps

### 1. Service Worker Registration

```bash
# Open browser console and check:
navigator.serviceWorker.getRegistrations().then(regs => console.log(regs))
```

Expected: Should show registered service worker at /sw.js

### 2. Manifest Validation

1. Open DevTools > Application > Manifest
2. Verify all fields are populated
3. Click on each icon to verify it loads
4. Check for warnings

### 3. Install Prompt

1. Look for install icon in address bar (desktop)
2. Or check for "Add to Home Screen" banner (mobile)
3. Try installing the app
4. Verify it opens in standalone mode

### 4. Offline Functionality

1. Open DevTools > Network
2. Check "Offline" checkbox
3. Refresh the page
4. Verify basic content loads from cache


## Lighthouse Audit Results

### Desktop Audit

**Date:** _To be filled after running audit_

**Scores:**
- Performance: ___ / 100
- Accessibility: ___ / 100
- Best Practices: ___ / 100
- SEO: ___ / 100
- PWA: ___ / 100

**Key Metrics:**
- FCP: ___ s
- LCP: ___ s
- TBT: ___ ms
- CLS: ___
- Speed Index: ___ s

**Issues Found:**
1. _List critical issues here_
2. _..._

**Opportunities:**
1. _List optimization opportunities_
2. _..._

### Mobile Audit

**Date:** _To be filled after running audit_

**Scores:**
- Performance: ___ / 100
- Accessibility: ___ / 100
- Best Practices: ___ / 100
- SEO: ___ / 100
- PWA: ___ / 100

**Key Metrics:**
- FCP: ___ s
- LCP: ___ s
- TBT: ___ ms
- CLS: ___
- Speed Index: ___ s

**Issues Found:**
1. _List critical issues here_
2. _..._

**Opportunities:**
1. _List optimization opportunities_
2. _..._


## Action Items

Based on Lighthouse results, prioritize fixes:

### Critical (Must Fix)
- [ ] _Issues that prevent PWA installation_
- [ ] _Accessibility violations_
- [ ] _Security issues_

### High Priority (Should Fix)
- [ ] _Performance issues affecting user experience_
- [ ] _SEO problems_
- [ ] _Best practice violations_

### Medium Priority (Nice to Have)
- [ ] _Minor performance optimizations_
- [ ] _Additional PWA features_
- [ ] _Enhanced offline support_

### Low Priority (Future Improvements)
- [ ] _Advanced caching strategies_
- [ ] _Background sync_
- [ ] _Advanced PWA features_

## Next Steps

1. Run Lighthouse audit following the steps above
2. Fill in the results sections
3. Create GitHub issues for any problems found
4. Implement fixes for critical and high-priority items
5. Re-run Lighthouse to verify improvements
6. Document final scores in task completion

## Resources

- [Lighthouse Documentation](https://developers.google.com/web/tools/lighthouse)
- [PWA Checklist](https://web.dev/pwa-checklist/)
- [Web Vitals](https://web.dev/vitals/)
- [Accessibility Guidelines](https://www.w3.org/WAI/WCAG21/quickref/)

