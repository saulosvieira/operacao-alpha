import api from './api';

export interface RankingEntry {
  userId: string;
  userName: string;
  position: number;
  totalScore: number;
  averageCorrect: number;
  totalExams: number;
  isCurrentUser?: boolean;
}

export interface Ranking {
  type: 'daily' | 'weekly' | 'monthly';
  careerId?: string;
  entries: RankingEntry[];
  totalEntries: number;
}

export interface UserPosition {
  position: number;
  totalScore: number;
  averageCorrect: number;
  totalExams: number;
  type: 'daily' | 'weekly' | 'monthly';
  careerId?: string;
}

/**
 * Get ranking with optional filters
 */
export const getRanking = async (params?: {
  type?: 'daily' | 'weekly' | 'monthly';
  careerId?: string;
  limit?: number;
}): Promise<Ranking> => {
  const response = await api.get<{ data: Ranking }>('/ranking', { params });
  return response.data.data;
};

/**
 * Get current user's position in ranking
 */
export const getMyPosition = async (params?: {
  type?: 'daily' | 'weekly' | 'monthly';
  careerId?: string;
}): Promise<UserPosition> => {
  const response = await api.get<{ data: UserPosition }>('/ranking/my-position', { params });
  return response.data.data;
};

/**
 * Ranking service object for component usage
 */
export const rankingService = {
  getRanking: async (type: 'daily' | 'weekly' | 'monthly') => {
    const response = await api.get<{ data: RankingEntry[] }>('/ranking', { params: { type } });
    return response.data.data;
  },
  getMyPosition: async (type: 'daily' | 'weekly' | 'monthly') => {
    const response = await api.get<{ data: number }>('/ranking/my-position', { params: { type } });
    return response.data.data;
  },
};
