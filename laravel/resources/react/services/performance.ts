import api from './api';

export interface Statistics {
  total_exams_completed: number;
  average_score: number;
  total_correct_answers: number;
  total_questions: number;
  accuracy_percentage: number;
  total_time_spent_minutes: number;
  strongest_career: string | null;
  weakest_career: string | null;
  career_breakdown: Array<{
    career_name: string;
    exams_completed: number;
    average_score: number;
    total_correct: number;
  }>;
}

export interface HistoryEntry {
  exam_id: string;
  exam_title: string;
  career_name: string;
  score: number;
  correct_answers: number;
  total_questions: number;
  time_spent_minutes: number;
  completed_at: string;
}

/**
 * Get user performance statistics
 */
export const getStatistics = async (): Promise<Statistics> => {
  const response = await api.get<{ data: Statistics }>('/performance/statistics');
  return response.data.data;
};

/**
 * Get user exam history
 */
export const getHistory = async (limit?: number): Promise<HistoryEntry[]> => {
  const response = await api.get<{ data: HistoryEntry[] }>('/performance/history', {
    params: { limit }
  });
  return response.data.data;
};

// Export as default object for convenience
export const performanceService = {
  getStatistics,
  getHistory,
};
