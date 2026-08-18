import '../../core/network/api_client.dart';
import '../models/career.dart';

class CareerRepository {
  final ApiClient _api;
  List<Career>? _cached;

  CareerRepository({required ApiClient api}) : _api = api;

  Future<List<Career>> listCareers() async {
    if (_cached != null) return _cached!;
    final response = await _api.get<Map<String, dynamic>>('/api/careers');
    final data = response.data!;
    _cached = (data['data'] as List? ?? []).map((e) => Career.fromJson(e as Map<String, dynamic>)).toList();
    return _cached!;
  }
}
