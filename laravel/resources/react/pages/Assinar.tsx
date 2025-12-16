import { useState } from 'react';
import { Link, useNavigate } from 'react-router-dom';
import { Check, Crown, Zap, Target, Shield, ArrowLeft } from 'lucide-react';
import { Button } from '@/components/ui/button';
import { useAuthStore } from '@/stores/authStore';
import { toast } from '@/hooks/use-toast';
import logoOficial from '@/assets/logo-oficial.png';

export default function Assinar() {
  const navigate = useNavigate();
  const user = useAuthStore(state => state.user);
  const [isLoading, setIsLoading] = useState(false);

  const beneficios = [
    {
      icon: Target,
      title: 'Simulados Ilimitados',
      description: 'Acesso completo a todos os simulados disponíveis',
    },
    {
      icon: Zap,
      title: 'Ranking Completo',
      description: 'Veja sua posição e compare com outros candidatos',
    },
    {
      icon: Shield,
      title: 'Análise de Desempenho',
      description: 'Relatórios detalhados por matéria e evolução',
    },
    {
      icon: Crown,
      title: 'Conteúdo Premium',
      description: 'Acesso prioritário a novos simulados e materiais',
    },
  ];

  const handleAssinar = async () => {
    if (!user) {
      navigate('/login');
      return;
    }

    setIsLoading(true);
    
    // Simular processo de pagamento
    await new Promise(resolve => setTimeout(resolve, 2000));
    
    toast({
      title: 'Assinatura ativada com sucesso!',
      description: 'Agora você tem acesso completo à Operação Alfa.',
    });
    
    setIsLoading(false);
    navigate('/simulados');
  };

  return (
    <div className="min-h-screen bg-background">
      {/* Header */}
      <header className="sticky top-0 z-40 bg-surface/95 backdrop-blur-sm border-b border-border">
        <div className="flex items-center justify-between px-4 py-3">
          <Link 
            to="/simulados"
            className="flex items-center gap-2 text-muted-foreground hover:text-foreground transition-colors"
          >
            <ArrowLeft size={20} />
            <span>Voltar</span>
          </Link>
          <div className="flex items-center gap-2">
            <img 
              src={logoOficial} 
              alt="Operação Alfa Logo" 
              className="w-8 h-8"
            />
            <span className="font-bold text-foreground">OPERAÇÃO ALFA</span>
          </div>
        </div>
      </header>

      <div className="p-4 pb-20 max-w-md mx-auto space-y-8">
        {/* Hero */}
        <div className="text-center space-y-4">
          <div className="w-20 h-20 bg-gradient-to-br from-primary/20 to-primary-600/20 rounded-2xl flex items-center justify-center mx-auto shadow-glow border border-primary/20">
            <img 
              src={logoOficial} 
              alt="Operação Alfa Logo" 
              className="w-12 h-12 opacity-90"
            />
          </div>
          <div>
            <h1 className="text-3xl font-bold text-foreground mb-2">
              Torne-se <span className="text-gradient-primary">Premium</span>
            </h1>
            <p className="text-muted-foreground">
              Acelere sua preparação com acesso completo a todos os recursos
            </p>
          </div>
        </div>

        {/* Benefícios */}
        <div className="space-y-4">
          <h2 className="text-lg font-semibold text-foreground">O que você ganha:</h2>
          <div className="space-y-3">
            {beneficios.map((beneficio, index) => {
              const Icon = beneficio.icon;
              return (
                <div key={index} className="card-tactical p-4">
                  <div className="flex items-start gap-3">
                    <div className="w-10 h-10 bg-primary/20 rounded-lg flex items-center justify-center flex-shrink-0">
                      <Icon className="text-primary" size={20} />
                    </div>
                    <div>
                      <h3 className="font-medium text-foreground">{beneficio.title}</h3>
                      <p className="text-sm text-muted-foreground mt-1">
                        {beneficio.description}
                      </p>
                    </div>
                  </div>
                </div>
              );
            })}
          </div>
        </div>

        {/* Plano */}
        <div className="card-tactical p-6 space-y-6 border-primary/30 bg-primary/5">
          <div className="text-center">
            <h3 className="text-2xl font-bold text-foreground">Plano Premium</h3>
            <div className="flex items-baseline justify-center gap-1 mt-2">
              <span className="text-3xl font-bold text-primary">R$ 29,90</span>
              <span className="text-muted-foreground">/mês</span>
            </div>
          </div>

          <div className="space-y-2">
            {[
              'Simulados ilimitados',
              'Ranking completo',
              'Análise de desempenho',
              'Suporte prioritário',
              'Sem anúncios'
            ].map((item, index) => (
              <div key={index} className="flex items-center gap-2">
                <Check className="text-primary flex-shrink-0" size={16} />
                <span className="text-sm text-foreground">{item}</span>
              </div>
            ))}
          </div>

          <Button 
            onClick={handleAssinar}
            disabled={isLoading}
            className="w-full btn-tactical bg-primary hover:bg-primary-600 text-primary-foreground"
            size="lg"
          >
            {isLoading ? 'Processando...' : 'Assinar Agora'}
          </Button>

          <p className="text-xs text-muted-foreground text-center">
            Cancele a qualquer momento. Sem taxas ocultas.
          </p>
        </div>

        {/* Login prompt para usuários não logados */}
        {!user && (
          <div className="card-tactical p-4 text-center">
            <p className="text-sm text-muted-foreground mb-3">
              Já tem uma conta?
            </p>
            <Link to="/login">
              <Button variant="outline" className="border-primary text-primary hover:bg-primary hover:text-primary-foreground">
                Fazer Login
              </Button>
            </Link>
          </div>
        )}
      </div>
    </div>
  );
}