import 'package:flutter/material.dart';

import '../../../core/localization/app_strings.dart';
import '../../../core/theme/app_colors.dart';
import '../../../core/theme/app_typography.dart';

class AboutScreen extends StatelessWidget {
  const AboutScreen({super.key});

  @override
  Widget build(BuildContext context) {
    final isDark = Theme.of(context).brightness == Brightness.dark;

    return Scaffold(
      appBar: AppBar(
        title: Text(AppStrings.get('about_title')),
        centerTitle: true,
      ),
      body: SingleChildScrollView(
        padding: const EdgeInsets.fromLTRB(16, 8, 16, 32),
        child: Column(
          children: [
            _buildAppHeader(isDark),
            const SizedBox(height: 24),
            _buildDescription(isDark),
            const SizedBox(height: 24),
            _buildInfoCard(
              isDark,
              title: 'الجامعة',
              value: 'جامعة إقليم سبأ',
              icon: Icons.school_rounded,
              gradientColors: [AppColors.primary, AppColors.primaryLight],
            ),
            const SizedBox(height: 12),
            _buildInfoCard(
              isDark,
              title: 'القسم',
              value: 'علوم الحاسب',
              icon: Icons.computer_rounded,
              gradientColors: [AppColors.secondary, AppColors.secondaryLight],
            ),
            const SizedBox(height: 12),
            _buildInfoCard(
              isDark,
              title: 'المشرف',
              value: 'الدكتور محمد شبيل',
              icon: Icons.person_outline_rounded,
              gradientColors: [AppColors.accent, AppColors.accentLight],
            ),
            const SizedBox(height: 12),
            _buildTeamSection(isDark),
            const SizedBox(height: 12),
            _buildInfoCard(
              isDark,
              title: 'البلد',
              value: 'اليمن',
              icon: Icons.public_rounded,
              gradientColors: [AppColors.info, const Color(0xFF64B5F6)],
            ),
            const SizedBox(height: 12),
            _buildInfoRow(isDark),
            const SizedBox(height: 32),
            _buildFooter(isDark),
          ],
        ),
      ),
    );
  }

  Widget _buildAppHeader(bool isDark) {
    return Container(
      width: double.infinity,
      padding: const EdgeInsets.symmetric(vertical: 32),
      decoration: BoxDecoration(
        color: isDark ? AppColors.darkCard : Colors.white,
        borderRadius: BorderRadius.circular(24),
        border: Border.all(
          color: isDark
              ? Colors.white.withOpacity(0.08)
              : AppColors.neutral200,
        ),
      ),
      child: Column(
        children: [
          Container(
            width: 88,
            height: 88,
            decoration: BoxDecoration(
              gradient: AppColors.primaryGradient,
              shape: BoxShape.circle,
              boxShadow: [
                BoxShadow(
                  color: AppColors.primary.withOpacity(0.3),
                  blurRadius: 16,
                  offset: const Offset(0, 6),
                ),
              ],
            ),
            child: const Icon(
              Icons.favorite_rounded,
              size: 44,
              color: Colors.white,
            ),
          ),
          const SizedBox(height: 20),
          Text(
            AppStrings.get('app_name'),
            style: AppTypography.headlineMedium.copyWith(
              color: isDark ? Colors.white : AppColors.neutral900,
              fontWeight: FontWeight.bold,
            ),
          ),
          const SizedBox(height: 6),
          Text(
            AppStrings.get('app_name'),
            style: AppTypography.bodyMedium.copyWith(
              color: isDark ? Colors.grey[400] : AppColors.neutral500,
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildDescription(bool isDark) {
    return Container(
      width: double.infinity,
      padding: const EdgeInsets.all(20),
      decoration: BoxDecoration(
        gradient: LinearGradient(
          colors: [
            AppColors.primary.withOpacity(0.08),
            AppColors.primaryLight.withOpacity(0.03),
          ],
          begin: Alignment.topLeft,
          end: Alignment.bottomRight,
        ),
        borderRadius: BorderRadius.circular(20),
        border: Border.all(
          color: AppColors.primary.withOpacity(isDark ? 0.2 : 0.15),
        ),
      ),
      child: Row(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Container(
            padding: const EdgeInsets.all(8),
            decoration: BoxDecoration(
              color: AppColors.primary.withOpacity(0.1),
              borderRadius: BorderRadius.circular(12),
            ),
            child: const Icon(
              Icons.info_outline_rounded,
              color: AppColors.primary,
              size: 22,
            ),
          ),
          const SizedBox(width: 12),
          Expanded(
            child: Text(
              AppStrings.get('app_description'),
              style: AppTypography.bodyMedium.copyWith(
                color: isDark ? Colors.grey[300] : AppColors.neutral700,
                height: 1.6,
              ),
              textAlign: TextAlign.right,
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildInfoCard(
    bool isDark, {
    required String title,
    required String value,
    required IconData icon,
    required List<Color> gradientColors,
  }) {
    return Container(
      width: double.infinity,
      padding: const EdgeInsets.all(16),
      decoration: BoxDecoration(
        color: isDark ? AppColors.darkCard : Colors.white,
        borderRadius: BorderRadius.circular(16),
        border: Border.all(
          color: isDark
              ? Colors.white.withOpacity(0.08)
              : AppColors.neutral200,
        ),
      ),
      child: Row(
        children: [
          Container(
            width: 48,
            height: 48,
            decoration: BoxDecoration(
              gradient: LinearGradient(
                colors: gradientColors,
                begin: Alignment.topLeft,
                end: Alignment.bottomRight,
              ),
              borderRadius: BorderRadius.circular(14),
            ),
            child: Icon(icon, color: Colors.white, size: 24),
          ),
          const SizedBox(width: 14),
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.end,
              children: [
                Text(
                  title,
                  style: TextStyle(
                    fontSize: 12,
                    color: isDark ? Colors.grey[400] : AppColors.neutral500,
                  ),
                  textAlign: TextAlign.right,
                ),
                const SizedBox(height: 2),
                Text(
                  value,
                  style: TextStyle(
                    fontSize: 15,
                    fontWeight: FontWeight.w600,
                    color: isDark ? Colors.white : AppColors.neutral800,
                  ),
                  textAlign: TextAlign.right,
                ),
              ],
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildTeamSection(bool isDark) {
    return Container(
      width: double.infinity,
      padding: const EdgeInsets.all(16),
      decoration: BoxDecoration(
        color: isDark ? AppColors.darkCard : Colors.white,
        borderRadius: BorderRadius.circular(16),
        border: Border.all(
          color: isDark
              ? Colors.white.withOpacity(0.08)
              : AppColors.neutral200,
        ),
      ),
      child: Column(
        children: [
          Row(
            children: [
              Container(
                width: 48,
                height: 48,
                decoration: BoxDecoration(
                  gradient: const LinearGradient(
                    colors: [AppColors.info, Color(0xFF64B5F6)],
                    begin: Alignment.topLeft,
                    end: Alignment.bottomRight,
                  ),
                  borderRadius: BorderRadius.circular(14),
                ),
                child: const Icon(
                  Icons.groups_rounded,
                  color: Colors.white,
                  size: 24,
                ),
              ),
              const SizedBox(width: 14),
              Expanded(
                child: Text(
                  'فريق العمل',
                  style: TextStyle(
                    fontSize: 12,
                    color: isDark ? Colors.grey[400] : AppColors.neutral500,
                  ),
                  textAlign: TextAlign.right,
                ),
              ),
            ],
          ),
          const SizedBox(height: 12),
          _teamMemberRow('إبتهال بركات', isDark),
          const Divider(height: 16, indent: 4, endIndent: 4),
          _teamMemberRow('هناء سعيد', isDark),
          const Divider(height: 16, indent: 4, endIndent: 4),
          _teamMemberRow('براءة النهمي', isDark),
        ],
      ),
    );
  }

  Widget _teamMemberRow(String name, bool isDark) {
    return Row(
      children: [
        Container(
          width: 32,
          height: 32,
          decoration: BoxDecoration(
            color: AppColors.primary.withOpacity(0.1),
            borderRadius: BorderRadius.circular(10),
          ),
          child: const Icon(
            Icons.person_rounded,
            size: 18,
            color: AppColors.primary,
          ),
        ),
        const SizedBox(width: 12),
        Text(
          name,
          style: TextStyle(
            fontSize: 15,
            fontWeight: FontWeight.w600,
            color: isDark ? Colors.white : AppColors.neutral800,
          ),
        ),
      ],
    );
  }

  Widget _buildInfoRow(bool isDark) {
    return Row(
      children: [
        Expanded(
          child: Container(
            padding: const EdgeInsets.all(16),
            decoration: BoxDecoration(
              color: isDark ? AppColors.darkCard : Colors.white,
              borderRadius: BorderRadius.circular(16),
              border: Border.all(
                color: isDark
                    ? Colors.white.withOpacity(0.08)
                    : AppColors.neutral200,
              ),
            ),
            child: Column(
              children: [
                const Icon(
                  Icons.tag_rounded,
                  size: 28,
                  color: AppColors.primary,
                ),
                const SizedBox(height: 8),
                Text(
                  AppStrings.get('version'),
                  style: TextStyle(
                    fontSize: 12,
                    color: isDark ? Colors.grey[400] : AppColors.neutral500,
                  ),
                ),
                const SizedBox(height: 2),
                Text(
                  '1.0.0',
                  style: TextStyle(
                    fontSize: 16,
                    fontWeight: FontWeight.w700,
                    color: isDark ? Colors.white : AppColors.neutral800,
                  ),
                ),
              ],
            ),
          ),
        ),
        const SizedBox(width: 12),
        Expanded(
          child: Container(
            padding: const EdgeInsets.all(16),
            decoration: BoxDecoration(
              color: isDark ? AppColors.darkCard : Colors.white,
              borderRadius: BorderRadius.circular(16),
              border: Border.all(
                color: isDark
                    ? Colors.white.withOpacity(0.08)
                    : AppColors.neutral200,
              ),
            ),
            child: Column(
              children: [
                const Icon(
                  Icons.calendar_month_rounded,
                  size: 28,
                  color: AppColors.secondary,
                ),
                const SizedBox(height: 8),
                Text(
                  AppStrings.get('year'),
                  style: TextStyle(
                    fontSize: 12,
                    color: isDark ? Colors.grey[400] : AppColors.neutral500,
                  ),
                ),
                const SizedBox(height: 2),
                Text(
                  '2026',
                  style: TextStyle(
                    fontSize: 16,
                    fontWeight: FontWeight.w700,
                    color: isDark ? Colors.white : AppColors.neutral800,
                  ),
                ),
              ],
            ),
          ),
        ),
      ],
    );
  }

  Widget _buildFooter(bool isDark) {
    return Text(
      '${AppStrings.get('app_name')} © 2026',
      style: AppTypography.caption.copyWith(
        color: isDark ? Colors.grey[600] : AppColors.neutral400,
      ),
    );
  }
}
