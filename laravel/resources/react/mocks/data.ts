import type { Career, Exam, Question, RankingEntry } from '@/types';

// Legacy type aliases for backward compatibility
export type Carreira = Career;
export type Simulado = Exam;
export type Questao = Question;

export const mockCarreiras: Carreira[] = [
  {
    id: 'pm-sp',
    name: 'Polícia Militar - SP',
    description: 'Concurso para Soldado PM 2ª Classe',
  },
  {
    id: 'bombeiro-rj',
    name: 'Corpo de Bombeiros - RJ', 
    description: 'Concurso para Soldado Bombeiro Militar',
  },
  {
    id: 'exercito',
    name: 'Exército Brasileiro',
    description: 'Concurso de Admissão ao Curso de Formação de Soldados',
  },
  {
    id: 'marinha',
    name: 'Marinha do Brasil',
    description: 'Processo Seletivo para Praças da Marinha',
  },
];

export const mockSimulados: Simulado[] = [
  {
    id: 'sim-001',
    title: 'Simulado Geral PM-SP 2024',
    careerId: 'pm-sp',
    durationMin: 180,
    numQuestions: 60,
    active: true,
  },
  {
    id: 'sim-002', 
    title: 'Português e Matemática',
    careerId: 'pm-sp',
    durationMin: 120,
    numQuestions: 40,
    active: true,
  },
  {
    id: 'sim-003',
    title: 'Legislação e Ética',
    careerId: 'bombeiro-rj',
    durationMin: 90,
    numQuestions: 30,
    active: true,
  },
  {
    id: 'sim-004',
    title: 'Conhecimentos Gerais - Exército',
    careerId: 'exercito',
    durationMin: 150,
    numQuestions: 50,
    active: true,
  },
];

export const mockQuestoes: Questao[] = [
  {
    id: 'q-001',
    questionNumber: 1,
    statement: 'Qual é o artigo da Constituição Federal que trata dos direitos fundamentais?',
    options: [
      { letter: 'A', text: 'Artigo 3º' },
      { letter: 'B', text: 'Artigo 5º' },
      { letter: 'C', text: 'Artigo 7º' },
      { letter: 'D', text: 'Artigo 14º' },
      { letter: 'E', text: 'Artigo 37º' },
    ],
    correctAnswer: 'B',
    explanation: 'O artigo 5º da CF/88 estabelece os direitos e deveres individuais e coletivos.',
  },
];

export const mockRankingDiario: RankingEntry[] = [
  { position: 1, userId: 'user-001', partialName: 'João S.', score: 95.5, averageTimeSeconds: 120 },
  { position: 2, userId: 'user-002', partialName: 'Maria O.', score: 92.0, averageTimeSeconds: 135 },
  { position: 3, userId: 'user-003', partialName: 'Pedro L.', score: 88.5, averageTimeSeconds: 110 },
  { position: 4, userId: 'user-004', partialName: 'Ana C.', score: 85.0, averageTimeSeconds: 145 },
  { position: 5, userId: 'user-005', partialName: 'Carlos R.', score: 82.5, averageTimeSeconds: 140 },
];
