import { Link } from 'react-router-dom';
import { Home, ArrowLeft } from 'lucide-react';
import { Button } from '@/components/ui/button';
import logoOficial from '@/assets/logo-oficial.png';

export default function NotFound() {
  return (
    <div className="min-h-screen bg-background flex items-center justify-center p-4">
      <div className="text-center space-y-8 max-w-md">
        {/* Logo */}
        <div className="flex justify-center">
          <img 
            src={logoOficial} 
            alt="Operação Alfa Logo" 
            className="w-16 h-16 opacity-60"
          />
        </div>

        {/* 404 */}
        <div className="space-y-4">
          <div className="text-6xl font-bold text-primary">404</div>
          <h1 className="text-2xl font-bold text-foreground">
            Página não encontrada
          </h1>
          <p className="text-muted-foreground">
            A página que você está procurando não existe ou foi movida.
          </p>
        </div>

        {/* Actions */}
        <div className="space-y-3">
          <Link to="/">
            <Button variant="tactical" className="w-full">
              <Home className="mr-2" size={16} />
              Voltar ao Início
            </Button>
          </Link>
          <Button 
            variant="outline" 
            onClick={() => window.history.back()}
            className="w-full"
          >
            <ArrowLeft className="mr-2" size={16} />
            Página Anterior
          </Button>
        </div>

        {/* Help Links */}
        <div className="pt-4 border-t border-border">
          <p className="text-sm text-muted-foreground mb-3">
            Precisa de ajuda? Acesse:
          </p>
          <div className="space-y-2">
            <Link 
              to="/simulados" 
              className="block text-sm text-primary hover:underline"
            >
              Simulados
            </Link>
            <Link 
              to="/carreiras" 
              className="block text-sm text-primary hover:underline"
            >
              Carreiras
            </Link>
            <Link 
              to="/ranking" 
              className="block text-sm text-primary hover:underline"
            >
              Ranking
            </Link>
          </div>
        </div>
      </div>
    </div>
  );
}
