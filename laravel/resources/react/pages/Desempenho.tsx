import { useState } from 'react';
import { Calendar, TrendingUp, Clock, Target } from 'lucide-react';
import { AppLayout } from '@/components/layout/AppLayout';
import { Button } from '@/components/ui/button';
import { Card } from '@/components/ui/card';
import { Tabs, TabsContent, TabsList, TabsTrigger } from '@/components/ui/tabs';
import { Badge } from '@/components/ui/badge';

export default function Desempenho() {
  const [periodo, setPeriodo] = useState('30d');

  const tentativas = [
    {
      id: '1',
      data: '2024-01-15',
      simulado: 'Direito Constitucional - Básico',
      nota: 85,
      acertos: 17,
      total: 20,
      tempo: '45:30'
    },
    {
      id: '2', 
      data: '2024-01-14',
      simulado: 'Português - Interpretação',
      nota: 72,
      acertos: 14,
      total: 20,
      tempo: '38:45'
    },
    {
      id: '3',
      data: '2024-01-13',
      simulado: 'Matemática - Básico',
      nota: 90,
      acertos: 18,
      total: 20,
      tempo: '42:15'
    }
  ];

  const materias = [
    {
      nome: 'Direito Constitucional',
      tentativas: 15,
      acertos: 180,
      total: 210,
      percentual: 86
    },
    {
      nome: 'Português',
      tentativas: 12,
      acertos: 165,
      total: 200,
      percentual: 83
    },
    {
      nome: 'Matemática',
      tentativas: 8,
      acertos: 120,
      total: 160,
      percentual: 75
    },
    {
      nome: 'Direito Administrativo',
      tentativas: 10,
      acertos: 140,
      total: 180,
      percentual: 78
    }
  ];

  const getBadgeVariant = (nota: number) => {
    if (nota >= 80) return 'default';
    if (nota >= 60) return 'secondary';
    return 'destructive';
  };

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

          {/* Filtro de Período */}
          <div className="flex gap-2">
            {[
              { value: '7d', label: '7 dias' },
              { value: '30d', label: '30 dias' },
              { value: '90d', label: '90 dias' }
            ].map((option) => (
              <Button
                key={option.value}
                variant={periodo === option.value ? 'tactical' : 'outline'}
                size="sm"
                onClick={() => setPeriodo(option.value)}
              >
                {option.label}
              </Button>
            ))}
          </div>
        </div>

        {/* Resumo Geral */}
        <div className="grid grid-cols-2 gap-4">
          <Card className="p-4 text-center">
            <Target className="text-primary mx-auto mb-2" size={24} />
            <div className="text-2xl font-bold text-foreground">82%</div>
            <div className="text-sm text-muted-foreground">Média Geral</div>
          </Card>
          <Card className="p-4 text-center">
            <Clock className="text-primary mx-auto mb-2" size={24} />
            <div className="text-2xl font-bold text-foreground">42m</div>
            <div className="text-sm text-muted-foreground">Tempo Médio</div>
          </Card>
        </div>

        {/* Tabs de Detalhamento */}
        <Tabs defaultValue="tentativas" className="space-y-4">
          <TabsList className="grid w-full grid-cols-2">
            <TabsTrigger value="tentativas">Por Tentativa</TabsTrigger>
            <TabsTrigger value="materias">Por Matéria</TabsTrigger>
          </TabsList>

          <TabsContent value="tentativas" className="space-y-4">
            <div className="space-y-3">
              {tentativas.map((tentativa) => (
                <Card key={tentativa.id} className="p-4">
                  <div className="space-y-3">
                    <div className="flex items-start justify-between">
                      <div className="flex-1">
                        <h3 className="font-semibold text-foreground text-sm">
                          {tentativa.simulado}
                        </h3>
                        <div className="flex items-center gap-2 mt-1">
                          <Calendar className="text-muted-foreground" size={14} />
                          <span className="text-xs text-muted-foreground">
                            {new Date(tentativa.data).toLocaleDateString('pt-BR')}
                          </span>
                        </div>
                      </div>
                      <Badge variant={getBadgeVariant(tentativa.nota)}>
                        {tentativa.nota}%
                      </Badge>
                    </div>
                    <div className="flex justify-between text-sm">
                      <span className="text-muted-foreground">
                        Acertos: {tentativa.acertos}/{tentativa.total}
                      </span>
                      <span className="text-muted-foreground">
                        Tempo: {tentativa.tempo}
                      </span>
                    </div>
                  </div>
                </Card>
              ))}
            </div>
          </TabsContent>

          <TabsContent value="materias" className="space-y-4">
            <div className="space-y-3">
              {materias.map((materia) => (
                <Card key={materia.nome} className="p-4">
                  <div className="space-y-3">
                    <div className="flex items-center justify-between">
                      <h3 className="font-semibold text-foreground text-sm">
                        {materia.nome}
                      </h3>
                      <Badge variant={getBadgeVariant(materia.percentual)}>
                        {materia.percentual}%
                      </Badge>
                    </div>
                    <div className="space-y-2">
                      <div className="flex justify-between text-sm text-muted-foreground">
                        <span>Tentativas: {materia.tentativas}</span>
                        <span>Acertos: {materia.acertos}/{materia.total}</span>
                      </div>
                      <div className="w-full bg-muted rounded-full h-2">
                        <div 
                          className="bg-primary h-2 rounded-full transition-all duration-300"
                          style={{ width: `${materia.percentual}%` }}
                        />
                      </div>
                    </div>
                  </div>
                </Card>
              ))}
            </div>
          </TabsContent>
        </Tabs>
      </div>
    </AppLayout>
  );
}