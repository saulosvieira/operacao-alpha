import { useEffect, useMemo } from 'react';
import { Link, useParams, useNavigate } from 'react-router-dom';
import { AppLayout } from '@/components/layout/AppLayout';
import { useExamsStore } from '@/stores/examsStore';
import { useAuthStore } from '@/stores/authStore';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Clock, BookOpen, ChevronLeft, Play, Lock } from 'lucide-react';
import type { Exam } from '@/types';
import { toast } from 'sonner';

export default function Simulado() {
  const { simuladoId } = useParams();
  const navigate = useNavigate();
  const { exams, fetchExams, isLoading, startAttempt } = useExamsStore();
  const user = useAuthStore((s) => s.user);
  const isSubscribed = user?.subscriptionStatus === 'active' || user?.subscriptionStatus === 'trial';

  useEffect(() => {
    if (exams.length === 0) {
      fetchExams();
    }
  }, [exams.length, fetchExams]);

  const simulado = useMemo<Exam | undefined>(() =>
    exams.find((s) => s.id === simuladoId),
  [exams, simuladoId]);

  // Basic SEO
  useEffect(() => {
    document.title = simulado
      ? `Simulado ${simulado.title} | Operação Alfa`
      : 'Simulado | Operação Alfa';

    let link: HTMLLinkElement | null = document.querySelector('link[rel="canonical"]');
    if (!link) {
      link = document.createElement('link');
      link.setAttribute('rel', 'canonical');
      document.head.appendChild(link);
    }
    link.setAttribute('href', window.location.href.split('#')[0]);
  }, [simulado]);

  const canAccessExam = simulado ? (simulado.isFree || isSubscribed) : false;
  const isBlocked = !canAccessExam;

  const handleIniciar = async () => {
    if (!simulado || !simuladoId) return;
    try {
      const attempt = await startAttempt(simuladoId);
      toast.success('Simulado iniciado!');
      navigate(`/simulado/${simuladoId}/tentativa/${attempt.id}`);
    } catch (e: any) {
      console.error('Error starting attempt:', e);
      toast.error(e.response?.data?.message || 'Não foi possível iniciar. Tente novamente.');
    }
  };

  return (
    <AppLayout>
      <div className="p-4 space-y-6">
        <header>
          <nav className="mb-2">
            <Link to="/simulados" className="inline-flex items-center text-sm text-muted-foreground hover:text-foreground transition-colors">
              <ChevronLeft size={16} className="mr-1" />
              Voltar
            </Link>
          </nav>
          <h1 className="text-2xl font-bold text-foreground">
            {simulado ? simulado.title : 'Simulado'}
          </h1>
          {simulado && (
            <p className="text-muted-foreground mt-1">Detalhes do simulado e informações antes de começar.</p>
          )}
        </header>

        {/* Loading */}
        {isLoading && (
          <div className="space-y-4">
            <div className="card-tactical p-4 space-y-3">
              <div className="h-4 bg-muted rounded animate-pulse" />
              <div className="h-3 bg-muted rounded w-2/3 animate-pulse" />
              <div className="flex gap-4">
                <div className="h-3 bg-muted rounded w-16 animate-pulse" />
                <div className="h-3 bg-muted rounded w-20 animate-pulse" />
              </div>
            </div>
          </div>
        )}

        {/* Not found */}
        {!isLoading && !simulado && (
          <section className="text-center py-12 space-y-4">
            <BookOpen className="mx-auto text-muted-foreground" size={48} />
            <div>
              <h2 className="font-medium text-foreground">Simulado não encontrado</h2>
              <p className="text-sm text-muted-foreground mt-1">Verifique o link ou escolha outro simulado.</p>
            </div>
            <div>
              <Link to="/simulados">
                <Button variant="secondary">Ver simulados</Button>
              </Link>
            </div>
          </section>
        )}

        {/* Details */}
        {simulado && (
          <main>
            <article className="card-tactical p-4 space-y-4">
              <div className="flex items-center gap-4 text-sm text-muted-foreground">
                <div className="flex items-center gap-1">
                  <Clock size={16} />
                  <span>{simulado.durationMin}min</span>
                </div>
                <div className="flex items-center gap-1">
                  <BookOpen size={16} />
                  <span>{simulado.numQuestions} questões</span>
                </div>
                <Badge variant="secondary" className="ml-auto text-xs">
                  {simulado.feedbackMode === 'immediate' ? 'Feedback Imediato' : 'Feedback Final'}
                </Badge>
              </div>

              <div className="flex items-center justify-between gap-3">
                <div className="text-sm text-muted-foreground">
                  {simulado.description || 'Simulado de preparação'}
                </div>
                {isBlocked ? (
                  <Link to="/assinar">
                    <Button size="sm" variant="outline">
                      <Lock size={16} className="mr-1" /> Liberar acesso
                    </Button>
                  </Link>
                ) : (
                  <Button size="sm" onClick={handleIniciar}>
                    <Play size={16} className="mr-1" /> Iniciar
                  </Button>
                )}
              </div>
            </article>
          </main>
        )}
      </div>
    </AppLayout>
  );
}
