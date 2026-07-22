import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:shared_preferences/shared_preferences.dart';
import 'app_strings.dart';

final localeProvider = StateNotifierProvider<LocaleNotifier, Locale>((ref) {
  return LocaleNotifier();
});

class LocaleNotifier extends StateNotifier<Locale> {
  LocaleNotifier() : super(const Locale('ar', 'SA'));

  Future<void> loadLocale() async {
    final prefs = await SharedPreferences.getInstance();
    final code = prefs.getString('locale') ?? 'ar';
    _setLocale(code);
  }

  Future<void> setLocale(String code) async {
    _setLocale(code);
    final prefs = await SharedPreferences.getInstance();
    await prefs.setString('locale', code);
  }

  void _setLocale(String code) {
    AppStrings.setLocale(code);
    state = code == 'en' ? const Locale('en', 'US') : const Locale('ar', 'SA');
  }
}
