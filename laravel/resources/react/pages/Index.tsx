import { Link } from 'react-router-dom';
import { Play, Trophy, Users, TrendingUp, Crown } from 'lucide-react';
import { AppLayout } from '@/components/layout/AppLayout';
import { useAuthStore } from '@/stores/authStore';
import { Button } from '@/components/ui/button';
import { Card } from '@/components/ui/card';
import logoOficial from '@/assets/logo-oficial.png';

export default function Index() {
  const { usuario } = useAuthStore();

  const quickActions = [
    {
      title: 'Novo Simulado',
      description: 'Continue seus estudos',
      icon: Play,
      href: '/simulados',
      color: 'text-primary'
    },
    {
      title: 'Ver Ranking',
      description: 'Sua posição atual',
      icon: Trophy,
      href: '/ranking',
      color: 'text-primary'
    },
      {
      title: 'Desempenho',
      description: 'Acompanhe evolução',
      icon: TrendingUp,
      href: '/desempenho',
      color: 'text-muted-foreground'
    },
    {
      title: 'Aprovados',
      description: 'Histórias de sucesso',
      icon: Users,
      href: '/aprovados',
      color: 'text-muted-foreground'
    }
  ];

  return (
    <AppLayout>
      <div className="p-4 space-y-6">
        {/* Header com Logo */}
        <div className="text-center space-y-4 py-8">
          <div className="flex justify-center">
            <img 
              src={logoOficial} 
              alt="Operação Alfa Logo" 
              className="w-20 h-20 opacity-90"
            />
          </div>
          <div>
            <h1 className="text-2xl font-bold text-foreground">
              Bem-vindo, {usuario?.nome || 'Estudante'}!
            </h1>
            <p className="text-muted-foreground">
              Continue sua preparação para os concursos
            </p>
          </div>
        </div>

        {/* Status da Assinatura */}
        {!usuario?.assinaturaAtiva && (
          <Card className="bg-gradient-to-r from-primary/10 to-primary/5 border-primary/20 p-4">
            <div className="flex items-center justify-between">
              <div className="flex items-center gap-3">
                <Crown className="text-primary" size={24} />
                <div>
                  <h3 className="font-semibold text-foreground">Upgrade para Premium</h3>
                  <p className="text-sm text-muted-foreground">
                    Acesso ilimitado a todos os simulados
                  </p>
                </div>
              </div>
              <Link to="/assinar">
                <Button variant="tactical" size="sm">
                  Assinar
                </Button>
              </Link>
            </div>
          </Card>
        )}

        {/* Ações Rápidas */}
        <div className="space-y-4">
          <h2 className="text-lg font-semibold text-foreground">Ações Rápidas</h2>
          <div className="grid grid-cols-2 gap-4">
            {quickActions.map((action) => (
              <Link key={action.title} to={action.href}>
                <Card className="p-4 hover:bg-muted/50 transition-colors cursor-pointer h-full">
                  <div className="space-y-3">
                    <action.icon className={action.color} size={24} />
                    <div>
                      <h3 className="font-semibold text-foreground text-sm">
                        {action.title}
                      </h3>
                      <p className="text-xs text-muted-foreground">
                        {action.description}
                      </p>
                    </div>
                  </div>
                </Card>
              </Link>
            ))}
          </div>
        </div>

        {/* Estatísticas Rápidas */}
        <div className="space-y-4">
          <h2 className="text-lg font-semibold text-foreground">Seus Números</h2>
          <div className="grid grid-cols-3 gap-4">
            <Card className="p-4 text-center">
              <div className="text-2xl font-bold text-primary">12</div>
              <div className="text-xs text-muted-foreground">Simulados</div>
            </Card>
            <Card className="p-4 text-center">
              <div className="text-2xl font-bold text-primary">78%</div>
              <div className="text-xs text-muted-foreground">Média</div>
            </Card>
            <Card className="p-4 text-center">
              <div className="text-2xl font-bold text-primary">#45</div>
              <div className="text-xs text-muted-foreground">Posição</div>
            </Card>
          </div>
        </div>
      </div>
    </AppLayout>
  );
}
