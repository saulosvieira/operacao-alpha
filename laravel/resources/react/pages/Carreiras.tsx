import { useState, useEffect } from 'react';
import { Link } from 'react-router-dom';
import { Search, Target, Users } from 'lucide-react';
import { AppLayout } from '@/components/layout/AppLayout';
import { Input } from '@/components/ui/input';
import { mockCarreiras } from '@/mocks/data';
import type { Career } from '@/types';

export default function Carreiras() {
  const [searchTerm, setSearchTerm] = useState('');
  const [carreiras, setCarreiras] = useState<Career[]>([]);
  const [isLoading, setIsLoading] = useState(true);

  useEffect(() => {
    // Simular carregamento
    const timer = setTimeout(() => {
      setCarreiras(mockCarreiras);
      setIsLoading(false);
    }, 800);
    return () => clearTimeout(timer);
  }, []);

  const carreirasFiltradas = carreiras.filter(carreira =>
    carreira.name.toLowerCase().includes(searchTerm.toLowerCase()) ||
    carreira.description?.toLowerCase().includes(searchTerm.toLowerCase())
  );

  if (isLoading) {
    return (
      <AppLayout>
        <div className="p-4 space-y-4">
          <div className="h-10 bg-muted rounded animate-pulse" />
          {[...Array(4)].map((_, i) => (
            <div key={i} className="card-tactical p-4 space-y-3">
              <div className="h-6 bg-muted rounded w-2/3 animate-pulse" />
              <div className="h-4 bg-muted rounded animate-pulse" />
              <div className="flex gap-2">
                <div className="h-6 bg-muted rounded w-16 animate-pulse" />
                <div className="h-6 bg-muted rounded w-20 animate-pulse" />
              </div>
            </div>
          ))}
        </div>
      </AppLayout>
    );
  }

  return (
    <AppLayout>
      <div className="p-4 space-y-6">
        {/* Header */}
        <div>
          <h1 className="text-2xl font-bold text-foreground mb-2">Carreiras</h1>
          <p className="text-muted-foreground">
            Escolha sua carreira e acesse simulados específicos
          </p>
        </div>

        {/* Busca */}
        <div className="relative">
          <Search className="absolute left-3 top-1/2 transform -translate-y-1/2 text-muted-foreground" size={20} />
          <Input
            placeholder="Buscar carreiras..."
            value={searchTerm}
            onChange={(e) => setSearchTerm(e.target.value)}
            className="pl-10"
          />
        </div>

        {/* Lista de Carreiras */}
        <div className="space-y-4">
          {carreirasFiltradas.length > 0 ? (
            carreirasFiltradas.map((carreira) => (
              <Link
                key={carreira.id}
                to={`/carreiras/${carreira.id}/simulados`}
                className="block"
              >
                <div className="card-tactical p-4 hover:shadow-glow hover:border-primary/30 transition-all duration-300">
                  <div className="flex items-start gap-3">
                    <div className="w-12 h-12 bg-primary/20 rounded-xl flex items-center justify-center flex-shrink-0">
                      <Target className="text-primary" size={24} />
                    </div>
                    <div className="flex-1 space-y-2">
                      <h3 className="font-semibold text-foreground">{carreira.name}</h3>
                      {carreira.description && (
                        <p className="text-sm text-muted-foreground">
                          {carreira.description}
                        </p>
                      )}
                      <div className="flex items-center gap-4 text-xs text-muted-foreground">
                        <div className="flex items-center gap-1">
                          <Users size={12} />
                          <span>Simulados disponíveis</span>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </Link>
            ))
          ) : (
            <div className="text-center py-12 space-y-4">
              <Target className="mx-auto text-muted-foreground" size={48} />
              <div>
                <h3 className="font-medium text-foreground">
                  {searchTerm ? 'Nenhuma carreira encontrada' : 'Nenhuma carreira disponível'}
                </h3>
                <p className="text-sm text-muted-foreground mt-1">
                  {searchTerm 
                    ? `Tente ajustar sua busca por "${searchTerm}"`
                    : 'Novas carreiras serão adicionadas em breve'
                  }
                </p>
              </div>
            </div>
          )}
        </div>
      </div>
    </AppLayout>
  );
}