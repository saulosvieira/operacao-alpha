import { useState, useEffect } from 'react';
import { Link, useNavigate } from 'react-router-dom';
import { User, Crown, LogOut, Settings, Shield, AlertCircle, Loader2, Edit } from 'lucide-react';
import { AppLayout } from '@/components/layout/AppLayout';
import { useAuthStore } from '@/stores/authStore';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import { Alert, AlertDescription } from '@/components/ui/alert';
import { Dialog, DialogContent, DialogDescription, DialogFooter, DialogHeader, DialogTitle } from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { subscriptionService } from '@/services/subscription';
import { toast } from '@/hooks/use-toast';
import type { Subscription } from '@/types';

export default function Account() {
  const navigate = useNavigate();
  const { user, logout, fetchUser } = useAuthStore();
  const [subscription, setSubscription] = useState<Subscription | null>(null);
  const [isLoading, setIsLoading] = useState(true);
  const [error, setError] = useState<string | null>(null);
  const [showDeleteDialog, setShowDeleteDialog] = useState(false);
  const [isDeleting, setIsDeleting] = useState(false);

  useEffect(() => {
    const fetchSubscription = async () => {
      if (!user) {
        navigate('/login');
        return;
      }

      try {
        setIsLoading(true);
        setError(null);
        const data = await subscriptionService.getStatus();
        setSubscription(data);
      } catch (err: any) {
        setError(err.response?.data?.message || 'Erro ao carregar dados da assinatura');
      } finally {
        setIsLoading(false);
      }
    };

    fetchSubscription();
  }, [user, navigate]);

  const handleLogout = async () => {
    try {
      await logout();
      navigate('/login');
    } catch (err: any) {
      toast({
        variant: 'destructive',
        title: 'Erro ao sair',
        description: 'Tente novamente.',
      });
    }
  };

  const handleCancelSubscription = async () => {
    try {
      await subscriptionService.cancel();
      toast({
        title: 'Assinatura cancelada',
        description: 'Sua assinatura foi cancelada com sucesso.',
      });
      // Refresh subscription status
      const data = await subscriptionService.getStatus();
      setSubscription(data);
      await fetchUser();
    } catch (err: any) {
      toast({
        variant: 'destructive',
        title: 'Erro ao cancelar assinatura',
        description: err.response?.data?.message || 'Tente novamente.',
      });
    }
  };

  const handleDeleteAccount = async () => {
    setIsDeleting(true);
    try {
      // TODO: Implement delete account API call
      toast({
        title: 'Conta excluída',
        description: 'Sua conta foi excluída com sucesso.',
      });
      await logout();
      navigate('/');
    } catch (err: any) {
      toast({
        variant: 'destructive',
        title: 'Erro ao excluir conta',
        description: err.response?.data?.message || 'Tente novamente.',
      });
    } finally {
      setIsDeleting(false);
      setShowDeleteDialog(false);
    }
  };

  if (!user) {
    return null;
  }

  if (isLoading) {
    return (
      <AppLayout>
        <div className="p-4 flex items-center justify-center min-h-[50vh]">
          <div className="text-center space-y-4">
            <Loader2 className="mx-auto h-8 w-8 animate-spin text-primary" />
            <p className="text-muted-foreground">Carregando dados da conta...</p>
          </div>
        </div>
      </AppLayout>
    );
  }

  const isSubscribed = user.subscriptionStatus === 'active' || user.subscriptionStatus === 'trial';

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
            {user.phone && (
              <p className="text-sm text-muted-foreground">{user.phone}</p>
            )}
          </div>
        </div>

        {/* Error Alert */}
        {error && (
          <Alert variant="destructive">
            <AlertCircle className="h-4 w-4" />
            <AlertDescription>{error}</AlertDescription>
          </Alert>
        )}

        {/* Subscription Status */}
        <div className="card-tactical p-4 space-y-3">
          <div className="flex items-center justify-between">
            <div className="flex items-center gap-3">
              <Crown className={isSubscribed ? 'text-primary' : 'text-muted-foreground'} size={24} />
              <div>
                <h3 className="font-semibold text-foreground">Assinatura</h3>
                <p className="text-sm text-muted-foreground">
                  {subscription?.planName || (isSubscribed ? 'Plano Premium ativo' : 'Plano gratuito')}
                </p>
                {subscription?.expiresAt && (
                  <p className="text-xs text-muted-foreground">
                    Expira em: {new Date(subscription.expiresAt).toLocaleDateString('pt-BR')}
                  </p>
                )}
              </div>
            </div>
            <Badge 
              variant={isSubscribed ? 'default' : 'secondary'}
              className={isSubscribed ? 'bg-primary text-primary-foreground' : ''}
            >
              {user.subscriptionStatus.toUpperCase()}
            </Badge>
          </div>
          
          {!isSubscribed ? (
            <Link to="/assinar">
              <Button variant="tactical" className="w-full">
                <Crown className="mr-2" size={16} />
                Assinar Premium
              </Button>
            </Link>
          ) : (
            <Button 
              variant="outline" 
              className="w-full"
              onClick={handleCancelSubscription}
            >
              Cancelar Assinatura
            </Button>
          )}
        </div>

        {/* Options Menu */}
        <div className="space-y-3">
          <div className="card-tactical p-1">
            <button className="w-full flex items-center gap-3 p-3 text-left hover:bg-muted/50 rounded-tactical transition-colors">
              <Edit className="text-muted-foreground" size={20} />
              <span className="text-foreground">Editar Perfil</span>
            </button>
          </div>

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

        {/* Danger Zone */}
        <div className="card-tactical p-4 border-destructive/30 space-y-3">
          <h3 className="font-semibold text-destructive">Zona de Perigo</h3>
          <p className="text-sm text-muted-foreground">
            Ações irreversíveis que afetam permanentemente sua conta
          </p>
          <Button 
            variant="destructive" 
            className="w-full"
            onClick={() => setShowDeleteDialog(true)}
          >
            Excluir Conta
          </Button>
        </div>

        {/* Version Info */}
        <div className="text-center py-8 space-y-2">
          <p className="text-xs text-muted-foreground">
            Operação ALFA v1.0.0
          </p>
          <p className="text-xs text-muted-foreground">
            © 2024 Operação ALFA. Todos os direitos reservados.
          </p>
        </div>
      </div>

      {/* Delete Account Dialog */}
      <Dialog open={showDeleteDialog} onOpenChange={setShowDeleteDialog}>
        <DialogContent>
          <DialogHeader>
            <DialogTitle>Excluir Conta</DialogTitle>
            <DialogDescription>
              Tem certeza que deseja excluir sua conta? Esta ação não pode ser desfeita.
              Todos os seus dados, incluindo histórico de simulados e progresso, serão permanentemente removidos.
            </DialogDescription>
          </DialogHeader>
          <DialogFooter>
            <Button 
              variant="outline" 
              onClick={() => setShowDeleteDialog(false)}
              disabled={isDeleting}
            >
              Cancelar
            </Button>
            <Button 
              variant="destructive" 
              onClick={handleDeleteAccount}
              disabled={isDeleting}
            >
              {isDeleting ? (
                <>
                  <Loader2 className="mr-2 h-4 w-4 animate-spin" />
                  Excluindo...
                </>
              ) : (
                'Excluir Permanentemente'
              )}
            </Button>
          </DialogFooter>
        </DialogContent>
      </Dialog>
    </AppLayout>
  );
}
