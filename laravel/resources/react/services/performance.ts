import api from './api';

export interface Statistics {
  totalExams: number;
  totalQuestions: number;
  correctAnswers: number;
  averageScore: number;
  averageTimeSeconds: number;
  bestScore: number;
  worstScore: number;
  improvementRate: number;
  byCareer: {
    careerId: string;
    careerName: string;
    totalExams: number;
    averageScore: number;
  }[];
  bySubject?: {
    subject: string;
    correctAnswers: number;
    totalQuestions: number;
    accuracy: number;
  }[];
}

export interface HistoryEntry {
  attemptId: string;
  examId: string;
  examTitle: string;
  careerId: string;
  careerName: string;
  completedAt: string;
  score: number;
  correctAnswers: number;
  totalQuestions: number;
  durationSeconds: number;
}

export interface History {
  entries: HistoryEntry[];
  total: number;
  page: number;
  perPage: number;
}

/**
 * Get user performance statistics
 */
export const getStatistics = async (params?: {
  careerId?: string;
  startDate?: string;
  endDate?: string;
}): Promise<Statistics> => {
  const response = await api.get<{ data: Statistics }>('/performance/statistics', { params });
  return response.data.data;
};

/**
 * Get user exam history
 */
export const getHistory = async (params?: {
  careerId?: string;
  page?: number;
  perPage?: number;
}): Promise<History> => {
  const response = await api.get<{ data: History }>('/performance/history', { params });
  return response.data.data;
};
