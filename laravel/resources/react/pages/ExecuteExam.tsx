import { useState, useEffect, useMemo } from 'react';
import { useParams, useNavigate } from 'react-router-dom';
import { AppLayout } from '@/components/layout/AppLayout';
import { QuestaoCard } from '@/components/QuestaoCard';
import { useExamsStore } from '@/stores/examsStore';
import { Button } from '@/components/ui/button';
import { Progress } from '@/components/ui/progress';
import { Alert, AlertDescription } from '@/components/ui/alert';
import { Clock, ChevronLeft, ChevronRight, Flag, AlertCircle, Loader2 } from 'lucide-react';
import type { AnswerOption, Question } from '@/types';
import { toast } from 'sonner';

export default function ExecuteExam() {
  const { examId, attemptId } = useParams();
  const navigate = useNavigate();
  const { 
    currentAttempt, 
    startAttempt, 
    submitAnswer, 
    finishAttempt, 
    isLoading, 
    error,
    clearError 
  } = useExamsStore();
  
  const [currentQuestion, setCurrentQuestion] = useState(0);
  const [timeRemaining, setTimeRemaining] = useState(0);
  const [answers, setAnswers] = useState<Record<string, AnswerOption>>({});
  const [questions, setQuestions] = useState<Question[]>([]);
  const [isInitializing, setIsInitializing] = useState(true);

  // Initialize attempt
  useEffect(() => {
    const initAttempt = async () => {
      if (!examId) {
        navigate('/simulados');
        return;
      }

      try {
        setIsInitializing(true);
        
        // If no attemptId, start a new attempt
        if (!attemptId && !currentAttempt) {
          const attempt = await startAttempt(examId);
          // Redirect to the attempt URL
          navigate(`/simulado/${examId}/tentativa/${attempt.id}`, { replace: true });
        }
        
        // TODO: Fetch questions for the exam
        // For now, we'll use mock data structure
        setQuestions([]);
        
      } catch (err: any) {
        toast.error(err.response?.data?.message || 'Erro ao iniciar simulado');
        navigate('/simulados');
      } finally {
        setIsInitializing(false);
      }
    };

    initAttempt();
    
    return () => clearError();
  }, [examId, attemptId, currentAttempt, startAttempt, navigate, clearError]);

  // Timer
  useEffect(() => {
    if (!currentAttempt || timeRemaining === 0) return;
    
    const timer = setInterval(() => {
      setTimeRemaining(prev => {
        if (prev <= 1) {
          handleFinish();
          return 0;
        }
        return prev - 1;
      });
    }, 1000);

    return () => clearInterval(timer);
  }, [currentAttempt, timeRemaining]);

  // SEO
  useEffect(() => {
    document.title = 'Executando Simulado | Operação Alfa';
  }, []);

  const formatTime = (seconds: number) => {
    const hours = Math.floor(seconds / 3600);
    const mins = Math.floor((seconds % 3600) / 60);
    const secs = seconds % 60;
    
    if (hours > 0) {
      return `${hours}:${mins.toString().padStart(2, '0')}:${secs.toString().padStart(2, '0')}`;
    }
    return `${mins}:${secs.toString().padStart(2, '0')}`;
  };

  const handleAnswer = async (letter: AnswerOption) => {
    if (!currentAttempt || !questions[currentQuestion]) return;
    
    const questionId = questions[currentQuestion].id;
    const newAnswers = { ...answers, [questionId]: letter };
    
    setAnswers(newAnswers);
    
    try {
      await submitAnswer(currentAttempt.id, questionId, letter);
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
      await finishAttempt(currentAttempt.id);
      toast.success('Simulado finalizado!');
      navigate('/desempenho');
    } catch (err: any) {
      toast.error(err.response?.data?.message || 'Erro ao finalizar simulado');
    }
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

  if (!currentAttempt || questions.length === 0) {
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
  const selectedAnswer = answers[currentQuestionObj?.id];

  return (
    <AppLayout>
      <div className="p-4 space-y-4">
        {/* Header with timer and progress */}
        <header className="card-tactical p-4">
          <div className="flex items-center justify-between mb-4">
            <h1 className="text-lg font-semibold text-foreground">
              Simulado em Andamento
            </h1>
            <div className="flex items-center gap-2 text-sm">
              <Clock size={16} />
              <span className={`font-mono ${timeRemaining < 300 ? 'text-red-600' : 'text-foreground'}`}>
                {formatTime(timeRemaining)}
              </span>
            </div>
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

        {/* Current question */}
        {currentQuestionObj && (
          <QuestaoCard
            questao={{
              ...currentQuestionObj,
              enunciado: currentQuestionObj.statement,
              alternativas: currentQuestionObj.options.map(opt => ({
                letra: opt.letter,
                texto: opt.text,
              })),
            }}
            numeroQuestao={currentQuestion + 1}
            respostaSelecionada={selectedAnswer}
            onResposta={handleAnswer}
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
