import '../../../core/theme/app_theme.dart';
import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:go_router/go_router.dart';
import '../../../data/repositories/performance_repository.dart';
import '../../../data/models/history_item.dart';
import '../../../data/models/exam.dart';
import '../../../services/providers.dart';
import '../../../routing/route_paths.dart';

final _perfRepoProvider = Provider<PerformanceRepository>((ref) {
  return PerformanceRepository(api: ref.watch(apiClientProvider), cache: ref.watch(cacheManagerProvider));
});

final _historyProvider = FutureProvider<List<HistoryItem>>((ref) async {
  return ref.watch(_perfRepoProvider).getHistory();
});

class HistoryScreen extends ConsumerWidget {
  const HistoryScreen({super.key});

  @override
  Widget build(BuildContext context, WidgetRef ref) {
    final historyAsync = ref.watch(_historyProvider);

    return Scaffold(
      appBar: AppBar(title: const Text('Histórico')),
      body: RefreshIndicator(
        onRefresh: () async => ref.invalidate(_historyProvider),
        child: historyAsync.when(
          loading: () => const Center(child: CircularProgressIndicator()),
          error: (_, __) => const Center(child: Text('Erro ao carregar histórico.')),
          data: (items) => items.isEmpty
              ? const Center(child: Text('Nenhuma tentativa registrada.'))
              : ListView.builder(
                  padding: const EdgeInsets.all(16),
                  itemCount: items.length,
                  itemBuilder: (context, index) {
                    final item = items[index];
                    return Card(
                      margin: const EdgeInsets.only(bottom: 12),
                      child: ListTile(
                        title: Text(item.examTitle),
                        subtitle: Text(
                          item.status == AttemptStatus.finished
                              ? 'Concluído - ${item.accuracyPercentage?.toStringAsFixed(1) ?? '-'}%'
                              : 'Em andamento',
                        ),
                        trailing: Icon(
                          item.status == AttemptStatus.finished ? Icons.check_circle : Icons.play_circle,
                          color: item.status == AttemptStatus.finished ? AppColors.primary : Colors.orange,
                        ),
                        onTap: () {
                          if (item.status == AttemptStatus.finished) {
                            context.push(RoutePaths.examResultado(item.examId, item.attemptId));
                          } else {
                            context.push(RoutePaths.tentativa(item.examId, item.attemptId));
                          }
                        },
                      ),
                    );
                  },
                ),
        ),
      ),
    );
  }
}
