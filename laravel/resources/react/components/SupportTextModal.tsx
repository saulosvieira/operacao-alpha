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
  const lines = text.split('\n');

  // Width of the line number gutter based on total lines
  const gutterWidth = String(lines.length).length;

  return (
    <Dialog open={isOpen} onOpenChange={onClose}>
      <DialogContent className="sm:max-w-2xl max-h-[80vh] overflow-y-auto bg-surface border-border">
        <DialogHeader>
          <DialogTitle className="text-lg font-bold text-foreground">
            Complemento da Questão
          </DialogTitle>
          <DialogDescription className="sr-only">
            Texto complementar da questão com linhas numeradas
          </DialogDescription>
        </DialogHeader>

        <div className="rounded-md border border-border overflow-hidden">
          <div className="overflow-x-auto">
            {lines.map((line, index) => (
              <div key={index} className="flex min-h-[1.75rem]">
                <span
                  className="flex-shrink-0 select-none text-right text-xs text-muted-foreground bg-muted/50 px-2 py-1 border-r border-border font-mono"
                  style={{ minWidth: `${gutterWidth + 1.5}ch` }}
                  aria-hidden="true"
                >
                  {index + 1}
                </span>
                <span className="flex-1 text-sm text-foreground px-3 py-1 whitespace-pre-wrap break-words leading-relaxed">
                  {line || '\u00A0'}
                </span>
              </div>
            ))}
          </div>
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
