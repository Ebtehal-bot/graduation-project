import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:go_router/go_router.dart';

import '../../../core/localization/app_strings.dart';
import '../../../core/theme/app_colors.dart';
import '../../../core/theme/app_typography.dart';
import '../../../data/models/dashboard_stats_model.dart';
import '../../../shared/widgets/widgets.dart';
import '../../auth/providers/auth_provider.dart';
import '../../notifications/providers/notifications_provider.dart';
import '../providers/home_provider.dart';

class HomeScreen extends ConsumerStatefulWidget {
  const HomeScreen({super.key});

  @override
  ConsumerState<HomeScreen> createState() => _HomeScreenState();
}

class _HomeScreenState extends ConsumerState<HomeScreen> {
  @override
  void initState() {
    super.initState();
    WidgetsBinding.instance.addPostFrameCallback((_) {
      ref.read(homeProvider.notifier).loadDashboard();
      ref.read(notificationsProvider.notifier).loadUnreadCount();
    });
  }

  @override
  Widget build(BuildContext context) {
    final authState = ref.watch(authProvider);
    final homeState = ref.watch(homeProvider);
    final isDark = Theme.of(context).brightness == Brightness.dark;
    debugPrint('[HomeScreen] === DASHBOARD REBUILT ===');

    return Scaffold(
      body: RefreshIndicator(
        onRefresh: () async {
          await ref.read(homeProvider.notifier).loadDashboard();
          ref.read(notificationsProvider.notifier).loadUnreadCount();
        },
        child: SingleChildScrollView(
          physics: const AlwaysScrollableScrollPhysics(),
          child: Column(
            children: [
              _buildHeader(authState, isDark),
              if (homeState.isLoading && homeState.stats == null)
                _buildSkeletons(isDark)
              else if (homeState.error != null && homeState.stats == null)
                _buildError()
              else
                _buildBody(homeState, isDark),
              const SizedBox(height: 24),
            ],
          ),
        ),
      ),
    );
  }

  Widget _buildHeader(AuthState authState, bool isDark) {
    final displayName = authState.userName ?? AppStrings.get('welcome');
    final initial = displayName.isNotEmpty ? displayName[0].toUpperCase() : '?';
    final greeting = _greeting;
    final unreadCount = ref.watch(unreadCountProvider);

    return Container(
      width: double.infinity,
      padding: EdgeInsets.only(
        top: MediaQuery.of(context).padding.top + 20,
        bottom: 28,
        left: 20,
        right: 20,
      ),
      decoration: const BoxDecoration(
        gradient: AppColors.primaryGradient,
        borderRadius: BorderRadius.only(
          bottomLeft: Radius.circular(32),
          bottomRight: Radius.circular(32),
        ),
      ),
      child: Column(
        children: [
          Row(
            children: [
              CircleAvatar(
                radius: 28,
                backgroundColor: Colors.white.withOpacity(0.2),
                child: Text(
                  initial,
                  style: const TextStyle(
                    fontSize: 24,
                    fontWeight: FontWeight.bold,
                    color: Colors.white,
                  ),
                ),
              ),
              const SizedBox(width: 14),
              Expanded(
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Text(
                      '$greeting،',
                      style: TextStyle(
                        fontSize: 14,
                        color: Colors.white.withOpacity(0.85),
                      ),
                    ),
                    const SizedBox(height: 2),
                    Text(
                      displayName,
                      style: AppTypography.displayMedium.copyWith(
                        color: Colors.white,
                      ),
                    ),
                  ],
                ),
              ),
              if (unreadCount > 0 && _isAdmin(authState))
                Material(
                  color: Colors.white.withOpacity(0.15),
                  borderRadius: BorderRadius.circular(12),
                  child: InkWell(
                    onTap: () => context.push('/notifications'),
                    borderRadius: BorderRadius.circular(12),
                    child: Padding(
                      padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 8),
                      child: Badge(
                        label: Text('$unreadCount'),
                        child: const Icon(
                          Icons.notifications_outlined,
                          color: Colors.white,
                          size: 22,
                        ),
                      ),
                    ),
                  ),
                ),
              const SizedBox(width: 8),
              Material(
                color: Colors.white.withOpacity(0.15),
                borderRadius: BorderRadius.circular(12),
                child: InkWell(
                  onTap: () => context.go('/profile'),
                  borderRadius: BorderRadius.circular(12),
                  child: Padding(
                    padding:
                        const EdgeInsets.symmetric(horizontal: 12, vertical: 8),
                    child: Row(
                      mainAxisSize: MainAxisSize.min,
                      children: [
                        Text(
                          AppStrings.get('profile'),
                          style: TextStyle(
                            fontSize: 12,
                            color: Colors.white.withOpacity(0.9),
                          ),
                        ),
                        const SizedBox(width: 4),
                        Icon(
                          Icons.arrow_forward_ios,
                          size: 12,
                          color: Colors.white.withOpacity(0.7),
                        ),
                      ],
                    ),
                  ),
                ),
              ),
            ],
          ),
          const SizedBox(height: 16),
          Text(
            AppStrings.get('overview'),
            style: TextStyle(
              fontSize: 15,
              color: Colors.white.withOpacity(0.8),
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildBody(HomeState state, bool isDark) {
    final stats = state.stats;
    if (stats == null) return _buildError();

    final authState = ref.read(authProvider);
    final isAdmin = _isAdmin(authState);
    final totalForCard = isAdmin
        ? stats.totalOrphans
        : stats.sponsoredOrphans + stats.unsponsoredOrphans;

    return Padding(
      padding: const EdgeInsets.symmetric(horizontal: 16),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          const SizedBox(height: 8),
          _buildStatsRow(state, isDark, totalForCard),
          const SizedBox(height: 16),
          _buildProgressSection(stats, isDark),
          const SizedBox(height: 24),
          _buildQuickActions(isDark, isAdmin),
          const SizedBox(height: 24),
          if (state.recentActivities.isNotEmpty) ...[
            _buildRecentActivities(state, isDark),
            const SizedBox(height: 24),
          ],
          if (state.recentOrphans.isNotEmpty) ...[
            _buildRecentOrphans(state, isDark),
            const SizedBox(height: 24),
          ],
        ],
      ),
    );
  }

  Widget _buildStatsRow(HomeState state, bool isDark, int totalOrphansValue) {
    final stats = state.stats!;
    return Column(
      children: [
        Row(
          children: [
            Expanded(
              child: AppStatCard(
                icon: Icons.people_alt_rounded,
                label: AppStrings.get('total_orphans'),
                value: _formatNumber(totalOrphansValue),
                color: AppColors.primary,
              ),
            ),
            const SizedBox(width: 12),
            Expanded(
              child: AppStatCard(
                icon: Icons.favorite_rounded,
                label: AppStrings.get('sponsored_orphans'),
                value: _formatNumber(stats.sponsoredOrphans),
                color: AppColors.success,
              ),
            ),
          ],
        ),
        const SizedBox(height: 12),
        Row(
          children: [
            Expanded(
              child: AppStatCard(
                icon: Icons.check_circle_rounded,
                label: AppStrings.get('active_sponsorships'),
                value: _formatNumber(stats.activeSponsorships),
                color: AppColors.primary,
              ),
            ),
            const SizedBox(width: 12),
            Expanded(
              child: AppStatCard(
                icon: Icons.history_rounded,
                label: AppStrings.get('completed_sponsorships'),
                value: _formatNumber(stats.completedSponsorships),
                color: AppColors.info,
              ),
            ),
          ],
        ),
        const SizedBox(height: 12),
        Row(
          children: [
            Expanded(
              child: AppStatCard(
                icon: Icons.warning_rounded,
                label: AppStrings.get('unsponsored_orphans'),
                value: _formatNumber(stats.unsponsoredOrphans),
                color: AppColors.error,
              ),
            ),
          ],
        ),
      ],
    );
  }

  Widget _buildProgressSection(DashboardStatsModel stats, bool isDark) {
    final coverage = stats.totalOrphans > 0
        ? stats.sponsoredOrphans / stats.totalOrphans
        : 0.0;

    return Container(
      width: double.infinity,
      padding: const EdgeInsets.all(20),
      decoration: BoxDecoration(
        gradient: LinearGradient(
          colors: [
            isDark ? AppColors.darkCard : Colors.white,
            isDark ? AppColors.darkSurfaceVariant : AppColors.neutral50,
          ],
          begin: Alignment.topLeft,
          end: Alignment.bottomRight,
        ),
        borderRadius: BorderRadius.circular(20),
        border: Border.all(
          color: isDark ? Colors.grey[800]! : AppColors.neutral200,
        ),
      ),
      child: Column(
        children: [
          Row(
            children: [
              Expanded(
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Text(
                      AppStrings.get('coverage_rate'),
                      style: TextStyle(
                        fontSize: 18,
                        fontWeight: FontWeight.w600,
                        color: isDark ? Colors.white : AppColors.neutral900,
                      ),
                    ),
                    const SizedBox(height: 8),
                    Text(
                      '${stats.sponsoredOrphans} ${AppStrings.get('out_of')} ${stats.totalOrphans}',
                      style: TextStyle(
                        fontSize: 14,
                        color: isDark ? Colors.grey[400] : AppColors.neutral600,
                      ),
                    ),
                    const SizedBox(height: 16),
                    TweenAnimationBuilder<double>(
                      tween: Tween(begin: 0, end: coverage),
                      duration: const Duration(milliseconds: 1200),
                      curve: Curves.easeOutCubic,
                      builder: (context, value, _) {
                        return Column(
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: [
                            ClipRRect(
                              borderRadius: BorderRadius.circular(8),
                              child: LinearProgressIndicator(
                                value: value,
                                minHeight: 10,
                                backgroundColor: isDark
                                    ? Colors.grey[800]
                                    : AppColors.neutral200,
                                valueColor:
                                    const AlwaysStoppedAnimation<Color>(
                                        AppColors.primary),
                              ),
                            ),
                            const SizedBox(height: 8),
                            Text(
                              '${(value * 100).toInt()}%',
                              style: const TextStyle(
                                fontSize: 28,
                                fontWeight: FontWeight.bold,
                                color: AppColors.primary,
                              ),
                            ),
                          ],
                        );
                      },
                    ),
                  ],
                ),
              ),
              const SizedBox(width: 20),
              SizedBox(
                width: 100,
                height: 100,
                child: TweenAnimationBuilder<double>(
                  tween: Tween(begin: 0, end: coverage),
                  duration: const Duration(milliseconds: 1200),
                  curve: Curves.easeOutCubic,
                  builder: (context, value, _) {
                    return Stack(
                      alignment: Alignment.center,
                      children: [
                        SizedBox(
                          width: 100,
                          height: 100,
                          child: CircularProgressIndicator(
                            value: value,
                            strokeWidth: 8,
                            strokeCap: StrokeCap.round,
                            backgroundColor: isDark
                                ? Colors.grey[800]!
                                : AppColors.neutral200,
                            valueColor:
                                const AlwaysStoppedAnimation<Color>(
                                    AppColors.primary),
                          ),
                        ),
                        Text(
                          '${(value * 100).toInt()}%',
                          style: TextStyle(
                            fontSize: 22,
                            fontWeight: FontWeight.bold,
                            color: isDark
                                ? Colors.white
                                : AppColors.neutral900,
                          ),
                        ),
                      ],
                    );
                  },
                ),
              ),
            ],
          ),
          const SizedBox(height: 16),
          const Divider(),
          const SizedBox(height: 16),
          Row(
            children: [
              _miniStat(
                AppStrings.get('total_sponsors'),
                _formatNumber(stats.totalSponsors),
                AppColors.primary,
                isDark,
              ),
            ],
          ),
        ],
      ),
    );
  }

  Widget _miniStat(String label, String value, Color color, bool isDark) {
    return Expanded(
      child: Row(
        children: [
          Container(
            width: 8,
            height: 8,
            decoration: BoxDecoration(color: color, shape: BoxShape.circle),
          ),
          const SizedBox(width: 8),
          Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Text(
                value,
                style: TextStyle(
                  fontSize: 16,
                  fontWeight: FontWeight.bold,
                  color: isDark ? Colors.white : AppColors.neutral900,
                ),
              ),
              Text(
                label,
                style: TextStyle(
                  fontSize: 11,
                  color: isDark ? Colors.grey[500] : AppColors.neutral500,
                ),
              ),
            ],
          ),
        ],
      ),
    );
  }

  Widget _buildQuickActions(bool isDark, bool isAdmin) {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Text(
          AppStrings.get('quick_actions'),
          style: TextStyle(
            fontSize: 18,
            fontWeight: FontWeight.w600,
            color: isDark ? Colors.white : AppColors.neutral900,
          ),
        ),
        const SizedBox(height: 12),
        Row(
          children: [
            Expanded(
              child: AppQuickAction(
                icon: Icons.people_rounded,
                label: AppStrings.get('nav_orphans'),
                color: AppColors.primary,
                onTap: () => context.go('/orphans'),
              ),
            ),
            const SizedBox(width: 12),
            Expanded(
              child: AppQuickAction(
                icon: Icons.favorite_rounded,
                label: AppStrings.get('nav_sponsorships'),
                color: AppColors.accent,
                onTap: () => context.go('/my-sponsorships'),
              ),
            ),
          ],
        ),
        const SizedBox(height: 12),
        Row(
          children: [
            Expanded(
              child: AppQuickAction(
                icon: Icons.bar_chart_rounded,
                label: AppStrings.get('reports'),
                color: AppColors.success,
                onTap: () => context.push('/reports'),
              ),
            ),
          ],
        ),
        if (isAdmin) ...[
          const SizedBox(height: 12),
          Row(
            children: [
              Expanded(
                child: AppQuickAction(
                  icon: Icons.notifications_rounded,
                  label: AppStrings.get('notifications'),
                  color: const Color(0xFF7C4DFF),
                  onTap: () => context.push('/notifications'),
                ),
              ),
              const SizedBox(width: 12),
              Expanded(
                child: AppQuickAction(
                  icon: Icons.person_rounded,
                  label: AppStrings.get('profile'),
                  color: AppColors.secondary,
                  onTap: () => context.go('/profile'),
                ),
              ),
            ],
          ),
        ] else ...[
          const SizedBox(height: 12),
          Row(
            children: [
              Expanded(
                child: AppQuickAction(
                  icon: Icons.person_rounded,
                  label: AppStrings.get('profile'),
                  color: AppColors.secondary,
                  onTap: () => context.go('/profile'),
                ),
              ),
            ],
          ),
        ],
      ],
    );
  }

  Widget _buildRecentActivities(
      HomeState state, bool isDark) {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Text(
          AppStrings.get('recent_activity'),
          style: TextStyle(
            fontSize: 18,
            fontWeight: FontWeight.w600,
            color: isDark ? Colors.white : AppColors.neutral900,
          ),
        ),
        const SizedBox(height: 12),
        ...state.recentActivities.take(5).map((activity) {
          return Container(
            width: double.infinity,
            margin: const EdgeInsets.only(bottom: 8),
            padding:
                const EdgeInsets.symmetric(horizontal: 14, vertical: 12),
            decoration: BoxDecoration(
              color: isDark ? AppColors.darkCard : Colors.white,
              borderRadius: BorderRadius.circular(16),
              border: Border.all(
                color: isDark ? Colors.grey[800]! : AppColors.neutral200,
              ),
            ),
            child: Row(
              children: [
                Container(
                  padding: const EdgeInsets.all(10),
                  decoration: BoxDecoration(
                    color: AppColors.primary.withOpacity(0.1),
                    shape: BoxShape.circle,
                  ),
                  child: const Icon(
                    Icons.favorite_rounded,
                    color: AppColors.primary,
                    size: 20,
                  ),
                ),
                const SizedBox(width: 12),
                Expanded(
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Text(
                        activity['orphan_name'] as String? ?? '',
                        style: TextStyle(
                          fontSize: 14,
                          fontWeight: FontWeight.w500,
                          color: isDark
                              ? Colors.white
                              : AppColors.neutral900,
                        ),
                      ),
                      const SizedBox(height: 2),
                      Text(
                        activity['action'] as String? ?? '',
                        style: TextStyle(
                          fontSize: 12,
                          color: isDark
                              ? Colors.grey[500]
                              : AppColors.neutral500,
                        ),
                      ),
                    ],
                  ),
                ),
              ],
            ),
          );
        }),
      ],
    );
  }

  Widget _buildRecentOrphans(HomeState state, bool isDark) {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Text(
          AppStrings.get('recent_orphans'),
          style: TextStyle(
            fontSize: 18,
            fontWeight: FontWeight.w600,
            color: isDark ? Colors.white : AppColors.neutral900,
          ),
        ),
        const SizedBox(height: 12),
        ...state.recentOrphans.take(5).map((orphan) {
          return Container(
            width: double.infinity,
            margin: const EdgeInsets.only(bottom: 8),
            padding:
                const EdgeInsets.symmetric(horizontal: 14, vertical: 12),
            decoration: BoxDecoration(
              color: isDark ? AppColors.darkCard : Colors.white,
              borderRadius: BorderRadius.circular(16),
              border: Border.all(
                color: isDark ? Colors.grey[800]! : AppColors.neutral200,
              ),
            ),
            child: Row(
              children: [
                Container(
                  padding: const EdgeInsets.all(10),
                  decoration: BoxDecoration(
                    color: AppColors.primary.withOpacity(0.1),
                    shape: BoxShape.circle,
                  ),
                  child: const Icon(
                    Icons.child_care_rounded,
                    color: AppColors.primary,
                    size: 20,
                  ),
                ),
                const SizedBox(width: 12),
                Expanded(
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Text(
                        orphan.name,
                        style: TextStyle(
                          fontSize: 14,
                          fontWeight: FontWeight.w500,
                          color: isDark
                              ? Colors.white
                              : AppColors.neutral900,
                        ),
                      ),
                      const SizedBox(height: 2),
                      Text(
                        orphan.isSponsored
                            ? AppStrings.get('sponsored')
                            : AppStrings.get('needs_sponsorship'),
                        style: TextStyle(
                          fontSize: 12,
                          color: orphan.isSponsored
                              ? AppColors.success
                              : AppColors.warning,
                        ),
                      ),
                    ],
                  ),
                ),
              ],
            ),
          );
        }),
      ],
    );
  }

  bool _isAdmin(AuthState authState) {
    return authState.user?.role?.toLowerCase() == 'admin';
  }

  Widget _buildSkeletons(bool isDark) {
    return Padding(
      padding: const EdgeInsets.all(16),
      child: Column(
        children: [
          const Row(
            children: [
              Expanded(child: ShimmerStatCard()),
              SizedBox(width: 12),
              Expanded(child: ShimmerStatCard()),
            ],
          ),
          const SizedBox(height: 12),
          const Row(
            children: [
              Expanded(child: ShimmerStatCard()),
              SizedBox(width: 12),
              Expanded(child: ShimmerStatCard()),
            ],
          ),
          const SizedBox(height: 12),
          const Row(
            children: [
              Expanded(
                child: ShimmerStatCard(),
              ),
            ],
          ),
          const SizedBox(height: 20),
          ShimmerLoading(
            child: Container(
              height: 180,
              decoration: BoxDecoration(
                color: isDark ? Colors.grey[800] : Colors.grey[200],
                borderRadius: BorderRadius.circular(20),
              ),
            ),
          ),
          const SizedBox(height: 24),
          Row(
            children: [
              Expanded(child: _shimmerQuickAction(isDark)),
              const SizedBox(width: 12),
              Expanded(child: _shimmerQuickAction(isDark)),
            ],
          ),
          const SizedBox(height: 12),
          Row(
            children: [
              Expanded(child: _shimmerQuickAction(isDark)),
            ],
          ),
          const SizedBox(height: 12),
          Row(
            children: [
              Expanded(child: _shimmerQuickAction(isDark)),
              const SizedBox(width: 12),
              Expanded(child: _shimmerQuickAction(isDark)),
            ],
          ),
        ],
      ),
    );
  }

  Widget _shimmerQuickAction(bool isDark) {
    return ShimmerLoading(
      child: Container(
        height: 100,
        decoration: BoxDecoration(
          color: isDark ? Colors.grey[800] : Colors.white,
          borderRadius: BorderRadius.circular(20),
        ),
      ),
    );
  }

  Widget _buildError() {
    return Padding(
      padding: const EdgeInsets.only(top: 24),
      child: AppErrorState(
        message: ref.watch(homeProvider).error,
        onRetry: () => ref.read(homeProvider.notifier).loadDashboard(),
      ),
    );
  }

  String get _greeting {
    final hour = DateTime.now().hour;
    if (hour < 12) return AppStrings.get('good_morning');
    if (hour < 17) return AppStrings.get('good_evening');
    return AppStrings.get('good_evening');
  }

  String _formatNumber(int n) {
    if (n >= 1000000) return '${(n / 1000000).toStringAsFixed(1)}M';
    if (n >= 1000) return '${(n / 1000).toStringAsFixed(1)}K';
    return n.toString();
  }
}
