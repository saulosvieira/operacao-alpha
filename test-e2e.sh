#!/bin/bash

# End-to-End Test Script for PWA Integration
# This script tests all critical functionality of the application

# Don't exit on error - we want to run all tests
set +e

BASE_URL="http://localhost:8090"
API_URL="$BASE_URL/api"

echo "========================================="
echo "End-to-End Testing - PWA Integration"
echo "========================================="
echo ""

# Colors for output
GREEN='\033[0;32m'
RED='\033[0;31m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

pass_count=0
fail_count=0

test_pass() {
    echo -e "${GREEN}✓ PASS${NC}: $1"
    ((pass_count++))
}

test_fail() {
    echo -e "${RED}✗ FAIL${NC}: $1"
    ((fail_count++))
}

test_info() {
    echo -e "${YELLOW}ℹ INFO${NC}: $1"
}

echo "1. Testing Application Accessibility"
echo "-------------------------------------"

# Test 1.1: Main page loads
HTTP_CODE=$(curl -s -o /dev/null -w "%{http_code}" $BASE_URL)
if [ "$HTTP_CODE" = "200" ]; then
    test_pass "Main page loads (HTTP 200)"
else
    test_fail "Main page loads (got HTTP $HTTP_CODE)"
fi

# Test 1.2: React root div exists
if curl -s $BASE_URL | grep -q '<div id="root">'; then
    test_pass "React root div present"
else
    test_fail "React root div not found"
fi

# Test 1.3: Vite scripts loaded
if curl -s $BASE_URL | grep -q 'vite'; then
    test_pass "Vite integration detected"
else
    test_fail "Vite integration not detected"
fi

echo ""
echo "2. Testing Authentication API"
echo "-------------------------------------"

# Test 2.1: Login with invalid credentials
RESPONSE=$(curl -s -X POST $API_URL/login \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{"email":"invalid@test.com","password":"wrongpass"}')

if echo "$RESPONSE" | grep -qi "credenciais\|invalid\|message"; then
    test_pass "Login rejects invalid credentials"
else
    test_fail "Login error message not correct"
fi

# Test 2.2: Login with valid credentials
RESPONSE=$(curl -s -X POST $API_URL/login \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{"email":"admin@simulados.com","password":"admin123"}')

if echo "$RESPONSE" | grep -q '"token"'; then
    test_pass "Login succeeds with valid credentials"
    TOKEN=$(echo "$RESPONSE" | grep -o '"token":"[^"]*"' | cut -d'"' -f4)
    test_info "Token obtained: ${TOKEN:0:20}..."
else
    test_fail "Login did not return token"
    TOKEN=""
fi

# Test 2.3: Access protected route without token
HTTP_CODE=$(curl -s -o /dev/null -w "%{http_code}" $API_URL/me \
  -H "Accept: application/json")

if [ "$HTTP_CODE" = "401" ]; then
    test_pass "Protected route returns 401 without token"
else
    test_fail "Protected route should return 401 (got $HTTP_CODE)"
fi

# Test 2.4: Access protected route with token
if [ -n "$TOKEN" ]; then
    HTTP_CODE=$(curl -s -o /dev/null -w "%{http_code}" $API_URL/me \
      -H "Accept: application/json" \
      -H "Authorization: Bearer $TOKEN")
    
    if [ "$HTTP_CODE" = "200" ]; then
        test_pass "Protected route accessible with valid token"
    else
        test_fail "Protected route should return 200 with token (got $HTTP_CODE)"
    fi
fi

echo ""
echo "3. Testing Career API"
echo "-------------------------------------"

# Test 3.1: List careers
if [ -n "$TOKEN" ]; then
    RESPONSE=$(curl -s $API_URL/careers \
      -H "Accept: application/json" \
      -H "Authorization: Bearer $TOKEN")
    
    if echo "$RESPONSE" | grep -q '"data"'; then
        test_pass "Careers API returns data"
    else
        test_fail "Careers API did not return expected format"
    fi
fi

echo ""
echo "4. Testing Exam API"
echo "-------------------------------------"

# Test 4.1: List exams
if [ -n "$TOKEN" ]; then
    RESPONSE=$(curl -s $API_URL/exams \
      -H "Accept: application/json" \
      -H "Authorization: Bearer $TOKEN")
    
    if echo "$RESPONSE" | grep -q '"data"'; then
        test_pass "Exams API returns data"
        # Extract first exam ID if available
        EXAM_ID=$(echo "$RESPONSE" | grep -o '"id":"[^"]*"' | head -1 | cut -d'"' -f4)
        if [ -n "$EXAM_ID" ]; then
            test_info "Found exam ID: $EXAM_ID"
        fi
    else
        test_fail "Exams API did not return expected format"
    fi
fi

# Test 4.2: Get exam details
if [ -n "$TOKEN" ] && [ -n "$EXAM_ID" ]; then
    HTTP_CODE=$(curl -s -o /dev/null -w "%{http_code}" $API_URL/exams/$EXAM_ID \
      -H "Accept: application/json" \
      -H "Authorization: Bearer $TOKEN")
    
    if [ "$HTTP_CODE" = "200" ]; then
        test_pass "Exam details API works"
    else
        test_fail "Exam details API returned $HTTP_CODE"
    fi
fi

echo ""
echo "5. Testing Ranking API"
echo "-------------------------------------"

if [ -n "$TOKEN" ]; then
    HTTP_CODE=$(curl -s -o /dev/null -w "%{http_code}" $API_URL/ranking \
      -H "Accept: application/json" \
      -H "Authorization: Bearer $TOKEN")
    
    if [ "$HTTP_CODE" = "200" ]; then
        test_pass "Ranking API accessible"
    else
        test_fail "Ranking API returned $HTTP_CODE"
    fi
fi

echo ""
echo "6. Testing Performance API"
echo "-------------------------------------"

if [ -n "$TOKEN" ]; then
    HTTP_CODE=$(curl -s -o /dev/null -w "%{http_code}" $API_URL/performance/statistics \
      -H "Accept: application/json" \
      -H "Authorization: Bearer $TOKEN")
    
    if [ "$HTTP_CODE" = "200" ]; then
        test_pass "Performance statistics API accessible"
    else
        test_fail "Performance statistics API returned $HTTP_CODE"
    fi
fi

echo ""
echo "7. Testing Approved API"
echo "-------------------------------------"

if [ -n "$TOKEN" ]; then
    HTTP_CODE=$(curl -s -o /dev/null -w "%{http_code}" $API_URL/approved \
      -H "Accept: application/json" \
      -H "Authorization: Bearer $TOKEN")
    
    if [ "$HTTP_CODE" = "200" ]; then
        test_pass "Approved API accessible"
    else
        test_fail "Approved API returned $HTTP_CODE"
    fi
fi

echo ""
echo "8. Testing Subscription API"
echo "-------------------------------------"

if [ -n "$TOKEN" ]; then
    HTTP_CODE=$(curl -s -o /dev/null -w "%{http_code}" $API_URL/plans \
      -H "Accept: application/json" \
      -H "Authorization: Bearer $TOKEN")
    
    if [ "$HTTP_CODE" = "200" ]; then
        test_pass "Plans API accessible"
    else
        test_fail "Plans API returned $HTTP_CODE"
    fi
fi

echo ""
echo "9. Testing PWA Assets"
echo "-------------------------------------"

# Test 9.1: Manifest exists
HTTP_CODE=$(curl -s -o /dev/null -w "%{http_code}" $BASE_URL/manifest.json)
if [ "$HTTP_CODE" = "200" ]; then
    test_pass "PWA manifest accessible"
else
    test_fail "PWA manifest returned $HTTP_CODE"
fi

# Test 9.2: Service Worker exists
HTTP_CODE=$(curl -s -o /dev/null -w "%{http_code}" $BASE_URL/sw.js)
if [ "$HTTP_CODE" = "200" ]; then
    test_pass "Service Worker accessible"
else
    test_fail "Service Worker returned $HTTP_CODE"
fi

# Test 9.3: Icons exist
HTTP_CODE=$(curl -s -o /dev/null -w "%{http_code}" $BASE_URL/icons/icon-192x192.png)
if [ "$HTTP_CODE" = "200" ]; then
    test_pass "PWA icons accessible"
else
    test_fail "PWA icons returned $HTTP_CODE"
fi

echo ""
echo "10. Testing Client-Side Routing"
echo "-------------------------------------"

# Test various routes return the SPA
for route in "/" "/carreiras" "/simulados" "/ranking" "/desempenho" "/aprovados" "/assinar" "/conta"; do
    HTTP_CODE=$(curl -s -o /dev/null -w "%{http_code}" $BASE_URL$route)
    if [ "$HTTP_CODE" = "200" ]; then
        test_pass "Route $route returns 200"
    else
        test_fail "Route $route returned $HTTP_CODE"
    fi
done

# Test 404 for invalid route
HTTP_CODE=$(curl -s -o /dev/null -w "%{http_code}" $BASE_URL/rota-invalida-xyz)
if [ "$HTTP_CODE" = "200" ]; then
    test_pass "Invalid route returns SPA (client-side 404)"
else
    test_fail "Invalid route should return SPA (got $HTTP_CODE)"
fi

echo ""
echo "========================================="
echo "Test Summary"
echo "========================================="
echo -e "${GREEN}Passed: $pass_count${NC}"
echo -e "${RED}Failed: $fail_count${NC}"
echo "Total: $((pass_count + fail_count))"
echo ""

if [ $fail_count -eq 0 ]; then
    echo -e "${GREEN}All tests passed!${NC}"
    exit 0
else
    echo -e "${RED}Some tests failed. Please review the output above.${NC}"
    exit 1
fi
