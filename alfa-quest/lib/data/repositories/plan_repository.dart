import '../../core/cache/cache_manager.dart';
import '../../core/network/api_client.dart';
import '../models/plan.dart';
import 'dart:async';

class PlanRepository {
  final ApiClient _api;
  final CacheManager _cache;

  PlanRepository({required ApiClient api, required CacheManager cache})
      : _api = api, _cache = cache;

  Future<List<Plan>> listPlans() async {
    final key = 'plans:list';
    final cached = await _cache.read<List<Plan>>(key, decode: (json) {
      return (json['items'] as List).map((e) => Plan.fromJson(e as Map<String, dynamic>)).toList();
    });
    if (cached != null) {
      if (cached.stale) unawaited(_revalidatePlans());
      return cached.value;
    }
    return _fetchPlans();
  }

  Future<List<Plan>> _fetchPlans() async {
    final response = await _api.get<Map<String, dynamic>>('/api/plans');
    final data = response.data!;
    final items = (data['data'] as List? ?? []).map((e) => Plan.fromJson(e as Map<String, dynamic>)).toList();
    await _cache.write('plans:list', {'items': items.map((e) => e.toJson()).toList()});
    return items;
  }

  Future<void> _revalidatePlans() async {
    try { await _fetchPlans(); } catch (_) {}
  }

  Future<CheckoutResponse> startCheckout(String planId) async {
    final response = await _api.post<Map<String, dynamic>>('/api/edduz/checkout', data: {'plan_id': planId});
    return CheckoutResponse.fromJson(response.data!);
  }

  Future<void> cancelSubscription() async {
    await _api.post('/api/subscription/cancel');
  }
}
