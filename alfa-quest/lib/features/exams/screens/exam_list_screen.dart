import '../../../core/theme/app_theme.dart';
import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:go_router/go_router.dart';
import '../../../data/repositories/exam_repository.dart';
import '../../../data/repositories/career_repository.dart';
import '../../../data/models/exam.dart';
import '../../../data/models/career.dart';
import '../../../services/providers.dart';
import '../../../routing/route_paths.dart';

final _examRepoProvider = Provider<ExamRepository>((ref) {
  return ExamRepository(api: ref.watch(apiClientProvider), cache: ref.watch(cacheManagerProvider));
});

final _careerRepoProvider = Provider<CareerRepository>((ref) {
  return CareerRepository(api: ref.watch(apiClientProvider));
});

final _selectedCareerProvider = StateProvider<int?>((ref) => null);

final _careersProvider = FutureProvider<List<Career>>((ref) async {
  return ref.watch(_careerRepoProvider).listCareers();
});

final _examsProvider = FutureProvider<List<Exam>>((ref) async {
  final careerId = ref.watch(_selectedCareerProvider);
  return ref.watch(_examRepoProvider).listExams(careerId: careerId);
});

class ExamListScreen extends ConsumerWidget {
  const ExamListScreen({super.key});

  @override
  Widget build(BuildContext context, WidgetRef ref) {
    final careersAsync = ref.watch(_careersProvider);
    final examsAsync = ref.watch(_examsProvider);
    final selectedCareer = ref.watch(_selectedCareerProvider);

    return RefreshIndicator(
      onRefresh: () async { ref.invalidate(_examsProvider); },
      child: Column(
        children: [
          // Career filter chips
          careersAsync.when(
            data: (careers) => SizedBox(
              height: 52,
              child: ListView(
                scrollDirection: Axis.horizontal,
                padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 8),
                children: [
                  Padding(
                    padding: const EdgeInsets.only(right: 8),
                    child: FilterChip(
                      selected: selectedCareer == null,
                      label: const Text('Todas as carreiras'),
                      onSelected: (_) => ref.read(_selectedCareerProvider.notifier).state = null,
                    ),
                  ),
                  ...careers.map((c) => Padding(
                    padding: const EdgeInsets.only(right: 8),
                    child: FilterChip(
                      selected: selectedCareer == c.id,
                      label: Text(c.name),
                      onSelected: (_) => ref.read(_selectedCareerProvider.notifier).state = c.id,
                    ),
                  )),
                ],
              ),
            ),
            loading: () => const SizedBox(height: 52),
            error: (_, __) => const SizedBox.shrink(),
          ),

          // Exam list
          Expanded(
            child: examsAsync.when(
              data: (exams) => exams.isEmpty
                  ? const Center(child: Text('Nenhum simulado disponível.'))
                  : ListView.builder(
                      padding: const EdgeInsets.symmetric(horizontal: 16),
                      itemCount: exams.length,
                      itemBuilder: (context, index) => _ExamCard(exam: exams[index]),
                    ),
              loading: () => const Center(child: CircularProgressIndicator()),
              error: (_, __) => const Center(child: Text('Erro ao carregar simulados.')),
            ),
          ),
        ],
      ),
    );
  }
}

class _ExamCard extends StatelessWidget {
  final Exam exam;
  const _ExamCard({required this.exam});

  @override
  Widget build(BuildContext context) {
    return Card(
      margin: const EdgeInsets.only(bottom: 12),
      child: ListTile(
        title: Text(exam.title, style: const TextStyle(fontWeight: FontWeight.bold)),
        subtitle: Row(
          children: [
            const Icon(Icons.quiz_outlined, size: 14),
            const SizedBox(width: 4),
            Text('${exam.numQuestions} questões'),
            const SizedBox(width: 16),
            const Icon(Icons.timer_outlined, size: 14),
            const SizedBox(width: 4),
            Text('${exam.durationMin} min'),
          ],
        ),
        trailing: exam.isFree
            ? Container(
                padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 4),
                decoration: BoxDecoration(
                  color: AppColors.primary.withValues(alpha: 0.15),
                  borderRadius: BorderRadius.circular(12),
                ),
                child: Text('Grátis', style: TextStyle(color: AppColors.primary, fontSize: 12)),
              )
            : null,
        onTap: () => context.push(RoutePaths.examDetail(exam.id)),
      ),
    );
  }
}
