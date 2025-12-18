// OPERAÇÃO ALFA - Type Definitions

// Career Types
export interface Career {
  id: string;
  name: string;
  description?: string;
}

// Answer Types
export type AnswerOption = 'A' | 'B' | 'C' | 'D' | 'E';

export interface Option {
  letter: AnswerOption;
  text: string;
  image?: string;
}

// Question Types
export interface Question {
  id: string;
  questionNumber: number;
  statement: string;
  statementImage?: string;
  options: Option[];
  correctAnswer?: AnswerOption; // Optional - only included based on feedback mode and attempt state
  explanation?: string; // Optional - only included when correctAnswer is included
}

// Exam Types
export type ExamStatus = 'draft' | 'published' | 'archived';
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

// Attempt Types
export interface Attempt {
  id: string;
  examId: string;
  userId: string;
  startedAt: string;
  finishedAt?: string;
  durationSeconds?: number;
  correctAnswers?: number;
  score?: number;
  questions?: Question[]; // Questions with conditional correctAnswer/explanation based on feedback mode
  answers?: Record<string, AnswerOption>; // questionId -> chosen answer
}

// Ranking Types
export type RankingType = 'daily' | 'weekly' | 'monthly';

export interface RankingEntry {
  position: number;
  userId: string;
  partialName: string;
  score: number;
  averageTimeSeconds: number;
}

// User Types
export interface User {
  id: string;
  name: string;
  email: string;
  phone?: string;
  role: 'admin' | 'consultant' | 'user';
  subscriptionStatus: 'active' | 'inactive' | 'trial' | 'cancelled';
  subscriptionExpiresAt?: string;
  avatarUrl?: string;
}

// Approved Types
export interface Approved {
  id: string;
  name: string;
  career: string;
  note?: string;
  avatarUrl?: string;
}

// Performance Types
export interface Statistics {
  totalExams: number;
  averageScore: number;
  totalCorrectAnswers: number;
  totalQuestions: number;
  bestScore: number;
  recentExams: Array<{
    examId: string;
    examTitle: string;
    score: number;
    completedAt: string;
  }>;
}

export interface HistoryEntry {
  id: string;
  examId: string;
  examTitle: string;
  score: number;
  correctAnswers: number;
  totalQuestions: number;
  completedAt: string;
}

// Subscription Types
export interface Plan {
  id: string;
  name: string;
  price: number;
  features: string[];
  popular?: boolean;
}

export interface Subscription {
  status: 'active' | 'inactive' | 'trial' | 'cancelled';
  expiresAt?: string;
  planName?: string;
}

// Notification Types
export interface NotificationSubscription {
  endpoint: string;
  keys: {
    p256dh: string;
    auth: string;
  };
}

// Store State Types
export interface AuthState {
  user: User | null;
  token: string | null;
  isLoading: boolean;
  error: string | null;
  login: (email: string, password: string) => Promise<void>;
  register: (name: string, email: string, password: string) => Promise<void>;
  logout: () => Promise<void>;
  fetchUser: () => Promise<void>;
  clearError: () => void;
}

export interface SubmitAnswerResponse {
  success: boolean;
  isCorrect?: boolean;
  correctAnswer?: AnswerOption;
  explanation?: string;
}

export interface ExamResult {
  attemptId: string;
  totalQuestions: number;
  correctAnswers: number;
  finalScore: number;
  totalTimeSeconds: number;
}

export interface ExamsState {
  exams: Exam[];
  currentAttempt: Attempt | null;
  isLoading: boolean;
  error: string | null;
  fetchExams: (careerId?: string) => Promise<void>;
  fetchExam: (examId: string) => Promise<Exam | null>;
  startAttempt: (examId: string) => Promise<Attempt>;
  submitAnswer: (attemptId: string, questionId: string, answer: AnswerOption) => Promise<SubmitAnswerResponse>;
  finishAttempt: (attemptId: string) => Promise<ExamResult>;
  clearError: () => void;
}

export interface RankingState {
  rankings: Record<RankingType, RankingEntry[]>;
  myPosition: Record<RankingType, number | null>;
  isLoading: boolean;
  error: string | null;
  fetchRanking: (type: RankingType, careerId?: string) => Promise<void>;
  clearError: () => void;
}

// Legacy types for backward compatibility (to be removed gradually)
/** @deprecated Use Career instead */
export type Carreira = Career;
/** @deprecated Use AnswerOption instead */
export type AlternativaLetra = AnswerOption;

/** @deprecated Use Question instead */
export interface Questao {
  id: string;
  enunciado: string;
  imagemUrl?: string;
  alternativas: Array<{
    letra: AlternativaLetra;
    texto: string;
    correta?: boolean;
  }>;
  dificuldade?: string;
  explicacao?: string;
}
/** @deprecated Use Exam instead */
export type Simulado = Exam;
/** @deprecated Use Attempt instead */
export type Tentativa = Attempt;
/** @deprecated Use User instead */
export type Usuario = User;

// Legacy state type for backward compatibility
/** @deprecated Use ExamsState instead */
export interface SimuladosState {
  simulados: Simulado[];
  tentativas: Tentativa[];
  tentativaAtiva: Tentativa | null;
  isLoading: boolean;
  fetchSimulados: () => Promise<void>;
  iniciarTentativa: (simuladoId: string) => Promise<string>;
  responderQuestao: (tentativaId: string, questaoId: string, resposta: AlternativaLetra) => Promise<void>;
  finalizarTentativa: (tentativaId: string) => Promise<void>;
}