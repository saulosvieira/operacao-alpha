# API Services

This directory contains all the API service modules for communicating with the Laravel backend.

## Services Overview

### `api.ts` - Base API Configuration
- Axios instance with base configuration
- Request interceptor for authentication tokens
- Response interceptor for error handling (401, 403, 422, 500)
- Automatic CSRF token inclusion

### `auth.ts` - Authentication Services
- `login(credentials)` - Login user
- `register(data)` - Register new user
- `logout()` - Logout current user
- `getMe()` - Get current authenticated user
- `isAuthenticated()` - Check if user is authenticated
- `getToken()` - Get stored auth token

### `exams.ts` - Exam Services
- `listExams(careerId?)` - List all available exams
- `getExam(examId)` - Get exam details
- `startAttempt(examId)` - Start a new exam attempt
- `getAttempt(attemptId)` - Get attempt details
- `submitAnswer(attemptId, data)` - Submit answer for a question
- `finishAttempt(attemptId)` - Finish exam and get results

### `careers.ts` - Career Services
- `listCareers()` - List all active careers
- `getCareer(careerId)` - Get career details
- `getCareerExams(careerId)` - Get all exams for a career

### `ranking.ts` - Ranking Services
- `getRanking(params?)` - Get ranking with filters
- `getMyPosition(params?)` - Get current user's position

### `performance.ts` - Performance Services
- `getStatistics(params?)` - Get user performance statistics
- `getHistory(params?)` - Get user exam history

### `subscription.ts` - Subscription Services
- `getPlans()` - Get all available subscription plans
- `subscribe(data)` - Create a new subscription
- `getStatus()` - Get current subscription status
- `cancel()` - Cancel current subscription

### `notifications.ts` - Notification Services
- `getVapidPublicKey()` - Get VAPID public key
- `subscribe(subscription)` - Subscribe to push notifications
- `unsubscribe(endpoint)` - Unsubscribe from push notifications
- `requestNotificationPermission()` - Request browser notification permission
- `subscribeToPushNotifications()` - Complete flow to subscribe to push
- `unsubscribeFromPushNotifications()` - Complete flow to unsubscribe
- `isSubscribedToPushNotifications()` - Check subscription status

## Usage Examples

### Authentication
```typescript
import { login, getMe } from '@/services/auth';

// Login
const { user, token } = await login({ email, password });

// Get current user
const user = await getMe();
```

### Exams
```typescript
import { listExams, startAttempt, submitAnswer, finishAttempt } from '@/services/exams';

// List exams
const exams = await listExams();

// Start exam
const attempt = await startAttempt(examId);

// Submit answer
await submitAnswer(attemptId, { questionId, answer: 'A' });

// Finish exam
const result = await finishAttempt(attemptId);
```

### Notifications
```typescript
import { subscribeToPushNotifications } from '@/services/notifications';

// Subscribe to push notifications
const success = await subscribeToPushNotifications();
```

## Error Handling

All services use the base API instance which automatically handles:
- 401 errors: Removes token and redirects to login
- 403 errors: Logs access forbidden
- 422 errors: Logs validation errors
- 500 errors: Logs server errors

You can catch errors in your components:

```typescript
try {
  const data = await someService();
} catch (error) {
  if (error.response?.status === 422) {
    // Handle validation errors
    console.error(error.response.data.errors);
  }
}
```

## Authentication

The API instance automatically includes the authentication token from localStorage in all requests. The token is stored after successful login/register and removed on logout or 401 errors.

## CSRF Protection

The API instance automatically includes the CSRF token from the meta tag in all requests.
