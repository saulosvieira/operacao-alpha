import { Users, Crown, MapPin, Calendar } from 'lucide-react';
import { AppLayout } from '@/components/layout/AppLayout';
import { Card } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar';

export default function Aprovados() {
  const aprovados = [
    {
      id: '1',
      nome: 'Maria Silva',
      carreira: 'Tribunal de Justiça - SP',
      cargo: 'Analista Judiciário',
      ano: 2024,
      observacao: 'Aprovada em 3º lugar! Estudou 8 meses usando apenas nossos simulados.',
      avatar: '/placeholder.svg',
      localizacao: 'São Paulo - SP'
    },
    {
      id: '2', 
      nome: 'João Santos',
      carreira: 'Receita Federal',
      cargo: 'Auditor Fiscal',
      ano: 2024,
      observacao: 'Conquistou a aprovação em 1º lugar após 1 ano de preparação intensa.',
      avatar: '/placeholder.svg',
      localizacao: 'Brasília - DF'
    },
    {
      id: '3',
      nome: 'Ana Costa',
      carreira: 'Polícia Federal',
      cargo: 'Escrivã',
      ano: 2023,
      observacao: 'Aprovada após 6 meses de estudo focado com nossos simulados.',
      avatar: '/placeholder.svg',
      localizacao: 'Rio de Janeiro - RJ'
    },
    {
      id: '4',
      nome: 'Pedro Oliveira',
      carreira: 'Banco Central',
      cargo: 'Analista',
      ano: 2023,
      observacao: 'Sonho realizado! Aprovado em 5º lugar com dedicação e método.',
      avatar: '/placeholder.svg',
      localizacao: 'Belo Horizonte - MG'
    },
    {
      id: '5',
      nome: 'Carla Mendes',
      carreira: 'TCU',
      cargo: 'Técnica de Controle',
      ano: 2023,
      observacao: 'Os simulados foram fundamentais para minha aprovação.',
      avatar: '/placeholder.svg',
      localizacao: 'Brasília - DF'
    },
    {
      id: '6',
      nome: 'Lucas Ferreira',
      carreira: 'INSS',
      cargo: 'Técnico do Seguro Social',
      ano: 2024,
      observacao: 'Primeira tentativa em concurso público e já aprovado!',
      avatar: '/placeholder.svg',
      localizacao: 'Salvador - BA'
    }
  ];

  const getInitials = (nome: string) => {
    return nome.split(' ').map(n => n[0]).join('').slice(0, 2);
  };

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

        {/* Estatísticas */}
        <div className="grid grid-cols-3 gap-4 mb-6">
          <Card className="p-4 text-center">
            <Crown className="text-primary mx-auto mb-2" size={20} />
            <div className="text-xl font-bold text-foreground">150+</div>
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

        {/* Lista de Aprovados */}
        <div className="space-y-4">
          {aprovados.map((aprovado) => (
            <Card key={aprovado.id} className="p-4">
              <div className="space-y-4">
                {/* Header do Card */}
                <div className="flex items-start gap-3">
                  <Avatar className="w-12 h-12">
                    <AvatarImage src={aprovado.avatar} alt={aprovado.nome} />
                    <AvatarFallback className="bg-primary/20 text-primary font-semibold">
                      {getInitials(aprovado.nome)}
                    </AvatarFallback>
                  </Avatar>
                  <div className="flex-1 min-w-0">
                    <div className="flex items-start justify-between gap-2">
                      <div>
                        <h3 className="font-semibold text-foreground">
                          {aprovado.nome}
                        </h3>
                        <p className="text-sm text-primary font-medium">
                          {aprovado.cargo}
                        </p>
                      </div>
                      <Badge variant="outline" className="text-xs">
                        {aprovado.ano}
                      </Badge>
                    </div>
                    <div className="flex items-center gap-1 mt-1">
                      <MapPin className="text-muted-foreground" size={12} />
                      <span className="text-xs text-muted-foreground">
                        {aprovado.localizacao}
                      </span>
                    </div>
                  </div>
                </div>

                {/* Carreira */}
                <div className="bg-muted/30 rounded-lg p-3">
                  <p className="text-sm font-medium text-foreground">
                    {aprovado.carreira}
                  </p>
                </div>

                {/* Depoimento */}
                <div className="pl-3 border-l-2 border-primary/30">
                  <p className="text-sm text-muted-foreground italic">
                    "{aprovado.observacao}"
                  </p>
                </div>
              </div>
            </Card>
          ))}
        </div>

        {/* Call to Action */}
        <Card className="bg-gradient-to-r from-primary/10 to-primary/5 border-primary/20 p-6 text-center">
          <Crown className="text-primary mx-auto mb-3" size={32} />
          <h3 className="text-lg font-semibold text-foreground mb-2">
            Seja o próximo aprovado!
          </h3>
          <p className="text-sm text-muted-foreground mb-4">
            Junte-se aos nossos estudantes que conquistaram a aprovação
          </p>
        </Card>
      </div>
    </AppLayout>
  );
}