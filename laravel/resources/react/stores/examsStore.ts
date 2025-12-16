import { create } from 'zustand';
import type { SimuladosState, Simulado, Tentativa, AlternativaLetra } from '@/types';
import * as examsService from '@/services/exams';

// Map API Exam to local Simulado type
const mapExamToSimulado = (exam: examsService.Exam): Simulado => ({
  id: exam.id,
  titulo: exam.title,
  carreiraId: exam.careerId,
  duracaoMin: exam.durationMin,
  numQuestoes: exam.numQuestions,
  modo: 'fixo', // Default mode
  ordemAleatoria: true, // Default
  status: exam.active ? 'publicado' : 'rascunho',
});

// Map API Attempt to local Tentativa type
const mapAttemptToTentativa = (attempt: examsService.Attempt): Tentativa => ({
  id: attempt.id,
  simuladoId: attempt.examId,
  userId: attempt.userId,
  iniciadoEm: attempt.startedAt,
  concluidoEm: attempt.finishedAt,
  duracaoSeg: attempt.durationSeconds,
  acertos: attempt.correctAnswers,
  nota: attempt.score,
  respostas: attempt.answers as Record<string, AlternativaLetra> | undefined,
});

export const useExamsStore = create<SimuladosState>((set, get) => ({
  simulados: [],
  tentativas: [],
  tentativaAtiva: null,
  isLoading: false,

  fetchSimulados: async () => {
    set({ isLoading: true });
    
    try {
      const exams = await examsService.listExams();
      const simulados = exams.map(mapExamToSimulado);
      
      set({ 
        simulados,
        isLoading: false 
      });
    } catch (error) {
      console.error('Failed to fetch exams:', error);
      set({ isLoading: false });
    }
  },

  iniciarTentativa: async (simuladoId: string): Promise<string> => {
    try {
      const attempt = await examsService.startAttempt(simuladoId);
      const novaTentativa = mapAttemptToTentativa(attempt);

      set(state => ({
        tentativas: [...state.tentativas, novaTentativa],
        tentativaAtiva: novaTentativa,
      }));

      return novaTentativa.id;
    } catch (error) {
      console.error('Failed to start attempt:', error);
      throw error;
    }
  },

  responderQuestao: async (tentativaId: string, questaoId: string, resposta: AlternativaLetra) => {
    try {
      // Submit answer to API
      await examsService.submitAnswer(tentativaId, {
        questionId: questaoId,
        answer: resposta,
      });

      // Update local state
      set(state => ({
        tentativas: state.tentativas.map(tentativa =>
          tentativa.id === tentativaId
            ? {
                ...tentativa,
                respostas: {
                  ...tentativa.respostas,
                  [questaoId]: resposta,
                },
              }
            : tentativa
        ),
        tentativaAtiva: state.tentativaAtiva?.id === tentativaId
          ? {
              ...state.tentativaAtiva,
              respostas: {
                ...state.tentativaAtiva.respostas,
                [questaoId]: resposta,
              },
            }
          : state.tentativaAtiva,
      }));
    } catch (error) {
      console.error('Failed to submit answer:', error);
      throw error;
    }
  },

  finalizarTentativa: async (tentativaId: string) => {
    try {
      const result = await examsService.finishAttempt(tentativaId);
      
      const tentativa = get().tentativas.find(t => t.id === tentativaId);
      if (!tentativa) return;

      const tentativaFinalizada: Tentativa = {
        ...tentativa,
        concluidoEm: new Date().toISOString(),
        duracaoSeg: result.totalTimeSeconds,
        acertos: result.correctAnswers,
        nota: result.finalScore,
      };

      set(state => ({
        tentativas: state.tentativas.map(t =>
          t.id === tentativaId ? tentativaFinalizada : t
        ),
        tentativaAtiva: null,
      }));
    } catch (error) {
      console.error('Failed to finish attempt:', error);
      throw error;
    }
  },
}));

// Export as useSimuladosStore for backward compatibility
export const useSimuladosStore = useExamsStore;
