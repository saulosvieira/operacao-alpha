import { useState, useEffect } from 'react';
import { Link } from 'react-router-dom';
import { Clock, BookOpen, Star, Play, Lock, AlertCircle } from 'lucide-react';
import { AppLayout } from '@/components/layout/AppLayout';
import { useExamsStore } from '@/stores/examsStore';
import { useAuthStore } from '@/stores/authStore';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import { Tabs, TabsContent, TabsList, TabsTrigger } from '@/components/ui/tabs';
import { Alert, AlertDescription } from '@/components/ui/alert';
import type { Exam } from '@/types';

interface ExamCardProps {
  exam: Exam;
}

function ExamCard({ exam }: ExamCardProps) {
  const user = useAuthStore(state => state.user);
  const isBlocked = user?.subscriptionStatus !== 'active' && user?.subscriptionStatus !== 'trial';

  return (
    <div className="card-tactical p-4 space-y-4 hover:shadow-glow transition-all duration-300">
      <div className="flex items-start justify-between gap-3">
        <div className="space-y-1 flex-1">
          <h3 className="font-semibold text-foreground line-clamp-2">
            {exam.title}
          </h3>
          {exam.description && (
            <p className="text-sm text-muted-foreground line-clamp-1">
              {exam.description}
            </p>
          )}
        </div>
        
        {isBlocked && (
          <Lock className="text-muted-foreground flex-shrink-0" size={16} />
        )}
      </div>

      <div className="flex items-center gap-4 text-sm text-muted-foreground">
        <div className="flex items-center gap-1">
          <Clock size={16} />
          <span>{exam.durationMin}min</span>
        </div>
        <div className="flex items-center gap-1">
          <BookOpen size={16} />
          <span>{exam.numQuestions} questões</span>
        </div>
      </div>

      <div className="flex items-center justify-between gap-3">
        <div className="flex gap-2">
          <Badge variant={exam.active ? "default" : "secondary"} className="text-xs">
            {exam.active ? 'Ativo' : 'Inativo'}
          </Badge>
        </div>

        <Link to={isBlocked ? '/assinar' : `/simulado/${exam.id}`}>
          <Button 
            size="sm" 
            variant={isBlocked ? 'outline' : 'default'}
            disabled={!exam.active}
          >
            <Play size={16} className="mr-1" />
            {isBlocked ? 'Assinar' : 'Iniciar'}
          </Button>
        </Link>
      </div>
    </div>
  );
}

function LoadingSkeleton() {
  return (
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
  );
}

function EmptyState({ icon: Icon, title, description }: { 
  icon: any; 
  title: string; 
  description: string;
}) {
  return (
    <div className="text-center py-12 space-y-4">
      <Icon className="mx-auto text-muted-foreground" size={48} />
      <div>
        <h3 className="font-medium text-foreground">{title}</h3>
        <p className="text-sm text-muted-foreground mt-1">{description}</p>
      </div>
    </div>
  );
}

export default function Exams() {
  const { exams, fetchExams, isLoading, error, clearError } = useExamsStore();
  const [activeTab, setActiveTab] = useState('todos');

  useEffect(() => {
    fetchExams();
    
    return () => clearError();
  }, [fetchExams, clearError]);

  if (isLoading && exams.length === 0) {
    return (
      <AppLayout>
        <div className="p-4">
          <LoadingSkeleton />
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

        {/* Error Alert */}
        {error && (
          <Alert variant="destructive">
            <AlertCircle className="h-4 w-4" />
            <AlertDescription>{error}</AlertDescription>
          </Alert>
        )}

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
            {isLoading ? (
              <LoadingSkeleton />
            ) : exams.length > 0 ? (
              <div className="space-y-4">
                {exams.map((exam) => (
                  <ExamCard key={exam.id} exam={exam} />
                ))}
              </div>
            ) : (
              <EmptyState
                icon={BookOpen}
                title="Nenhum simulado disponível"
                description="Novos simulados serão publicados em breve"
              />
            )}
          </TabsContent>

          <TabsContent value="favoritos" className="space-y-4">
            <EmptyState
              icon={Star}
              title="Nenhum simulado realizado"
              description="Seus últimos simulados aparecerão aqui"
            />
          </TabsContent>
        </Tabs>
      </div>
    </AppLayout>
  );
}
