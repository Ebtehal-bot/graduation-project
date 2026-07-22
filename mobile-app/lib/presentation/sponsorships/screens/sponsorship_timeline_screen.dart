import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:cached_network_image/cached_network_image.dart';

import '../../../core/localization/app_strings.dart';
import '../../../core/theme/app_colors.dart';
import '../../../data/models/sponsorship_model.dart';
import '../../../presentation/settings/providers/app_settings_provider.dart';
import '../../../shared/widgets/widgets.dart';
import '../providers/sponsorships_provider.dart';
import '../widgets/sponsorship_timeline_widget.dart';

class SponsorshipTimelineScreen extends ConsumerWidget {
  final int sponsorshipId;

  const SponsorshipTimelineScreen({
    super.key,
    required this.sponsorshipId,
  });

  @override
  Widget build(BuildContext context, WidgetRef ref) {
    final sponsorshipAsync = ref.watch(sponsorshipDetailProvider(sponsorshipId));
    final isDark = Theme.of(context).brightness == Brightness.dark;
    final appSettings = ref.watch(appSettingsProvider);

    return Scaffold(
      appBar: AppBar(
        title: Text(AppStrings.get('sponsorship_path')),
      ),
      body: sponsorshipAsync.when(
        loading: () => const Center(child: CircularProgressIndicator()),
        error: (e, _) => Center(
          child: AppErrorState(
            message: e.toString(),
            onRetry: () =>
                ref.refresh(sponsorshipDetailProvider(sponsorshipId)),
          ),
        ),
        data: (sponsorship) => _TimelineContent(
          sponsorship: sponsorship,
          isDark: isDark,
          logoUrl: appSettings.settings?.siteLogo,
        ),
      ),
    );
  }
}

class _TimelineContent extends StatelessWidget {
  final SponsorshipModel sponsorship;
  final bool isDark;
  final String? logoUrl;

  const _TimelineContent({
    required this.sponsorship,
    required this.isDark,
    this.logoUrl,
  });

  @override
  Widget build(BuildContext context) {
    return SingleChildScrollView(
      padding: const EdgeInsets.all(16),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          if (logoUrl != null)
            _buildLogoHeader(logoUrl!, isDark),
          _buildInfoCard(),
          const SizedBox(height: 20),
          _buildRemainingDaysCard(),
          const SizedBox(height: 20),
          Text(
            AppStrings.get('sponsorship_path'),
            style: TextStyle(
              fontSize: 18,
              fontWeight: FontWeight.w600,
              color: isDark ? Colors.white : AppColors.neutral900,
            ),
          ),
          const SizedBox(height: 12),
          SponsorshipTimelineWidget(currentStatus: sponsorship.status),
        ],
      ),
    );
  }

  Widget _buildLogoHeader(String url, bool isDark) {
    return Center(
      child: Padding(
        padding: const EdgeInsets.only(bottom: 16),
        child: SizedBox(
          height: 44,
          child: CachedNetworkImage(
            imageUrl: url,
            fit: BoxFit.contain,
            placeholder: (_, __) => const SizedBox.shrink(),
            errorWidget: (_, __, ___) => const SizedBox.shrink(),
          ),
        ),
      ),
    );
  }

  Widget _buildInfoCard() {
    return Card(
      child: Padding(
        padding: const EdgeInsets.all(20),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Row(
              children: [
                Container(
                  width: 56,
                  height: 56,
                  decoration: BoxDecoration(
                    borderRadius: BorderRadius.circular(14),
                    color: AppColors.primaryLight.withOpacity(0.2),
                  ),
                  child: sponsorship.orphanImage != null
                      ? ClipRRect(
                          borderRadius: BorderRadius.circular(14),
                          child: CachedNetworkImage(
                            imageUrl: sponsorship.orphanImage!,
                            fit: BoxFit.cover,
                            placeholder: (_, __) => _avatarPlaceholder(),
                            errorWidget: (_, __, ___) => _avatarPlaceholder(),
                          ),
                        )
                      : _avatarPlaceholder(),
                ),
                const SizedBox(width: 14),
                Expanded(
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Text(
                        sponsorship.orphanName ?? '---',
                        style: TextStyle(
                          fontSize: 18,
                          fontWeight: FontWeight.w600,
                          color:
                              isDark ? Colors.white : AppColors.neutral900,
                        ),
                      ),
                      const SizedBox(height: 4),
                      _statusBadge(sponsorship.status),
                    ],
                  ),
                ),
              ],
            ),
            const Divider(height: 24),
            _infoRow(AppStrings.get('sponsorship_type'),
                sponsorship.typeLabel),
            _infoRow(AppStrings.get('monthly_amount'),
                '${sponsorship.monthlyAmount.toStringAsFixed(0)} ${AppStrings.get('sar')}'),
            _infoRow(AppStrings.get('status'), sponsorship.statusLabel),
            _infoRow(AppStrings.get('start_date'),
                sponsorship.startDate ?? '---'),
            _infoRow(AppStrings.get('end_date'),
                sponsorship.endDate ?? '---'),
          ],
        ),
      ),
    );
  }

  Widget _buildRemainingDaysCard() {
    final remaining = sponsorship.remainingDays;
    if (remaining == null) return const SizedBox.shrink();

    return Container(
      width: double.infinity,
      padding: const EdgeInsets.all(20),
      decoration: BoxDecoration(
        gradient: LinearGradient(
          colors: remaining > 30
              ? [AppColors.primary, AppColors.primaryDark]
              : [AppColors.warning, AppColors.accentDark],
          begin: Alignment.topLeft,
          end: Alignment.bottomRight,
        ),
        borderRadius: BorderRadius.circular(20),
      ),
      child: Row(
        children: [
          const Icon(Icons.timer_rounded, color: Colors.white, size: 32),
          const SizedBox(width: 16),
          Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Text(
                AppStrings.get('remaining_days'),
                style: const TextStyle(
                  color: Colors.white70,
                  fontSize: 13,
                ),
              ),
              const SizedBox(height: 4),
              Text(
                '$remaining ${AppStrings.get('days')}',
                style: const TextStyle(
                  color: Colors.white,
                  fontSize: 28,
                  fontWeight: FontWeight.bold,
                ),
              ),
            ],
          ),
        ],
      ),
    );
  }

  Widget _avatarPlaceholder() {
    return Container(
      decoration: BoxDecoration(
        color: AppColors.primaryLight.withOpacity(0.2),
        borderRadius: BorderRadius.circular(14),
      ),
      alignment: Alignment.center,
      child: Text(
        (sponsorship.orphanName ?? '?').isNotEmpty
            ? sponsorship.orphanName![0].toUpperCase()
            : '?',
        style: const TextStyle(
          fontSize: 24,
          fontWeight: FontWeight.bold,
          color: AppColors.primary,
        ),
      ),
    );
  }

  Widget _statusBadge(String status) {
    Color color;
    switch (status) {
      case 'active':
        color = AppColors.success;
        break;
      case 'inactive':
      case 'pending':
        color = AppColors.warning;
        break;
      case 'ended':
      case 'completed':
      case 'stopped':
        color = AppColors.info;
        break;
      default:
        color = AppColors.neutral500;
    }
    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 4),
      decoration: BoxDecoration(
        color: color.withOpacity(0.15),
        borderRadius: BorderRadius.circular(10),
      ),
      child: Text(
        sponsorship.statusLabel,
        style: TextStyle(
          fontSize: 12,
          fontWeight: FontWeight.w600,
          color: color,
        ),
      ),
    );
  }

  Widget _infoRow(String label, String value) {
    return Padding(
      padding: const EdgeInsets.only(bottom: 8),
      child: Row(
        children: [
          SizedBox(
            width: 120,
            child: Text(
              label,
              style: TextStyle(
                fontSize: 13,
                color: isDark ? Colors.grey[400] : AppColors.neutral500,
              ),
            ),
          ),
          Expanded(
            child: Text(
              value,
              style: TextStyle(
                fontSize: 14,
                fontWeight: FontWeight.w500,
                color: isDark ? Colors.white : AppColors.neutral900,
              ),
            ),
          ),
        ],
      ),
    );
  }
}
