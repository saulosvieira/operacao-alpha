import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:go_router/go_router.dart';
import '../../../data/repositories/user_repository.dart';
import '../../../services/providers.dart';
import '../../../core/errors/app_exception.dart';
import '../../../routing/route_paths.dart';

class DeleteAccountScreen extends ConsumerStatefulWidget {
  const DeleteAccountScreen({super.key});
  @override
  ConsumerState<DeleteAccountScreen> createState() => _DeleteAccountScreenState();
}

class _DeleteAccountScreenState extends ConsumerState<DeleteAccountScreen> {
  final _controller = TextEditingController();
  bool _isDeleting = false;

  bool get _canDelete => _controller.text == 'EXCLUIR';

  Future<void> _delete() async {
    setState(() => _isDeleting = true);
    try {
      final repo = UserRepository(api: ref.read(apiClientProvider), cache: ref.read(cacheManagerProvider));
      await repo.deleteAccount();
      final sessionManager = ref.read(sessionManagerProvider);
      await sessionManager.clearAll();
      if (mounted) {
        ScaffoldMessenger.of(context).showSnackBar(const SnackBar(content: Text('Conta excluída com sucesso.')));
        context.go(RoutePaths.login);
      }
    } on AppException catch (e) {
      if (mounted) ScaffoldMessenger.of(context).showSnackBar(SnackBar(content: Text(e.message)));
    } finally {
      if (mounted) setState(() => _isDeleting = false);
    }
  }

  @override
  void dispose() {
    _controller.dispose();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: const Text('Excluir Conta')),
      body: Padding(
        padding: const EdgeInsets.all(24),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Icon(Icons.warning_amber, size: 48, color: Theme.of(context).colorScheme.error),
            const SizedBox(height: 16),
            const Text('Esta ação é irreversível', style: TextStyle(fontSize: 20, fontWeight: FontWeight.bold)),
            const SizedBox(height: 12),
            const Text('Ao excluir sua conta, todos os seus dados serão removidos permanentemente, incluindo histórico de simulados, progresso e assinatura.'),
            const SizedBox(height: 24),
            const Text('Digite EXCLUIR para confirmar:', style: TextStyle(fontWeight: FontWeight.bold)),
            const SizedBox(height: 8),
            TextField(
              controller: _controller,
              onChanged: (_) => setState(() {}),
              decoration: const InputDecoration(hintText: 'EXCLUIR'),
            ),
            const SizedBox(height: 24),
            SizedBox(
              width: double.infinity,
              child: FilledButton(
                onPressed: (_canDelete && !_isDeleting) ? _delete : null,
                style: FilledButton.styleFrom(backgroundColor: Theme.of(context).colorScheme.error),
                child: _isDeleting
                    ? const SizedBox(width: 24, height: 24, child: CircularProgressIndicator(strokeWidth: 2))
                    : const Text('Excluir minha conta'),
              ),
            ),
          ],
        ),
      ),
    );
  }
}
