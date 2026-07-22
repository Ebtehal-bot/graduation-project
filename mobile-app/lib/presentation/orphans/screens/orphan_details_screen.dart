import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:go_router/go_router.dart';
import 'package:cached_network_image/cached_network_image.dart';

import '../../../core/localization/app_strings.dart';
import '../../../core/theme/app_colors.dart';
import '../../../data/models/orphan_model.dart';
import '../../../presentation/settings/providers/app_settings_provider.dart';
import '../../../shared/widgets/widgets.dart';
import '../../favorites/providers/favorites_provider.dart';
import '../providers/orphans_provider.dart';

class OrphanDetailsScreen extends ConsumerWidget {
  final int orphanId;

  const OrphanDetailsScreen({super.key, required this.orphanId});

  @override
  Widget build(BuildContext context, WidgetRef ref) {
    final orphanAsync = ref.watch(orphanDetailsProvider(orphanId));
    final favorites = ref.watch(favoritesProvider);
    final isDark = Theme.of(context).brightness == Brightness.dark;
    final appSettings = ref.watch(appSettingsProvider);

    return orphanAsync.when(
      loading: () => Scaffold(
        appBar: AppBar(),
        body: Padding(
          padding: const EdgeInsets.all(16),
          child: Column(
            children: [
              const ShimmerPlaceholder(height: 250, borderRadius: 20),
              const SizedBox(height: 16),
              ...List.generate(5, (_) => const Padding(
                padding: EdgeInsets.only(bottom: 12),
                child: ShimmerCard(),
              )),
            ],
          ),
        ),
      ),
      error: (e, _) => Scaffold(
        appBar: AppBar(),
        body: Center(
          child: AppErrorState(
            message: e.toString(),
            onRetry: () => ref.refresh(orphanDetailsProvider(orphanId)),
          ),
        ),
      ),
      data: (orphan) => _OrphanDetailContent(
        orphan: orphan,
        isFavorite: favorites.contains(orphan.id),
        isDark: isDark,
        logoUrl: appSettings.settings?.siteLogo,
      ),
    );
  }
}

class _OrphanDetailContent extends StatelessWidget {
  final OrphanModel orphan;
  final bool isFavorite;
  final bool isDark;
  final String? logoUrl;

  const _OrphanDetailContent({
    required this.orphan,
    required this.isFavorite,
    required this.isDark,
    this.logoUrl,
  });

  @override
  Widget build(BuildContext context) {
    final sponsorshipActive = orphan.sponsorship != null &&
        ((orphan.sponsorship!['sponsorship_status'] ??
                    orphan.sponsorship!['status'] ??
                    '')
                .toString()
                .toLowerCase() ==
            'active');
    return Scaffold(
      body: CustomScrollView(
        slivers: [
          _buildSliverAppBar(context),
          SliverToBoxAdapter(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                if (logoUrl != null)
                  _buildLogoHeader(logoUrl!, isDark),
                _buildInfoSection(context),
                const SizedBox(height: 16),
                if (orphan.educationStatus != null ||
                    orphan.healthStatus != null)
                  _buildEducationHealth(context),
                const SizedBox(height: 16),
                if (orphan.motherName != null ||
                    orphan.guardianName != null)
                  _buildFamilySection(context),
                const SizedBox(height: 16),
                if (sponsorshipActive)
                  _buildSponsorshipInfo(context)
                else
                  _buildSponsorshipCTA(context),
                const SizedBox(height: 24),
              ],
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildSliverAppBar(BuildContext context) {
    return SliverAppBar(
      expandedHeight: 260,
      pinned: true,
      flexibleSpace: FlexibleSpaceBar(
        background: Hero(
          tag: 'orphan_${orphan.id}',
          child: Stack(
            fit: StackFit.expand,
            children: [
              if (orphan.photo != null)
                CachedNetworkImage(
                  imageUrl: orphan.photo!,
                  fit: BoxFit.cover,
                  placeholder: (_, __) => _gradientHeader(),
                  errorWidget: (_, __, ___) => _gradientHeader(),
                )
              else
                _gradientHeader(),
              Container(
                decoration: BoxDecoration(
                  gradient: LinearGradient(
                    begin: Alignment.topCenter,
                    end: Alignment.bottomCenter,
                    colors: [
                      Colors.transparent,
                      Colors.black.withOpacity(0.7),
                    ],
                  ),
                ),
              ),
              Positioned(
                bottom: 20,
                left: 20,
                right: 20,
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Text(
                      orphan.name,
                      style: const TextStyle(
                        fontSize: 28,
                        fontWeight: FontWeight.bold,
                        color: Colors.white,
                      ),
                    ),
                    const SizedBox(height: 6),
                    Wrap(
                      spacing: 12,
                      runSpacing: 4,
                      children: [
                        _infoBadge(Icons.person_outline, orphan.genderLabel),
                        if (orphan.age != null)
                          _infoBadge(Icons.cake_outlined,
                              '${orphan.age} ${AppStrings.get('years_old')}'),
                        _buildStatusBadge(),
                      ],
                    ),
                  ],
                ),
              ),
            ],
          ),
        ),
      ),
    );
  }

  Widget _gradientHeader() {
    return Container(
      decoration: const BoxDecoration(
        gradient: AppColors.primaryGradient,
      ),
      alignment: Alignment.center,
      child: Text(
        orphan.name.isNotEmpty ? orphan.name[0].toUpperCase() : '?',
        style: const TextStyle(
          fontSize: 72,
          fontWeight: FontWeight.bold,
          color: Colors.white38,
        ),
      ),
    );
  }

  Widget _infoBadge(IconData icon, String label) {
    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 5),
      decoration: BoxDecoration(
        color: Colors.white.withOpacity(0.2),
        borderRadius: BorderRadius.circular(12),
      ),
      child: Row(
        mainAxisSize: MainAxisSize.min,
        children: [
          Icon(icon, size: 14, color: Colors.white),
          const SizedBox(width: 4),
          Text(
            label,
            style: const TextStyle(
              fontSize: 12,
              color: Colors.white,
              fontWeight: FontWeight.w500,
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildStatusBadge() {
    final sponsorship = orphan.sponsorship;
    String label;
    Color bgColor;

    if (sponsorship == null) {
      label = AppStrings.get('needs_sponsorship');
      bgColor = AppColors.warning.withOpacity(0.8);
    } else {
      final status = (sponsorship['sponsorship_status'] ??
              sponsorship['status'] ??
              '')
          .toString()
          .toLowerCase();
      if (status == 'active') {
        label = AppStrings.get('sponsored_currently');
        bgColor = AppColors.success.withOpacity(0.8);
      } else {
        label = AppStrings.get('sponsorship_stopped');
        bgColor = AppColors.error.withOpacity(0.8);
      }
    }

    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 5),
      decoration: BoxDecoration(
        color: bgColor,
        borderRadius: BorderRadius.circular(12),
      ),
      child: Text(
        label,
        style: const TextStyle(
          fontSize: 12,
          fontWeight: FontWeight.w600,
          color: Colors.white,
        ),
      ),
    );
  }

  Widget _buildLogoHeader(String url, bool isDark) {
    return Center(
      child: Padding(
        padding: const EdgeInsets.only(bottom: 16, top: 8),
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

  Widget _buildInfoSection(BuildContext context) {
    return Padding(
      padding: const EdgeInsets.symmetric(horizontal: 16),
      child: Card(
        child: Padding(
          padding: const EdgeInsets.all(20),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Row(
                children: [
                  const Icon(Icons.info_outline, color: AppColors.primary, size: 20),
                  const SizedBox(width: 8),
                  Text(
                    AppStrings.get('personal_info'),
                    style: TextStyle(
                      fontSize: 18,
                      fontWeight: FontWeight.w600,
                      color: isDark ? Colors.white : AppColors.neutral900,
                    ),
                  ),
                ],
              ),
              const Divider(height: 24),
              _infoRow(AppStrings.get('file_number'), orphan.fileNumber ?? '---'),
              _infoRow(AppStrings.get('gender'), orphan.genderLabel),
              _infoRow(AppStrings.get('age'), orphan.age != null ? '${orphan.age} ${AppStrings.get('years_old')}' : '---'),
              if (orphan.birthDate != null)
                _infoRow(AppStrings.get('birth_date'), orphan.birthDate!),
              if (orphan.religion != null)
                _infoRow(AppStrings.get('religion'), orphan.religion!),
              if (orphan.nationality != null)
                _infoRow(AppStrings.get('nationality'), orphan.nationality!),
              if (orphan.branchName != null)
                _infoRow(AppStrings.get('branch'), orphan.branchName!),
              if (orphan.addressGov != null)
                _infoRow(AppStrings.get('address'), '${orphan.addressGov}${orphan.addressDist != null ? ' - ${orphan.addressDist}' : ''}'),
            ],
          ),
        ),
      ),
    );
  }

  Widget _buildEducationHealth(BuildContext context) {
    return Padding(
      padding: const EdgeInsets.symmetric(horizontal: 16),
      child: Card(
        child: Padding(
          padding: const EdgeInsets.all(20),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Row(
                children: [
                  const Icon(Icons.school_outlined, color: AppColors.primary, size: 20),
                  const SizedBox(width: 8),
                  Text(
                    AppStrings.get('education_health'),
                    style: TextStyle(
                      fontSize: 18,
                      fontWeight: FontWeight.w600,
                      color: isDark ? Colors.white : AppColors.neutral900,
                    ),
                  ),
                ],
              ),
              const Divider(height: 24),
              if (orphan.educationStatus != null)
                _infoRow(AppStrings.get('education_status'), orphan.educationStatus!),
              if (orphan.schoolName != null)
                _infoRow(AppStrings.get('school'), orphan.schoolName!),
              if (orphan.academicLevel != null)
                _infoRow(AppStrings.get('academic_level'), orphan.academicLevel!),
              if (orphan.healthStatus != null)
                _infoRow(AppStrings.get('health_status'), orphan.healthStatus!),
              if (orphan.talents != null)
                _infoRow(AppStrings.get('talents'), orphan.talents!),
              if (orphan.quranMemorization != null)
                _infoRow(AppStrings.get('quran_memorization'), orphan.quranMemorization!),
            ],
          ),
        ),
      ),
    );
  }

  Widget _buildFamilySection(BuildContext context) {
    return Padding(
      padding: const EdgeInsets.symmetric(horizontal: 16),
      child: Card(
        child: Padding(
          padding: const EdgeInsets.all(20),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Row(
                children: [
                  const Icon(Icons.family_restroom, color: AppColors.primary, size: 20),
                  const SizedBox(width: 8),
                  Text(
                    AppStrings.get('family_info'),
                    style: TextStyle(
                      fontSize: 18,
                      fontWeight: FontWeight.w600,
                      color: isDark ? Colors.white : AppColors.neutral900,
                    ),
                  ),
                ],
              ),
              const Divider(height: 24),
              if (orphan.fatherDeathCause != null)
                _infoRow(AppStrings.get('father_death_cause'), orphan.fatherDeathCause!),
              if (orphan.motherName != null)
                _infoRow(AppStrings.get('mother_name'), orphan.motherName!),
              if (orphan.motherStatus != null)
                _infoRow(AppStrings.get('mother_status'), orphan.motherStatus!),
              if (orphan.guardianName != null)
                _infoRow(AppStrings.get('guardian_name'), orphan.guardianName!),
              if (orphan.guardianRelation != null)
                _infoRow(AppStrings.get('guardian_relation'), orphan.guardianRelation!),
              if (orphan.guardianPhone != null)
                _infoRow(AppStrings.get('guardian_phone'), orphan.guardianPhone!),
            ],
          ),
        ),
      ),
    );
  }

  Widget _buildSponsorshipInfo(BuildContext context) {
    final sp = orphan.sponsorship!;
    return Padding(
      padding: const EdgeInsets.symmetric(horizontal: 16),
      child: Card(
        child: Padding(
          padding: const EdgeInsets.all(20),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Row(
                children: [
                  const Icon(Icons.favorite_rounded, color: AppColors.accent, size: 20),
                  const SizedBox(width: 8),
                  Text(
                    AppStrings.get('sponsorship_details'),
                    style: TextStyle(
                      fontSize: 18,
                      fontWeight: FontWeight.w600,
                      color: isDark ? Colors.white : AppColors.neutral900,
                    ),
                  ),
                ],
              ),
              const Divider(height: 24),
              _infoRow(AppStrings.get('sponsor_name'), (sp['sponsor_name'] ?? sp['sponsor'] ?? '---').toString()),
              _infoRow(AppStrings.get('monthly_amount'), '${sp['monthly_amount'] ?? sp['amount'] ?? '0'} ${AppStrings.get('sar')}'),
              _infoRow(AppStrings.get('sponsorship_type'), (sp['sponsorship_type'] ?? sp['type'] ?? '---').toString()),
              _infoRow(AppStrings.get('status'), (sp['sponsorship_status'] ?? sp['status'] ?? '---').toString()),
              if (sp['sponsorship_start_date'] != null || sp['start_date'] != null)
                _infoRow(AppStrings.get('start_date'), (sp['sponsorship_start_date'] ?? sp['start_date']).toString()),
              if (sp['sponsorship_end_date'] != null || sp['end_date'] != null)
                _infoRow(AppStrings.get('end_date'), (sp['sponsorship_end_date'] ?? sp['end_date']).toString()),
            ],
          ),
        ),
      ),
    );
  }

  Widget _buildSponsorshipCTA(BuildContext context) {
    return Padding(
      padding: const EdgeInsets.symmetric(horizontal: 16),
      child: SizedBox(
        width: double.infinity,
        height: 56,
        child: ElevatedButton(
          onPressed: () => context.push('/sponsorship-request', extra: orphan),
          style: ElevatedButton.styleFrom(
            backgroundColor: AppColors.primary,
            foregroundColor: Colors.white,
            shape: RoundedRectangleBorder(
              borderRadius: BorderRadius.circular(16),
            ),
            elevation: 4,
            shadowColor: AppColors.primary.withOpacity(0.4),
          ),
          child: Text(
            AppStrings.get('sponsor_now'),
            style: const TextStyle(
              fontSize: 18,
              fontWeight: FontWeight.bold,
            ),
          ),
        ),
      ),
    );
  }

  Widget _infoRow(String label, String value) {
    return Padding(
      padding: const EdgeInsets.only(bottom: 12),
      child: Row(
        crossAxisAlignment: CrossAxisAlignment.start,
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
