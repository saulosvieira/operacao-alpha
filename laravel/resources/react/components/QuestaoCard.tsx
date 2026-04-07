import { useState } from 'react';
import { Button } from '@/components/ui/button';
import { BookOpen, Download, Loader2 } from 'lucide-react';
import type { Question, AnswerOption } from '@/types';
import { SupportTextModal } from './SupportTextModal';

interface QuestaoCardProps {
  question: Question;
  questionNumber: number;
  selectedAnswer?: AnswerOption;
  onAnswer: (letter: AnswerOption) => void;
  showFeedback?: boolean;
  feedbackMode?: 'immediate' | 'final';
  feedbackCorrectAnswer?: AnswerOption; // Correct answer from feedback response
  feedbackExplanation?: string; // Explanation from feedback response
}

export function QuestaoCard({ 
  question, 
  questionNumber, 
  selectedAnswer, 
  onAnswer,
  showFeedback = false,
  feedbackMode = 'final',
  feedbackCorrectAnswer,
  feedbackExplanation,
}: QuestaoCardProps) {
  // For immediate mode, use feedback data; for final mode or results, use question data
  const correctAnswer = showFeedback ? (feedbackCorrectAnswer || question.correctAnswer) : question.correctAnswer;
  const explanation = showFeedback ? (feedbackExplanation || question.explanation) : question.explanation;
  
  // Determine if we should show the correct answer (only if we have the actual answer)
  const hasCorrectAnswerData = correctAnswer !== undefined && (correctAnswer as string) !== '';
  const shouldShowCorrectAnswer = showFeedback && hasCorrectAnswerData;
  
  console.log('QuestaoCard:', { 
    questionId: question.id, 
    showFeedback, 
    feedbackCorrectAnswer, 
    correctAnswer, 
    hasCorrectAnswerData, 
    shouldShowCorrectAnswer 
  });
  const isAnswered = selectedAnswer !== undefined;
  
  // In immediate mode, lock the question after answering (even if we don't have feedback data)
  const isLocked = feedbackMode === 'immediate' && showFeedback;

  const [isSupportTextOpen, setIsSupportTextOpen] = useState(false);
  const [isDownloading, setIsDownloading] = useState(false);

  const handleDownloadPdf = async (url: string) => {
    setIsDownloading(true);
    try {
      const response = await fetch(url);
      if (!response.ok) throw new Error('Download failed');
      const blob = await response.blob();
      const blobUrl = URL.createObjectURL(blob);
      const filename = url.split('/').pop() || 'material-apoio.pdf';
      const a = document.createElement('a');
      a.href = blobUrl;
      a.download = filename;
      document.body.appendChild(a);
      a.click();
      document.body.removeChild(a);
      URL.revokeObjectURL(blobUrl);
    } catch {
      // Fallback: try anchor with download attribute directly
      const a = document.createElement('a');
      a.href = url;
      a.download = url.split('/').pop() || 'material-apoio.pdf';
      a.target = '_blank';
      document.body.appendChild(a);
      a.click();
      document.body.removeChild(a);
    } finally {
      setIsDownloading(false);
    }
  };
  
  return (
    <article className="card-tactical p-6 space-y-4">
      <header className="flex items-center justify-between">
        <div className="flex items-center gap-3">
          <h2 className="text-lg font-semibold text-foreground">
            Questão {questionNumber}
          </h2>
          {(question.year || question.examBoard) && (
            <div className="flex items-center gap-2 text-xs text-muted-foreground">
              {question.examBoard && (
                <span className="px-2 py-0.5 rounded-full bg-muted border border-border">
                  {question.examBoard}
                </span>
              )}
              {question.year && (
                <span className="px-2 py-0.5 rounded-full bg-muted border border-border">
                  {question.year}
                </span>
              )}
            </div>
          )}
        </div>
        <div className="flex items-center gap-2">
          {question.supportText && (
            <Button
              variant="outline"
              size="sm"
              onClick={() => setIsSupportTextOpen(true)}
            >
              <BookOpen className="h-4 w-4 mr-1" />
              Complemento da Questão
            </Button>
          )}
          {question.supportPdfUrl && (
            <Button
              variant="ghost"
              size="sm"
              disabled={isDownloading}
              onClick={() => handleDownloadPdf(question.supportPdfUrl!)}
            >
              {isDownloading ? (
                <Loader2 className="h-4 w-4 mr-1 animate-spin" />
              ) : (
                <Download className="h-4 w-4 mr-1" />
              )}
              Baixar PDF
            </Button>
          )}
        </div>
      </header>

      <div className="space-y-4">
        {/* Statement */}
        <p className="text-foreground leading-relaxed">
          {question.statement}
        </p>

        {/* Statement Image */}
        {question.statementImage && (
          <div className="flex justify-center">
            <img 
              src={question.statementImage} 
              alt="Imagem do enunciado"
              className="max-w-full h-auto rounded-lg border border-border"
            />
          </div>
        )}

        {/* Options */}
        <div className="space-y-2">
          {question.options.map((option) => {
            const isSelected = selectedAnswer === option.letter;
            const isCorrect = correctAnswer === option.letter;
            
            let buttonVariant: "default" | "outline" | "secondary" = "outline";
            let additionalClasses = "";
            
            // Apply feedback styling if we should show correct answer
            if (shouldShowCorrectAnswer) {
              if (isCorrect) {
                buttonVariant = "default";
                additionalClasses = "bg-green-600 hover:bg-green-700 text-white border-green-600";
              } else if (isSelected && !isCorrect) {
                additionalClasses = "bg-red-600 hover:bg-red-700 text-white border-red-600";
              }
            } else if (isSelected) {
              buttonVariant = "default";
              // If locked but no feedback data (resumed attempt), show neutral locked state
              if (isLocked && !hasCorrectAnswerData) {
                additionalClasses = "opacity-75 cursor-not-allowed";
              }
            }

            return (
              <div key={option.letter} className="space-y-2">
                <Button
                  variant={buttonVariant}
                  className={`w-full justify-start text-left p-4 h-auto whitespace-normal ${additionalClasses}`}
                  onClick={() => !isLocked && onAnswer(option.letter)}
                  disabled={isLocked}
                >
                  <span className="font-semibold mr-3 flex-shrink-0">
                    {option.letter})
                  </span>
                  <span className="flex-1">
                    {option.text}
                  </span>
                </Button>
                
                {/* Option Image */}
                {option.image && (
                  <div className="flex justify-center pl-8">
                    <img 
                      src={option.image} 
                      alt={`Imagem da alternativa ${option.letter}`}
                      className="max-w-full h-auto rounded-lg border border-border"
                    />
                  </div>
                )}
              </div>
            );
          })}
        </div>

        {/* Immediate mode feedback indicator with explanation */}
        {feedbackMode === 'immediate' && isAnswered && shouldShowCorrectAnswer && (
          <div className={`mt-4 p-4 rounded-lg border ${
            selectedAnswer === correctAnswer 
              ? 'bg-green-50 border-green-600 text-green-900' 
              : 'bg-red-50 border-red-600 text-red-900'
          }`}>
            <p className="font-semibold text-sm">
              {selectedAnswer === correctAnswer 
                ? '✓ Resposta correta!' 
                : `✗ Resposta incorreta. Resposta correta: ${correctAnswer}`}
            </p>
            {explanation && (
              <p className="mt-2 text-sm opacity-90">
                {explanation}
              </p>
            )}
          </div>
        )}

        {/* Explanation - shown on final mode results */}
        {feedbackMode !== 'immediate' && shouldShowCorrectAnswer && explanation && (
          <div className="mt-4 p-4 bg-muted/50 rounded-lg border border-border">
            <h3 className="font-semibold text-foreground mb-2">Explicação:</h3>
            <p className="text-muted-foreground text-sm">
              {explanation}
            </p>
          </div>
        )}
        
        {/* Locked indicator for resumed attempts without feedback data */}
        {feedbackMode === 'immediate' && isLocked && !hasCorrectAnswerData && isAnswered && (
          <div className="mt-4 p-3 rounded-lg border bg-muted/50 border-border text-muted-foreground">
            <p className="font-semibold text-sm">
              🔒 Questão já respondida
            </p>
          </div>
        )}
      </div>

      {question.supportText && (
        <SupportTextModal
          text={question.supportText}
          isOpen={isSupportTextOpen}
          onClose={() => setIsSupportTextOpen(false)}
        />
      )}
    </article>
  );
}