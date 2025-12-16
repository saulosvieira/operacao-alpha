import { useState, useEffect } from 'react';
import { Trophy, Medal, Target, AlertCircle, Loader2 } from 'lucide-react';
import { AppLayout } from '@/components/layout/AppLayout';
import { Badge } from '@/components/ui/badge';
import { Tabs, TabsContent, TabsList, TabsTrigger } from '@/components/ui/tabs';
import { Alert, AlertDescription } from '@/components/ui/alert';
import { rankingService } from '@/services/ranking';
import type { RankingEntry, RankingType } from '@/types';

interface RankingTableProps {
  entries: RankingEntry[];
  myPosition?: number;
  isLoading?: boolean;
}

function RankingTable({ entries, myPosition, isLoading }: RankingTableProps) {
  const formatTime = (seconds: number) => {
    const minutes = Math.floor(seconds / 60);
    const secs = seconds % 60;
    return `${minutes}:${secs.toString().padStart(2, '0')}`;
  };

  const getPositionIcon = (pos: number) => {
    if (pos === 1) return <Trophy className="text-yellow-500" size={20} />;
    if (pos === 2) return <Medal className="text-gray-400" size={20} />;
    if (pos === 3) return <Medal className="text-amber-600" size={20} />;
    return <span className="text-muted-foreground font-mono text-sm">{pos}°</span>;
  };

  if (isLoading) {
    return (
      <div className="space-y-2">
        {[...Array(5)].map((_, i) => (
          <div key={i} className="card-tactical p-3 space-y-2">
            <div className="flex items-center gap-3">
              <div className="w-8 h-8 bg-muted rounded animate-pulse" />
              <div className="space-y-1 flex-1">
                <div className="h-4 bg-muted rounded w-32 animate-pulse" />
                <div className="h-3 bg-muted rounded w-24 animate-pulse" />
              </div>
              <div className="h-6 bg-muted rounded w-16 animate-pulse" />
            </div>
          </div>
        ))}
      </div>
    );
  }

  if (entries.length === 0) {
    return (
      <div className="text-center py-12 space-y-4">
        <Trophy className="mx-auto text-muted-foreground" size={48} />
        <div>
          <h3 className="font-medium text-foreground">Nenhum ranking disponível</h3>
          <p className="text-sm text-muted-foreground mt-1">
            Complete simulados para aparecer no ranking
          </p>
        </div>
      </div>
    );
  }

  return (
    <div className="space-y-4">
      {/* My Position (if outside TOP 10) */}
      {myPosition && myPosition > 10 && (
        <div className="card-tactical p-3 bg-primary/10 border-primary/30">
          <div className="flex items-center justify-between">
            <div className="flex items-center gap-3">
              <div className="flex items-center justify-center w-8 h-8">
                <Target className="text-primary" size={16} />
              </div>
              <div>
                <span className="font-medium text-foreground">Sua posição</span>
                <p className="text-sm text-muted-foreground">Você está em {myPosition}°</p>
              </div>
            </div>
            <Badge variant="outline" className="border-primary text-primary">
              {myPosition}°
            </Badge>
          </div>
        </div>
      )}

      {/* Ranking Table */}
      <div className="space-y-2">
        {entries.map((entry) => (
          <div 
            key={entry.userId}
            className="card-tactical p-3 hover:bg-muted/30 transition-colors"
          >
            <div className="flex items-center justify-between">
              <div className="flex items-center gap-3">
                <div className="flex items-center justify-center w-8 h-8">
                  {getPositionIcon(entry.position)}
                </div>
                <div>
                  <span className="font-medium text-foreground">
                    {entry.partialName}
                  </span>
                  <div className="flex items-center gap-4 text-sm text-muted-foreground">
                    <span>{entry.score}% acertos</span>
                    <span>⏱ {formatTime(entry.averageTimeSeconds)}</span>
                  </div>
                </div>
              </div>
              
              <div className="text-right">
                <div className="text-lg font-bold text-primary">
                  {entry.score.toFixed(1)}%
                </div>
              </div>
            </div>
          </div>
        ))}
      </div>
    </div>
  );
}

export default function Ranking() {
  const [activeTab, setActiveTab] = useState<RankingType>('daily');
  const [rankings, setRankings] = useState<Record<RankingType, RankingEntry[]>>({
    daily: [],
    weekly: [],
    monthly: [],
  });
  const [myPosition, setMyPosition] = useState<Record<RankingType, number | null>>({
    daily: null,
    weekly: null,
    monthly: null,
  });
  const [isLoading, setIsLoading] = useState(true);
  const [error, setError] = useState<string | null>(null);

  useEffect(() => {
    const fetchRanking = async () => {
      try {
        setIsLoading(true);
        setError(null);
        const data = await rankingService.getRanking(activeTab);
        setRankings(prev => ({ ...prev, [activeTab]: data }));
        
        // Fetch my position
        try {
          const position = await rankingService.getMyPosition(activeTab);
          setMyPosition(prev => ({ ...prev, [activeTab]: position }));
        } catch (err) {
          // User might not have any attempts yet
          setMyPosition(prev => ({ ...prev, [activeTab]: null }));
        }
      } catch (err: any) {
        setError(err.response?.data?.message || 'Erro ao carregar ranking');
      } finally {
        setIsLoading(false);
      }
    };

    fetchRanking();
  }, [activeTab]);

  return (
    <AppLayout>
      <div className="p-4 space-y-6">
        {/* Header */}
        <div className="text-center space-y-2">
          <h1 className="text-2xl font-bold text-foreground">Ranking</h1>
          <p className="text-muted-foreground">
            Veja sua posição entre os melhores militares
          </p>
        </div>

        {/* Error Alert */}
        {error && (
          <Alert variant="destructive">
            <AlertCircle className="h-4 w-4" />
            <AlertDescription>{error}</AlertDescription>
          </Alert>
        )}

        {/* Tabs */}
        <Tabs value={activeTab} onValueChange={(value) => setActiveTab(value as RankingType)}>
          <TabsList className="grid w-full grid-cols-3 bg-muted/50">
            <TabsTrigger 
              value="daily"
              className="data-[state=active]:bg-primary data-[state=active]:text-primary-foreground"
            >
              Diário
            </TabsTrigger>
            <TabsTrigger 
              value="weekly"
              className="data-[state=active]:bg-primary data-[state=active]:text-primary-foreground"
            >
              Semanal
            </TabsTrigger>
            <TabsTrigger 
              value="monthly"
              className="data-[state=active]:bg-primary data-[state=active]:text-primary-foreground"
            >
              Mensal
            </TabsTrigger>
          </TabsList>

          <TabsContent value="daily">
            <RankingTable 
              entries={rankings.daily} 
              myPosition={myPosition.daily || undefined}
              isLoading={isLoading}
            />
          </TabsContent>

          <TabsContent value="weekly">
            <RankingTable 
              entries={rankings.weekly} 
              myPosition={myPosition.weekly || undefined}
              isLoading={isLoading}
            />
          </TabsContent>

          <TabsContent value="monthly">
            <RankingTable 
              entries={rankings.monthly} 
              myPosition={myPosition.monthly || undefined}
              isLoading={isLoading}
            />
          </TabsContent>
        </Tabs>
      </div>
    </AppLayout>
  );
}