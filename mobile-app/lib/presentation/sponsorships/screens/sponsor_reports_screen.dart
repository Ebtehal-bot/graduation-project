import 'package:cached_network_image/cached_network_image.dart';
import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';

import '../../../core/localization/app_strings.dart';
import '../../../core/theme/app_colors.dart';
import '../../../data/repositories/sponsorship_repository.dart';
import '../../../presentation/settings/providers/app_settings_provider.dart';
import '../../../shared/widgets/widgets.dart';

class SponsorReportsScreen extends ConsumerStatefulWidget {
  const SponsorReportsScreen({super.key});

  @override
  ConsumerState<SponsorReportsScreen> createState() =>
      _SponsorReportsScreenState();
}

class _SponsorReportsScreenState extends ConsumerState<SponsorReportsScreen> {
  Map<String, dynamic>? _reportData;
  bool _isLoading = true;
  String? _error;

  @override
  void initState() {
    super.initState();
    _loadReports();
  }

  Future<void> _loadReports() async {
    setState(() {
      _isLoading = true;
      _error = null;
    });
    try {
      final repo = ref.read(sponsorshipRepositoryProvider);
      final data = await repo.getSponsorReports();
      if (mounted) {
        setState(() {
          _reportData = data;
          _isLoading = false;
        });
      }
    } catch (e) {
      if (mounted) {
        setState(() {
          _error = e.toString();
          _isLoading = false;
        });
      }
    }
  }

  @override
  Widget build(BuildContext context) {
    final isDark = Theme.of(context).brightness == Brightness.dark;
    final appSettings = ref.watch(appSettingsProvider);

    return Scaffold(
      appBar: AppBar(
        title: Text(AppStrings.get('reports')),
      ),
      body: RefreshIndicator(
        onRefresh: _loadReports,
        child: _isLoading
            ? _buildSkeletons(isDark)
            : _error != null
                ? Center(
                    child: AppErrorState(
                      message: _error,
                      onRetry: _loadReports,
                    ),
                  )
                : SingleChildScrollView(
                    physics: const AlwaysScrollableScrollPhysics(),
                    padding: const EdgeInsets.all(16),
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        if (appSettings.settings?.siteLogo != null)
                          _buildLogoHeader(appSettings.settings!.siteLogo!, isDark),
                        _buildStatsGrid(isDark),
                        if (_reportData != null &&
                            _reportData!['recent_sponsorships'] is List &&
                            (_reportData!['recent_sponsorships'] as List)
                                .isNotEmpty) ...[
                          const SizedBox(height: 24),
                          _buildRecentList(isDark),
                        ],
                      ],
                    ),
                  ),
      ),
    );
  }

  Widget _buildLogoHeader(String logoUrl, bool isDark) {
    return Center(
      child: Padding(
        padding: const EdgeInsets.only(bottom: 20),
        child: SizedBox(
          height: 48,
          child: CachedNetworkImage(
            imageUrl: logoUrl,
            fit: BoxFit.contain,
            placeholder: (_, __) => const SizedBox.shrink(),
            errorWidget: (_, __, ___) => const SizedBox.shrink(),
          ),
        ),
      ),
    );
  }

  Widget _buildStatsGrid(bool isDark) {
    final data = _reportData;
    if (data == null) return const SizedBox.shrink();

    final totalSponsorships = data['total_sponsorships'];
    final activeSponsorships = data['active_sponsorships'];
    final completedSponsorships = data['completed_sponsorships'];
    final totalDonated = data['total_donated'];
    final averageDuration = data['average_duration'];
    final lastActivity = data['last_activity'];

    return Column(
      children: [
        Row(
          children: [
            if (totalSponsorships != null)
              Expanded(
                child: _statCard(
                  Icons.favorite_rounded,
                  '$totalSponsorships',
                  AppStrings.get('total_sponsorships'),
                  AppColors.primary,
                  isDark,
                ),
              ),
            if (totalSponsorships != null && activeSponsorships != null)
              const SizedBox(width: 12),
            if (activeSponsorships != null)
              Expanded(
                child: _statCard(
                  Icons.check_circle_rounded,
                  '$activeSponsorships',
                  AppStrings.get('active_sponsorships'),
                  AppColors.success,
                  isDark,
                ),
              ),
          ],
        ),
        if (completedSponsorships != null || totalDonated != null) ...[
          const SizedBox(height: 12),
          Row(
            children: [
              if (completedSponsorships != null)
                Expanded(
                  child: _statCard(
                    Icons.history_rounded,
                    '$completedSponsorships',
                    AppStrings.get('completed_sponsorships'),
                    AppColors.info,
                    isDark,
                  ),
                ),
              if (completedSponsorships != null && totalDonated != null)
                const SizedBox(width: 12),
              if (totalDonated != null)
                Expanded(
                  child: _statCard(
                    Icons.monetization_on_rounded,
                    '$totalDonated',
                    AppStrings.get('total_donated'),
                    AppColors.accent,
                    isDark,
                  ),
                ),
            ],
          ),
        ],
        if (averageDuration != null || lastActivity != null) ...[
          const SizedBox(height: 12),
          Row(
            children: [
              if (averageDuration != null)
                Expanded(
                  child: _statCard(
                    Icons.timeline_rounded,
                    '$averageDuration ${AppStrings.get('days')}',
                    AppStrings.get('average_duration'),
                    AppColors.warning,
                    isDark,
                  ),
                ),
              if (averageDuration != null && lastActivity != null)
                const SizedBox(width: 12),
              if (lastActivity != null)
                Expanded(
                  child: _statCard(
                    Icons.access_time_rounded,
                    _formatDate(lastActivity as String),
                    AppStrings.get('last_activity'),
                    AppColors.secondary,
                    isDark,
                  ),
                ),
            ],
          ),
        ],
      ],
    );
  }

  Widget _buildRecentList(bool isDark) {
    final raw = _reportData!['recent_sponsorships'];
    final list = (raw is List) ? raw : <dynamic>[];
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        AppSectionHeader(
          title: AppStrings.get('recent_sponsorships'),
        ),
        const SizedBox(height: 12),
        ...list
            .whereType<Map<String, dynamic>>()
            .map((item) {
          final sp = item;
          return Container(
            margin: const EdgeInsets.only(bottom: 8),
            padding: const EdgeInsets.all(14),
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
                  width: 48,
                  height: 48,
                  decoration: BoxDecoration(
                    borderRadius: BorderRadius.circular(12),
                    color: AppColors.primaryLight.withOpacity(0.2),
                  ),
                  alignment: Alignment.center,
                  child: Text(
                    (sp['orphan_name'] as String? ?? '?')[0].toUpperCase(),
                    style: const TextStyle(
                      fontSize: 20,
                      fontWeight: FontWeight.bold,
                      color: AppColors.primary,
                    ),
                  ),
                ),
                const SizedBox(width: 12),
                Expanded(
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Text(
                        sp['orphan_name'] as String? ?? '---',
                        style: TextStyle(
                          fontSize: 14,
                          fontWeight: FontWeight.w500,
                          color: isDark ? Colors.white : AppColors.neutral900,
                        ),
                      ),
                      const SizedBox(height: 4),
                      Row(
                        children: [
                          _statusBadge(sp['status'] as String? ?? ''),
                          const SizedBox(width: 8),
                          Text(
                            '${sp['amount'] ?? '0'} ${AppStrings.get('sar')}',
                            style: const TextStyle(
                              fontSize: 13,
                              fontWeight: FontWeight.w600,
                              color: AppColors.primary,
                            ),
                          ),
                        ],
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

  Widget _statusBadge(String status) {
    final color = _statusColor(status);
    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 2),
      decoration: BoxDecoration(
        color: color.withOpacity(0.15),
        borderRadius: BorderRadius.circular(8),
      ),
      child: Text(
        status,
        style: TextStyle(
          fontSize: 11,
          fontWeight: FontWeight.w600,
          color: color,
        ),
      ),
    );
  }

  Widget _statCard(IconData icon, String value, String label, Color color,
      bool isDark) {
    return Container(
      padding: const EdgeInsets.all(16),
      decoration: BoxDecoration(
        gradient: LinearGradient(
          colors: [
            color.withOpacity(isDark ? 0.2 : 0.1),
            color.withOpacity(isDark ? 0.1 : 0.05),
          ],
          begin: Alignment.topLeft,
          end: Alignment.bottomRight,
        ),
        borderRadius: BorderRadius.circular(20),
        border: Border.all(
          color: color.withOpacity(isDark ? 0.3 : 0.2),
        ),
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Icon(icon, color: color, size: 22),
          const SizedBox(height: 10),
          Text(
            value,
            style: TextStyle(
              fontSize: 22,
              fontWeight: FontWeight.bold,
              color: isDark ? Colors.white : color,
            ),
          ),
          const SizedBox(height: 4),
          Text(
            label,
            style: TextStyle(
              fontSize: 11,
              color: isDark ? Colors.grey[400] : AppColors.neutral600,
            ),
          ),
        ],
      ),
    );
  }

  Color _statusColor(String status) {
    switch (status) {
      case 'active':
        return AppColors.success;
      case 'inactive':
      case 'pending':
        return AppColors.warning;
      case 'ended':
      case 'completed':
      case 'stopped':
        return AppColors.info;
      default:
        return AppColors.neutral500;
    }
  }

  String _formatDate(String dateStr) {
    final dt = DateTime.tryParse(dateStr);
    if (dt == null) return dateStr;
    return '${dt.year}/${dt.month}/${dt.day}';
  }

  Widget _buildSkeletons(bool isDark) {
    return const Padding(
      padding: EdgeInsets.all(16),
      child: Column(
        children: [
          Row(
            children: [
              Expanded(child: ShimmerStatCard()),
              SizedBox(width: 12),
              Expanded(child: ShimmerStatCard()),
            ],
          ),
          SizedBox(height: 12),
          Row(
            children: [
              Expanded(child: ShimmerStatCard()),
              SizedBox(width: 12),
              Expanded(child: ShimmerStatCard()),
            ],
          ),
          SizedBox(height: 12),
          Row(
            children: [
              Expanded(child: ShimmerStatCard()),
              SizedBox(width: 12),
              Expanded(child: ShimmerStatCard()),
            ],
          ),
        ],
      ),
    );
  }
}
