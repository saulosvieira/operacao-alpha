import { useState, useEffect } from 'react';
import { Link } from 'react-router-dom';
import { Clock, BookOpen, Star, Play, Lock } from 'lucide-react';
import { AppLayout } from '@/components/layout/AppLayout';
import { useSimuladosStore } from '@/stores/simuladosStore';
import { useAuthStore } from '@/stores/authStore';
import { mockCarreiras } from '@/mocks/data';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import { Tabs, TabsContent, TabsList, TabsTrigger } from '@/components/ui/tabs';
import type { Simulado } from '@/types';

function SimuladoCard({ simulado }: { simulado: Simulado }) {
  const user = useAuthStore(state => state.user);
  const carreira = mockCarreiras.find(c => c.id === simulado.carreiraId);
  const isSubscribed = user?.subscriptionStatus === 'active' || user?.subscriptionStatus === 'trial';
  const isBlocked = !simulado.isFree && !isSubscribed;

  return (
    <div className="card-tactical p-4 space-y-4 hover:shadow-glow transition-all duration-300">
      <div className="flex items-start justify-between gap-3">
        <div className="space-y-1 flex-1">
          <h3 className="font-semibold text-foreground line-clamp-2">
            {simulado.titulo}
          </h3>
          <p className="text-sm text-muted-foreground">
            {carreira?.nome}
          </p>
        </div>
        
        {isBlocked && (
          <Lock className="text-muted-foreground flex-shrink-0" size={16} />
        )}
      </div>

      <div className="flex items-center gap-4 text-sm text-muted-foreground">
        <div className="flex items-center gap-1">
          <Clock size={16} />
          <span>{simulado.duracaoMin}min</span>
        </div>
        <div className="flex items-center gap-1">
          <BookOpen size={16} />
          <span>{simulado.numQuestoes} questões</span>
        </div>
      </div>

      <div className="flex items-center justify-between gap-3">
        <div className="flex gap-2">
          <Badge variant="secondary" className="text-xs">
            {simulado.modo === 'fixo' ? 'Fixo' : 'Sorteio'}
          </Badge>
          {simulado.ordemAleatoria && (
            <Badge variant="outline" className="text-xs">
              Aleatório
            </Badge>
          )}
          {!simulado.isFree && (
            <Badge variant="default" className="text-xs bg-amber-600 hover:bg-amber-700">
              Premium
            </Badge>
          )}
        </div>

        <Link to={isBlocked ? '/assinar' : `/simulado/${simulado.id}`}>
          <Button 
            size="sm" 
            variant={isBlocked ? 'outline' : 'default'}
          >
            <Play size={16} className="mr-1" />
            {isBlocked ? 'Liberar acesso' : 'Iniciar'}
          </Button>
        </Link>
      </div>
    </div>
  );
}

export default function Simulados() {
  const { simulados, fetchSimulados, isLoading } = useSimuladosStore();
  const [activeTab, setActiveTab] = useState('todos');

  useEffect(() => {
    fetchSimulados();
  }, [fetchSimulados]);

  if (isLoading) {
    return (
      <AppLayout>
        <div className="p-4">
          <div className="space-y-4">
            {[...Array(3)].map((_, i) => (
              <div key={i} className="card-tactical p-4 space-y-3">
                <div className="h-4 bg-muted rounded animate-pulse" />
                <div className="h-3 bg-muted rounded w-2/3 animate-pulse" />
                <div className="flex gap-4">
                  <div className="h-3 bg-muted rounded w-16 animate-pulse" />
                  <div className="h-3 bg-muted rounded w-20 animate-pulse" />
                </div>
              </div>
            ))}
          </div>
        </div>
      </AppLayout>
    );
  }

  return (
    <AppLayout>
      <div className="p-4 space-y-6">
        {/* Header */}
        <div>
          <h1 className="text-2xl font-bold text-foreground mb-2">Simulados</h1>
          <p className="text-muted-foreground">
            Pratique com simulados das principais carreiras militares
          </p>
        </div>

        {/* Tabs */}
        <Tabs value={activeTab} onValueChange={setActiveTab}>
          <TabsList className="grid w-full grid-cols-2 bg-muted/50">
            <TabsTrigger 
              value="todos" 
              className="data-[state=active]:bg-primary data-[state=active]:text-primary-foreground"
            >
              Todos
            </TabsTrigger>
            <TabsTrigger 
              value="favoritos"
              className="data-[state=active]:bg-primary data-[state=active]:text-primary-foreground"
            >
              Meus Últimos
            </TabsTrigger>
          </TabsList>

          <TabsContent value="todos" className="space-y-4">
            {simulados.length > 0 ? (
              <div className="space-y-4">
                {simulados.map((simulado) => (
                  <SimuladoCard key={simulado.id} simulado={simulado} />
                ))}
              </div>
            ) : (
              <div className="text-center py-12 space-y-4">
                <BookOpen className="mx-auto text-muted-foreground" size={48} />
                <div>
                  <h3 className="font-medium text-foreground">Nenhum simulado disponível</h3>
                  <p className="text-sm text-muted-foreground mt-1">
                    Novos simulados serão publicados em breve
                  </p>
                </div>
              </div>
            )}
          </TabsContent>

          <TabsContent value="favoritos" className="space-y-4">
            <div className="text-center py-12 space-y-4">
              <Star className="mx-auto text-muted-foreground" size={48} />
              <div>
                <h3 className="font-medium text-foreground">Nenhum simulado realizado</h3>
                <p className="text-sm text-muted-foreground mt-1">
                  Seus últimos simulados aparecerão aqui
                </p>
              </div>
            </div>
          </TabsContent>
        </Tabs>
      </div>
    </AppLayout>
  );
}