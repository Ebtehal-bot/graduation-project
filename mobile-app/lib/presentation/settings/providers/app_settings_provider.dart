import 'package:flutter/foundation.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';

import '../../../data/models/app_settings_model.dart';
import '../../../data/repositories/settings_repository.dart';

class AppSettingsState {
  final AppSettingsModel? settings;
  final bool isLoading;
  final String? error;

  const AppSettingsState({
    this.settings,
    this.isLoading = false,
    this.error,
  });

  AppSettingsState copyWith({
    AppSettingsModel? settings,
    bool? isLoading,
    String? error,
  }) {
    return AppSettingsState(
      settings: settings ?? this.settings,
      isLoading: isLoading ?? this.isLoading,
      error: error,
    );
  }
}

class AppSettingsNotifier extends StateNotifier<AppSettingsState> {
  final SettingsRepository _repository;

  AppSettingsNotifier(this._repository) : super(const AppSettingsState());

  Future<void> loadSettings() async {
    state = state.copyWith(isLoading: true, error: null);
    try {
      final settings = await _repository.getSettings();
      debugPrint('[AppSettingsNotifier] Settings loaded: siteName=${settings.siteName}, logo=${settings.siteLogo}');
      state = AppSettingsState(settings: settings, isLoading: false);
    } catch (e) {
      debugPrint('[AppSettingsNotifier] Error loading settings: $e');
      state = state.copyWith(isLoading: false, error: e.toString());
    }
  }
}

final appSettingsProvider = StateNotifierProvider<AppSettingsNotifier, AppSettingsState>((ref) {
  return AppSettingsNotifier(ref.watch(settingsRepositoryProvider));
});
