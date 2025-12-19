import api from './api';

export type FeedbackMode = 'immediate' | 'final';

export interface Exam {
  id: string;
  careerId: string;
  title: string;
  description?: string;
  durationMin: number;
  numQuestions: number;
  active: boolean;
  isFree?: boolean;
  feedbackMode: FeedbackMode; // Controls when answers are revealed
}

export interface QuestionOption {
  letter: 'A' | 'B' | 'C' | 'D' | 'E';
  text: string;
  image?: string;
}

export interface Question {
  id: string;
  questionNumber: number;
  statement: string;
  statementImage?: string;
  options: QuestionOption[];
  correctAnswer?: 'A' | 'B' | 'C' | 'D' | 'E'; // Optional - only included based on feedback mode and attempt state
  explanation?: string; // Optional - only included when correctAnswer is included
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
  questions?: Question[]; // Questions with conditional correctAnswer/explanation based on feedback mode
  answers?: Record<string, 'A' | 'B' | 'C' | 'D' | 'E'>; // questionId -> answer
}

export interface SubmitAnswerData {
  questionId: string;
  answer: 'A' | 'B' | 'C' | 'D' | 'E';
}

export interface SubmitAnswerResponse {
  success: boolean;
  isCorrect?: boolean; // Only included in immediate feedback mode
  correctAnswer?: 'A' | 'B' | 'C' | 'D' | 'E'; // Only included in immediate feedback mode
  explanation?: string; // Only included in immediate feedback mode
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
 * Start a new exam attempt (or resume existing one)
 */
export const startAttempt = async (examId: string): Promise<Attempt> => {
  const response = await api.post<{ 
    data: {
      attempt: {
        id: string;
        userId: string;
        examId: string;
        startedAt: string;
        finishedAt?: string;
        durationSeconds?: number;
        correctAnswers?: number;
        score?: number;
        answers?: Record<string, 'A' | 'B' | 'C' | 'D' | 'E'>; // Existing answers for resumed attempts
      };
      exam: Exam;
      questions: Question[];
      initialTimerSeconds: number;
    }
  }>(`/exams/${examId}/start`);
  
  // Return attempt with questions included
  return {
    id: response.data.data.attempt.id,
    userId: response.data.data.attempt.userId,
    examId: response.data.data.attempt.examId,
    startedAt: response.data.data.attempt.startedAt,
    finishedAt: response.data.data.attempt.finishedAt,
    durationSeconds: response.data.data.attempt.durationSeconds,
    correctAnswers: response.data.data.attempt.correctAnswers,
    score: response.data.data.attempt.score,
    questions: response.data.data.questions,
    answers: response.data.data.attempt.answers, // Include existing answers
  };
};

export interface FeedbackDataItem {
  isCorrect: boolean;
  correctAnswer: 'A' | 'B' | 'C' | 'D' | 'E';
  explanation?: string;
  chosenAnswer: 'A' | 'B' | 'C' | 'D' | 'E';
}

export interface AttemptWithFeedback extends Attempt {
  feedbackData?: Record<string, FeedbackDataItem>;
  initialTimerSeconds?: number;
}

/**
 * Get attempt details with questions
 */
export const getAttempt = async (attemptId: string): Promise<AttemptWithFeedback> => {
  const response = await api.get<{ 
    data: {
      attempt: {
        id: string;
        userId: string;
        examId: string;
        startedAt: string;
        finishedAt?: string;
        durationSeconds?: number;
        correctAnswers?: number;
        score?: number;
        answers?: Record<string, 'A' | 'B' | 'C' | 'D' | 'E'>;
      };
      exam: Exam & { questions?: Question[] };
      feedbackData?: Record<string, FeedbackDataItem>;
      initialTimerSeconds?: number;
    }
  }>(`/exams/attempts/${attemptId}`);
  
  const { attempt, exam, feedbackData, initialTimerSeconds } = response.data.data;
  
  return {
    id: attempt.id,
    userId: attempt.userId,
    examId: attempt.examId,
    startedAt: attempt.startedAt,
    finishedAt: attempt.finishedAt,
    durationSeconds: attempt.durationSeconds,
    correctAnswers: attempt.correctAnswers,
    score: attempt.score,
    questions: exam.questions,
    answers: attempt.answers,
    feedbackData,
    initialTimerSeconds,
  };
};

/**
 * Submit answer for a question
 * Returns feedback data if exam is in immediate feedback mode
 */
export const submitAnswer = async (
  attemptId: string,
  data: SubmitAnswerData
): Promise<SubmitAnswerResponse> => {
  // Convert camelCase to snake_case for API
  const apiData = {
    question_id: data.questionId,
    answer: data.answer,
  };
  
  const response = await api.post<{ data: SubmitAnswerResponse }>(`/exams/attempts/${attemptId}/answer`, apiData);
  return response.data.data;
};

/**
 * Finish exam attempt and get results
 */
export const finishAttempt = async (attemptId: string): Promise<ExamResult> => {
  const response = await api.post<{ data: ExamResult }>(`/exams/attempts/${attemptId}/finish`);
  return response.data.data;
};
