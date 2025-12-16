import { create } from 'zustand';
import type { AuthState, User } from '@/types';
import * as authService from '@/services/auth';

// Map API User to local User type
const mapApiUserToUser = (apiUser: authService.User): User => ({
  id: apiUser.id,
  name: apiUser.name,
  email: apiUser.email,
  phone: apiUser.phone,
  role: apiUser.role,
  subscriptionStatus: apiUser.subscriptionStatus,
  subscriptionExpiresAt: apiUser.subscriptionExpiresAt,
  avatarUrl: undefined,
});

export const useAuthStore = create<AuthState>((set, get) => ({
  user: null,
  token: localStorage.getItem('auth_token'),
  isLoading: false,
  error: null,

  login: async (email: string, password: string): Promise<void> => {
    set({ isLoading: true, error: null });
    
    try {
      const response = await authService.login({ email, password });
      
      const user = mapApiUserToUser(response.user);
      set({ user, token: response.token, isLoading: false, error: null });
      
      // Store user data in localStorage for persistence
      localStorage.setItem('operacao-alfa-user', JSON.stringify(user));
    } catch (error: any) {
      console.error('Login error:', error);
      const errorMessage = error.response?.data?.message || 'Erro ao fazer login. Tente novamente.';
      set({ isLoading: false, error: errorMessage });
      throw error;
    }
  },

  register: async (name: string, email: string, password: string): Promise<void> => {
    set({ isLoading: true, error: null });
    
    try {
      const response = await authService.register({
        name,
        email,
        password,
        password_confirmation: password,
      });
      
      const user = mapApiUserToUser(response.user);
      set({ user, token: response.token, isLoading: false, error: null });
      
      localStorage.setItem('operacao-alfa-user', JSON.stringify(user));
    } catch (error: any) {
      console.error('Register error:', error);
      const errorMessage = error.response?.data?.message || 'Erro ao registrar. Tente novamente.';
      set({ isLoading: false, error: errorMessage });
      throw error;
    }
  },

  logout: async () => {
    try {
      await authService.logout();
    } catch (error) {
      console.error('Logout error:', error);
    } finally {
      set({ user: null, token: null, error: null });
      localStorage.removeItem('operacao-alfa-user');
      localStorage.removeItem('auth_token');
    }
  },

  fetchUser: async () => {
    const token = get().token;
    if (!token) return;

    set({ isLoading: true });
    
    try {
      const apiUser = await authService.getMe();
      const user = mapApiUserToUser(apiUser);
      set({ user, isLoading: false });
      localStorage.setItem('operacao-alfa-user', JSON.stringify(user));
    } catch (error) {
      console.error('Fetch user error:', error);
      set({ user: null, token: null, isLoading: false });
      localStorage.removeItem('operacao-alfa-user');
      localStorage.removeItem('auth_token');
    }
  },

  clearError: () => {
    set({ error: null });
  },
}));

// Legacy alias for backward compatibility
// Components using 'usuario' will still work
export const useAuthStoreLegacy = () => {
  const store = useAuthStore();
  return {
    ...store,
    usuario: store.user ? {
      id: store.user.id,
      nome: store.user.name,
      email: store.user.email,
      assinaturaAtiva: store.user.subscriptionStatus === 'active' || store.user.subscriptionStatus === 'trial',
      avatarUrl: store.user.avatarUrl,
    } : null,
  };
};

// Initialize store with saved user and validate token
const initializeAuth = async () => {
  const savedUser = localStorage.getItem('operacao-alfa-user');
  const token = authService.getToken();
  
  if (savedUser && token) {
    try {
      // Set initial state from localStorage
      useAuthStore.setState({ 
        user: JSON.parse(savedUser),
        token,
      });
      
      // Validate token by fetching current user from API
      const apiUser = await authService.getMe();
      const user = mapApiUserToUser(apiUser);
      useAuthStore.setState({ user });
      
      // Update localStorage with fresh data
      localStorage.setItem('operacao-alfa-user', JSON.stringify(user));
    } catch (error) {
      console.error('Failed to restore session:', error);
      // Clear invalid session
      useAuthStore.setState({ user: null, token: null });
      localStorage.removeItem('operacao-alfa-user');
      localStorage.removeItem('auth_token');
    }
  }
};

// Initialize auth on load
if (typeof window !== 'undefined') {
  initializeAuth();
}
