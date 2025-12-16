import { useState, useEffect } from 'react';
import { useParams, Link } from 'react-router-dom';
import { ArrowLeft, Play, Clock, FileText, Star } from 'lucide-react';
import { AppLayout } from '@/components/layout/AppLayout';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import { mockCarreiras, mockSimulados } from '@/mocks/data';
import type { Carreira, Simulado } from '@/types';

export default function CarreiraSimulados() {
  const { carreiraId } = useParams<{ carreiraId: string }>();
  const [carreira, setCarreira] = useState<Carreira | null>(null);
  const [simulados, setSimulados] = useState<Simulado[]>([]);
  const [isLoading, setIsLoading] = useState(true);

  useEffect(() => {
    const timer = setTimeout(() => {
      // Buscar carreira
      const carreiraEncontrada = mockCarreiras.find(c => c.id === carreiraId);
      setCarreira(carreiraEncontrada || null);
      
      // Buscar simulados da carreira
      const simuladosCarreira = mockSimulados.filter(s => s.carreiraId === carreiraId);
      setSimulados(simuladosCarreira);
      
      setIsLoading(false);
    }, 600);

    return () => clearTimeout(timer);
  }, [carreiraId]);

  if (isLoading) {
    return (
      <AppLayout>
        <div className="p-4 space-y-4">
          <div className="h-8 bg-muted rounded w-1/3 animate-pulse" />
          <div className="h-10 bg-muted rounded animate-pulse" />
          {[...Array(3)].map((_, i) => (
            <div key={i} className="card-tactical p-4 space-y-3">
              <div className="h-6 bg-muted rounded w-2/3 animate-pulse" />
              <div className="h-4 bg-muted rounded animate-pulse" />
              <div className="flex gap-2">
                <div className="h-6 bg-muted rounded w-16 animate-pulse" />
                <div className="h-6 bg-muted rounded w-20 animate-pulse" />
              </div>
            </div>
          ))}
        </div>
      </AppLayout>
    );
  }

  if (!carreira) {
    return (
      <AppLayout>
        <div className="p-4 text-center py-12 space-y-4">
          <h2 className="text-xl font-semibold text-foreground">
            Carreira não encontrada
          </h2>
          <p className="text-muted-foreground">
            A carreira que você está procurando não existe.
          </p>
          <Link to="/carreiras">
            <Button variant="outline">
              <ArrowLeft className="mr-2" size={16} />
              Voltar para Carreiras
            </Button>
          </Link>
        </div>
      </AppLayout>
    );
  }

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
              {carreira.nome}
            </h1>
            {carreira.descricao && (
              <p className="text-muted-foreground">
                {carreira.descricao}
              </p>
            )}
          </div>
        </div>

        {/* Estatísticas */}
        <div className="grid grid-cols-3 gap-4">
          <div className="card-tactical p-4 text-center">
            <div className="text-2xl font-bold text-primary">{simulados.length}</div>
            <div className="text-xs text-muted-foreground">Simulados</div>
          </div>
          <div className="card-tactical p-4 text-center">
            <div className="text-2xl font-bold text-primary">45m</div>
            <div className="text-xs text-muted-foreground">Duração Média</div>
          </div>
          <div className="card-tactical p-4 text-center">
            <div className="text-2xl font-bold text-primary">20</div>
            <div className="text-xs text-muted-foreground">Questões</div>
          </div>
        </div>

        {/* Lista de Simulados */}
        <div className="space-y-4">
          <h2 className="text-lg font-semibold text-foreground">
            Simulados Disponíveis
          </h2>
          
          {simulados.length > 0 ? (
            <div className="space-y-3">
              {simulados.map((simulado) => (
                <div key={simulado.id} className="card-tactical p-4">
                  <div className="space-y-4">
                    {/* Header do Simulado */}
                    <div className="flex items-start justify-between gap-4">
                      <div className="flex-1">
                        <h3 className="font-semibold text-foreground mb-2">
                          {simulado.titulo}
                        </h3>
                        <div className="flex flex-wrap gap-2 text-sm text-muted-foreground">
                          <div className="flex items-center gap-1">
                            <Clock size={14} />
                            <span>{simulado.duracaoMin} min</span>
                          </div>
                          <div className="flex items-center gap-1">
                            <FileText size={14} />
                            <span>{simulado.numQuestoes} questões</span>
                          </div>
                        </div>
                      </div>
                      <div className="flex flex-col gap-2">
                        <Badge 
                          variant={simulado.status === 'publicado' ? 'default' : 'secondary'}
                        >
                          {simulado.status === 'publicado' ? 'Disponível' : 'Em breve'}
                        </Badge>
                        {simulado.ordemAleatoria && (
                          <Badge variant="outline" className="text-xs">
                            <Star size={12} className="mr-1" />
                            Ordem Aleatória
                          </Badge>
                        )}
                      </div>
                    </div>

                    {/* Informações do Simulado */}
                    <div className="bg-muted/30 rounded-lg p-3 space-y-2">
                      <div className="text-sm">
                        <span className="text-muted-foreground">Modo:</span>{' '}
                        <span className="text-foreground font-medium">
                          {simulado.modo === 'fixo' ? 'Questões Fixas' : 'Sorteio Aleatório'}
                        </span>
                      </div>
                      {simulado.modo === 'sorteio' && (
                        <div className="text-sm text-muted-foreground">
                          As questões serão selecionadas aleatoriamente
                        </div>
                      )}
                    </div>

                    {/* Ações */}
                    <div className="flex gap-3">
                      <Link to={`/simulados/${simulado.id}`} className="flex-1">
                        <Button 
                          variant="tactical" 
                          className="w-full"
                          disabled={simulado.status !== 'publicado'}
                        >
                          <Play className="mr-2" size={16} />
                          {simulado.status === 'publicado' ? 'Iniciar Simulado' : 'Em Breve'}
                        </Button>
                      </Link>
                      <Button variant="outline" size="default">
                        Ver Detalhes
                      </Button>
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