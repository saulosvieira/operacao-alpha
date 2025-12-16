import { useState, useEffect } from 'react';
import { Users, Crown, MapPin, Calendar, AlertCircle, Loader2 } from 'lucide-react';
import { AppLayout } from '@/components/layout/AppLayout';
import { Card } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar';
import { Alert, AlertDescription } from '@/components/ui/alert';
import { approvedService } from '@/services';
import type { Approved } from '@/types';

export default function ApprovedPage() {
  const [approved, setApproved] = useState<Approved[]>([]);
  const [isLoading, setIsLoading] = useState(true);
  const [error, setError] = useState<string | null>(null);

  useEffect(() => {
    const fetchApproved = async () => {
      try {
        setIsLoading(true);
        setError(null);
        const data = await approvedService.listApproved();
        setApproved(data);
      } catch (err: any) {
        setError(err.response?.data?.message || 'Erro ao carregar aprovados');
      } finally {
        setIsLoading(false);
      }
    };

    fetchApproved();
  }, []);

  const getInitials = (name: string) => {
    return name.split(' ').map(n => n[0]).join('').slice(0, 2);
  };

  if (isLoading) {
    return (
      <AppLayout>
        <div className="p-4 flex items-center justify-center min-h-[50vh]">
          <div className="text-center space-y-4">
            <Loader2 className="mx-auto h-8 w-8 animate-spin text-primary" />
            <p className="text-muted-foreground">Carregando aprovados...</p>
          </div>
        </div>
      </AppLayout>
    );
  }

  return (
    <AppLayout>
      <div className="p-4 space-y-6">
        {/* Header */}
        <div className="space-y-4">
          <div className="flex items-center justify-between">
            <div>
              <h1 className="text-2xl font-bold text-foreground">Aprovados</h1>
              <p className="text-muted-foreground">Histórias de sucesso reais</p>
            </div>
            <Users className="text-primary" size={24} />
          </div>
        </div>

        {/* Error Alert */}
        {error && (
          <Alert variant="destructive">
            <AlertCircle className="h-4 w-4" />
            <AlertDescription>{error}</AlertDescription>
          </Alert>
        )}

        {/* Statistics */}
        {approved.length > 0 && (
          <div className="grid grid-cols-3 gap-4 mb-6">
            <Card className="p-4 text-center">
              <Crown className="text-primary mx-auto mb-2" size={20} />
              <div className="text-xl font-bold text-foreground">{approved.length}+</div>
              <div className="text-xs text-muted-foreground">Aprovados</div>
            </Card>
            <Card className="p-4 text-center">
              <MapPin className="text-primary mx-auto mb-2" size={20} />
              <div className="text-xl font-bold text-foreground">25</div>
              <div className="text-xs text-muted-foreground">Estados</div>
            </Card>
            <Card className="p-4 text-center">
              <Calendar className="text-primary mx-auto mb-2" size={20} />
              <div className="text-xl font-bold text-foreground">2024</div>
              <div className="text-xs text-muted-foreground">Último</div>
            </Card>
          </div>
        )}

        {/* Approved List */}
        {approved.length > 0 ? (
          <div className="space-y-4">
            {approved.map((person) => (
              <Card key={person.id} className="p-4">
                <div className="space-y-4">
                  {/* Card Header */}
                  <div className="flex items-start gap-3">
                    <Avatar className="w-12 h-12">
                      <AvatarImage src={person.avatarUrl} alt={person.name} />
                      <AvatarFallback className="bg-primary/20 text-primary font-semibold">
                        {getInitials(person.name)}
                      </AvatarFallback>
                    </Avatar>
                    <div className="flex-1 min-w-0">
                      <div className="flex items-start justify-between gap-2">
                        <div>
                          <h3 className="font-semibold text-foreground">
                            {person.name}
                          </h3>
                          <p className="text-sm text-primary font-medium">
                            {person.career}
                          </p>
                        </div>
                        <Badge variant="outline" className="text-xs">
                          2024
                        </Badge>
                      </div>
                    </div>
                  </div>

                  {/* Career */}
                  <div className="bg-muted/30 rounded-lg p-3">
                    <p className="text-sm font-medium text-foreground">
                      {person.career}
                    </p>
                  </div>

                  {/* Testimonial */}
                  {person.note && (
                    <div className="pl-3 border-l-2 border-primary/30">
                      <p className="text-sm text-muted-foreground italic">
                        "{person.note}"
                      </p>
                    </div>
                  )}
                </div>
              </Card>
            ))}
          </div>
        ) : (
          <div className="text-center py-12 space-y-4">
            <Users className="mx-auto text-muted-foreground" size={48} />
            <div>
              <h3 className="font-medium text-foreground">Nenhum aprovado cadastrado</h3>
              <p className="text-sm text-muted-foreground mt-1">
                Em breve teremos histórias de sucesso para compartilhar
              </p>
            </div>
          </div>
        )}

        {/* Call to Action */}
        {approved.length > 0 && (
          <Card className="bg-gradient-to-r from-primary/10 to-primary/5 border-primary/20 p-6 text-center">
            <Crown className="text-primary mx-auto mb-3" size={32} />
            <h3 className="text-lg font-semibold text-foreground mb-2">
              Seja o próximo aprovado!
            </h3>
            <p className="text-sm text-muted-foreground mb-4">
              Junte-se aos nossos estudantes que conquistaram a aprovação
            </p>
          </Card>
        )}
      </div>
    </AppLayout>
  );
}
