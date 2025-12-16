#!/bin/bash

# PWA Validation Script
# Checks basic PWA requirements before running Lighthouse

echo "🔍 Validating PWA Configuration..."
echo ""

# Colors
GREEN='\033[0;32m'
RED='\033[0;31m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Check if app is running
echo "1. Checking if application is running..."
if curl -s -o /dev/null -w "%{http_code}" http://localhost:8090 | grep -q "200"; then
    echo -e "${GREEN}✓${NC} Application is running at http://localhost:8090"
else
    echo -e "${RED}✗${NC} Application is not running. Start with: docker-compose up"
    exit 1
fi
echo ""

# Check manifest.json
echo "2. Checking manifest.json..."
if curl -s http://localhost:8090/manifest.json | jq . > /dev/null 2>&1; then
    echo -e "${GREEN}✓${NC} manifest.json is valid JSON"
    
    # Check required fields
    MANIFEST=$(curl -s http://localhost:8090/manifest.json)
    
    if echo "$MANIFEST" | jq -e '.name' > /dev/null; then
        echo -e "${GREEN}✓${NC} manifest has 'name' field"
    else
        echo -e "${RED}✗${NC} manifest missing 'name' field"
    fi
    
    if echo "$MANIFEST" | jq -e '.short_name' > /dev/null; then
        echo -e "${GREEN}✓${NC} manifest has 'short_name' field"
    else
        echo -e "${RED}✗${NC} manifest missing 'short_name' field"
    fi
    
    if echo "$MANIFEST" | jq -e '.start_url' > /dev/null; then
        echo -e "${GREEN}✓${NC} manifest has 'start_url' field"
    else
        echo -e "${RED}✗${NC} manifest missing 'start_url' field"
    fi
    
    if echo "$MANIFEST" | jq -e '.display' > /dev/null; then
        echo -e "${GREEN}✓${NC} manifest has 'display' field"
    else
        echo -e "${RED}✗${NC} manifest missing 'display' field"
    fi

    
    # Check icons
    ICON_COUNT=$(echo "$MANIFEST" | jq '.icons | length')
    if [ "$ICON_COUNT" -ge 2 ]; then
        echo -e "${GREEN}✓${NC} manifest has $ICON_COUNT icons (minimum 2 required)"
    else
        echo -e "${RED}✗${NC} manifest has only $ICON_COUNT icons (minimum 2 required)"
    fi
else
    echo -e "${RED}✗${NC} manifest.json is not valid or not accessible"
fi
echo ""

# Check service worker
echo "3. Checking service worker..."
if curl -s -o /dev/null -w "%{http_code}" http://localhost:8090/sw.js | grep -q "200"; then
    echo -e "${GREEN}✓${NC} Service worker (sw.js) is accessible"
else
    echo -e "${RED}✗${NC} Service worker (sw.js) is not accessible"
fi
echo ""

# Check icons
echo "4. Checking PWA icons..."
ICON_SIZES=("72x72" "96x96" "128x128" "144x144" "152x152" "192x192" "384x384" "512x512")
MISSING_ICONS=0

for size in "${ICON_SIZES[@]}"; do
    if curl -s -o /dev/null -w "%{http_code}" "http://localhost:8090/icons/icon-${size}.png" | grep -q "200"; then
        echo -e "${GREEN}✓${NC} Icon ${size} exists"
    else
        echo -e "${RED}✗${NC} Icon ${size} is missing"
        MISSING_ICONS=$((MISSING_ICONS + 1))
    fi
done

if [ $MISSING_ICONS -eq 0 ]; then
    echo -e "${GREEN}✓${NC} All required icons are present"
else
    echo -e "${YELLOW}⚠${NC} $MISSING_ICONS icon(s) missing"
fi
echo ""

# Check meta tags
echo "5. Checking meta tags..."
HTML=$(curl -s http://localhost:8090)

if echo "$HTML" | grep -q 'name="viewport"'; then
    echo -e "${GREEN}✓${NC} Viewport meta tag present"
else
    echo -e "${RED}✗${NC} Viewport meta tag missing"
fi

if echo "$HTML" | grep -q 'name="theme-color"'; then
    echo -e "${GREEN}✓${NC} Theme color meta tag present"
else
    echo -e "${RED}✗${NC} Theme color meta tag missing"
fi

if echo "$HTML" | grep -q 'rel="manifest"'; then
    echo -e "${GREEN}✓${NC} Manifest link present"
else
    echo -e "${RED}✗${NC} Manifest link missing"
fi

if echo "$HTML" | grep -q 'name="description"'; then
    echo -e "${GREEN}✓${NC} Description meta tag present"
else
    echo -e "${RED}✗${NC} Description meta tag missing"
fi
echo ""

echo "✅ Basic PWA validation complete!"
echo ""
echo "Next steps:"
echo "1. Open Chrome and navigate to http://localhost:8090"
echo "2. Press F12 to open DevTools"
echo "3. Go to the Lighthouse tab"
echo "4. Select all categories and run the audit"
echo "5. Document results in LIGHTHOUSE_VALIDATION.md"
echo ""
