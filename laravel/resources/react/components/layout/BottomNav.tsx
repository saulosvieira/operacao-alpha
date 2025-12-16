import { useLocation, Link } from 'react-router-dom';
import { 
  Target, 
  BookOpen, 
  Trophy, 
  BarChart3, 
  Medal,
  User
} from 'lucide-react';
import { cn } from '@/lib/utils';

interface NavItem {
  id: string;
  label: string;
  icon: typeof Target;
  path: string;
}

const navItems: NavItem[] = [
  {
    id: 'carreiras',
    label: 'Carreiras',
    icon: Target,
    path: '/carreiras',
  },
  {
    id: 'simulados',
    label: 'Simulados',
    icon: BookOpen,
    path: '/simulados',
  },
  {
    id: 'ranking',
    label: 'Ranking',
    icon: Trophy,
    path: '/ranking',
  },
  {
    id: 'desempenho',
    label: 'Desempenho',
    icon: BarChart3,
    path: '/desempenho',
  },
  {
    id: 'aprovados',
    label: 'Aprovados',
    icon: Medal,
    path: '/aprovados',
  },
];

export function BottomNav() {
  const location = useLocation();

  return (
    <nav className="fixed bottom-0 left-0 right-0 z-50 bg-surface/95 backdrop-blur-sm border-t border-border">
      <div className="flex items-center justify-around px-2 py-1">
        {navItems.map((item) => {
          const isActive = location.pathname.startsWith(item.path);
          const Icon = item.icon;
          
          return (
            <Link
              key={item.id}
              to={item.path}
              className={cn(
                'flex flex-col items-center gap-1 px-3 py-2 min-h-touch rounded-lg transition-colors',
                'focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2 focus:ring-offset-surface',
                isActive 
                  ? 'text-primary bg-primary/10' 
                  : 'text-muted-foreground hover:text-foreground hover:bg-muted/50'
              )}
            >
              <Icon size={20} />
              <span className="text-xs font-medium">{item.label}</span>
            </Link>
          );
        })}
        
        {/* User Profile Button */}
        <Link
          to="/conta"
          className={cn(
            'flex flex-col items-center gap-1 px-3 py-2 min-h-touch rounded-lg transition-colors',
            'focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2 focus:ring-offset-surface',
            location.pathname === '/conta'
              ? 'text-primary bg-primary/10'
              : 'text-muted-foreground hover:text-foreground hover:bg-muted/50'
          )}
        >
          <User size={20} />
          <span className="text-xs font-medium">Conta</span>
        </Link>
      </div>
    </nav>
  );
}