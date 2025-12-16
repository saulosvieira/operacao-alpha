import { useState, useEffect } from 'react';
import { Calendar, TrendingUp, Clock, Target, AlertCircle, Loader2 } from 'lucide-react';
import { AppLayout } from '@/components/layout/AppLayout';
import { Button } from '@/components/ui/button';
import { Card } from '@/components/ui/card';
import { Tabs, TabsContent, TabsList, TabsTrigger } from '@/components/ui/tabs';
import { Badge } from '@/components/ui/badge';
import { Alert, AlertDescription } from '@/components/ui/alert';
import { performanceService } from '@/services/performance';
import type { Statistics, HistoryEntry } from '@/types';

export default function Performance() {
  const [period, setPeriod] = useState('30d');
  const [statistics, setStatistics] = useState<Statistics | null>(null);
  const [history, setHistory] = useState<HistoryEntry[]>([]);
  const [isLoading, setIsLoading] = useState(true);
  const [error, setError] = useState<string | null>(null);

  useEffect(() => {
    const fetchData = async () => {
      try {
        setIsLoading(true);
        setError(null);
        
        const [statsData, historyData] = await Promise.all([
          performanceService.getStatistics(),
          performanceService.getHistory(),
        ]);
        
        setStatistics(statsData);
        setHistory(historyData);
      } catch (err: any) {
        setError(err.response?.data?.message || 'Erro ao carregar dados de desempenho');
      } finally {
        setIsLoading(false);
      }
    };

    fetchData();
  }, [period]);

  const getBadgeVariant = (score: number) => {
    if (score >= 80) return 'default';
    if (score >= 60) return 'secondary';
    return 'destructive';
  };

  if (isLoading) {
    return (
      <AppLayout>
        <div className="p-4 flex items-center justify-center min-h-[50vh]">
          <div className="text-center space-y-4">
            <Loader2 className="mx-auto h-8 w-8 animate-spin text-primary" />
            <p className="text-muted-foreground">Carregando desempenho...</p>
          </div>
        </div>
      </AppLayout>
    );
  }

  return (
    <AppLayout>
      <div className="p-4 space-y-6">
        {/* Header */}
        <div className="space-y-4">
          <div className="flex items-center justify-between">
            <div>
              <h1 className="text-2xl font-bold text-foreground">Desempenho</h1>
              <p className="text-muted-foreground">Acompanhe sua evolução</p>
            </div>
            <TrendingUp className="text-primary" size={24} />
          </div>

          {/* Period Filter */}
          <div className="flex gap-2">
            {[
              { value: '7d', label: '7 dias' },
              { value: '30d', label: '30 dias' },
              { value: '90d', label: '90 dias' }
            ].map((option) => (
              <Button
                key={option.value}
                variant={period === option.value ? 'tactical' : 'outline'}
                size="sm"
                onClick={() => setPeriod(option.value)}
              >
                {option.label}
              </Button>
            ))}
          </div>
        </div>

        {/* Error Alert */}
        {error && (
          <Alert variant="destructive">
            <AlertCircle className="h-4 w-4" />
            <AlertDescription>{error}</AlertDescription>
          </Alert>
        )}

        {/* Overall Summary */}
        {statistics && (
          <div className="grid grid-cols-2 gap-4">
            <Card className="p-4 text-center">
              <Target className="text-primary mx-auto mb-2" size={24} />
              <div className="text-2xl font-bold text-foreground">
                {statistics.averageScore.toFixed(0)}%
              </div>
              <div className="text-sm text-muted-foreground">Média Geral</div>
            </Card>
            <Card className="p-4 text-center">
              <TrendingUp className="text-primary mx-auto mb-2" size={24} />
              <div className="text-2xl font-bold text-foreground">
                {statistics.totalExams}
              </div>
              <div className="text-sm text-muted-foreground">Simulados</div>
            </Card>
          </div>
        )}

        {/* Detail Tabs */}
        <Tabs defaultValue="history" className="space-y-4">
          <TabsList className="grid w-full grid-cols-2">
            <TabsTrigger value="history">Histórico</TabsTrigger>
            <TabsTrigger value="stats">Estatísticas</TabsTrigger>
          </TabsList>

          <TabsContent value="history" className="space-y-4">
            {history.length > 0 ? (
              <div className="space-y-3">
                {history.map((entry) => (
                  <Card key={entry.id} className="p-4">
                    <div className="space-y-3">
                      <div className="flex items-start justify-between">
                        <div className="flex-1">
                          <h3 className="font-semibold text-foreground text-sm">
                            {entry.examTitle}
                          </h3>
                          <div className="flex items-center gap-2 mt-1">
                            <Calendar className="text-muted-foreground" size={14} />
                            <span className="text-xs text-muted-foreground">
                              {new Date(entry.completedAt).toLocaleDateString('pt-BR')}
                            </span>
                          </div>
                        </div>
                        <Badge variant={getBadgeVariant(entry.score)}>
                          {entry.score.toFixed(0)}%
                        </Badge>
                      </div>
                      <div className="flex justify-between text-sm">
                        <span className="text-muted-foreground">
                          Acertos: {entry.correctAnswers}/{entry.totalQuestions}
                        </span>
                      </div>
                    </div>
                  </Card>
                ))}
              </div>
            ) : (
              <div className="text-center py-12 space-y-4">
                <Clock className="mx-auto text-muted-foreground" size={48} />
                <div>
                  <h3 className="font-medium text-foreground">Nenhum histórico disponível</h3>
                  <p className="text-sm text-muted-foreground mt-1">
                    Complete simulados para ver seu histórico
                  </p>
                </div>
              </div>
            )}
          </TabsContent>

          <TabsContent value="stats" className="space-y-4">
            {statistics && statistics.recentExams.length > 0 ? (
              <div className="space-y-3">
                <Card className="p-4">
                  <div className="space-y-3">
                    <h3 className="font-semibold text-foreground">Resumo Geral</h3>
                    <div className="space-y-2">
                      <div className="flex justify-between text-sm">
                        <span className="text-muted-foreground">Total de Simulados:</span>
                        <span className="font-medium">{statistics.totalExams}</span>
                      </div>
                      <div className="flex justify-between text-sm">
                        <span className="text-muted-foreground">Média de Acertos:</span>
                        <span className="font-medium">{statistics.averageScore.toFixed(1)}%</span>
                      </div>
                      <div className="flex justify-between text-sm">
                        <span className="text-muted-foreground">Melhor Nota:</span>
                        <span className="font-medium">{statistics.bestScore.toFixed(1)}%</span>
                      </div>
                      <div className="flex justify-between text-sm">
                        <span className="text-muted-foreground">Total de Questões:</span>
                        <span className="font-medium">{statistics.totalQuestions}</span>
                      </div>
                      <div className="flex justify-between text-sm">
                        <span className="text-muted-foreground">Acertos Totais:</span>
                        <span className="font-medium">{statistics.totalCorrectAnswers}</span>
                      </div>
                    </div>
                  </div>
                </Card>

                <div className="space-y-2">
                  <h3 className="font-semibold text-foreground text-sm">Simulados Recentes</h3>
                  {statistics.recentExams.map((exam) => (
                    <Card key={exam.examId} className="p-3">
                      <div className="flex items-center justify-between">
                        <div className="flex-1">
                          <p className="text-sm font-medium text-foreground">{exam.examTitle}</p>
                          <p className="text-xs text-muted-foreground">
                            {new Date(exam.completedAt).toLocaleDateString('pt-BR')}
                          </p>
                        </div>
                        <Badge variant={getBadgeVariant(exam.score)}>
                          {exam.score.toFixed(0)}%
                        </Badge>
                      </div>
                    </Card>
                  ))}
                </div>
              </div>
            ) : (
              <div className="text-center py-12 space-y-4">
                <Target className="mx-auto text-muted-foreground" size={48} />
                <div>
                  <h3 className="font-medium text-foreground">Nenhuma estatística disponível</h3>
                  <p className="text-sm text-muted-foreground mt-1">
                    Complete simulados para ver suas estatísticas
                  </p>
                </div>
              </div>
            )}
          </TabsContent>
        </Tabs>
      </div>
    </AppLayout>
  );
}
