import 'package:flutter/material.dart';
import '../../core/localization/app_strings.dart';
import '../../core/theme/app_colors.dart';
import '../../data/models/sponsorship_model.dart';

class AppSponsorshipCard extends StatelessWidget {
  final SponsorshipModel sponsorship;
  final VoidCallback? onTap;

  const AppSponsorshipCard({
    super.key,
    required this.sponsorship,
    this.onTap,
  });

  Color _statusColor(String status) {
    switch (status) {
      case 'active':
        return AppColors.success;
      case 'pending':
        return AppColors.warning;
      case 'approved':
        return AppColors.success;
      case 'rejected':
        return AppColors.error;
      case 'completed':
        return AppColors.info;
      case 'cancelled':
        return AppColors.neutral500;
      default:
        return AppColors.neutral500;
    }
  }

  IconData _statusIcon(String status) {
    switch (status) {
      case 'active':
        return Icons.check_circle;
      case 'pending':
        return Icons.hourglass_empty;
      case 'approved':
        return Icons.verified;
      case 'rejected':
        return Icons.cancel;
      case 'completed':
        return Icons.done_all;
      case 'cancelled':
        return Icons.block;
      default:
        return Icons.help_outline;
    }
  }

  String _dateText() {
    if (sponsorship.startDate != null) {
      final d = DateTime.tryParse(sponsorship.startDate!);
      if (d != null) {
        return '${d.year}-${d.month.toString().padLeft(2, '0')}-${d.day.toString().padLeft(2, '0')}';
      }
      return sponsorship.startDate!;
    }
    return '';
  }

  @override
  Widget build(BuildContext context) {
    final isDark = Theme.of(context).brightness == Brightness.dark;
    final color = _statusColor(sponsorship.status);

    return Material(
      color: Colors.transparent,
      child: InkWell(
        onTap: onTap,
        borderRadius: BorderRadius.circular(20),
        child: Container(
          margin: const EdgeInsets.only(bottom: 12),
          padding: const EdgeInsets.all(16),
          decoration: BoxDecoration(
            color: isDark ? const Color(0xFF1E1E1E) : Colors.white,
            borderRadius: BorderRadius.circular(20),
            border: Border.all(
              color: isDark ? Colors.grey[800]! : AppColors.neutral200,
            ),
            boxShadow: [
              BoxShadow(
                color: Colors.black.withOpacity(isDark ? 0.3 : 0.04),
                blurRadius: 12,
                offset: const Offset(0, 4),
              ),
            ],
          ),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Row(
                children: [
                  Container(
                    width: 52,
                    height: 52,
                    decoration: BoxDecoration(
                      color: color.withOpacity(0.1),
                      borderRadius: BorderRadius.circular(16),
                    ),
                    child: Icon(
                      _statusIcon(sponsorship.status),
                      color: color,
                      size: 26,
                    ),
                  ),
                  const SizedBox(width: 12),
                  Expanded(
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Text(
                          sponsorship.orphanName ?? AppStrings.get('orphans_title'),
                          style: TextStyle(
                            fontSize: 16,
                            fontWeight: FontWeight.w600,
                            color: isDark ? Colors.white : AppColors.neutral900,
                          ),
                        ),
                        const SizedBox(height: 2),
                        Text(
                          '${sponsorship.monthlyAmount.toStringAsFixed(0)} ${AppStrings.get('riyal')}',
                          style: TextStyle(
                            fontSize: 13,
                            color: isDark ? Colors.grey[400] : AppColors.neutral600,
                          ),
                        ),
                      ],
                    ),
                  ),
                  Container(
                    padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 6),
                    decoration: BoxDecoration(
                      color: color.withOpacity(0.1),
                      borderRadius: BorderRadius.circular(8),
                    ),
                    child: Row(
                      mainAxisSize: MainAxisSize.min,
                      children: [
                        Icon(_statusIcon(sponsorship.status), size: 12, color: color),
                        const SizedBox(width: 4),
                        Text(
                          sponsorship.statusLabel,
                          style: TextStyle(
                            fontSize: 11,
                            fontWeight: FontWeight.w600,
                            color: color,
                          ),
                        ),
                      ],
                    ),
                  ),
                ],
              ),
              if (sponsorship.startDate != null) ...[
                const SizedBox(height: 12),
                Row(
                  children: [
                    Icon(Icons.calendar_today, size: 14, color: isDark ? Colors.grey[500] : AppColors.neutral400),
                    const SizedBox(width: 6),
                    Text(
                      '${AppStrings.get('date_added')}: ${_dateText()}',
                      style: TextStyle(
                        fontSize: 12,
                        color: isDark ? Colors.grey[500] : AppColors.neutral500,
                      ),
                    ),
                    if (sponsorship.branchName != null) ...[
                      const SizedBox(width: 16),
                      Icon(Icons.location_on_outlined, size: 14, color: isDark ? Colors.grey[500] : AppColors.neutral400),
                      const SizedBox(width: 4),
                      Expanded(
                        child: Text(
                          sponsorship.branchName!,
                          style: TextStyle(
                            fontSize: 12,
                            color: isDark ? Colors.grey[500] : AppColors.neutral500,
                          ),
                          overflow: TextOverflow.ellipsis,
                        ),
                      ),
                    ],
                  ],
                ),
              ],
            ],
          ),
        ),
      ),
    );
  }
}
