import { useState } from 'react';
import { useNavigate } from 'react-router-dom';
import { Crown, X, Check } from 'lucide-react';
import {
  Dialog,
  DialogContent,
  DialogDescription,
  DialogHeader,
  DialogTitle,
  DialogTrigger,
} from '@/components/ui/dialog';
import { Button } from '@/components/ui/button';

interface PaywallModalProps {
  isOpen: boolean;
  onClose: () => void;
  feature?: string;
}

export function PaywallModal({ isOpen, onClose, feature = 'este conteúdo' }: PaywallModalProps) {
  const navigate = useNavigate();

  const beneficios = [
    'Simulados ilimitados',
    'Ranking completo',
    'Análise detalhada',
    'Suporte prioritário',
  ];

  const handleAssinar = () => {
    onClose();
    navigate('/assinar');
  };

  return (
    <Dialog open={isOpen} onOpenChange={onClose}>
      <DialogContent className="sm:max-w-md bg-surface border-border">
        <DialogHeader className="text-center space-y-4">
          <div className="flex justify-center">
            <div className="w-16 h-16 bg-gradient-to-br from-primary to-primary-600 rounded-2xl flex items-center justify-center shadow-glow">
              <Crown className="text-primary-foreground" size={28} />
            </div>
          </div>
          
          <div>
            <DialogTitle className="text-xl font-bold text-foreground">
              Conteúdo Premium
            </DialogTitle>
            <DialogDescription className="text-muted-foreground mt-2">
              Você precisa de uma assinatura para acessar {feature}
            </DialogDescription>
          </div>
        </DialogHeader>

        <div className="space-y-4">
          {/* Benefícios */}
          <div className="space-y-2">
            <h4 className="text-sm font-semibold text-foreground">Com o Premium você tem:</h4>
            <div className="space-y-2">
              {beneficios.map((beneficio, index) => (
                <div key={index} className="flex items-center gap-2">
                  <Check className="text-primary flex-shrink-0" size={16} />
                  <span className="text-sm text-foreground">{beneficio}</span>
                </div>
              ))}
            </div>
          </div>

          {/* Preço */}
          <div className="bg-primary/10 rounded-tactical p-4 text-center border border-primary/20">
            <div className="flex items-baseline justify-center gap-1">
              <span className="text-2xl font-bold text-primary">R$ 29,90</span>
              <span className="text-muted-foreground text-sm">/mês</span>
            </div>
            <p className="text-xs text-muted-foreground mt-1">
              Cancele quando quiser
            </p>
          </div>

          {/* Ações */}
          <div className="flex gap-3">
            <Button 
              variant="outline" 
              onClick={onClose}
              className="flex-1"
            >
              Voltar
            </Button>
            <Button 
              onClick={handleAssinar}
              className="flex-1 bg-primary hover:bg-primary-600 text-primary-foreground"
            >
              Assinar Agora
            </Button>
          </div>
        </div>
      </DialogContent>
    </Dialog>
  );
}