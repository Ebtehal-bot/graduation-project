import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';

import '../../../core/localization/app_strings.dart';
import '../../../core/localization/localization_provider.dart';
import '../../../core/theme/app_colors.dart';
import '../providers/settings_provider.dart';
import 'help_center_screen.dart';

class SettingsScreen extends ConsumerWidget {
  const SettingsScreen({super.key});

  @override
  Widget build(BuildContext context, WidgetRef ref) {
    final settings = ref.watch(settingsProvider);
    final currentLocale = ref.watch(localeProvider);
    final isDark = Theme.of(context).brightness == Brightness.dark;

    return Scaffold(
      appBar: AppBar(
        title: Text(AppStrings.get('settings')),
      ),
      body: ListView(
        padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 8),
        children: [
          // --- Language Section ---
          _sectionHeader(AppStrings.get('language'), isDark),
          Card(
            child: Column(
              children: [
                RadioListTile<String>(
                  title: Text(AppStrings.get('arabic')),
                  subtitle: Text(
                    'العربية',
                    style: TextStyle(
                      color: isDark ? Colors.grey[400] : AppColors.neutral500,
                    ),
                  ),
                  value: 'ar',
                  groupValue: currentLocale.languageCode,
                  activeColor: AppColors.primary,
                  toggleable: false,
                  onChanged: (value) {
                    if (value != null) {
                      ref.read(localeProvider.notifier).setLocale(value);
                    }
                  },
                ),
                RadioListTile<String>(
                  title: Text(AppStrings.get('english')),
                  subtitle: Text(
                    'English',
                    style: TextStyle(
                      color: isDark ? Colors.grey[400] : AppColors.neutral500,
                    ),
                  ),
                  value: 'en',
                  groupValue: currentLocale.languageCode,
                  activeColor: AppColors.primary,
                  toggleable: false,
                  onChanged: (value) {
                    if (value != null) {
                      ref.read(localeProvider.notifier).setLocale(value);
                    }
                  },
                ),
              ],
            ),
          ),
          const SizedBox(height: 24),

          // --- Theme Section ---
          _sectionHeader(AppStrings.get('theme'), isDark),
          Card(
            child: Column(
              children: [
                ListTile(
                  leading: const Icon(Icons.light_mode_rounded),
                  title: Text(AppStrings.get('light')),
                  trailing: Radio<ThemeMode>(
                    value: ThemeMode.light,
                    groupValue: settings.themeMode,
                    activeColor: AppColors.primary,
                    onChanged: (value) {
                      if (value != null) {
                        ref.read(settingsProvider.notifier).setThemeMode(value);
                      }
                    },
                  ),
                  onTap: () => ref.read(settingsProvider.notifier).setThemeMode(ThemeMode.light),
                ),
                ListTile(
                  leading: const Icon(Icons.dark_mode_rounded),
                  title: Text(AppStrings.get('dark')),
                  trailing: Radio<ThemeMode>(
                    value: ThemeMode.dark,
                    groupValue: settings.themeMode,
                    activeColor: AppColors.primary,
                    onChanged: (value) {
                      if (value != null) {
                        ref.read(settingsProvider.notifier).setThemeMode(value);
                      }
                    },
                  ),
                  onTap: () => ref.read(settingsProvider.notifier).setThemeMode(ThemeMode.dark),
                ),
                ListTile(
                  leading: const Icon(Icons.settings_brightness_rounded),
                  title: Text(AppStrings.get('system')),
                  trailing: Radio<ThemeMode>(
                    value: ThemeMode.system,
                    groupValue: settings.themeMode,
                    activeColor: AppColors.primary,
                    onChanged: (value) {
                      if (value != null) {
                        ref.read(settingsProvider.notifier).setThemeMode(value);
                      }
                    },
                  ),
                  onTap: () => ref.read(settingsProvider.notifier).setThemeMode(ThemeMode.system),
                ),
              ],
            ),
          ),
          const SizedBox(height: 24),

          // --- Notifications Section ---
          _sectionHeader(AppStrings.get('notifications'), isDark),
          Card(
            child: SwitchListTile(
              title: Text(AppStrings.get('push_notifications')),
              subtitle: Text(
                AppStrings.get('push_notifications_desc'),
                style: TextStyle(
                  color: isDark ? Colors.grey[400] : AppColors.neutral500,
                ),
              ),
              value: settings.notificationsEnabled,
              activeColor: AppColors.primary,
              onChanged: (value) {
                ref
                    .read(settingsProvider.notifier)
                    .toggleNotifications(value);
              },
            ),
          ),
          const SizedBox(height: 24),

          // --- Help Center Section ---
          _sectionHeader(AppStrings.get('help_center'), isDark),
          Card(
            child: ListTile(
              leading: const Icon(Icons.help_outline_rounded, color: AppColors.primary),
              title: Text(AppStrings.get('help_center')),
              subtitle: Text(
                AppStrings.get('help_getting_started'),
                style: TextStyle(
                  color: isDark ? Colors.grey[400] : AppColors.neutral500,
                  fontSize: 12,
                ),
              ),
              trailing: Icon(
                Icons.arrow_forward_ios,
                size: 16,
                color: isDark ? Colors.grey[600] : AppColors.neutral400,
              ),
              onTap: () {
                Navigator.of(context).push(
                  MaterialPageRoute(builder: (_) => const HelpCenterScreen()),
                );
              },
            ),
          ),
          const SizedBox(height: 24),
          Center(
            child: Text(
              '${AppStrings.get('app_version')} 1.0.0',
              style: TextStyle(
                color: isDark ? Colors.grey[600] : AppColors.neutral400,
                fontSize: 12,
              ),
            ),
          ),
          const SizedBox(height: 24),
        ],
      ),
    );
  }

  Widget _sectionHeader(String title, bool isDark) {
    return Padding(
      padding: const EdgeInsets.only(bottom: 8, left: 4),
      child: Text(
        title,
        style: TextStyle(
          fontSize: 16,
          fontWeight: FontWeight.w600,
          color: isDark ? Colors.grey[300] : AppColors.neutral700,
        ),
      ),
    );
  }
}
