import { useState, useEffect } from 'react';
import { useParams, Link } from 'react-router-dom';
import { ArrowLeft, Play, Clock, FileText, AlertCircle, Loader2 } from 'lucide-react';
import { AppLayout } from '@/components/layout/AppLayout';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import { Alert, AlertDescription } from '@/components/ui/alert';
import { careersService } from '@/services/careers';
import type { Career } from '@/types';

/** Exam data as returned by the /careers/{id}/exams API endpoint */
interface CareerExam {
  id: number;
  career_id: number;
  title: string;
  description?: string;
  time_limit_minutes: number;
  active: boolean;
  questions_count: number;
  is_free?: boolean;
  feedback_mode?: string;
}

export default function CarreiraSimulados() {
  const { carreiraId } = useParams<{ carreiraId: string }>();
  const [career, setCareer] = useState<Career | null>(null);
  const [exams, setExams] = useState<CareerExam[]>([]);
  const [isLoading, setIsLoading] = useState(true);
  const [error, setError] = useState<string | null>(null);

  useEffect(() => {
    const fetchData = async () => {
      if (!carreiraId) return;
      
      try {
        setIsLoading(true);
        setError(null);
        
        // Fetch career details
        const careerData = await careersService.getCareer(carreiraId);
        setCareer(careerData);
        
        // Fetch exams for this career
        const examsData = await careersService.getCareerExams(carreiraId);
        setExams(examsData);
      } catch (err: any) {
        console.error('Error fetching career data:', err);
        setError(err?.response?.data?.message || 'Erro ao carregar dados da carreira');
      } finally {
        setIsLoading(false);
      }
    };

    fetchData();
  }, [carreiraId]);

  if (isLoading) {
    return (
      <AppLayout>
        <div className="p-4 flex items-center justify-center min-h-[50vh]">
          <div className="text-center space-y-4">
            <Loader2 className="mx-auto h-8 w-8 animate-spin text-primary" />
            <p className="text-muted-foreground">Carregando dados da carreira...</p>
          </div>
        </div>
      </AppLayout>
    );
  }

  if (error || !career) {
    return (
      <AppLayout>
        <div className="p-4 space-y-6">
          <Link to="/carreiras">
            <Button variant="ghost" size="sm">
              <ArrowLeft className="mr-2" size={16} />
              Voltar para Carreiras
            </Button>
          </Link>
          
          {error && (
            <Alert variant="destructive">
              <AlertCircle className="h-4 w-4" />
              <AlertDescription>{error}</AlertDescription>
            </Alert>
          )}
          
          <div className="text-center py-12 space-y-4">
            <h2 className="text-xl font-semibold text-foreground">
              Carreira não encontrada
            </h2>
            <p className="text-muted-foreground">
              A carreira que você está procurando não existe ou não está disponível.
            </p>
          </div>
        </div>
      </AppLayout>
    );
  }

  // Calculate statistics from real data
  const totalExams = exams.length;
  const avgDuration = exams.length > 0 
    ? Math.round(exams.reduce((sum, exam) => sum + exam.time_limit_minutes, 0) / exams.length)
    : 0;
  const avgQuestions = exams.length > 0
    ? Math.round(exams.reduce((sum, exam) => sum + exam.questions_count, 0) / exams.length)
    : 0;

  return (
    <AppLayout>
      <div className="p-4 space-y-6">
        {/* Header */}
        <div className="space-y-4">
          <Link to="/carreiras">
            <Button variant="ghost" size="sm" className="mb-2">
              <ArrowLeft className="mr-2" size={16} />
              Voltar para Carreiras
            </Button>
          </Link>
          
          <div>
            <h1 className="text-2xl font-bold text-foreground mb-2">
              {career.name}
            </h1>
            {career.description && (
              <p className="text-muted-foreground">
                {career.description}
              </p>
            )}
          </div>
        </div>

        {/* Estatísticas */}
        <div className="grid grid-cols-3 gap-4">
          <div className="card-tactical p-4 text-center">
            <div className="text-2xl font-bold text-primary">{totalExams}</div>
            <div className="text-xs text-muted-foreground">Simulados</div>
          </div>
          <div className="card-tactical p-4 text-center">
            <div className="text-2xl font-bold text-primary">
              {avgDuration > 0 ? `${avgDuration}m` : '-'}
            </div>
            <div className="text-xs text-muted-foreground">Duração Média</div>
          </div>
          <div className="card-tactical p-4 text-center">
            <div className="text-2xl font-bold text-primary">
              {avgQuestions > 0 ? avgQuestions : '-'}
            </div>
            <div className="text-xs text-muted-foreground">Questões</div>
          </div>
        </div>

        {/* Lista de Simulados */}
        <div className="space-y-4">
          <h2 className="text-lg font-semibold text-foreground">
            Simulados Disponíveis
          </h2>
          
          {exams.length > 0 ? (
            <div className="space-y-3">
              {exams.map((exam) => (
                <div key={exam.id} className="card-tactical p-4">
                  <div className="space-y-4">
                    {/* Header do Simulado */}
                    <div className="flex items-start justify-between gap-4">
                      <div className="flex-1">
                        <h3 className="font-semibold text-foreground mb-2">
                          {exam.title}
                        </h3>
                        {exam.description && (
                          <p className="text-sm text-muted-foreground mb-2">
                            {exam.description}
                          </p>
                        )}
                        <div className="flex flex-wrap gap-2 text-sm text-muted-foreground">
                          <div className="flex items-center gap-1">
                            <Clock size={14} />
                            <span>{exam.time_limit_minutes} min</span>
                          </div>
                          <div className="flex items-center gap-1">
                            <FileText size={14} />
                            <span>{exam.questions_count} questões</span>
                          </div>
                        </div>
                      </div>
                      <div className="flex flex-col gap-2">
                        <Badge variant={exam.active ? 'default' : 'secondary'}>
                          {exam.active ? 'Disponível' : 'Em breve'}
                        </Badge>
                        {exam.is_free && (
                          <Badge variant="outline" className="text-xs">
                            Gratuito
                          </Badge>
                        )}
                      </div>
                    </div>

                    {/* Informações do Simulado */}
                    <div className="bg-muted/30 rounded-lg p-3 space-y-2">
                      <div className="text-sm">
                        <span className="text-muted-foreground">Feedback:</span>{' '}
                        <span className="text-foreground font-medium">
                          {exam.feedback_mode === 'immediate' ? 'Imediato' : 'Ao Final'}
                        </span>
                      </div>
                      <div className="text-sm text-muted-foreground">
                        {exam.feedback_mode === 'immediate' 
                          ? 'Você verá o resultado após cada questão'
                          : 'Você verá o resultado apenas ao finalizar o simulado'
                        }
                      </div>
                    </div>

                    {/* Ações */}
                    <div className="flex gap-3">
                      <Link to={`/simulado/${exam.id}`} className="flex-1">
                        <Button 
                          variant="tactical" 
                          className="w-full"
                          disabled={!exam.active}
                        >
                          <Play className="mr-2" size={16} />
                          {exam.active ? 'Iniciar Simulado' : 'Em Breve'}
                        </Button>
                      </Link>
                    </div>
                  </div>
                </div>
              ))}
            </div>
          ) : (
            <div className="text-center py-12 space-y-4">
              <FileText className="mx-auto text-muted-foreground" size={48} />
              <div>
                <h3 className="font-medium text-foreground">
                  Nenhum simulado disponível
                </h3>
                <p className="text-sm text-muted-foreground mt-1">
                  Simulados para esta carreira serão adicionados em breve
                </p>
              </div>
              <Link to="/carreiras">
                <Button variant="outline">
                  Explorar Outras Carreiras
                </Button>
              </Link>
            </div>
          )}
        </div>
      </div>
    </AppLayout>
  );
}