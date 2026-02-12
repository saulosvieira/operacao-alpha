import { useState, useEffect } from 'react';
import { Link } from 'react-router-dom';
import { Search, Target, Users, AlertCircle, Loader2 } from 'lucide-react';
import { AppLayout } from '@/components/layout/AppLayout';
import { Input } from '@/components/ui/input';
import { Alert, AlertDescription } from '@/components/ui/alert';
import { careersService } from '@/services/careers';
import type { Career } from '@/types';

/**
 * Extracts error message from API error response
 * Prioritizes API message when available, falls back to default message
 * @param error - Error object from API call
 * @returns User-friendly error message
 */
function getErrorMessage(error: any): string {
  // Priority: API message > Generic message
  return error?.response?.data?.message || 'Erro ao carregar carreiras';
}

/**
 * Formats the exam count text with proper singular/plural grammar
 * @param count - Number of exams available
 * @returns Formatted text string
 */
function formatSimuladosText(count: number): string {
  if (count === 0) {
    return '0 simulados (em breve)';
  }
  if (count === 1) {
    return '1 simulado disponível';
  }
  return `${count} simulados disponíveis`;
}

/**
 * Filters careers based on search term
 * @param careers - Array of careers to filter
 * @param searchTerm - Search term to filter by
 * @returns Filtered array of careers, sorted alphabetically by name
 */
function filterCareers(careers: Career[], searchTerm: string): Career[] {
  const filtered = !searchTerm
    ? careers
    : careers.filter(career => {
        const lowerSearchTerm = searchTerm.toLowerCase();
        return (
          career.name.toLowerCase().includes(lowerSearchTerm) ||
          career.description?.toLowerCase().includes(lowerSearchTerm)
        );
      });
  
  // Ensure alphabetical ordering using localeCompare for proper handling of special characters
  return filtered.sort((a, b) => a.name.localeCompare(b.name, 'pt-BR', { sensitivity: 'base' }));
}

export default function Careers() {
  const [searchTerm, setSearchTerm] = useState('');
  const [careers, setCareers] = useState<Career[]>([]);
  const [isLoading, setIsLoading] = useState(true);
  const [error, setError] = useState<string | null>(null);

  useEffect(() => {
    const fetchCareers = async () => {
      try {
        setIsLoading(true);
        setError(null);
        const data = await careersService.listCareers();
        setCareers(data);
      } catch (err: any) {
        setError(getErrorMessage(err));
      } finally {
        setIsLoading(false);
      }
    };

    fetchCareers();
  }, []);

  const filteredCareers = filterCareers(careers, searchTerm);

  if (isLoading) {
    return (
      <AppLayout>
        <div className="p-4 flex items-center justify-center min-h-[50vh]">
          <div className="text-center space-y-4">
            <Loader2 className="mx-auto h-8 w-8 animate-spin text-primary" />
            <p className="text-muted-foreground">Carregando carreiras...</p>
          </div>
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

        {/* Error Alert */}
        {error && (
          <Alert variant="destructive">
            <AlertCircle className="h-4 w-4" />
            <AlertDescription>{error}</AlertDescription>
          </Alert>
        )}

        {/* Search */}
        <div className="relative">
          <Search className="absolute left-3 top-1/2 transform -translate-y-1/2 text-muted-foreground" size={20} />
          <Input
            placeholder="Buscar carreiras..."
            value={searchTerm}
            onChange={(e) => setSearchTerm(e.target.value)}
            className="pl-10"
          />
        </div>

        {/* Careers List */}
        <div className="space-y-4">
          {filteredCareers.length > 0 ? (
            filteredCareers.map((career) => (
              <Link
                key={career.id}
                to={`/carreiras/${career.id}/simulados`}
                className="block"
              >
                <div className="card-tactical p-4 hover:shadow-glow hover:border-primary/30 transition-all duration-300">
                  <div className="flex items-start gap-3">
                    <div className="w-12 h-12 bg-primary/20 rounded-xl flex items-center justify-center flex-shrink-0">
                      <Target className="text-primary" size={24} />
                    </div>
                    <div className="flex-1 space-y-2">
                      <h3 className="font-semibold text-foreground">{career.name}</h3>
                      {career.description && (
                        <p className="text-sm text-muted-foreground">
                          {career.description}
                        </p>
                      )}
                      <div className="flex items-center gap-4 text-xs text-muted-foreground">
                        <div className="flex items-center gap-1">
                          <Users size={12} />
                          <span>{formatSimuladosText(career.exams_count)}</span>
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
                    ? `Não encontramos resultados para "${searchTerm}". Tente usar termos diferentes ou verifique a ortografia.`
                    : 'Novas carreiras serão adicionadas em breve. Volte mais tarde para conferir as novidades!'
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
