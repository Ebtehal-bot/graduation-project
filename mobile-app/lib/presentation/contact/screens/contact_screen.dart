import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:url_launcher/url_launcher.dart';

import '../../../core/localization/app_strings.dart';
import '../../../core/theme/app_colors.dart';
import '../../../core/theme/app_typography.dart';
import '../../../data/repositories/settings_repository.dart';

class ContactScreen extends ConsumerStatefulWidget {
  const ContactScreen({super.key});

  @override
  ConsumerState<ContactScreen> createState() => _ContactScreenState();
}

class _ContactScreenState extends ConsumerState<ContactScreen> {
  String _email = '';
  String _phone = '';
  String _whatsapp = '';

  @override
  void initState() {
    super.initState();
    _loadSettings();
  }

  Future<void> _loadSettings() async {
    try {
      final settings = await ref.read(settingsRepositoryProvider).getSettings();
      if (mounted) {
        setState(() {
          _email = settings.siteEmail ?? '';
          _phone = settings.sitePhone ?? '';
          _whatsapp = settings.siteWhatsapp ?? settings.sitePhone ?? '';
        });
      }
    } catch (_) {}
  }

  @override
  Widget build(BuildContext context) {
    final isDark = Theme.of(context).brightness == Brightness.dark;

    return Scaffold(
      appBar: AppBar(
        title: Text(AppStrings.get('contact_us')),
        centerTitle: true,
      ),
      body: SingleChildScrollView(
        padding: const EdgeInsets.fromLTRB(16, 8, 16, 32),
        child: Column(
          children: [
            _buildHeader(isDark),
            const SizedBox(height: 24),
            _buildContactCard(
              isDark,
              icon: Icons.email_outlined,
              iconColor: AppColors.primary,
              gradientColors: [AppColors.primary, AppColors.primaryLight],
              title: AppStrings.get('email'),
              value: _email,
              subtitle: 'نحن هنا لمساعدتك 24 ساعة',
              onTap: _sendEmail,
            ),
            const SizedBox(height: 12),
            _buildContactCard(
              isDark,
              icon: Icons.phone_outlined,
              iconColor: AppColors.success,
              gradientColors: [AppColors.success, const Color(0xFF81C784)],
              title: AppStrings.get('phone'),
              value: _phone,
              subtitle: AppStrings.get('working_hours_detail'),
              onTap: _callPhone,
            ),
            const SizedBox(height: 12),
            _buildContactCard(
              isDark,
              icon: Icons.chat_outlined,
              iconColor: const Color(0xFF25D366),
              gradientColors: [const Color(0xFF25D366), const Color(0xFF75E075)],
              title: AppStrings.get('whatsapp'),
              value: _whatsapp,
              subtitle: AppStrings.get('working_hours'),
              onTap: _openWhatsApp,
            ),
            const SizedBox(height: 12),
            _buildContactCard(
              isDark,
              icon: Icons.location_on_outlined,
              iconColor: AppColors.accent,
              gradientColors: [AppColors.accent, AppColors.accentLight],
              title: 'الدولة',
              value: 'اليمن',
              subtitle: null,
              onTap: null,
            ),
          ],
        ),
      ),
    );
  }

  Future<void> _sendEmail() async {
    if (_email.isEmpty) return;
    final uri = Uri(scheme: 'mailto', path: _email);
    if (await canLaunchUrl(uri)) {
      await launchUrl(uri);
    }
  }

  Future<void> _callPhone() async {
    final clean = _phone.replaceAll(RegExp(r'[^\d+]'), '');
    if (clean.isEmpty) return;
    final uri = Uri(scheme: 'tel', path: clean);
    if (await canLaunchUrl(uri)) {
      await launchUrl(uri);
    }
  }

  Future<void> _openWhatsApp() async {
    final clean = _whatsapp.replaceAll(RegExp(r'[^\d+]'), '');
    if (clean.isEmpty) return;
    final uri = Uri.parse('https://wa.me/$clean');
    if (await canLaunchUrl(uri)) {
      await launchUrl(uri, mode: LaunchMode.externalApplication);
    }
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
              Icons.headset_mic_rounded,
              size: 36,
              color: Colors.white,
            ),
          ),
          const SizedBox(height: 16),
          Text(
            AppStrings.get('contact_us'),
            style: AppTypography.headlineMedium.copyWith(
              color: Colors.white,
              fontWeight: FontWeight.bold,
            ),
          ),
          const SizedBox(height: 6),
          Text(
            'نحن هنا لمساعدتك في أي وقت',
            style: AppTypography.bodyMedium.copyWith(
              color: Colors.white.withOpacity(0.8),
            ),
            textAlign: TextAlign.center,
          ),
        ],
      ),
    );
  }

  Widget _buildContactCard(
    bool isDark, {
    required IconData icon,
    required Color iconColor,
    required List<Color> gradientColors,
    required String title,
    required String value,
    String? subtitle,
    VoidCallback? onTap,
  }) {
    return GestureDetector(
      onTap: onTap,
      child: Container(
        width: double.infinity,
        padding: const EdgeInsets.all(16),
        decoration: BoxDecoration(
          color: isDark ? AppColors.darkCard : Colors.white,
          borderRadius: BorderRadius.circular(18),
          border: Border.all(
            color: isDark ? Colors.white.withOpacity(0.08) : AppColors.neutral200,
          ),
        ),
        child: Row(
          children: [
            Container(
              width: 52,
              height: 52,
              decoration: BoxDecoration(
                gradient: LinearGradient(
                  colors: gradientColors,
                  begin: Alignment.topLeft,
                  end: Alignment.bottomRight,
                ),
                borderRadius: BorderRadius.circular(16),
              ),
              child: Icon(icon, color: Colors.white, size: 26),
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
                      letterSpacing: 0.3,
                    ),
                    textAlign: TextAlign.right,
                  ),
                  if (subtitle != null)
                    Column(
                      children: [
                        const SizedBox(height: 2),
                        Text(
                          subtitle,
                          style: TextStyle(
                            fontSize: 11,
                            color: isDark ? Colors.grey[500] : AppColors.neutral400,
                          ),
                          textAlign: TextAlign.right,
                        ),
                      ],
                    ),
                ],
              ),
            ),
            if (onTap != null)
              Padding(
                padding: const EdgeInsets.only(left: 8),
                child: Container(
                  width: 36,
                  height: 36,
                  decoration: BoxDecoration(
                    color: isDark ? Colors.white.withOpacity(0.05) : AppColors.neutral100,
                    borderRadius: BorderRadius.circular(12),
                  ),
                  child: const Icon(
                    Icons.arrow_back_ios_new_rounded,
                    size: 16,
                    color: AppColors.primary,
                  ),
                ),
              ),
          ],
        ),
      ),
    );
  }
}
