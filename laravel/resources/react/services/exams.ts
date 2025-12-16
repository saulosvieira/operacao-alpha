import api from './api';

export interface Exam {
  id: string;
  careerId: string;
  title: string;
  description?: string;
  durationMin: number;
  numQuestions: number;
  active: boolean;
}

export interface Question {
  id: string;
  questionNumber: number;
  statement: string;
  statementImage?: string;
  options: {
    A: string;
    B: string;
    C: string;
    D: string;
    E: string;
  };
  optionImages?: {
    A?: string;
    B?: string;
    C?: string;
    D?: string;
    E?: string;
  };
  explanation?: string;
}

export interface Attempt {
  id: string;
  userId: string;
  examId: string;
  startedAt: string;
  finishedAt?: string;
  durationSeconds?: number;
  correctAnswers?: number;
  score?: number;
  questions: Question[];
  answers?: Record<string, string>; // questionId -> answer
}

export interface SubmitAnswerData {
  questionId: string;
  answer: 'A' | 'B' | 'C' | 'D' | 'E';
}

export interface ExamResult {
  attemptId: string;
  totalQuestions: number;
  correctAnswers: number;
  finalScore: number;
  totalTimeSeconds: number;
}

/**
 * List all available exams
 */
export const listExams = async (careerId?: string): Promise<Exam[]> => {
  const params = careerId ? { career_id: careerId } : {};
  const response = await api.get<{ data: Exam[] }>('/exams', { params });
  return response.data.data;
};

/**
 * Get exam details by ID
 */
export const getExam = async (examId: string): Promise<Exam> => {
  const response = await api.get<{ data: Exam }>(`/exams/${examId}`);
  return response.data.data;
};

/**
 * Start a new exam attempt
 */
export const startAttempt = async (examId: string): Promise<Attempt> => {
  const response = await api.post<{ data: Attempt }>(`/exams/${examId}/start`);
  return response.data.data;
};

/**
 * Get attempt details
 */
export const getAttempt = async (attemptId: string): Promise<Attempt> => {
  const response = await api.get<{ data: Attempt }>(`/attempts/${attemptId}`);
  return response.data.data;
};

/**
 * Submit answer for a question
 */
export const submitAnswer = async (
  attemptId: string,
  data: SubmitAnswerData
): Promise<void> => {
  await api.post(`/attempts/${attemptId}/answer`, data);
};

/**
 * Finish exam attempt and get results
 */
export const finishAttempt = async (attemptId: string): Promise<ExamResult> => {
  const response = await api.post<{ data: ExamResult }>(`/attempts/${attemptId}/finish`);
  return response.data.data;
};
