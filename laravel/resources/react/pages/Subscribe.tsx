import { useState, useEffect } from 'react';
import { Link, useNavigate } from 'react-router-dom';
import { Check, Crown, Zap, Target, Shield, ArrowLeft, AlertCircle, Loader2 } from 'lucide-react';
import { Button } from '@/components/ui/button';
import { Alert, AlertDescription } from '@/components/ui/alert';
import { useAuthStore } from '@/stores/authStore';
import { subscriptionService } from '@/services/subscription';
import { toast } from '@/hooks/use-toast';
import logoOficial from '@/assets/logo-oficial.png';
import type { Plan } from '@/types';

export default function Subscribe() {
  const navigate = useNavigate();
  const { user } = useAuthStore();
  const [plans, setPlans] = useState<Plan[]>([]);
  const [isLoading, setIsLoading] = useState(true);
  const [isSubscribing, setIsSubscribing] = useState(false);
  const [error, setError] = useState<string | null>(null);

  const benefits = [
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

  useEffect(() => {
    const fetchPlans = async () => {
      try {
        setIsLoading(true);
        setError(null);
        const data = await subscriptionService.getPlans();
        setPlans(data);
      } catch (err: any) {
        setError(err.response?.data?.message || 'Erro ao carregar planos');
      } finally {
        setIsLoading(false);
      }
    };

    fetchPlans();
  }, []);

  const handleSubscribe = async (planId: string) => {
    if (!user) {
      navigate('/login');
      return;
    }

    setIsSubscribing(true);
    
    try {
      await subscriptionService.subscribe(planId);
      
      toast({
        title: 'Assinatura ativada com sucesso!',
        description: 'Agora você tem acesso completo à Operação Alfa.',
      });
      
      navigate('/simulados');
    } catch (err: any) {
      toast({
        variant: 'destructive',
        title: 'Erro ao processar assinatura',
        description: err.response?.data?.message || 'Tente novamente mais tarde.',
      });
    } finally {
      setIsSubscribing(false);
    }
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

        {/* Error Alert */}
        {error && (
          <Alert variant="destructive">
            <AlertCircle className="h-4 w-4" />
            <AlertDescription>{error}</AlertDescription>
          </Alert>
        )}

        {/* Benefits */}
        <div className="space-y-4">
          <h2 className="text-lg font-semibold text-foreground">O que você ganha:</h2>
          <div className="space-y-3">
            {benefits.map((benefit, index) => {
              const Icon = benefit.icon;
              return (
                <div key={index} className="card-tactical p-4">
                  <div className="flex items-start gap-3">
                    <div className="w-10 h-10 bg-primary/20 rounded-lg flex items-center justify-center flex-shrink-0">
                      <Icon className="text-primary" size={20} />
                    </div>
                    <div>
                      <h3 className="font-medium text-foreground">{benefit.title}</h3>
                      <p className="text-sm text-muted-foreground mt-1">
                        {benefit.description}
                      </p>
                    </div>
                  </div>
                </div>
              );
            })}
          </div>
        </div>

        {/* Plans */}
        {isLoading ? (
          <div className="flex items-center justify-center py-12">
            <Loader2 className="h-8 w-8 animate-spin text-primary" />
          </div>
        ) : plans.length > 0 ? (
          <div className="space-y-4">
            {plans.map((plan) => (
              <div 
                key={plan.id} 
                className={`card-tactical p-6 space-y-6 ${plan.popular ? 'border-primary/30 bg-primary/5' : ''}`}
              >
                {plan.popular && (
                  <div className="flex justify-center">
                    <Badge className="bg-primary text-primary-foreground">
                      Mais Popular
                    </Badge>
                  </div>
                )}
                
                <div className="text-center">
                  <h3 className="text-2xl font-bold text-foreground">{plan.name}</h3>
                  <div className="flex items-baseline justify-center gap-1 mt-2">
                    <span className="text-3xl font-bold text-primary">
                      R$ {plan.price.toFixed(2)}
                    </span>
                    <span className="text-muted-foreground">/mês</span>
                  </div>
                </div>

                <div className="space-y-2">
                  {plan.features.map((feature, index) => (
                    <div key={index} className="flex items-center gap-2">
                      <Check className="text-primary flex-shrink-0" size={16} />
                      <span className="text-sm text-foreground">{feature}</span>
                    </div>
                  ))}
                </div>

                <Button 
                  onClick={() => handleSubscribe(plan.id)}
                  disabled={isSubscribing}
                  className="w-full btn-tactical bg-primary hover:bg-primary-600 text-primary-foreground"
                  size="lg"
                >
                  {isSubscribing ? (
                    <>
                      <Loader2 className="mr-2 h-4 w-4 animate-spin" />
                      Processando...
                    </>
                  ) : (
                    'Assinar Agora'
                  )}
                </Button>

                <p className="text-xs text-muted-foreground text-center">
                  Cancele a qualquer momento. Sem taxas ocultas.
                </p>
              </div>
            ))}
          </div>
        ) : (
          <div className="text-center py-12 space-y-4">
            <Crown className="mx-auto text-muted-foreground" size={48} />
            <div>
              <h3 className="font-medium text-foreground">Nenhum plano disponível</h3>
              <p className="text-sm text-muted-foreground mt-1">
                Novos planos serão disponibilizados em breve
              </p>
            </div>
          </div>
        )}

        {/* Login prompt for non-logged users */}
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
