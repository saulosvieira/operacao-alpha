import '../../../core/theme/app_theme.dart';
import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:go_router/go_router.dart';
import '../../../data/repositories/exam_repository.dart';
import '../../../data/models/exam.dart';
import '../../../services/providers.dart';
import '../../../routing/route_paths.dart';
import '../../../core/errors/app_exception.dart';

class ExamDetailScreen extends ConsumerStatefulWidget {
  final String examId;
  final String? highlightAttemptId;

  const ExamDetailScreen({super.key, required this.examId, this.highlightAttemptId});

  @override
  ConsumerState<ExamDetailScreen> createState() => _ExamDetailScreenState();
}

class _ExamDetailScreenState extends ConsumerState<ExamDetailScreen> {
  Exam? _exam;
  bool _isLoading = true;
  bool _isStarting = false;
  String? _error;

  @override
  void initState() {
    super.initState();
    _loadExam();
  }

  Future<void> _loadExam() async {
    setState(() { _isLoading = true; _error = null; });
    try {
      final repo = ExamRepository(api: ref.read(apiClientProvider), cache: ref.read(cacheManagerProvider));
      _exam = await repo.getExam(widget.examId);
    } on NotFoundException {
      _error = 'Simulado não encontrado.';
    } catch (e) {
      _error = 'Erro ao carregar detalhes.';
    }
    if (mounted) setState(() => _isLoading = false);
  }

  Future<void> _startAttempt() async {
    setState(() => _isStarting = true);
    try {
      final repo = ExamRepository(api: ref.read(apiClientProvider), cache: ref.read(cacheManagerProvider));
      final result = await repo.startAttempt(widget.examId);
      if (mounted) {
        context.push(RoutePaths.tentativa(result.examId, result.attemptId));
      }
    } on AppException catch (e) {
      if (mounted) ScaffoldMessenger.of(context).showSnackBar(SnackBar(content: Text(e.message)));
    } finally {
      if (mounted) setState(() => _isStarting = false);
    }
  }

  @override
  Widget build(BuildContext context) {
    if (_isLoading) return const Scaffold(body: Center(child: CircularProgressIndicator()));
    if (_error != null) {
      return Scaffold(
        appBar: AppBar(),
        body: Center(
          child: Column(
            mainAxisAlignment: MainAxisAlignment.center,
            children: [
              Text(_error!),
              const SizedBox(height: 16),
              FilledButton(onPressed: () => context.pop(), child: const Text('Voltar')),
            ],
          ),
        ),
      );
    }

    final exam = _exam!;
    return Scaffold(
      appBar: AppBar(title: const Text('Detalhes do Simulado')),
      body: Padding(
        padding: const EdgeInsets.all(16),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Text(exam.title, style: const TextStyle(fontSize: 22, fontWeight: FontWeight.bold)),
            if (exam.description != null) ...[
              const SizedBox(height: 8),
              Text(exam.description!, style: TextStyle(color: Colors.grey[400])),
            ],
            const SizedBox(height: 24),
            Wrap(
              spacing: 12,
              children: [
                _MetaChip(icon: Icons.quiz_outlined, label: '${exam.numQuestions} questões'),
                _MetaChip(icon: Icons.timer_outlined, label: '${exam.durationMin} min'),
                _MetaChip(icon: exam.isFree ? Icons.lock_open : Icons.lock, label: exam.isFree ? 'Gratuito' : 'Premium'),
              ],
            ),
            const Spacer(),
            SizedBox(
              width: double.infinity,
              height: 52,
              child: FilledButton(
                onPressed: _isStarting ? null : _startAttempt,
                child: _isStarting
                    ? const SizedBox(width: 24, height: 24, child: CircularProgressIndicator(strokeWidth: 2))
                    : const Text('Iniciar Simulado'),
              ),
            ),
          ],
        ),
      ),
    );
  }
}

class _MetaChip extends StatelessWidget {
  final IconData icon;
  final String label;
  const _MetaChip({required this.icon, required this.label});

  @override
  Widget build(BuildContext context) {
    return Chip(
      avatar: Icon(icon, size: 16, color: AppColors.primary),
      label: Text(label),
      backgroundColor: Colors.grey[900],
    );
  }
}
