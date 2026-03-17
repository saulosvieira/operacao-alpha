import {
  Dialog,
  DialogContent,
  DialogHeader,
  DialogTitle,
  DialogDescription,
} from '@/components/ui/dialog';
import { Button } from '@/components/ui/button';

interface SupportTextModalProps {
  text: string;
  isOpen: boolean;
  onClose: () => void;
}

export function SupportTextModal({ text, isOpen, onClose }: SupportTextModalProps) {
  return (
    <Dialog open={isOpen} onOpenChange={onClose}>
      <DialogContent className="sm:max-w-2xl max-h-[80vh] overflow-y-auto bg-surface border-border">
        <DialogHeader>
          <DialogTitle className="text-lg font-bold text-foreground">
            Material de Apoio
          </DialogTitle>
          <DialogDescription className="sr-only">
            Texto de apoio da questão
          </DialogDescription>
        </DialogHeader>

        <div className="whitespace-pre-wrap text-sm text-foreground leading-relaxed">
          {text}
        </div>

        <div className="flex justify-end pt-2">
          <Button variant="outline" onClick={onClose}>
            Fechar
          </Button>
        </div>
      </DialogContent>
    </Dialog>
  );
}
