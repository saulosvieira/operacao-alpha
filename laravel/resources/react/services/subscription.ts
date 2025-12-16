import api from './api';

export interface Plan {
  id: string;
  name: string;
  type: 'free' | 'monthly' | 'yearly';
  price: number;
  currency: string;
  features: string[];
  popular?: boolean;
}

export interface Subscription {
  id: string;
  userId: string;
  planId: string;
  planName: string;
  status: 'active' | 'inactive' | 'trial' | 'cancelled';
  startDate: string;
  expiresAt?: string;
  autoRenew: boolean;
  platformId?: string;
}

export interface SubscriptionStatus {
  hasActiveSubscription: boolean;
  subscription?: Subscription;
  plan?: Plan;
  daysRemaining?: number;
  canAccessPremiumContent: boolean;
}

export interface CreateSubscriptionData {
  planId: string;
  paymentMethod: string;
  paymentToken?: string;
}

/**
 * Get all available subscription plans
 */
export const getPlans = async (): Promise<Plan[]> => {
  const response = await api.get<{ data: Plan[] }>('/plans');
  return response.data.data;
};

/**
 * Create a new subscription
 */
export const subscribe = async (data: CreateSubscriptionData): Promise<Subscription> => {
  const response = await api.post<{ data: Subscription }>('/subscribe', data);
  return response.data.data;
};

/**
 * Get current subscription status
 */
export const getStatus = async (): Promise<SubscriptionStatus> => {
  const response = await api.get<{ data: SubscriptionStatus }>('/subscription/status');
  return response.data.data;
};

/**
 * Cancel current subscription
 */
export const cancel = async (): Promise<void> => {
  await api.post('/subscription/cancel');
};
