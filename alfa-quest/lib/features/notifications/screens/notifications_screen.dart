import '../../../core/theme/app_theme.dart';
import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:go_router/go_router.dart';
import '../../../data/repositories/notifications_repository.dart';
import '../../../data/models/notification_models.dart';
import '../../../services/providers.dart';
import '../../../routing/app_router.dart';

final _notifRepoProvider = Provider<NotificationsRepository>((ref) {
  return NotificationsRepository(ref.watch(databaseProvider));
});

final _notificationsProvider = FutureProvider<List<AppInboxNotification>>((ref) async {
  final repo = ref.watch(_notifRepoProvider);
  await repo.markAllRead();
  return repo.listLatest();
});

class NotificationsScreen extends ConsumerWidget {
  const NotificationsScreen({super.key});

  @override
  Widget build(BuildContext context, WidgetRef ref) {
    final notifsAsync = ref.watch(_notificationsProvider);

    return Scaffold(
      appBar: AppBar(title: const Text('Notificações')),
      body: notifsAsync.when(
        loading: () => const Center(child: CircularProgressIndicator()),
        error: (_, __) => const Center(child: Text('Erro ao carregar notificações.')),
        data: (notifications) => notifications.isEmpty
            ? const Center(child: Text('Nenhuma notificação.'))
            : ListView.builder(
                itemCount: notifications.length,
                itemBuilder: (context, index) {
                  final n = notifications[index];
                  return ListTile(
                    leading: Icon(
                      Icons.notifications,
                      color: n.read ? Colors.grey : AppColors.primary,
                    ),
                    title: Text(n.title ?? 'Notificação'),
                    subtitle: n.body != null
                        ? Text(n.body!, maxLines: 2, overflow: TextOverflow.ellipsis)
                        : null,
                    trailing: Text(
                      _formatDate(n.receivedAt),
                      style: TextStyle(fontSize: 12, color: Colors.grey[500]),
                    ),
                    onTap: () {
                      if (n.url != null) {
                        final deepLinkHandler = ref.read(deepLinkHandlerProvider);
                        final route = deepLinkHandler.resolve(Uri.parse(n.url!));
                        if (route != null) context.push(route);
                      }
                    },
                  );
                },
              ),
      ),
    );
  }

  String _formatDate(DateTime dt) {
    final now = DateTime.now();
    final diff = now.difference(dt);
    if (diff.inMinutes < 60) return '${diff.inMinutes}min';
    if (diff.inHours < 24) return '${diff.inHours}h';
    return '${diff.inDays}d';
  }
}
