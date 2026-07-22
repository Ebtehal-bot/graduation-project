import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:go_router/go_router.dart';

import '../../presentation/auth/providers/auth_provider.dart';
import '../../presentation/auth/screens/login_screen.dart';
import '../../presentation/auth/screens/register_screen.dart';
import '../../presentation/auth/screens/splash_screen.dart';
import '../network/session_manager.dart';
import '../../presentation/favorites/screens/favorites_screen.dart';
import '../../presentation/home/screens/home_screen.dart';
import '../../presentation/notifications/screens/notifications_screen.dart';
import '../../presentation/orphans/screens/orphan_details_screen.dart';
import '../../presentation/orphans/screens/orphans_screen.dart';
import '../../presentation/profile/screens/profile_screen.dart';
import '../../presentation/settings/screens/settings_screen.dart';
import '../../presentation/sponsorships/screens/my_sponsorships_screen.dart';
import '../../presentation/sponsorships/screens/sponsor_reports_screen.dart';
import '../../presentation/sponsorships/screens/sponsorship_request_screen.dart';
import '../../presentation/sponsorships/screens/sponsorship_timeline_screen.dart';
import '../../presentation/about/screens/about_screen.dart';
import '../../presentation/contact/screens/contact_screen.dart';
import '../../data/models/orphan_model.dart';
import '../localization/app_strings.dart';
import 'route_names.dart';

final _rootNavigatorKey = GlobalKey<NavigatorState>();

GoRouter _createRouter(Ref<dynamic> ref) {
  return GoRouter(
    navigatorKey: _rootNavigatorKey,
    initialLocation: RouteNames.splash,
    debugLogDiagnostics: false,

    redirect: (context, state) {
      final authState = ref.read(authProvider);
      final isAuthenticated = authState.isAuthenticated;
      final isAuthUnknown = authState.status == AuthStatus.unknown;
      final location = state.uri.toString();

      if (isAuthUnknown) return null;

      final isOnSplash = location == RouteNames.splash;
      final isOnLogin = location == RouteNames.login;
      final isOnRegister = location == RouteNames.register;
      final isOnAuthPage = isOnLogin || isOnRegister;

      if (isOnSplash) {
        return isAuthenticated ? RouteNames.home : RouteNames.login;
      }

      if (!isAuthenticated && !isOnAuthPage) {
        return RouteNames.login;
      }

      if (isAuthenticated && isOnAuthPage) {
        return RouteNames.home;
      }

      return null;
    },

    routes: [
      GoRoute(
        path: RouteNames.splash,
        name: 'splash',
        builder: (context, state) => const SplashScreen(),
      ),

      GoRoute(
        path: RouteNames.login,
        name: 'login',
        builder: (context, state) => const LoginScreen(),
      ),

      GoRoute(
        path: RouteNames.register,
        name: 'register',
        builder: (context, state) => const RegisterScreen(),
      ),

      StatefulShellRoute.indexedStack(
        builder: (context, state, navigationShell) {
          return _AppShell(navigationShell: navigationShell);
        },
        branches: [
          StatefulShellBranch(
            routes: [
              GoRoute(
                path: RouteNames.home,
                name: 'home',
                builder: (context, state) => const HomeScreen(),
              ),
            ],
          ),
          StatefulShellBranch(
            routes: [
              GoRoute(
                path: RouteNames.orphans,
                name: 'orphans',
                builder: (context, state) => const OrphansScreen(),
              ),
            ],
          ),
          StatefulShellBranch(
            routes: [
              GoRoute(
                path: RouteNames.mySponsorships,
                name: 'mySponsorships',
                builder: (context, state) => const MySponsorshipsScreen(),
              ),
            ],
          ),
          StatefulShellBranch(
            routes: [
              GoRoute(
                path: RouteNames.profile,
                name: 'profile',
                builder: (context, state) => const ProfileScreen(),
              ),
            ],
          ),
        ],
      ),

      GoRoute(
        path: RouteNames.orphanDetails,
        name: 'orphanDetails',
        parentNavigatorKey: _rootNavigatorKey,
        builder: (context, state) {
          final orphanId = int.tryParse(state.pathParameters['orphanId'] ?? '') ?? 0;
          return OrphanDetailsScreen(orphanId: orphanId);
        },
      ),

      GoRoute(
        path: RouteNames.sponsorshipRequest,
        name: 'sponsorshipRequest',
        parentNavigatorKey: _rootNavigatorKey,
        builder: (context, state) => SponsorshipRequestScreen(
          orphan: state.extra as OrphanModel?,
        ),
      ),

      GoRoute(
        path: RouteNames.notifications,
        name: 'notifications',
        parentNavigatorKey: _rootNavigatorKey,
        builder: (context, state) => const NotificationsScreen(),
      ),

      GoRoute(
        path: RouteNames.settings,
        name: 'settings',
        parentNavigatorKey: _rootNavigatorKey,
        builder: (context, state) => const SettingsScreen(),
      ),

      GoRoute(
        path: RouteNames.favorites,
        name: 'favorites',
        parentNavigatorKey: _rootNavigatorKey,
        builder: (context, state) => const FavoritesScreen(),
      ),

      GoRoute(
        path: RouteNames.reports,
        name: 'reports',
        parentNavigatorKey: _rootNavigatorKey,
        builder: (context, state) => const SponsorReportsScreen(),
      ),

      GoRoute(
        path: RouteNames.sponsorshipTimeline,
        name: 'sponsorshipTimeline',
        parentNavigatorKey: _rootNavigatorKey,
        builder: (context, state) {
          final sponsorshipId = int.tryParse(state.pathParameters['sponsorshipId'] ?? '') ?? 0;
          return SponsorshipTimelineScreen(sponsorshipId: sponsorshipId);
        },
      ),

      GoRoute(
        path: RouteNames.about,
        name: 'about',
        parentNavigatorKey: _rootNavigatorKey,
        builder: (context, state) => const AboutScreen(),
      ),

      GoRoute(
        path: RouteNames.contact,
        name: 'contact',
        parentNavigatorKey: _rootNavigatorKey,
        builder: (context, state) => const ContactScreen(),
      ),
    ],

    errorBuilder: (context, state) => Scaffold(
      body: Center(
        child: Column(
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            Icon(Icons.error_outline, size: 64, color: Colors.grey[400]),
            const SizedBox(height: 16),
            Text(
              AppStrings.get('page_not_found'),
              style: const TextStyle(fontSize: 18, fontWeight: FontWeight.w600),
            ),
            if (state.error != null) ...[
              const SizedBox(height: 8),
              Text(
                '${state.error}',
                style: TextStyle(fontSize: 14, color: Colors.grey[500]),
                textAlign: TextAlign.center,
              ),
            ],
            const SizedBox(height: 24),
            ElevatedButton(
              onPressed: () => context.go(RouteNames.home),
              child: Text(AppStrings.get('nav_home')),
            ),
          ],
        ),
      ),
    ),
  );
}

final appRouterProvider = Provider<GoRouter>((ref) {
  final router = _createRouter(ref);

  ref.listen<AuthState>(authProvider, (prev, next) {
    if (prev?.status != next.status) {
      ref.read(sessionExpiredProvider.notifier).state = false;
      router.refresh();
    }
  });

  return router;
});

class _AppShell extends StatelessWidget {
  final StatefulNavigationShell navigationShell;

  const _AppShell({required this.navigationShell});

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      body: navigationShell,
      bottomNavigationBar: NavigationBar(
        selectedIndex: navigationShell.currentIndex,
        onDestinationSelected: (index) {
          navigationShell.goBranch(
            index,
            initialLocation: index == navigationShell.currentIndex,
          );
        },
        destinations: [
          NavigationDestination(
            icon: const Icon(Icons.home_outlined),
            selectedIcon: const Icon(Icons.home),
            label: AppStrings.get('nav_home'),
          ),
          NavigationDestination(
            icon: const Icon(Icons.people_outline),
            selectedIcon: const Icon(Icons.people),
            label: AppStrings.get('nav_orphans'),
          ),
          NavigationDestination(
            icon: const Icon(Icons.favorite_outline),
            selectedIcon: const Icon(Icons.favorite),
            label: AppStrings.get('nav_sponsorships'),
          ),
          NavigationDestination(
            icon: const Icon(Icons.person_outline),
            selectedIcon: const Icon(Icons.person),
            label: AppStrings.get('nav_profile'),
          ),
        ],
      ),
    );
  }
}
