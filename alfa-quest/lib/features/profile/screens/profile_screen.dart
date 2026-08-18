import '../../../core/theme/app_theme.dart';
import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:go_router/go_router.dart';
import 'package:url_launcher/url_launcher.dart';
import '../../../data/repositories/user_repository.dart';
import '../../../data/models/user.dart';
import '../../../services/providers.dart';
import '../../../core/config/app_config.dart';
import '../../../core/errors/app_exception.dart';
import '../../../routing/route_paths.dart';

final _userRepoProvider = Provider<UserRepository>((ref) {
  return UserRepository(api: ref.watch(apiClientProvider), cache: ref.watch(cacheManagerProvider));
});

final _profileProvider = FutureProvider<User>((ref) async {
  return ref.watch(_userRepoProvider).getProfile();
});

class ProfileScreen extends ConsumerStatefulWidget {
  const ProfileScreen({super.key});
  @override
  ConsumerState<ProfileScreen> createState() => _ProfileScreenState();
}

class _ProfileScreenState extends ConsumerState<ProfileScreen> {
  final _nameController = TextEditingController();
  final _emailController = TextEditingController();
  final _phoneController = TextEditingController();
  bool _isDirty = false;
  bool _isSaving = false;
  User? _user;

  @override
  void dispose() {
    _nameController.dispose();
    _emailController.dispose();
    _phoneController.dispose();
    super.dispose();
  }

  void _initControllers(User user) {
    if (_user?.id == user.id && _isDirty) return;
    _user = user;
    _nameController.text = user.name;
    _emailController.text = user.email;
    _phoneController.text = user.phone ?? '';
  }

  Future<void> _save() async {
    if (!_isDirty || _user == null) return;
    setState(() => _isSaving = true);

    final changes = <String, dynamic>{};
    if (_nameController.text.trim() != _user!.name) changes['name'] = _nameController.text.trim();
    if (_phoneController.text.trim() != (_user!.phone ?? '')) changes['phone'] = _phoneController.text.trim();

    if (changes.isEmpty) {
      setState(() { _isSaving = false; _isDirty = false; });
      return;
    }

    try {
      final repo = ref.read(_userRepoProvider);
      await repo.updateProfile(changes);
      if (mounted) {
        ScaffoldMessenger.of(context).showSnackBar(const SnackBar(content: Text('Perfil atualizado!')));
        setState(() => _isDirty = false);
        ref.invalidate(_profileProvider);
      }
    } on AppException catch (e) {
      if (mounted) ScaffoldMessenger.of(context).showSnackBar(SnackBar(content: Text(e.message)));
    } finally {
      if (mounted) setState(() => _isSaving = false);
    }
  }

  Future<void> _logout() async {
    final authManager = ref.read(authManagerProvider);
    await authManager.logout();
    if (mounted) context.go(RoutePaths.login);
  }

  @override
  Widget build(BuildContext context) {
    final profileAsync = ref.watch(_profileProvider);

    return profileAsync.when(
      loading: () => const Center(child: CircularProgressIndicator()),
      error: (_, __) => const Center(child: Text('Erro ao carregar perfil.')),
      data: (user) {
        _initControllers(user);
        return SingleChildScrollView(
          padding: const EdgeInsets.all(16),
          child: Column(
            children: [
              // Avatar placeholder
              const CircleAvatar(radius: 48, child: Icon(Icons.person, size: 48)),
              const SizedBox(height: 16),

              // Subscription tile
              Card(
                child: ListTile(
                  leading: Icon(Icons.credit_card, color: AppColors.primary),
                  title: const Text('Assinatura'),
                  trailing: Chip(label: Text(user.subscriptionStatus.name.toUpperCase(), style: const TextStyle(fontSize: 11))),
                  onTap: () => context.push(RoutePaths.planos),
                ),
              ),
              const SizedBox(height: 16),

              // Form fields
              TextField(
                controller: _nameController,
                decoration: const InputDecoration(labelText: 'Nome'),
                onChanged: (_) => setState(() => _isDirty = true),
              ),
              const SizedBox(height: 12),
              TextField(
                controller: _emailController,
                decoration: const InputDecoration(labelText: 'E-mail'),
                readOnly: true,
              ),
              const SizedBox(height: 12),
              TextField(
                controller: _phoneController,
                decoration: const InputDecoration(labelText: 'Telefone'),
                keyboardType: TextInputType.phone,
                onChanged: (_) => setState(() => _isDirty = true),
              ),
              const SizedBox(height: 24),

              // Save
              SizedBox(
                width: double.infinity,
                child: FilledButton(
                  onPressed: (_isDirty && !_isSaving) ? _save : null,
                  child: _isSaving
                      ? const SizedBox(width: 24, height: 24, child: CircularProgressIndicator(strokeWidth: 2))
                      : const Text('Salvar alterações'),
                ),
              ),
              const SizedBox(height: 12),

              // Change password
              SizedBox(
                width: double.infinity,
                child: OutlinedButton.icon(
                  onPressed: () => _showChangePasswordDialog(),
                  icon: const Icon(Icons.lock_outline),
                  label: const Text('Alterar senha'),
                ),
              ),
              const SizedBox(height: 24),
              const Divider(),
              const SizedBox(height: 16),

              // Links
              Card(
                child: Column(
                  children: [
                    ListTile(
                      leading: Icon(Icons.info_outline, color: AppColors.primary),
                      title: const Text('Política de Privacidade'),
                      trailing: const Icon(Icons.open_in_new, size: 18),
                      onTap: () => launchUrl(Uri.parse('${AppConfig.apiBaseUrl}/politica-de-privacidade'), mode: LaunchMode.externalApplication),
                    ),
                    ListTile(
                      leading: Icon(Icons.description_outlined, color: AppColors.primary),
                      title: const Text('Termos de Uso'),
                      trailing: const Icon(Icons.open_in_new, size: 18),
                      onTap: () => launchUrl(Uri.parse('${AppConfig.apiBaseUrl}/termos-de-uso'), mode: LaunchMode.externalApplication),
                    ),
                  ],
                ),
              ),
              const SizedBox(height: 24),
              const Divider(),
              const SizedBox(height: 16),

              // Logout
              SizedBox(
                width: double.infinity,
                child: OutlinedButton.icon(
                  onPressed: _logout,
                  icon: const Icon(Icons.logout),
                  label: const Text('Sair da conta'),
                ),
              ),
              const SizedBox(height: 12),

              // Delete
              TextButton.icon(
                onPressed: () => context.push(RoutePaths.excluirConta),
                icon: Icon(Icons.delete_forever, color: Theme.of(context).colorScheme.error),
                label: Text('Excluir conta', style: TextStyle(color: Theme.of(context).colorScheme.error)),
              ),
            ],
          ),
        );
      },
    );
  }

  void _showChangePasswordDialog() {
    final currentCtrl = TextEditingController();
    final newCtrl = TextEditingController();
    final confirmCtrl = TextEditingController();

    showDialog(
      context: context,
      builder: (ctx) => AlertDialog(
        title: const Text('Alterar Senha'),
        content: Column(
          mainAxisSize: MainAxisSize.min,
          children: [
            TextField(controller: currentCtrl, obscureText: true, decoration: const InputDecoration(labelText: 'Senha atual')),
            const SizedBox(height: 8),
            TextField(controller: newCtrl, obscureText: true, decoration: const InputDecoration(labelText: 'Nova senha')),
            const SizedBox(height: 8),
            TextField(controller: confirmCtrl, obscureText: true, decoration: const InputDecoration(labelText: 'Confirmar nova senha')),
          ],
        ),
        actions: [
          TextButton(onPressed: () => Navigator.pop(ctx), child: const Text('Cancelar')),
          FilledButton(
            onPressed: () async {
              try {
                final repo = ref.read(_userRepoProvider);
                await repo.changePassword(
                  currentPassword: currentCtrl.text,
                  newPassword: newCtrl.text,
                  confirmPassword: confirmCtrl.text,
                );
                if (ctx.mounted) Navigator.pop(ctx);
                if (mounted) ScaffoldMessenger.of(context).showSnackBar(const SnackBar(content: Text('Senha alterada!')));
              } on AppException catch (e) {
                if (mounted) ScaffoldMessenger.of(context).showSnackBar(SnackBar(content: Text(e.message)));
              }
            },
            child: const Text('Salvar'),
          ),
        ],
      ),
    );
  }
}
