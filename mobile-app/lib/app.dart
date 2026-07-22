import 'package:flutter/material.dart';
import 'package:flutter_localizations/flutter_localizations.dart';
import 'package:flutter_screenutil/flutter_screenutil.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'core/localization/localization_provider.dart';
import 'core/theme/app_theme.dart';
import 'core/router/app_router.dart';
import 'presentation/settings/providers/settings_provider.dart';
import 'presentation/settings/providers/app_settings_provider.dart';

class OrphanSponsorshipApp extends ConsumerStatefulWidget {
  const OrphanSponsorshipApp({super.key});

  @override
  ConsumerState<OrphanSponsorshipApp> createState() => _OrphanSponsorshipAppState();
}

class _OrphanSponsorshipAppState extends ConsumerState<OrphanSponsorshipApp> {
  @override
  void initState() {
    super.initState();
    WidgetsBinding.instance.addPostFrameCallback((_) {
      ref.read(settingsProvider.notifier).loadSettings();
      ref.read(appSettingsProvider.notifier).loadSettings();
      ref.read(localeProvider.notifier).loadLocale();
    });
  }

  @override
  Widget build(BuildContext context) {
    final router = ref.watch(appRouterProvider);
    final settings = ref.watch(settingsProvider);
    final locale = ref.watch(localeProvider);

    return ScreenUtilInit(
      designSize: const Size(360, 780),
      minTextAdapt: true,
      splitScreenMode: true,
      builder: (context, child) {
        return Directionality(
          textDirection: locale.languageCode == 'ar' ? TextDirection.rtl : TextDirection.ltr,
          child: MaterialApp.router(
            title: 'تطبيق كفيل لرعاية وكفالة الأيتام',
            debugShowCheckedModeBanner: false,
            theme: AppTheme.lightTheme,
            darkTheme: AppTheme.darkTheme,
            themeMode: settings.themeMode,
            routerConfig: router,
            locale: locale,
            supportedLocales: const [
              Locale('ar', 'SA'),
              Locale('en', 'US'),
            ],
            localizationsDelegates: const [
              GlobalMaterialLocalizations.delegate,
              GlobalWidgetsLocalizations.delegate,
              GlobalCupertinoLocalizations.delegate,
            ],
          ),
        );
      },
    );
  }
}
