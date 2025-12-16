import api from './api';

export interface LoginCredentials {
  email: string;
  password: string;
}

export interface RegisterData {
  name: string;
  email: string;
  phone?: string;
  password: string;
  password_confirmation: string;
}

export interface User {
  id: string;
  name: string;
  email: string;
  phone?: string;
  role: 'admin' | 'consultor' | 'user';
  subscriptionStatus: 'active' | 'inactive' | 'trial' | 'cancelled';
  subscriptionExpiresAt?: string;
}

export interface AuthResponse {
  user: User;
  token: string;
}

/**
 * Login user with email and password
 */
export const login = async (credentials: LoginCredentials): Promise<AuthResponse> => {
  const response = await api.post<AuthResponse>('/login', credentials);
  
  // Store token in localStorage
  if (response.data.token) {
    localStorage.setItem('auth_token', response.data.token);
  }
  
  return response.data;
};

/**
 * Register new user
 */
export const register = async (data: RegisterData): Promise<AuthResponse> => {
  const response = await api.post<AuthResponse>('/register', data);
  
  // Store token in localStorage
  if (response.data.token) {
    localStorage.setItem('auth_token', response.data.token);
  }
  
  return response.data;
};

/**
 * Logout current user
 */
export const logout = async (): Promise<void> => {
  try {
    await api.post('/logout');
  } finally {
    // Always remove token, even if request fails
    localStorage.removeItem('auth_token');
  }
};

/**
 * Get current authenticated user
 */
export const getMe = async (): Promise<User> => {
  const response = await api.get<{ user: User }>('/me');
  return response.data.user;
};

/**
 * Check if user is authenticated
 */
export const isAuthenticated = (): boolean => {
  return !!localStorage.getItem('auth_token');
};

/**
 * Get stored auth token
 */
export const getToken = (): string | null => {
  return localStorage.getItem('auth_token');
};
