import { Link } from 'react-router-dom';
import { useAuthStore } from '@/stores/authStore';
import logoOficial from '@/assets/logo-oficial.png';

export function TopBar() {
  const user = useAuthStore(state => state.user);
  const isSubscribed = user?.subscriptionStatus === 'active' || user?.subscriptionStatus === 'trial';

  return (
    <header className="sticky top-0 z-40 bg-surface/95 backdrop-blur-sm border-b border-border">
      <div className="flex items-center justify-between px-4 py-3">
        {/* Logo */}
        <Link 
          to="/simulados" 
          className="flex items-center gap-3 focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2 focus:ring-offset-surface rounded-lg"
        >
          <div className="flex items-center gap-2">
            <img 
              src={logoOficial} 
              alt="Operação Alfa Logo" 
              className="w-8 h-8"
            />
            <div className="flex flex-col">
              <span className="text-sm font-bold text-foreground leading-none">OPERAÇÃO</span>
              <span className="text-xs text-primary font-semibold leading-none">ALFA</span>
            </div>
          </div>
        </Link>

        {/* User Info */}
        {user && (
          <Link
            to="/conta"
            className="flex items-center gap-2 px-3 py-2 rounded-lg hover:bg-muted/50 transition-colors focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2 focus:ring-offset-surface"
          >
            <div className="w-8 h-8 rounded-full bg-primary/20 flex items-center justify-center">
              <span className="text-primary text-sm font-medium">
                {user.name.charAt(0).toUpperCase()}
              </span>
            </div>
            {isSubscribed && (
              <div className="w-2 h-2 bg-primary rounded-full" title="Assinatura Ativa" />
            )}
          </Link>
        )}
      </div>
    </header>
  );
}