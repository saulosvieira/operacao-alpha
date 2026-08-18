import '../../../core/theme/app_theme.dart';
import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:url_launcher/url_launcher.dart';
import '../../../data/repositories/plan_repository.dart';
import '../../../data/models/plan.dart';
import '../../../services/providers.dart';
import '../../../core/errors/app_exception.dart';

final _planRepoProvider = Provider<PlanRepository>((ref) {
  return PlanRepository(api: ref.watch(apiClientProvider), cache: ref.watch(cacheManagerProvider));
});

final _plansProvider = FutureProvider<List<Plan>>((ref) async {
  return ref.watch(_planRepoProvider).listPlans();
});

class PlansScreen extends ConsumerWidget {
  const PlansScreen({super.key});

  @override
  Widget build(BuildContext context, WidgetRef ref) {
    final plansAsync = ref.watch(_plansProvider);

    return Scaffold(
      appBar: AppBar(title: const Text('Planos')),
      body: plansAsync.when(
        loading: () => const Center(child: CircularProgressIndicator()),
        error: (_, __) => const Center(child: Text('Erro ao carregar planos.')),
        data: (plans) => plans.isEmpty
            ? const Center(child: Text('Nenhum plano disponível.'))
            : ListView.builder(
                padding: const EdgeInsets.all(16),
                itemCount: plans.length,
                itemBuilder: (context, index) {
                  final plan = plans[index];
                  return Card(
                    margin: const EdgeInsets.only(bottom: 16),
                    child: Padding(
                      padding: const EdgeInsets.all(16),
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          Text(plan.name, style: const TextStyle(fontSize: 20, fontWeight: FontWeight.bold)),
                          const SizedBox(height: 8),
                          Text(plan.description, style: TextStyle(color: Colors.grey[400])),
                          const SizedBox(height: 12),
                          Text('R\$ ${plan.price.toStringAsFixed(2)}', style: TextStyle(fontSize: 24, fontWeight: FontWeight.bold, color: AppColors.primary)),
                          Text('${plan.durationDays} dias', style: TextStyle(color: Colors.grey[500])),
                          const SizedBox(height: 12),
                          ...plan.features.map((f) => Padding(
                            padding: const EdgeInsets.only(bottom: 4),
                            child: Row(
                              children: [
                                Icon(Icons.check, size: 16, color: AppColors.primary),
                                const SizedBox(width: 8),
                                Expanded(child: Text(f)),
                              ],
                            ),
                          )),
                          const SizedBox(height: 16),
                          SizedBox(
                            width: double.infinity,
                            child: FilledButton(
                              onPressed: () => _startCheckout(context, ref, plan.id),
                              child: const Text('Assinar'),
                            ),
                          ),
                        ],
                      ),
                    ),
                  );
                },
              ),
      ),
    );
  }

  Future<void> _startCheckout(BuildContext context, WidgetRef ref, String planId) async {
    try {
      final repo = ref.read(_planRepoProvider);
      final result = await repo.startCheckout(planId);
      await launchUrl(Uri.parse(result.checkoutUrl), mode: LaunchMode.externalApplication);
    } on AppException catch (e) {
      if (context.mounted) ScaffoldMessenger.of(context).showSnackBar(SnackBar(content: Text(e.message)));
    }
  }
}
