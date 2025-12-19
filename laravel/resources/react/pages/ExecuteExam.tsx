import { useState, useEffect, useRef } from 'react';
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
import { toast } from 'sonner';

export default function ExecuteExam() {
  const { examId, attemptId } = useParams();
  const navigate = useNavigate();
  const { 
    currentAttempt, 
    startAttempt,
    loadAttempt,
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
  const [timerSeconds, setTimerSeconds] = useState<number | null>(null);
  const initializedRef = useRef(false);
  
  // Helper to get localStorage key for current question
  const getStorageKey = (attId: string) => `attempt_${attId}_currentQuestion`;

  // Initialize attempt and load questions - runs only once per examId/attemptId combination
  useEffect(() => {
    // Prevent re-initialization when currentAttempt changes (e.g., after submitting answer)
    if (initializedRef.current) {
      return;
    }
    
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
        
        // Load the attempt
        const attemptData = await loadAttempt(attemptId);
        if (attemptData && attemptData.questions) {
          setQuestions(attemptData.questions);
          
          // Set timer from API (remaining time)
          if (attemptData.initialTimerSeconds !== undefined) {
            setTimerSeconds(attemptData.initialTimerSeconds);
          } else if (examData) {
            // Fallback to full duration
            setTimerSeconds(examData.durationMin * 60);
          }
          
          // Restore current question from localStorage
          const savedQuestion = localStorage.getItem(getStorageKey(attemptId));
          if (savedQuestion) {
            const questionIndex = parseInt(savedQuestion, 10);
            if (!isNaN(questionIndex) && questionIndex >= 0 && questionIndex < attemptData.questions.length) {
              setCurrentQuestion(questionIndex);
            }
          }
        }
        
        // For immediate feedback mode, use feedbackData from API if available
        if (examData?.feedbackMode === 'immediate') {
          if (attemptData?.feedbackData) {
            // Use feedback data from API (includes correct answers)
            const existingFeedback: Record<string, { isCorrect: boolean; correctAnswer: string; explanation?: string }> = {};
            Object.entries(attemptData.feedbackData).forEach(([questionId, feedback]) => {
              existingFeedback[String(questionId)] = {
                isCorrect: feedback.isCorrect,
                correctAnswer: feedback.correctAnswer,
                explanation: feedback.explanation,
              };
            });
            setFeedbackData(existingFeedback);
          } else if (attemptData?.answers) {
            // Fallback: mark as answered but without feedback details
            const existingFeedback: Record<string, { isCorrect: boolean; correctAnswer: string; explanation?: string }> = {};
            Object.entries(attemptData.answers).forEach(([questionId]) => {
              existingFeedback[String(questionId)] = {
                isCorrect: false,
                correctAnswer: '',
                explanation: undefined,
              };
            });
            setFeedbackData(existingFeedback);
          }
        }
        
        // Mark as initialized
        initializedRef.current = true;
        
      } catch (err: any) {
        toast.error(err.response?.data?.message || 'Erro ao iniciar simulado');
        navigate('/simulados');
      } finally {
        setIsInitializing(false);
      }
    };

    initAttempt();
    
    return () => clearError();
  }, [examId, attemptId, startAttempt, loadAttempt, fetchExam, navigate, clearError]);

  // SEO
  useEffect(() => {
    document.title = 'Executando Simulado | Operação Alfa';
  }, []);

  const handleAnswer = async (letter: AnswerOption) => {
    console.log('handleAnswer called with:', letter);
    
    if (!currentAttempt || !questions[currentQuestion]) {
      console.log('No currentAttempt or question, returning');
      return;
    }
    
    const questionId = questions[currentQuestion].id;
    console.log('Question ID:', questionId, 'feedbackData keys:', Object.keys(feedbackData));
    
    // Check if question already has feedback (prevent re-answering in immediate mode)
    if (exam?.feedbackMode === 'immediate' && feedbackData[questionId]) {
      console.log('Question already answered with feedback, ignoring');
      return;
    }
    
    try {
      console.log('Submitting answer...');
      const response = await submitAnswer(currentAttempt.id, questionId, letter);
      console.log('Submit response:', response);
      
      // Handle immediate feedback mode
      if (exam?.feedbackMode === 'immediate' && response && response.isCorrect !== undefined) {
        const key = String(questionId);
        console.log('Setting feedback for key:', key, 'response:', response);
        console.log('response.correctAnswer:', response.correctAnswer, 'type:', typeof response.correctAnswer);
        
        const newFeedback = {
          isCorrect: response.isCorrect || false,
          correctAnswer: response.correctAnswer || '',
          explanation: response.explanation,
        };
        console.log('New feedback item:', newFeedback);
        
        setFeedbackData(prev => {
          const newState = {
            ...prev,
            [key]: newFeedback,
          };
          console.log('New feedbackData state:', newState);
          return newState;
        });
      }
    } catch (err: any) {
      console.error('Error submitting answer:', err);
      toast.error('Erro ao salvar resposta');
    }
  };

  const handleNext = () => {
    if (currentQuestion < questions.length - 1) {
      const newIndex = currentQuestion + 1;
      setCurrentQuestion(newIndex);
      if (attemptId) {
        localStorage.setItem(getStorageKey(attemptId), String(newIndex));
      }
    }
  };

  const handlePrevious = () => {
    if (currentQuestion > 0) {
      const newIndex = currentQuestion - 1;
      setCurrentQuestion(newIndex);
      if (attemptId) {
        localStorage.setItem(getStorageKey(attemptId), String(newIndex));
      }
    }
  };

  const handleFinish = async () => {
    if (!currentAttempt) return;
    
    try {
      const result = await finishAttempt(currentAttempt.id);
      // Clean up localStorage
      localStorage.removeItem(getStorageKey(currentAttempt.id));
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

  if (isInitializing) {
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
  const questionIdStr = String(currentQuestionObj?.id);
  const selectedAnswer = currentAttempt.answers?.[questionIdStr];
  const questionFeedback = feedbackData[questionIdStr];
  
  console.log('ExecuteExam render:', { 
    questionIdStr, 
    feedbackDataKeys: Object.keys(feedbackData), 
    questionFeedback,
    feedbackCorrectAnswer: questionFeedback?.correctAnswer
  });

  return (
    <AppLayout>
      <div className="p-4 space-y-4">
        {/* Header with timer and progress */}
        <header className="card-tactical p-4">
          <div className="flex items-center justify-between mb-4">
            <h1 className="text-lg font-semibold text-foreground">
              {exam.title}
            </h1>
            {timerSeconds !== null && (
              <Timer 
                initialSeconds={timerSeconds}
                onExpire={handleTimerExpire}
              />
            )}
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
            feedbackCorrectAnswer={questionFeedback?.correctAnswer ? questionFeedback.correctAnswer as AnswerOption : undefined}
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
