import { useState, useEffect, useMemo } from 'react';
import { useParams, useNavigate } from 'react-router-dom';
import { AppLayout } from '@/components/layout/AppLayout';
import { QuestaoCard } from '@/components/QuestaoCard';
import { useSimuladosStore } from '@/stores/simuladosStore';
import { Button } from '@/components/ui/button';
import { Progress } from '@/components/ui/progress';
import { Clock, ChevronLeft, ChevronRight, Flag } from 'lucide-react';
import { mockQuestoes } from '@/mocks/data';
import type { AlternativaLetra } from '@/types';
import { toast } from 'sonner';

export default function ExecutarSimulado() {
  const { simuladoId, tentativaId } = useParams();
  const navigate = useNavigate();
  const { simulados, tentativaAtiva, responderQuestao, finalizarTentativa } = useSimuladosStore();
  
  const [questaoAtual, setQuestaoAtual] = useState(0);
  const [tempoRestante, setTempoRestante] = useState(0);
  const [respostas, setRespostas] = useState<Record<string, AlternativaLetra>>({});

  const simulado = useMemo(() => 
    simulados.find(s => s.id === simuladoId), [simulados, simuladoId]
  );

  // Mock questões para o simulado
  const questoes = useMemo(() => {
    if (!simulado) return [];
    // Por enquanto usamos questões mock repetidas até atingir o número necessário
    const questoesNecessarias = simulado.numQuestoes;
    const questoesDisponiveis = [...mockQuestoes];
    const questoesSimulado = [];
    
    for (let i = 0; i < questoesNecessarias; i++) {
      questoesSimulado.push({
        ...questoesDisponiveis[i % questoesDisponiveis.length],
        id: `q-${simuladoId}-${i + 1}`,
      });
    }
    
    return simulado.ordemAleatoria 
      ? questoesSimulado.sort(() => Math.random() - 0.5)
      : questoesSimulado;
  }, [simulado, simuladoId]);

  // Timer
  useEffect(() => {
    if (!simulado) return;
    
    setTempoRestante(simulado.duracaoMin * 60);
    
    const timer = setInterval(() => {
      setTempoRestante(prev => {
        if (prev <= 1) {
          handleFinalizar();
          return 0;
        }
        return prev - 1;
      });
    }, 1000);

    return () => clearInterval(timer);
  }, [simulado]);

  // SEO
  useEffect(() => {
    document.title = simulado 
      ? `Executando: ${simulado.titulo} | Operação Alfa`
      : 'Executando Simulado | Operação Alfa';
  }, [simulado]);

  const formatarTempo = (segundos: number) => {
    const horas = Math.floor(segundos / 3600);
    const mins = Math.floor((segundos % 3600) / 60);
    const secs = segundos % 60;
    
    if (horas > 0) {
      return `${horas}:${mins.toString().padStart(2, '0')}:${secs.toString().padStart(2, '0')}`;
    }
    return `${mins}:${secs.toString().padStart(2, '0')}`;
  };

  const handleResposta = (letra: AlternativaLetra) => {
    if (!tentativaAtiva || !questoes[questaoAtual]) return;
    
    const questaoId = questoes[questaoAtual].id;
    const novasRespostas = { ...respostas, [questaoId]: letra };
    
    setRespostas(novasRespostas);
    responderQuestao(tentativaAtiva.id, questaoId, letra);
  };

  const handleProxima = () => {
    if (questaoAtual < questoes.length - 1) {
      setQuestaoAtual(questaoAtual + 1);
    }
  };

  const handleAnterior = () => {
    if (questaoAtual > 0) {
      setQuestaoAtual(questaoAtual - 1);
    }
  };

  const handleFinalizar = async () => {
    if (!tentativaAtiva) return;
    
    try {
      await finalizarTentativa(tentativaAtiva.id);
      toast.success('Simulado finalizado!');
      navigate('/simulados');
    } catch (error) {
      toast.error('Erro ao finalizar simulado');
    }
  };

  if (!simulado || !questoes.length) {
    return (
      <AppLayout>
        <div className="p-4 text-center">
          <p className="text-muted-foreground">Carregando simulado...</p>
        </div>
      </AppLayout>
    );
  }

  const progresso = ((questaoAtual + 1) / questoes.length) * 100;
  const questaoAtualObj = questoes[questaoAtual];
  const respostaSelecionada = respostas[questaoAtualObj?.id];

  return (
    <AppLayout>
      <div className="p-4 space-y-4">
        {/* Header com timer e progresso */}
        <header className="card-tactical p-4">
          <div className="flex items-center justify-between mb-4">
            <h1 className="text-lg font-semibold text-foreground">
              {simulado.titulo}
            </h1>
            <div className="flex items-center gap-2 text-sm">
              <Clock size={16} />
              <span className={`font-mono ${tempoRestante < 300 ? 'text-red-600' : 'text-foreground'}`}>
                {formatarTempo(tempoRestante)}
              </span>
            </div>
          </div>
          
          <div className="space-y-2">
            <div className="flex justify-between text-sm text-muted-foreground">
              <span>Questão {questaoAtual + 1} de {questoes.length}</span>
              <span>{Math.round(progresso)}% concluído</span>
            </div>
            <Progress value={progresso} className="h-2" />
          </div>
        </header>

        {/* Questão atual */}
        {questaoAtualObj && (
          <QuestaoCard
            questao={questaoAtualObj}
            numeroQuestao={questaoAtual + 1}
            respostaSelecionada={respostaSelecionada}
            onResposta={handleResposta}
          />
        )}

        {/* Navegação */}
        <div className="flex items-center justify-between gap-4">
          <Button 
            variant="outline" 
            onClick={handleAnterior}
            disabled={questaoAtual === 0}
          >
            <ChevronLeft size={16} className="mr-1" />
            Anterior
          </Button>

          <div className="flex gap-2">
            {questaoAtual === questoes.length - 1 ? (
              <Button onClick={handleFinalizar} variant="default">
                <Flag size={16} className="mr-1" />
                Finalizar
              </Button>
            ) : (
              <Button 
                onClick={handleProxima}
                disabled={questaoAtual === questoes.length - 1}
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