import 'package:flutter/material.dart';

import '../../../core/localization/app_strings.dart';
import '../../../core/theme/app_colors.dart';
import '../../../core/theme/app_typography.dart';

class HelpCenterScreen extends StatelessWidget {
  const HelpCenterScreen({super.key});

  @override
  Widget build(BuildContext context) {
    final isDark = Theme.of(context).brightness == Brightness.dark;

    return Scaffold(
      appBar: AppBar(
        title: Text(AppStrings.get('help_center')),
        centerTitle: true,
      ),
      body: ListView(
        padding: const EdgeInsets.fromLTRB(16, 8, 16, 32),
        children: [
          _buildHeader(isDark),
          const SizedBox(height: 20),
          _buildSection(
            isDark,
            icon: Icons.rocket_launch_rounded,
            gradientColors: [AppColors.primary, AppColors.primaryLight],
            title: AppStrings.get('help_getting_started'),
            description: AppStrings.get('help_getting_started_desc'),
          ),
          const SizedBox(height: 12),
          _buildSection(
            isDark,
            icon: Icons.dashboard_rounded,
            gradientColors: [const Color(0xFF7C4DFF), const Color(0xFFB388FF)],
            title: AppStrings.get('help_dashboard'),
            description: AppStrings.get('help_dashboard_desc'),
          ),
          const SizedBox(height: 12),
          _buildSection(
            isDark,
            icon: Icons.people_rounded,
            gradientColors: [AppColors.success, const Color(0xFF81C784)],
            title: AppStrings.get('help_orphans'),
            description: AppStrings.get('help_orphans_desc'),
          ),
          const SizedBox(height: 12),
          _buildSection(
            isDark,
            icon: Icons.favorite_rounded,
            gradientColors: [AppColors.accent, AppColors.accentLight],
            title: AppStrings.get('help_sponsorships'),
            description: AppStrings.get('help_sponsorships_desc'),
          ),
          const SizedBox(height: 12),
          _buildSection(
            isDark,
            icon: Icons.bar_chart_rounded,
            gradientColors: [AppColors.info, const Color(0xFF64B5F6)],
            title: AppStrings.get('help_reports'),
            description: AppStrings.get('help_reports_desc'),
          ),
          const SizedBox(height: 12),
          _buildSection(
            isDark,
            icon: Icons.notifications_rounded,
            gradientColors: [const Color(0xFFFF7043), const Color(0xFFFFAB91)],
            title: AppStrings.get('help_notifications'),
            description: AppStrings.get('help_notifications_desc'),
          ),
          const SizedBox(height: 12),
          _buildSection(
            isDark,
            icon: Icons.person_rounded,
            gradientColors: [AppColors.primary, AppColors.primaryLight],
            title: AppStrings.get('help_profile'),
            description: AppStrings.get('help_profile_desc'),
          ),
          const SizedBox(height: 12),
          _buildSection(
            isDark,
            icon: Icons.settings_rounded,
            gradientColors: [AppColors.secondary, AppColors.secondaryLight],
            title: AppStrings.get('help_settings'),
            description: AppStrings.get('help_settings_desc'),
          ),
          const SizedBox(height: 24),
          _buildFaqSection(isDark),
          const SizedBox(height: 24),
          _buildContactCard(isDark),
          const SizedBox(height: 32),
        ],
      ),
    );
  }

  Widget _buildHeader(bool isDark) {
    return Container(
      width: double.infinity,
      padding: const EdgeInsets.symmetric(vertical: 28, horizontal: 20),
      decoration: BoxDecoration(
        gradient: AppColors.primaryGradient,
        borderRadius: BorderRadius.circular(24),
      ),
      child: Column(
        children: [
          Container(
            width: 72,
            height: 72,
            decoration: BoxDecoration(
              color: Colors.white.withOpacity(0.15),
              shape: BoxShape.circle,
            ),
            child: const Icon(
              Icons.help_outline_rounded,
              size: 36,
              color: Colors.white,
            ),
          ),
          const SizedBox(height: 16),
          Text(
            AppStrings.get('help_center'),
            style: AppTypography.headlineMedium.copyWith(
              color: Colors.white,
              fontWeight: FontWeight.bold,
            ),
          ),
          const SizedBox(height: 6),
          Text(
            AppStrings.get('help_intro_desc'),
            style: AppTypography.bodyMedium.copyWith(
              color: Colors.white.withOpacity(0.85),
              height: 1.5,
            ),
            textAlign: TextAlign.center,
          ),
        ],
      ),
    );
  }

  Widget _buildSection(
    bool isDark, {
    required IconData icon,
    required List<Color> gradientColors,
    required String title,
    required String description,
  }) {
    return Container(
      width: double.infinity,
      padding: const EdgeInsets.all(16),
      decoration: BoxDecoration(
        color: isDark ? AppColors.darkCard : Colors.white,
        borderRadius: BorderRadius.circular(18),
        border: Border.all(
          color: isDark ? Colors.white.withOpacity(0.08) : AppColors.neutral200,
        ),
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Row(
            children: [
              Container(
                width: 44,
                height: 44,
                decoration: BoxDecoration(
                  gradient: LinearGradient(
                    colors: gradientColors,
                    begin: Alignment.topLeft,
                    end: Alignment.bottomRight,
                  ),
                  borderRadius: BorderRadius.circular(14),
                ),
                child: Icon(icon, color: Colors.white, size: 22),
              ),
              const SizedBox(width: 12),
              Expanded(
                child: Text(
                  title,
                  style: TextStyle(
                    fontSize: 16,
                    fontWeight: FontWeight.w600,
                    color: isDark ? Colors.white : AppColors.neutral900,
                  ),
                ),
              ),
            ],
          ),
          const SizedBox(height: 12),
          Text(
            description,
            style: TextStyle(
              fontSize: 13,
              color: isDark ? Colors.grey[400] : AppColors.neutral600,
              height: 1.6,
            ),
            textAlign: TextAlign.start,
          ),
        ],
      ),
    );
  }

  Widget _buildFaqSection(bool isDark) {
    return Container(
      width: double.infinity,
      padding: const EdgeInsets.all(16),
      decoration: BoxDecoration(
        color: isDark ? AppColors.darkCard : Colors.white,
        borderRadius: BorderRadius.circular(18),
        border: Border.all(
          color: isDark ? Colors.white.withOpacity(0.08) : AppColors.neutral200,
        ),
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Row(
            children: [
              Container(
                width: 44,
                height: 44,
                decoration: BoxDecoration(
                  gradient: const LinearGradient(
                    colors: [AppColors.warning, Color(0xFFFFD54F)],
                    begin: Alignment.topLeft,
                    end: Alignment.bottomRight,
                  ),
                  borderRadius: BorderRadius.circular(14),
                ),
                child: const Icon(
                  Icons.help_rounded,
                  color: Colors.white,
                  size: 22,
                ),
              ),
              const SizedBox(width: 12),
              Text(
                AppStrings.get('help_faq'),
                style: TextStyle(
                  fontSize: 16,
                  fontWeight: FontWeight.w600,
                  color: isDark ? Colors.white : AppColors.neutral900,
                ),
              ),
            ],
          ),
          const SizedBox(height: 16),
          _faqItem(
            isDark,
            question: AppStrings.get('help_faq_q1'),
            answer: AppStrings.get('help_faq_a1'),
          ),
          const Divider(height: 24, indent: 4, endIndent: 4),
          _faqItem(
            isDark,
            question: AppStrings.get('help_faq_q2'),
            answer: AppStrings.get('help_faq_a2'),
          ),
          const Divider(height: 24, indent: 4, endIndent: 4),
          _faqItem(
            isDark,
            question: AppStrings.get('help_faq_q3'),
            answer: AppStrings.get('help_faq_a3'),
          ),
          const Divider(height: 24, indent: 4, endIndent: 4),
          _faqItem(
            isDark,
            question: AppStrings.get('help_faq_q4'),
            answer: AppStrings.get('help_faq_a4'),
          ),
        ],
      ),
    );
  }

  Widget _faqItem(
    bool isDark, {
    required String question,
    required String answer,
  }) {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Row(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Container(
              margin: const EdgeInsets.only(top: 2),
              width: 20,
              height: 20,
              decoration: BoxDecoration(
                color: AppColors.primary.withOpacity(0.1),
                borderRadius: BorderRadius.circular(6),
              ),
              child: const Center(
                child: Text(
                  '?',
                  style: TextStyle(
                    fontSize: 12,
                    fontWeight: FontWeight.bold,
                    color: AppColors.primary,
                  ),
                ),
              ),
            ),
            const SizedBox(width: 10),
            Expanded(
              child: Text(
                question,
                style: TextStyle(
                  fontSize: 14,
                  fontWeight: FontWeight.w600,
                  color: isDark ? Colors.white : AppColors.neutral900,
                ),
              ),
            ),
          ],
        ),
        const SizedBox(height: 8),
        Padding(
          padding: const EdgeInsets.only(left: 30),
          child: Text(
            answer,
            style: TextStyle(
              fontSize: 13,
              color: isDark ? Colors.grey[400] : AppColors.neutral600,
              height: 1.5,
            ),
          ),
        ),
      ],
    );
  }

  Widget _buildContactCard(bool isDark) {
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
        borderRadius: BorderRadius.circular(18),
        border: Border.all(
          color: AppColors.primary.withOpacity(isDark ? 0.2 : 0.15),
        ),
      ),
      child: Column(
        children: [
          Container(
            width: 56,
            height: 56,
            decoration: BoxDecoration(
              color: AppColors.primary.withOpacity(0.1),
              shape: BoxShape.circle,
            ),
            child: const Icon(
              Icons.headset_mic_rounded,
              color: AppColors.primary,
              size: 28,
            ),
          ),
          const SizedBox(height: 12),
          Text(
            AppStrings.get('help_contact'),
            style: TextStyle(
              fontSize: 16,
              fontWeight: FontWeight.w600,
              color: isDark ? Colors.white : AppColors.neutral900,
            ),
          ),
          const SizedBox(height: 6),
          Text(
            AppStrings.get('help_contact_desc'),
            style: TextStyle(
              fontSize: 13,
              color: isDark ? Colors.grey[400] : AppColors.neutral600,
              height: 1.5,
            ),
            textAlign: TextAlign.center,
          ),
        ],
      ),
    );
  }
}
