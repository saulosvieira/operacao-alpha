import '../../../core/theme/app_theme.dart';
import 'package:flutter/material.dart';
import 'package:url_launcher/url_launcher.dart';

class ForceUpdateScreen extends StatelessWidget {
  const ForceUpdateScreen({super.key});

  @override
  Widget build(BuildContext context) {
    return PopScope(
      canPop: false,
      child: Scaffold(
        body: Center(
          child: Padding(
            padding: const EdgeInsets.all(32),
            child: Column(
              mainAxisAlignment: MainAxisAlignment.center,
              children: [
                Icon(Icons.system_update, size: 80, color: AppColors.primary),
                const SizedBox(height: 24),
                const Text('Atualização Obrigatória', style: TextStyle(fontSize: 24, fontWeight: FontWeight.bold)),
                const SizedBox(height: 16),
                const Text('Uma nova versão do aplicativo está disponível. Atualize para continuar usando o Operação Alfa.', textAlign: TextAlign.center),
                const SizedBox(height: 32),
                SizedBox(
                  width: double.infinity,
                  child: FilledButton(
                    onPressed: () => launchUrl(
                      Uri.parse('https://play.google.com/store/apps/details?id=br.com.operacaoalfa.app'),
                      mode: LaunchMode.externalApplication,
                    ),
                    child: const Text('Atualizar agora'),
                  ),
                ),
              ],
            ),
          ),
        ),
      ),
    );
  }
}
