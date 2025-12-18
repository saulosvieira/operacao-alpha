import { create } from 'zustand';
import type { ExamsState, Exam, Attempt, AnswerOption, Question } from '@/types';
import * as examsService from '@/services/exams';

export const useExamsStore = create<ExamsState>((set, get) => ({
  exams: [],
  currentAttempt: null,
  isLoading: false,
  error: null,

  fetchExams: async (careerId?: string) => {
    set({ isLoading: true, error: null });
    
    try {
      const exams = await examsService.listExams(careerId);
      
      set({ 
        exams,
        isLoading: false 
      });
    } catch (error: any) {
      console.error('Failed to fetch exams:', error);
      set({ 
        isLoading: false,
        error: error.response?.data?.message || 'Erro ao carregar simulados'
      });
    }
  },

  fetchExam: async (examId: string): Promise<Exam | null> => {
    set({ isLoading: true, error: null });
    
    try {
      const exam = await examsService.getExam(examId);
      set({ isLoading: false });
      return exam;
    } catch (error: any) {
      console.error('Failed to fetch exam:', error);
      set({ 
        isLoading: false,
        error: error.response?.data?.message || 'Erro ao carregar simulado'
      });
      return null;
    }
  },

  startAttempt: async (examId: string): Promise<Attempt> => {
    set({ isLoading: true, error: null });
    
    try {
      const attempt = await examsService.startAttempt(examId);

      set({
        currentAttempt: attempt,
        isLoading: false,
      });

      return attempt;
    } catch (error: any) {
      console.error('Failed to start attempt:', error);
      set({ 
        isLoading: false,
        error: error.response?.data?.message || 'Erro ao iniciar tentativa'
      });
      throw error;
    }
  },

  submitAnswer: async (attemptId: string, questionId: string, answer: AnswerOption) => {
    try {
      const response = await examsService.submitAnswer(attemptId, {
        questionId,
        answer,
      });

      // Update local state with the answer
      set(state => {
        if (!state.currentAttempt || state.currentAttempt.id !== attemptId) {
          return state;
        }

        return {
          currentAttempt: {
            ...state.currentAttempt,
            answers: {
              ...state.currentAttempt.answers,
              [questionId]: answer,
            },
          },
        };
      });

      return response;
    } catch (error: any) {
      console.error('Failed to submit answer:', error);
      set({ 
        error: error.response?.data?.message || 'Erro ao salvar resposta'
      });
      throw error;
    }
  },

  finishAttempt: async (attemptId: string) => {
    set({ isLoading: true, error: null });
    
    try {
      const result = await examsService.finishAttempt(attemptId);
      
      set({
        currentAttempt: null,
        isLoading: false,
      });

      return result;
    } catch (error: any) {
      console.error('Failed to finish attempt:', error);
      set({ 
        isLoading: false,
        error: error.response?.data?.message || 'Erro ao finalizar tentativa'
      });
      throw error;
    }
  },

  clearError: () => {
    set({ error: null });
  },
}));

// Export as useSimuladosStore for backward compatibility
export const useSimuladosStore = useExamsStore;
