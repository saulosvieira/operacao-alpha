import { useState } from 'react';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import type { Questao, AlternativaLetra } from '@/types';

interface QuestaoCardProps {
  questao: Questao;
  numeroQuestao: number;
  respostaSelecionada?: AlternativaLetra;
  onResposta: (letra: AlternativaLetra) => void;
  showResult?: boolean;
}

export function QuestaoCard({ 
  questao, 
  numeroQuestao, 
  respostaSelecionada, 
  onResposta,
  showResult = false 
}: QuestaoCardProps) {
  return (
    <article className="card-tactical p-6 space-y-4">
      <header className="flex items-center justify-between">
        <h2 className="text-lg font-semibold text-foreground">
          Questão {numeroQuestao}
        </h2>
        <Badge variant="secondary" className="text-xs">
          {questao.dificuldade}
        </Badge>
      </header>

      <div className="space-y-4">
        <p className="text-foreground leading-relaxed">
          {questao.enunciado}
        </p>

        {questao.imagemUrl && (
          <div className="flex justify-center">
            <img 
              src={questao.imagemUrl} 
              alt="Imagem da questão"
              className="max-w-full h-auto rounded-lg border border-border"
            />
          </div>
        )}

        <div className="space-y-2">
          {questao.alternativas.map((alternativa) => {
            const isSelected = respostaSelecionada === alternativa.letra;
            const isCorrect = alternativa.correta;
            
            let buttonVariant: "default" | "outline" | "secondary" = "outline";
            let additionalClasses = "";
            
            if (showResult) {
              if (isCorrect) {
                buttonVariant = "default";
                additionalClasses = "bg-green-600 hover:bg-green-700 text-white border-green-600";
              } else if (isSelected && !isCorrect) {
                additionalClasses = "bg-red-600 hover:bg-red-700 text-white border-red-600";
              }
            } else if (isSelected) {
              buttonVariant = "default";
            }

            return (
              <Button
                key={alternativa.letra}
                variant={buttonVariant}
                className={`w-full justify-start text-left p-4 h-auto whitespace-normal ${additionalClasses}`}
                onClick={() => !showResult && onResposta(alternativa.letra)}
                disabled={showResult}
              >
                <span className="font-semibold mr-3 flex-shrink-0">
                  {alternativa.letra})
                </span>
                <span className="flex-1">
                  {alternativa.texto}
                </span>
              </Button>
            );
          })}
        </div>

        {showResult && questao.explicacao && (
          <div className="mt-4 p-4 bg-muted/50 rounded-lg border border-border">
            <h3 className="font-semibold text-foreground mb-2">Explicação:</h3>
            <p className="text-muted-foreground text-sm">
              {questao.explicacao}
            </p>
          </div>
        )}
      </div>
    </article>
  );
}