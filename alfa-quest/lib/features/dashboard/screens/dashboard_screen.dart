import '../../../core/theme/app_theme.dart';
import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:go_router/go_router.dart';
import '../../../data/repositories/performance_repository.dart';
import '../../../data/repositories/ranking_repository.dart';
import '../../../data/models/performance.dart';
import '../../../data/models/ranking.dart';
import '../../../services/providers.dart';
import '../../../routing/route_paths.dart';

final _performanceRepoProvider = Provider<PerformanceRepository>((ref) {
  return PerformanceRepository(
    api: ref.watch(apiClientProvider),
    cache: ref.watch(cacheManagerProvider),
  );
});

final _rankingRepoProvider = Provider<RankingRepository>((ref) {
  return RankingRepository(
    api: ref.watch(apiClientProvider),
    cache: ref.watch(cacheManagerProvider),
  );
});

final _statsProvider = FutureProvider<PerformanceStatistics?>((ref) async {
  try {
    return await ref.watch(_performanceRepoProvider).getStatistics();
  } catch (_) { return null; }
});

final _positionProvider = FutureProvider<MyRankingPosition?>((ref) async {
  try {
    return await ref.watch(_rankingRepoProvider).getMyPosition();
  } catch (_) { return null; }
});

class DashboardScreen extends ConsumerWidget {
  const DashboardScreen({super.key});

  @override
  Widget build(BuildContext context, WidgetRef ref) {
    final statsAsync = ref.watch(_statsProvider);
    final positionAsync = ref.watch(_positionProvider);

    return RefreshIndicator(
      onRefresh: () async {
        ref.invalidate(_statsProvider);
        ref.invalidate(_positionProvider);
      },
      child: ListView(
        padding: const EdgeInsets.all(16),
        children: [
          // Stats cards
          statsAsync.when(
            data: (stats) => stats != null ? _buildStatsCards(stats) : _buildEmptyState(),
            loading: () => const Center(child: CircularProgressIndicator()),
            error: (_, __) => _buildErrorCard(),
          ),
          const SizedBox(height: 16),

          // Ranking position
          positionAsync.when(
            data: (pos) => _buildPositionCard(pos),
            loading: () => const SizedBox.shrink(),
            error: (_, __) => const SizedBox.shrink(),
          ),
          const SizedBox(height: 24),

          // Quick actions
          FilledButton.icon(
            onPressed: () => context.go(RoutePaths.simulados),
            icon: const Icon(Icons.quiz),
            label: const Text('Ver Simulados'),
          ),
          const SizedBox(height: 12),
          OutlinedButton.icon(
            onPressed: () => context.push(RoutePaths.historico),
            icon: const Icon(Icons.history),
            label: const Text('Histórico de Tentativas'),
          ),
        ],
      ),
    );
  }

  Widget _buildStatsCards(PerformanceStatistics stats) {
    return Column(
      children: [
        Row(
          children: [
            Expanded(child: _StatCard(label: 'Simulados', value: '${stats.totalExamsCompleted}', icon: Icons.assignment_turned_in)),
            const SizedBox(width: 12),
            Expanded(child: _StatCard(label: 'Aproveitamento', value: '${stats.accuracyPercentage.toStringAsFixed(1)}%', icon: Icons.trending_up)),
          ],
        ),
      ],
    );
  }

  Widget _buildPositionCard(MyRankingPosition? pos) {
    return Card(
      color: AppColors.primary.withValues(alpha: 0.15),
      child: ListTile(
        leading: Icon(Icons.emoji_events, color: AppColors.primary),
        title: Text('Sua posição', style: TextStyle(color: AppColors.primary)),
        subtitle: Text(
          pos?.position != null
              ? '${pos!.position}º lugar - ${pos.score} pontos'
              : 'Complete simulados para aparecer no ranking',
          style: const TextStyle(color: Colors.white),
        ),
      ),
    );
  }

  Widget _buildEmptyState() {
    return const Card(
      child: Padding(
        padding: EdgeInsets.all(24),
        child: Column(
          children: [
            Icon(Icons.school, size: 48, color: AppColors.primary),
            SizedBox(height: 16),
            Text('Comece agora!', style: TextStyle(fontSize: 18, fontWeight: FontWeight.bold)),
            SizedBox(height: 8),
            Text('Faça seu primeiro simulado para ver suas estatísticas.', textAlign: TextAlign.center),
          ],
        ),
      ),
    );
  }

  Widget _buildErrorCard() {
    return const Card(
      child: Padding(
        padding: EdgeInsets.all(16),
        child: Text('Não foi possível carregar estatísticas.'),
      ),
    );
  }
}

class _StatCard extends StatelessWidget {
  final String label;
  final String value;
  final IconData icon;

  const _StatCard({required this.label, required this.value, required this.icon});

  @override
  Widget build(BuildContext context) {
    return Card(
      child: Padding(
        padding: const EdgeInsets.all(16),
        child: Column(
          children: [
            Icon(icon, color: AppColors.primary),
            const SizedBox(height: 8),
            Text(value, style: const TextStyle(fontSize: 24, fontWeight: FontWeight.bold)),
            Text(label, style: TextStyle(color: Colors.grey[400])),
          ],
        ),
      ),
    );
  }
}
