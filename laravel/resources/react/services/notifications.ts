import api from './api';

export interface NotificationSubscription {
  endpoint: string;
  keys: {
    p256dh: string;
    auth: string;
  };
}

export interface VapidPublicKey {
  publicKey: string;
}

/**
 * Get VAPID public key for push notifications
 */
export const getVapidPublicKey = async (): Promise<string> => {
  const response = await api.get<{ publicKey: string }>('/notifications/vapid-key');
  return response.data.publicKey;
};

/**
 * Subscribe to push notifications
 */
export const subscribe = async (subscription: NotificationSubscription): Promise<void> => {
  await api.post('/notifications/subscribe', {
    endpoint: subscription.endpoint,
    public_key: subscription.keys.p256dh,
    auth_token: subscription.keys.auth,
  });
};

/**
 * Unsubscribe from push notifications
 */
export const unsubscribe = async (endpoint: string): Promise<void> => {
  await api.post('/notifications/unsubscribe', { endpoint });
};

/**
 * Request notification permission and subscribe
 */
export const requestNotificationPermission = async (): Promise<boolean> => {
  if (!('Notification' in window)) {
    console.warn('This browser does not support notifications');
    return false;
  }

  if (!('serviceWorker' in navigator)) {
    console.warn('This browser does not support service workers');
    return false;
  }

  const permission = await Notification.requestPermission();
  return permission === 'granted';
};

/**
 * Subscribe user to push notifications
 */
export const subscribeToPushNotifications = async (): Promise<boolean> => {
  try {
    // Request permission
    const hasPermission = await requestNotificationPermission();
    if (!hasPermission) {
      return false;
    }

    // Get service worker registration
    const registration = await navigator.serviceWorker.ready;

    // Get VAPID public key from server
    const vapidPublicKey = await getVapidPublicKey();

    // Convert VAPID key to Uint8Array
    const convertedVapidKey = urlBase64ToUint8Array(vapidPublicKey);

    // Subscribe to push notifications
    const subscription = await registration.pushManager.subscribe({
      userVisibleOnly: true,
      applicationServerKey: convertedVapidKey as BufferSource,
    });

    // Send subscription to server
    const subscriptionData: NotificationSubscription = {
      endpoint: subscription.endpoint,
      keys: {
        p256dh: arrayBufferToBase64(subscription.getKey('p256dh')!),
        auth: arrayBufferToBase64(subscription.getKey('auth')!),
      },
    };

    await subscribe(subscriptionData);
    return true;
  } catch (error) {
    console.error('Error subscribing to push notifications:', error);
    return false;
  }
};

/**
 * Unsubscribe user from push notifications
 */
export const unsubscribeFromPushNotifications = async (): Promise<boolean> => {
  try {
    const registration = await navigator.serviceWorker.ready;
    const subscription = await registration.pushManager.getSubscription();

    if (subscription) {
      await unsubscribe(subscription.endpoint);
      await subscription.unsubscribe();
      return true;
    }

    return false;
  } catch (error) {
    console.error('Error unsubscribing from push notifications:', error);
    return false;
  }
};

/**
 * Check if user is subscribed to push notifications
 */
export const isSubscribedToPushNotifications = async (): Promise<boolean> => {
  try {
    if (!('serviceWorker' in navigator)) {
      return false;
    }

    const registration = await navigator.serviceWorker.ready;
    const subscription = await registration.pushManager.getSubscription();

    return subscription !== null;
  } catch (error) {
    console.error('Error checking push notification subscription:', error);
    return false;
  }
};

// Helper functions

/**
 * Convert URL-safe base64 string to Uint8Array
 */
function urlBase64ToUint8Array(base64String: string): Uint8Array {
  const padding = '='.repeat((4 - (base64String.length % 4)) % 4);
  const base64 = (base64String + padding).replace(/-/g, '+').replace(/_/g, '/');

  const rawData = window.atob(base64);
  const outputArray = new Uint8Array(rawData.length);

  for (let i = 0; i < rawData.length; ++i) {
    outputArray[i] = rawData.charCodeAt(i);
  }

  return outputArray;
}

/**
 * Convert ArrayBuffer to base64 string
 */
function arrayBufferToBase64(buffer: ArrayBuffer): string {
  const bytes = new Uint8Array(buffer);
  let binary = '';
  for (let i = 0; i < bytes.byteLength; i++) {
    binary += String.fromCharCode(bytes[i]);
  }
  return window.btoa(binary);
}
