import { useState, useEffect } from 'react';
import { useParams, useNavigate } from 'react-router-dom';
import { AppLayout } from '@/components/layout/AppLayout';
import { QuestaoCard } from '@/components/QuestaoCard';
import { Timer } from '@/components/Timer';
import { useExamsStore } from '@/stores/examsStore';
import { Button } from '@/components/ui/button';
import { Progress } from '@/components/ui/progress';
import { Alert, AlertDescription } from '@/components/ui/alert';
import { ChevronLeft, ChevronRight, Flag, AlertCircle, Loader2 } from 'lucide-react';
import type { AnswerOption, Question, Exam } from '@/types';
import { getAttempt } from '@/services/exams';
import { toast } from 'sonner';

export default function ExecuteExam() {
  const { examId, attemptId } = useParams();
  const navigate = useNavigate();
  const { 
    currentAttempt, 
    startAttempt, 
    submitAnswer, 
    finishAttempt, 
    fetchExam,
    isLoading, 
    error,
    clearError 
  } = useExamsStore();
  
  const [currentQuestion, setCurrentQuestion] = useState(0);
  const [questions, setQuestions] = useState<Question[]>([]);
  const [exam, setExam] = useState<Exam | null>(null);
  const [isInitializing, setIsInitializing] = useState(true);
  const [feedbackData, setFeedbackData] = useState<Record<string, { isCorrect: boolean; correctAnswer: string; explanation?: string }>>({});

  // Initialize attempt and load questions
  useEffect(() => {
    const initAttempt = async () => {
      if (!examId) {
        navigate('/simulados');
        return;
      }

      try {
        setIsInitializing(true);
        
        // Load exam details first to get feedback mode
        const examData = await fetchExam(examId);
        if (examData) {
          setExam(examData);
        }
        
        // If no attemptId, start a new attempt
        if (!attemptId) {
          const attempt = await startAttempt(examId);
          // Redirect to the attempt URL
          navigate(`/simulado/${examId}/tentativa/${attempt.id}`, { replace: true });
          return;
        }
        
        // If we have attemptId but no currentAttempt, or currentAttempt doesn't match
        if (attemptId && (!currentAttempt || currentAttempt.id !== attemptId)) {
          // Load the specific attempt
          const attemptData = await getAttempt(attemptId);
          if (attemptData && attemptData.questions) {
            setQuestions(attemptData.questions);
          }
          
          // For immediate feedback mode, mark already answered questions as having feedback
          // This prevents re-answering when resuming an attempt
          if (examData?.feedbackMode === 'immediate' && attemptData?.answers) {
            const existingFeedback: Record<string, { isCorrect: boolean; correctAnswer: string; explanation?: string }> = {};
            Object.keys(attemptData.answers).forEach(questionId => {
              // Mark as answered - we don't have the actual feedback data, but we know it was answered
              // The question will be locked and show the selected answer
              existingFeedback[questionId] = {
                isCorrect: false, // We don't know, but it doesn't matter for locking
                correctAnswer: '', // We don't have this info when resuming
                explanation: undefined,
              };
            });
            setFeedbackData(existingFeedback);
          }
        } else if (currentAttempt && currentAttempt.questions) {
          // Use current attempt questions
          setQuestions(currentAttempt.questions);
          
          // For immediate feedback mode, mark already answered questions
          if (examData?.feedbackMode === 'immediate' && currentAttempt.answers) {
            const existingFeedback: Record<string, { isCorrect: boolean; correctAnswer: string; explanation?: string }> = {};
            Object.keys(currentAttempt.answers).forEach(questionId => {
              existingFeedback[questionId] = {
                isCorrect: false,
                correctAnswer: '',
                explanation: undefined,
              };
            });
            setFeedbackData(existingFeedback);
          }
        }
        
      } catch (err: any) {
        toast.error(err.response?.data?.message || 'Erro ao iniciar simulado');
        navigate('/simulados');
      } finally {
        setIsInitializing(false);
      }
    };

    initAttempt();
    
    return () => clearError();
  }, [examId, attemptId, currentAttempt, startAttempt, fetchExam, navigate, clearError]);

  // SEO
  useEffect(() => {
    document.title = 'Executando Simulado | Operação Alfa';
  }, []);

  const handleAnswer = async (letter: AnswerOption) => {
    if (!currentAttempt || !questions[currentQuestion]) return;
    
    const questionId = questions[currentQuestion].id;
    
    // Check if question already has feedback (prevent re-answering in immediate mode)
    if (exam?.feedbackMode === 'immediate' && feedbackData[questionId]) {
      console.log('Question already answered with feedback, ignoring');
      return;
    }
    
    try {
      const response = await submitAnswer(currentAttempt.id, questionId, letter);
      
      console.log('Submit answer response:', response);
      console.log('Exam feedback mode:', exam?.feedbackMode);
      console.log('Response has feedback data:', response?.isCorrect !== undefined);
      
      // Handle immediate feedback mode
      if (exam?.feedbackMode === 'immediate' && response && response.isCorrect !== undefined) {
        console.log('Setting feedback data for question:', questionId);
        console.log('Feedback data:', {
          isCorrect: response.isCorrect,
          correctAnswer: response.correctAnswer,
          explanation: response.explanation,
        });
        setFeedbackData(prev => {
          const newData = {
            ...prev,
            [questionId]: {
              isCorrect: response.isCorrect || false,
              correctAnswer: response.correctAnswer || '',
              explanation: response.explanation,
            },
          };
          console.log('New feedbackData state:', newData);
          return newData;
        });
      }
    } catch (err: any) {
      toast.error('Erro ao salvar resposta');
    }
  };

  const handleNext = () => {
    if (currentQuestion < questions.length - 1) {
      setCurrentQuestion(currentQuestion + 1);
    }
  };

  const handlePrevious = () => {
    if (currentQuestion > 0) {
      setCurrentQuestion(currentQuestion - 1);
    }
  };

  const handleFinish = async () => {
    if (!currentAttempt) return;
    
    try {
      const result = await finishAttempt(currentAttempt.id);
      toast.success('Simulado finalizado!');
      // Navigate to results page with result data
      navigate(`/simulado/${examId}/resultado/${currentAttempt.id}`, {
        state: { result }
      });
    } catch (err: any) {
      toast.error(err.response?.data?.message || 'Erro ao finalizar simulado');
    }
  };

  const handleTimerExpire = () => {
    toast.warning('Tempo esgotado! Finalizando simulado...');
    handleFinish();
  };

  if (isInitializing || isLoading) {
    return (
      <AppLayout>
        <div className="p-4 flex items-center justify-center min-h-[50vh]">
          <div className="text-center space-y-4">
            <Loader2 className="mx-auto h-8 w-8 animate-spin text-primary" />
            <p className="text-muted-foreground">Carregando simulado...</p>
          </div>
        </div>
      </AppLayout>
    );
  }

  if (!currentAttempt || questions.length === 0 || !exam) {
    return (
      <AppLayout>
        <div className="p-4">
          <Alert variant="destructive">
            <AlertCircle className="h-4 w-4" />
            <AlertDescription>
              Não foi possível carregar o simulado. Por favor, tente novamente.
            </AlertDescription>
          </Alert>
          <Button 
            onClick={() => navigate('/simulados')} 
            className="mt-4"
          >
            Voltar para Simulados
          </Button>
        </div>
      </AppLayout>
    );
  }

  const progress = ((currentQuestion + 1) / questions.length) * 100;
  const currentQuestionObj = questions[currentQuestion];
  const selectedAnswer = currentAttempt.answers?.[currentQuestionObj?.id];
  const questionFeedback = feedbackData[currentQuestionObj?.id];

  return (
    <AppLayout>
      <div className="p-4 space-y-4">
        {/* Header with timer and progress */}
        <header className="card-tactical p-4">
          <div className="flex items-center justify-between mb-4">
            <h1 className="text-lg font-semibold text-foreground">
              {exam.title}
            </h1>
            <Timer 
              initialSeconds={exam.durationMin * 60}
              onExpire={handleTimerExpire}
            />
          </div>
          
          <div className="space-y-2">
            <div className="flex justify-between text-sm text-muted-foreground">
              <span>Questão {currentQuestion + 1} de {questions.length}</span>
              <span>{Math.round(progress)}% concluído</span>
            </div>
            <Progress value={progress} className="h-2" />
          </div>
        </header>

        {/* Error Alert */}
        {error && (
          <Alert variant="destructive">
            <AlertCircle className="h-4 w-4" />
            <AlertDescription>{error}</AlertDescription>
          </Alert>
        )}

        {/* Immediate feedback alert */}
        {questionFeedback && exam.feedbackMode === 'immediate' && (
          <Alert variant={questionFeedback.isCorrect ? 'default' : 'destructive'}>
            <AlertCircle className="h-4 w-4" />
            <AlertDescription>
              {questionFeedback.isCorrect ? (
                <span className="text-green-600 font-semibold">Correto!</span>
              ) : (
                <>
                  <span className="text-red-600 font-semibold">Incorreto.</span>
                  {' '}Resposta correta: {questionFeedback.correctAnswer}
                </>
              )}
              {questionFeedback.explanation && (
                <p className="mt-2 text-sm">{questionFeedback.explanation}</p>
              )}
            </AlertDescription>
          </Alert>
        )}

        {/* Current question */}
        {currentQuestionObj && (
          <QuestaoCard
            question={currentQuestionObj}
            questionNumber={currentQuestion + 1}
            selectedAnswer={selectedAnswer}
            onAnswer={handleAnswer}
            showFeedback={exam.feedbackMode === 'immediate' && !!questionFeedback}
            feedbackMode={exam.feedbackMode}
            feedbackCorrectAnswer={questionFeedback?.correctAnswer as AnswerOption | undefined}
            feedbackExplanation={questionFeedback?.explanation}
          />
        )}

        {/* Navigation */}
        <div className="flex items-center justify-between gap-4">
          <Button 
            variant="outline" 
            onClick={handlePrevious}
            disabled={currentQuestion === 0 || isLoading}
          >
            <ChevronLeft size={16} className="mr-1" />
            Anterior
          </Button>

          <div className="flex gap-2">
            {currentQuestion === questions.length - 1 ? (
              <Button 
                onClick={handleFinish} 
                variant="default"
                disabled={isLoading}
              >
                {isLoading ? (
                  <>
                    <Loader2 className="mr-2 h-4 w-4 animate-spin" />
                    Finalizando...
                  </>
                ) : (
                  <>
                    <Flag size={16} className="mr-1" />
                    Finalizar
                  </>
                )}
              </Button>
            ) : (
              <Button 
                onClick={handleNext}
                disabled={currentQuestion === questions.length - 1 || isLoading}
              >
                Próxima
                <ChevronRight size={16} className="ml-1" />
              </Button>
            )}
          </div>
        </div>
      </div>
    </AppLayout>
  );
}
