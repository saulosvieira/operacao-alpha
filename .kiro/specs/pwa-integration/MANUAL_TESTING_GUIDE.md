# Manual Testing Guide - PWA Integration

This guide provides step-by-step instructions for manual testing of the PWA integration.

## Prerequisites

1. Ensure Docker containers are running:
   ```bash
   docker-compose ps
   ```

2. Ensure Vite dev server is running:
   ```bash
   docker-compose --profile dev up -d simulados-vite
   ```

3. Open the application in your browser:
   ```
   http://localhost:8090
   ```

---

## 1. Authentication Flow Testing

### Test 1.1: Invalid Login
1. Navigate to http://localhost:8090/login
2. Enter invalid credentials:
   - Email: `invalid@test.com`
   - Password: `wrongpass`
3. Click "Login"
4. **Expected:** Error message "Credenciais inválidas" appears

### Test 1.2: Valid Login
1. Navigate to http://localhost:8090/login
2. Enter valid credentials:
   - Email: `admin@simulados.com`
   - Password: `admin123`
3. Click "Login"
4. **Expected:** 
   - Redirected to home page
   - User menu shows logged-in state
   - Token saved in localStorage (check DevTools → Application → Local Storage)

### Test 1.3: Protected Routes
1. Open DevTools → Application → Local Storage
2. Delete the auth token
3. Try to navigate to `/simulados` or `/conta`
4. **Expected:** Redirected to `/login`

### Test 1.4: Logout
1. Login with valid credentials
2. Click on user menu → Logout
3. **Expected:**
   - Token removed from localStorage
   - Redirected to `/login`
   - Cannot access protected routes

---

## 2. Navigation Testing

### Test 2.1: Client-Side Navigation
1. Login to the application
2. Click through all menu items:
   - Home (/)
   - Carreiras (/carreiras)
   - Simulados (/simulados)
   - Ranking (/ranking)
   - Desempenho (/desempenho)
   - Aprovados (/aprovados)
   - Assinar (/assinar)
   - Conta (/conta)
3. **Expected:**
   - Page changes without full reload
   - URL updates in address bar
   - No white flash between pages
   - Browser back/forward buttons work

### Test 2.2: Page Refresh
1. Navigate to any route (e.g., `/simulados`)
2. Press F5 to refresh
3. **Expected:**
   - Page reloads
   - Same route is maintained
   - No 404 error

### Test 2.3: 404 Page
1. Navigate to a non-existent route: http://localhost:8090/rota-invalida-xyz
2. **Expected:**
   - Custom 404 page appears (if implemented)
   - Or React Router shows "Not Found" message

---

## 3. Exam Execution Testing

### Test 3.1: List Exams
1. Login and navigate to `/simulados`
2. **Expected:**
   - List of available exams appears
   - Each exam shows: title, description, duration, number of questions

### Test 3.2: Start Exam
1. Click on an exam
2. Click "Iniciar" button
3. **Expected:**
   - Redirected to exam execution page
   - Timer starts counting
   - First question appears

### Test 3.3: Answer Questions
1. Select an answer for the current question
2. Click "Próxima" or navigate to next question
3. **Expected:**
   - Answer is saved (visual feedback)
   - Next question appears
   - Progress indicator updates

### Test 3.4: Finish Exam
1. Answer some questions
2. Click "Finalizar" button
3. **Expected:**
   - Confirmation dialog appears
   - After confirmation, results page shows
   - Score is calculated correctly
   - Correct/incorrect answers are shown

---

## 4. Ranking and Performance Testing

### Test 4.1: Ranking Page
1. Navigate to `/ranking`
2. **Expected:**
   - List of users with scores appears
   - Current user's position is highlighted
   - Can filter by career or time period

### Test 4.2: Performance Page
1. Navigate to `/desempenho`
2. **Expected:**
   - Statistics cards show: total exams, average score, etc.
   - Charts/graphs display performance over time
   - History of completed exams appears

---

## 5. Subscription System Testing

### Test 5.1: Free User Paywall
1. Logout and login with free user:
   - Email: `free@test.com`
   - Password: `senha123`
2. Try to access a premium exam
3. **Expected:**
   - Paywall modal appears
   - "Assinar" button redirects to `/assinar`

### Test 5.2: Plans Page
1. Navigate to `/assinar`
2. **Expected:**
   - Available plans are displayed
   - Each plan shows: name, price, features
   - "Assinar" buttons are present

### Test 5.3: Premium User Access
1. Logout and login with premium user:
   - Email: `premium@test.com`
   - Password: `senha123`
2. Try to access premium exams
3. **Expected:**
   - Full access to all exams
   - No paywall appears

---

## 6. Responsiveness Testing

### Test 6.1: Mobile View (375px)
1. Open DevTools (F12)
2. Toggle device toolbar (Ctrl+Shift+M)
3. Select "iPhone SE" or set width to 375px
4. Navigate through the application
5. **Expected:**
   - Mobile menu (hamburger) appears
   - Layout adapts to narrow screen
   - All content is accessible
   - No horizontal scrolling

### Test 6.2: Tablet View (768px)
1. In DevTools, select "iPad" or set width to 768px
2. Navigate through the application
3. **Expected:**
   - Layout adapts to medium screen
   - Navigation may show as tabs or condensed menu
   - Content uses available space efficiently

### Test 6.3: Desktop View (1920px)
1. In DevTools, set width to 1920px or use full screen
2. Navigate through the application
3. **Expected:**
   - Full navigation menu visible
   - Content uses full width appropriately
   - No excessive whitespace
   - Multi-column layouts where appropriate

---

## 7. PWA Testing

### Test 7.1: Service Worker Registration
1. Open DevTools → Application → Service Workers
2. **Expected:**
   - Service Worker registered for http://localhost:8090
   - Status: "activated and is running"
   - Source: /sw.js

### Test 7.2: Manifest Validation
1. Open DevTools → Application → Manifest
2. **Expected:**
   - Manifest loads without errors
   - Name: "Operação Alfa - Simulados"
   - Icons: Multiple sizes (72, 96, 128, 144, 152, 192, 384, 512)
   - Theme color: #1e40af (or configured color)
   - Display: standalone

### Test 7.3: Icon Accessibility
1. In DevTools → Application → Manifest
2. Click on each icon
3. **Expected:**
   - All icons load successfully
   - No 404 errors
   - Icons display correctly

### Test 7.4: Installation Prompt (Desktop)
1. In Chrome, look for install icon in address bar
2. Click the install icon
3. **Expected:**
   - Installation dialog appears
   - After installing, app opens in standalone window
   - App icon appears in OS application menu

### Test 7.5: Installation (Mobile)
1. Open http://localhost:8090 on mobile device
2. Look for "Add to Home Screen" banner
3. **Expected:**
   - Installation prompt appears (may require HTTPS in production)
   - After installing, app icon appears on home screen
   - Opening app shows splash screen
   - App runs in standalone mode (no browser UI)

---

## 8. Push Notifications Testing

### Test 8.1: Request Permission
1. Navigate to `/conta` or page that requests notification permission
2. Click "Permitir notificações" button
3. **Expected:**
   - Browser permission prompt appears
   - Options: "Allow" / "Block"

### Test 8.2: Grant Permission
1. Click "Allow" on permission prompt
2. **Expected:**
   - Success message appears
   - Subscription is saved to database
   - Console shows subscription object (check DevTools)

### Test 8.3: Send Test Notification (Backend)
1. Use Tinker to send a test notification:
   ```bash
   docker exec -it simulados-app php artisan tinker
   ```
2. Run:
   ```php
   $user = User::find(1);
   $action = app(SendNotificationAction::class);
   $action->execute($user->id, 'Test Title', 'Test message body');
   ```
3. **Expected:**
   - Notification appears on device/browser
   - Clicking notification opens the app

---

## 9. API Validation Testing

### Test 9.1: Network Tab Inspection
1. Open DevTools → Network tab
2. Filter by "api"
3. Navigate through the application
4. **Expected:**
   - All API requests return 200 (or appropriate status)
   - Response format is JSON
   - Responses have "data" property
   - No CORS errors

### Test 9.2: Error Handling
1. Open DevTools → Network tab
2. Try to submit invalid data (e.g., empty form)
3. **Expected:**
   - API returns 422 (Validation Error)
   - Error messages are displayed in UI
   - Specific field errors are shown

### Test 9.3: Authentication Errors
1. Manually delete auth token from localStorage
2. Try to access protected API endpoint
3. **Expected:**
   - API returns 401 (Unauthorized)
   - User is redirected to login page
   - Error message is displayed

---

## 10. Performance Testing

### Test 10.1: Initial Load Time
1. Open DevTools → Network tab
2. Hard refresh (Ctrl+Shift+R)
3. **Expected:**
   - Page loads in < 3 seconds
   - No blocking resources
   - Progressive rendering (content appears quickly)

### Test 10.2: Navigation Speed
1. Navigate between pages
2. Observe transition time
3. **Expected:**
   - Page transitions are instant (< 100ms)
   - No loading spinners for navigation
   - Smooth animations

---

## Test Credentials

### Admin User
- Email: `admin@simulados.com`
- Password: `admin123`
- Role: admin
- Subscription: active

### Premium User
- Email: `premium@test.com`
- Password: `senha123`
- Role: user
- Subscription: active

### Free User
- Email: `free@test.com`
- Password: `senha123`
- Role: user
- Subscription: inactive

### Trial User
- Email: `trial@test.com`
- Password: `senha123`
- Role: user
- Subscription: trial

---

## Troubleshooting

### Application Not Loading
```bash
# Check if containers are running
docker-compose ps

# Check Laravel logs
docker exec simulados-app tail -f storage/logs/laravel.log

# Check Nginx logs
docker logs simulados-webserver
```

### Vite Not Running
```bash
# Start Vite dev server
docker-compose --profile dev up -d simulados-vite

# Check Vite logs
docker logs simulados-vite

# Verify Vite is accessible
curl http://localhost:5173
```

### API Errors
```bash
# Check API routes
docker exec simulados-app php artisan route:list | grep api

# Test API directly
curl -X POST http://localhost:8090/api/login \
  -H "Content-Type: application/json" \
  -d '{"email":"admin@simulados.com","password":"admin123"}'
```

### Database Issues
```bash
# Check database connection
docker exec simulados-app php artisan tinker --execute="DB::connection()->getPdo();"

# Re-run migrations and seeders
docker exec simulados-app php artisan migrate:fresh --seed
```

---

## Reporting Issues

When reporting issues, please include:
1. **Steps to reproduce**
2. **Expected behavior**
3. **Actual behavior**
4. **Browser and version**
5. **Console errors** (DevTools → Console)
6. **Network errors** (DevTools → Network)
7. **Screenshots** (if applicable)

---

## Success Criteria

The application passes manual testing if:
- ✅ All authentication flows work correctly
- ✅ Navigation is smooth and client-side
- ✅ All pages load without errors
- ✅ APIs return correct data and status codes
- ✅ PWA can be installed and works offline (basic functionality)
- ✅ Responsive design works on mobile, tablet, and desktop
- ✅ No console errors during normal usage
- ✅ Performance is acceptable (< 3s initial load)
