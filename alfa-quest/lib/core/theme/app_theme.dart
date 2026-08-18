import 'package:flutter/material.dart';

// Cores extraídas do CSS do frontend React (index.css)
// Primary: HSL(160, 100%, 44%) = #00E095
// Background: HSL(220, 13%, 4%) = #08090B
// Card: HSL(222, 11%, 8%) = #121316
// Secondary: HSL(225, 8%, 12%) = #1C1D21
// Primary-600: HSL(160, 100%, 39%) = #00C684
// Muted-foreground: HSL(217, 14%, 71%) = #AAB2BF

class AppColors {
  static const primary = Color(0xFF00E095);
  static const primaryDark = Color(0xFF00C684);
  static const background = Color(0xFF08090B);
  static const card = Color(0xFF121316);
  static const secondary = Color(0xFF1C1D21);
  static const foreground = Color(0xFFF1F5F9);
  static const mutedForeground = Color(0xFFAAB2BF);
  static const border = Color(0xFF252A31);
  static const destructive = Color(0xFFEF4444);
}

final darkTheme = ThemeData(
  useMaterial3: true,
  brightness: Brightness.dark,
  colorScheme: ColorScheme.dark(
    primary: AppColors.primary,
    onPrimary: AppColors.background,
    secondary: AppColors.primary,
    onSecondary: AppColors.background,
    surface: AppColors.background,
    onSurface: AppColors.foreground,
    error: AppColors.destructive,
    outline: AppColors.border,
  ),
  scaffoldBackgroundColor: AppColors.background,
  cardColor: AppColors.card,
  cardTheme: CardThemeData(
    color: AppColors.card,
    elevation: 0,
    shape: RoundedRectangleBorder(
      borderRadius: BorderRadius.circular(16),
      side: BorderSide(color: AppColors.border.withValues(alpha: 0.5)),
    ),
  ),
  appBarTheme: const AppBarTheme(
    backgroundColor: AppColors.background,
    foregroundColor: AppColors.foreground,
    elevation: 0,
    centerTitle: false,
  ),
  navigationBarTheme: NavigationBarThemeData(
    backgroundColor: AppColors.card,
    indicatorColor: AppColors.primary.withValues(alpha: 0.2),
    iconTheme: WidgetStateProperty.resolveWith((states) {
      if (states.contains(WidgetState.selected)) {
        return const IconThemeData(color: AppColors.primary);
      }
      return const IconThemeData(color: AppColors.mutedForeground);
    }),
    labelTextStyle: WidgetStateProperty.resolveWith((states) {
      if (states.contains(WidgetState.selected)) {
        return const TextStyle(color: AppColors.primary, fontSize: 12, fontWeight: FontWeight.w600);
      }
      return const TextStyle(color: AppColors.mutedForeground, fontSize: 12);
    }),
  ),
  filledButtonTheme: FilledButtonThemeData(
    style: FilledButton.styleFrom(
      backgroundColor: AppColors.primary,
      foregroundColor: AppColors.background,
      shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
      padding: const EdgeInsets.symmetric(horizontal: 24, vertical: 14),
      textStyle: const TextStyle(fontSize: 16, fontWeight: FontWeight.w600),
    ),
  ),
  outlinedButtonTheme: OutlinedButtonThemeData(
    style: OutlinedButton.styleFrom(
      foregroundColor: AppColors.foreground,
      side: BorderSide(color: AppColors.border),
      shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
      padding: const EdgeInsets.symmetric(horizontal: 24, vertical: 14),
    ),
  ),
  textButtonTheme: TextButtonThemeData(
    style: TextButton.styleFrom(
      foregroundColor: AppColors.primary,
    ),
  ),
  inputDecorationTheme: InputDecorationTheme(
    filled: false,
    border: UnderlineInputBorder(borderSide: BorderSide(color: AppColors.border)),
    enabledBorder: UnderlineInputBorder(borderSide: BorderSide(color: AppColors.border)),
    focusedBorder: const UnderlineInputBorder(borderSide: BorderSide(color: AppColors.primary)),
    labelStyle: const TextStyle(color: AppColors.mutedForeground),
    hintStyle: const TextStyle(color: AppColors.mutedForeground),
  ),
  chipTheme: ChipThemeData(
    backgroundColor: AppColors.secondary,
    selectedColor: AppColors.primary.withValues(alpha: 0.2),
    labelStyle: const TextStyle(color: AppColors.foreground),
    side: BorderSide(color: AppColors.border),
    shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(20)),
  ),
  dividerColor: AppColors.border,
  textTheme: const TextTheme(
    headlineLarge: TextStyle(color: AppColors.foreground, fontWeight: FontWeight.bold),
    headlineMedium: TextStyle(color: AppColors.foreground, fontWeight: FontWeight.bold),
    titleLarge: TextStyle(color: AppColors.foreground, fontWeight: FontWeight.w600),
    titleMedium: TextStyle(color: AppColors.foreground),
    bodyLarge: TextStyle(color: AppColors.foreground),
    bodyMedium: TextStyle(color: AppColors.foreground),
    bodySmall: TextStyle(color: AppColors.mutedForeground),
    labelLarge: TextStyle(color: AppColors.foreground),
  ),
  checkboxTheme: CheckboxThemeData(
    fillColor: WidgetStateProperty.resolveWith((states) {
      if (states.contains(WidgetState.selected)) return AppColors.primary;
      return Colors.transparent;
    }),
    checkColor: WidgetStateProperty.all(AppColors.background),
    side: BorderSide(color: AppColors.border),
  ),
  listTileTheme: const ListTileThemeData(
    iconColor: AppColors.mutedForeground,
    textColor: AppColors.foreground,
  ),
  snackBarTheme: SnackBarThemeData(
    backgroundColor: AppColors.card,
    contentTextStyle: const TextStyle(color: AppColors.foreground),
    shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
    behavior: SnackBarBehavior.floating,
  ),
);

final lightTheme = darkTheme; // App é dark-only, como o site
