import { Link } from 'react-router-dom';
import { User, Crown, LogOut, Settings, Shield } from 'lucide-react';
import { AppLayout } from '@/components/layout/AppLayout';
import { useAuthStore } from '@/stores/authStore';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';

export default function Conta() {
  const { user, logout } = useAuthStore();
  const isSubscribed = user?.subscriptionStatus === 'active' || user?.subscriptionStatus === 'trial';

  if (!user) {
    return null;
  }

  const handleLogout = () => {
    logout();
  };

  return (
    <AppLayout>
      <div className="p-4 space-y-6">
        {/* Header */}
        <div className="text-center space-y-4">
          <div className="w-20 h-20 bg-primary/20 rounded-2xl flex items-center justify-center mx-auto">
            <User className="text-primary" size={32} />
          </div>
          <div>
            <h1 className="text-2xl font-bold text-foreground">{user.name}</h1>
            <p className="text-muted-foreground">{user.email}</p>
          </div>
        </div>

        {/* Status da Assinatura */}
        <div className="card-tactical p-4 space-y-3">
          <div className="flex items-center justify-between">
            <div className="flex items-center gap-3">
              <Crown className={isSubscribed ? 'text-primary' : 'text-muted-foreground'} size={24} />
              <div>
                <h3 className="font-semibold text-foreground">Assinatura</h3>
                <p className="text-sm text-muted-foreground">
                  {isSubscribed ? 'Plano Premium ativo' : 'Plano gratuito'}
                </p>
              </div>
            </div>
            <Badge 
              variant={isSubscribed ? 'default' : 'secondary'}
              className={isSubscribed ? 'bg-primary text-primary-foreground' : ''}
            >
              {isSubscribed ? 'ATIVO' : 'GRATUITO'}
            </Badge>
          </div>
          
          {!isSubscribed && (
            <Link to="/assinar">
              <Button variant="tactical" className="w-full">
                <Crown className="mr-2" size={16} />
                Assinar Premium
              </Button>
            </Link>
          )}
        </div>

        {/* Menu de Opções */}
        <div className="space-y-3">
          <div className="card-tactical p-1">
            <button className="w-full flex items-center gap-3 p-3 text-left hover:bg-muted/50 rounded-tactical transition-colors">
              <Settings className="text-muted-foreground" size={20} />
              <span className="text-foreground">Configurações</span>
            </button>
          </div>

          <div className="card-tactical p-1">
            <button className="w-full flex items-center gap-3 p-3 text-left hover:bg-muted/50 rounded-tactical transition-colors">
              <Shield className="text-muted-foreground" size={20} />
              <span className="text-foreground">Privacidade</span>
            </button>
          </div>

          <div className="card-tactical p-1">
            <button 
              onClick={handleLogout}
              className="w-full flex items-center gap-3 p-3 text-left hover:bg-destructive/50 rounded-tactical transition-colors text-destructive"
            >
              <LogOut size={20} />
              <span>Sair da conta</span>
            </button>
          </div>
        </div>

        {/* Informações da Versão */}
        <div className="text-center py-8 space-y-2">
          <p className="text-xs text-muted-foreground">
            Operação ALFA v1.0.0
          </p>
          <p className="text-xs text-muted-foreground">
            © 2024 Operação ALFA. Todos os direitos reservados.
          </p>
        </div>
      </div>
    </AppLayout>
  );
}