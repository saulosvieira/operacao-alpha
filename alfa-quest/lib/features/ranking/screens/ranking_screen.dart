import '../../../core/theme/app_theme.dart';
import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import '../../../data/repositories/ranking_repository.dart';
import '../../../data/models/ranking.dart';
import '../../../services/providers.dart';

final _rankingRepoProvider = Provider<RankingRepository>((ref) {
  return RankingRepository(api: ref.watch(apiClientProvider), cache: ref.watch(cacheManagerProvider));
});

final _weeklyRankingProvider = FutureProvider<List<RankingEntry>>((ref) async {
  return ref.watch(_rankingRepoProvider).getWeeklyRanking();
});

final _myPositionProvider = FutureProvider<MyRankingPosition?>((ref) async {
  return ref.watch(_rankingRepoProvider).getMyPosition();
});

class RankingScreen extends ConsumerWidget {
  const RankingScreen({super.key});

  @override
  Widget build(BuildContext context, WidgetRef ref) {
    final rankingAsync = ref.watch(_weeklyRankingProvider);
    final positionAsync = ref.watch(_myPositionProvider);

    return RefreshIndicator(
      onRefresh: () async {
        ref.invalidate(_weeklyRankingProvider);
        ref.invalidate(_myPositionProvider);
      },
      child: ListView(
        padding: const EdgeInsets.all(16),
        children: [
          // My position card
          positionAsync.when(
            data: (pos) => Card(
              color: AppColors.primary.withValues(alpha: 0.15),
              child: Padding(
                padding: const EdgeInsets.all(16),
                child: Row(
                  children: [
                    Icon(Icons.emoji_events, color: AppColors.primary, size: 32),
                    const SizedBox(width: 16),
                    Expanded(
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          Text('Sua posição', style: TextStyle(color: AppColors.primary, fontWeight: FontWeight.bold)),
                          const SizedBox(height: 4),
                          Text(
                            pos?.position != null
                                ? '${pos!.position}º lugar - ${pos.score} pontos'
                                : 'Complete simulados para aparecer no ranking',
                            style: const TextStyle(color: Colors.white),
                          ),
                        ],
                      ),
                    ),
                  ],
                ),
              ),
            ),
            loading: () => const SizedBox.shrink(),
            error: (_, __) => const SizedBox.shrink(),
          ),
          const SizedBox(height: 16),

          // Ranking list
          rankingAsync.when(
            data: (entries) => entries.isEmpty
                ? _buildEmptyState()
                : Column(
                    children: entries.map((entry) => _RankingTile(entry: entry)).toList(),
                  ),
            loading: () => const Center(child: CircularProgressIndicator()),
            error: (_, __) => const Center(child: Text('Erro ao carregar ranking.')),
          ),
        ],
      ),
    );
  }

  Widget _buildEmptyState() {
    return Center(
      child: Column(
        mainAxisAlignment: MainAxisAlignment.center,
        children: [
          const SizedBox(height: 80),
          Icon(Icons.leaderboard, size: 64, color: Colors.grey[600]),
          const SizedBox(height: 16),
          const Text('Ranking indisponível', style: TextStyle(fontSize: 18, fontWeight: FontWeight.bold)),
          const SizedBox(height: 8),
          Text('Nenhum dado de ranking disponível no momento.', style: TextStyle(color: Colors.grey[400])),
        ],
      ),
    );
  }
}

class _RankingTile extends StatelessWidget {
  final RankingEntry entry;
  const _RankingTile({required this.entry});

  @override
  Widget build(BuildContext context) {
    return Card(
      color: entry.isCurrentUser ? AppColors.primary.withValues(alpha: 0.2) : null,
      margin: const EdgeInsets.only(bottom: 8),
      child: ListTile(
        leading: CircleAvatar(
          backgroundColor: _positionColor(entry.position),
          child: Text('${entry.position}', style: const TextStyle(fontWeight: FontWeight.bold, color: Colors.white)),
        ),
        title: Text(entry.userName, style: TextStyle(fontWeight: entry.isCurrentUser ? FontWeight.bold : FontWeight.normal)),
        trailing: Text('${entry.score} pts', style: const TextStyle(fontWeight: FontWeight.bold)),
      ),
    );
  }

  Color _positionColor(int pos) {
    if (pos == 1) return Colors.amber;
    if (pos == 2) return Colors.grey;
    if (pos == 3) return Colors.brown;
    return Colors.grey[700]!;
  }
}
