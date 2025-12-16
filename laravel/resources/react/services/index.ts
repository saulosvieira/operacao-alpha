// Export all services for convenient imports
export * from './api';
export * from './auth';
export * from './exams';
export * from './careers';
export * from './ranking';
export * from './performance';
export * from './approved';

// Export subscription services with explicit names to avoid conflicts
export {
  getPlans,
  subscribe as subscribeToService,
  getStatus as getSubscriptionStatus,
  cancel as cancelSubscription,
  type Plan,
  type Subscription,
  type SubscriptionStatus,
  type CreateSubscriptionData,
} from './subscription';

// Export notification services with explicit names to avoid conflicts
export {
  getVapidPublicKey,
  subscribe as subscribeToNotifications,
  unsubscribe as unsubscribeFromNotifications,
  requestNotificationPermission,
  subscribeToPushNotifications,
  unsubscribeFromPushNotifications,
  isSubscribedToPushNotifications,
  type NotificationSubscription,
  type VapidPublicKey,
} from './notifications';

// Re-export default api instance
export { default as api } from './api';
