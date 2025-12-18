import { useState, useEffect } from 'react';
import { useParams, useNavigate, useLocation } from 'react-router-dom';
import { AppLayout } from '@/components/layout/AppLayout';
import { Button } from '@/components/ui/button';
import { Card } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { Alert, AlertDescription } from '@/components/ui/alert';
import { 
  Trophy, 
  Clock, 
  CheckCircle2, 
  XCircle, 
  Home, 
  RotateCcw,
  ChevronDown,
  ChevronUp,
  AlertCircle,
  Loader2
} from 'lucide-react';
import type { ExamResult as ExamResultType, Question, AnswerOption } from '@/types';
import { getAttempt } from '@/services/exams';
import { toast } from 'sonner';

export default function ExamResult() {
  const { attemptId } = useParams();
  const navigate = useNavigate();
  const location = useLocation();
  
  // Result data can come from location state (after finishing) or be fetched
  const [result, setResult] = useState<ExamResultType | null>(location.state?.result || null);
  const [questions, setQuestions] = useState<Question[]>([]);
  const [userAnswers, setUserAnswers] = useState<Record<string, AnswerOption>>({});
  const [expandedQuestions, setExpandedQuestions] = useState<Set<string>>(new Set());
  const [isLoading, setIsLoading] = useState(!location.state?.result);
  const [error, setError] = useState<string | null>(null);

  useEffect(() => {
    document.title = 'Resultado do Simulado | Operação Alfa';
  }, []);

  useEffect(() => {
    const loadAttemptData = async () => {
      if (!attemptId) {
        navigate('/simulados');
        return;
      }

      try {
        setIsLoading(true);
        const attemptData = await getAttempt(attemptId);
        
        // Set questions and answers
        if (attemptData.questions) {
          setQuestions(attemptData.questions);
        }
        
        if (attemptData.answers) {
          setUserAnswers(attemptData.answers);
        }
        
        // If we don't have result data from navigation, construct it from attempt
        if (!result && attemptData.finishedAt) {
          setResult({
            attemptId: attemptData.id,
            totalQuestions: attemptData.questions?.length || 0,
            correctAnswers: attemptData.correctAnswers || 0,
            finalScore: attemptData.score || 0,
            totalTimeSeconds: attemptData.durationSeconds || 0,
          });
        }
        
      } catch (err: any) {
        console.error('Failed to load attempt data:', err);
        setError(err.response?.data?.message || 'Erro ao carregar resultado');
        toast.error('Erro ao carregar resultado do simulado');
      } finally {
        setIsLoading(false);
      }
    };

    loadAttemptData();
  }, [attemptId, result, navigate]);

  const toggleQuestion = (questionId: string) => {
    setExpandedQuestions(prev => {
      const newSet = new Set(prev);
      if (newSet.has(questionId)) {
        newSet.delete(questionId);
      } else {
        newSet.add(questionId);
      }
      return newSet;
    });
  };

  const formatTime = (seconds: number): string => {
    const hours = Math.floor(seconds / 3600);
    const minutes = Math.floor((seconds % 3600) / 60);
    const secs = seconds % 60;
    
    if (hours > 0) {
      return `${hours}h ${minutes}m ${secs}s`;
    }
    return `${minutes}m ${secs}s`;
  };

  if (isLoading) {
    return (
      <AppLayout>
        <div className="p-4 flex items-center justify-center min-h-[50vh]">
          <div className="text-center space-y-4">
            <Loader2 className="mx-auto h-8 w-8 animate-spin text-primary" />
            <p className="text-muted-foreground">Carregando resultado...</p>
          </div>
        </div>
      </AppLayout>
    );
  }

  if (error || !result) {
    return (
      <AppLayout>
        <div className="p-4">
          <Alert variant="destructive">
            <AlertCircle className="h-4 w-4" />
            <AlertDescription>
              {error || 'Não foi possível carregar o resultado. Por favor, tente novamente.'}
            </AlertDescription>
          </Alert>
          <Button 
            onClick={() => navigate('/desempenho')} 
            className="mt-4"
          >
            Ver Desempenho
          </Button>
        </div>
      </AppLayout>
    );
  }

  const scorePercentage = result.finalScore;
  const correctCount = result.correctAnswers;
  const totalQuestions = result.totalQuestions;
  const timeSpent = formatTime(result.totalTimeSeconds);

  return (
    <AppLayout>
      <div className="p-4 space-y-6">
        {/* Score Summary Card */}
        <Card className="card-tactical p-6">
          <div className="text-center space-y-4">
            <div className="flex justify-center">
              <div className={`p-4 rounded-full ${
                scorePercentage >= 70 
                  ? 'bg-green-100 text-green-600' 
                  : scorePercentage >= 50 
                  ? 'bg-yellow-100 text-yellow-600' 
                  : 'bg-red-100 text-red-600'
              }`}>
                <Trophy size={48} />
              </div>
            </div>
            
            <div>
              <h1 className="text-3xl font-bold text-foreground mb-2">
                {scorePercentage.toFixed(1)}%
              </h1>
              <p className="text-muted-foreground">
                Sua pontuação final
              </p>
            </div>

            <div className="grid grid-cols-2 gap-4 pt-4 border-t border-border">
              <div className="space-y-1">
                <div className="flex items-center justify-center gap-2 text-green-600">
                  <CheckCircle2 size={20} />
                  <span className="text-2xl font-semibold">{correctCount}</span>
                </div>
                <p className="text-sm text-muted-foreground">Acertos</p>
              </div>
              
              <div className="space-y-1">
                <div className="flex items-center justify-center gap-2 text-red-600">
                  <XCircle size={20} />
                  <span className="text-2xl font-semibold">{totalQuestions - correctCount}</span>
                </div>
                <p className="text-sm text-muted-foreground">Erros</p>
              </div>
            </div>

            <div className="flex items-center justify-center gap-2 text-muted-foreground pt-2">
              <Clock size={16} />
              <span className="text-sm">Tempo: {timeSpent}</span>
            </div>
          </div>
        </Card>

        {/* Performance Message */}
        <Alert variant={scorePercentage >= 70 ? 'default' : 'destructive'}>
          <AlertDescription className="text-center">
            {scorePercentage >= 70 ? (
              <span className="text-green-600 font-semibold">
                Parabéns! Excelente desempenho!
              </span>
            ) : scorePercentage >= 50 ? (
              <span className="text-yellow-600 font-semibold">
                Bom trabalho! Continue praticando para melhorar.
              </span>
            ) : (
              <span className="text-red-600 font-semibold">
                Continue estudando! A prática leva à perfeição.
              </span>
            )}
          </AlertDescription>
        </Alert>

        {/* Questions Review Section */}
        <div className="space-y-4">
          <h2 className="text-xl font-semibold text-foreground">
            Revisão das Questões
          </h2>
          
          {questions.map((question, index) => {
            const userAnswer = userAnswers[question.id];
            const isCorrect = userAnswer === question.correctAnswer;
            const isExpanded = expandedQuestions.has(question.id);
            
            return (
              <Card key={question.id} className="card-tactical overflow-hidden">
                {/* Question Header - Always Visible */}
                <button
                  onClick={() => toggleQuestion(question.id)}
                  className="w-full p-4 flex items-center justify-between hover:bg-muted/50 transition-colors"
                >
                  <div className="flex items-center gap-3">
                    <div className={`p-2 rounded-full ${
                      isCorrect 
                        ? 'bg-green-100 text-green-600' 
                        : 'bg-red-100 text-red-600'
                    }`}>
                      {isCorrect ? (
                        <CheckCircle2 size={20} />
                      ) : (
                        <XCircle size={20} />
                      )}
                    </div>
                    
                    <div className="text-left">
                      <p className="font-semibold text-foreground">
                        Questão {index + 1}
                      </p>
                      <div className="flex items-center gap-2 mt-1">
                        <Badge variant={isCorrect ? 'default' : 'destructive'} className="text-xs">
                          {isCorrect ? 'Correta' : 'Incorreta'}
                        </Badge>
                        {userAnswer && (
                          <span className="text-xs text-muted-foreground">
                            Sua resposta: {userAnswer}
                          </span>
                        )}
                      </div>
                    </div>
                  </div>
                  
                  {isExpanded ? (
                    <ChevronUp size={20} className="text-muted-foreground" />
                  ) : (
                    <ChevronDown size={20} className="text-muted-foreground" />
                  )}
                </button>

                {/* Question Details - Expandable */}
                {isExpanded && (
                  <div className="p-4 pt-0 space-y-4 border-t border-border">
                    {/* Statement */}
                    <div>
                      <p className="text-foreground leading-relaxed">
                        {question.statement}
                      </p>
                      
                      {question.statementImage && (
                        <div className="mt-3 flex justify-center">
                          <img 
                            src={question.statementImage} 
                            alt="Imagem do enunciado"
                            className="max-w-full h-auto rounded-lg border border-border"
                          />
                        </div>
                      )}
                    </div>

                    {/* Options */}
                    <div className="space-y-2">
                      {question.options.map((option) => {
                        const isUserAnswer = userAnswer === option.letter;
                        const isCorrectAnswer = question.correctAnswer === option.letter;
                        
                        let bgColor = 'bg-muted/30';
                        let borderColor = 'border-border';
                        let textColor = 'text-foreground';
                        
                        if (isCorrectAnswer) {
                          bgColor = 'bg-green-50';
                          borderColor = 'border-green-600';
                          textColor = 'text-green-900';
                        } else if (isUserAnswer && !isCorrect) {
                          bgColor = 'bg-red-50';
                          borderColor = 'border-red-600';
                          textColor = 'text-red-900';
                        }
                        
                        return (
                          <div key={option.letter} className="space-y-2">
                            <div className={`p-3 rounded-lg border-2 ${bgColor} ${borderColor}`}>
                              <div className="flex items-start gap-2">
                                <span className={`font-semibold flex-shrink-0 ${textColor}`}>
                                  {option.letter})
                                </span>
                                <span className={`flex-1 ${textColor}`}>
                                  {option.text}
                                </span>
                                {isCorrectAnswer && (
                                  <CheckCircle2 size={18} className="text-green-600 flex-shrink-0" />
                                )}
                                {isUserAnswer && !isCorrect && (
                                  <XCircle size={18} className="text-red-600 flex-shrink-0" />
                                )}
                              </div>
                            </div>
                            
                            {option.image && (
                              <div className="flex justify-center pl-6">
                                <img 
                                  src={option.image} 
                                  alt={`Imagem da alternativa ${option.letter}`}
                                  className="max-w-full h-auto rounded-lg border border-border"
                                />
                              </div>
                            )}
                          </div>
                        );
                      })}
                    </div>

                    {/* Explanation */}
                    {question.explanation && (
                      <div className="p-4 bg-blue-50 rounded-lg border border-blue-200">
                        <h4 className="font-semibold text-blue-900 mb-2">Explicação:</h4>
                        <p className="text-blue-800 text-sm leading-relaxed">
                          {question.explanation}
                        </p>
                      </div>
                    )}
                  </div>
                )}
              </Card>
            );
          })}
        </div>

        {/* Action Buttons */}
        <div className="flex flex-col sm:flex-row gap-3 pt-4">
          <Button 
            onClick={() => navigate('/desempenho')}
            variant="default"
            className="flex-1"
          >
            <Trophy size={16} className="mr-2" />
            Ver Desempenho
          </Button>
          
          <Button 
            onClick={() => navigate('/simulados')}
            variant="outline"
            className="flex-1"
          >
            <Home size={16} className="mr-2" />
            Voltar aos Simulados
          </Button>
        </div>
      </div>
    </AppLayout>
  );
}
