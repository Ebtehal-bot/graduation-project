import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:go_router/go_router.dart';
import '../../../core/errors/api_exception.dart';
import '../../../core/localization/app_strings.dart';
import '../../../core/theme/app_colors.dart';
import '../../../data/models/orphan_model.dart';
import '../../../data/repositories/orphans_repository.dart';
import '../../../data/repositories/sponsorship_repository.dart';
import '../../home/providers/home_provider.dart';
import '../../sponsorships/providers/sponsorships_provider.dart';

class SponsorshipRequestScreen extends ConsumerStatefulWidget {
  final OrphanModel? orphan;

  const SponsorshipRequestScreen({super.key, this.orphan});

  @override
  ConsumerState<SponsorshipRequestScreen> createState() => _SponsorshipRequestScreenState();
}

class _SponsorshipRequestScreenState extends ConsumerState<SponsorshipRequestScreen> {
  final _formKey = GlobalKey<FormState>();
  final _amountController = TextEditingController();
  final _notesController = TextEditingController();

  OrphanModel? _orphan;
  String _sponsorshipType = 'financial';
  bool _isSubmitting = false;
  String? _errorMessage;

  List<OrphanModel> _orphans = [];
  bool _isLoadingOrphans = false;
  bool _orphansLoaded = false;

  @override
  void initState() {
    super.initState();
    WidgetsBinding.instance.addPostFrameCallback((_) {
      _resolveOrphan();
    });
  }

  void _resolveOrphan() {
    if (widget.orphan != null) {
      setState(() {
        _orphan = widget.orphan;
      });
    } else {
      _loadOrphans();
    }
  }

  Future<void> _loadOrphans() async {
    if (_isLoadingOrphans || _orphansLoaded) return;
    setState(() => _isLoadingOrphans = true);
    try {
      final repository = ref.read(orphansRepositoryProvider);
      final data = await repository.getOrphans();
      final orphans = data
          .whereType<Map<String, dynamic>>()
          .map((e) => OrphanModel.fromJson(e))
          .toList();
      if (mounted) {
        setState(() {
          _orphans = orphans;
          _isLoadingOrphans = false;
          _orphansLoaded = true;
        });
      }
    } on ApiException catch (e) {
      if (mounted) {
        setState(() {
          _isLoadingOrphans = false;
          _errorMessage = e.message;
        });
      }
    } catch (e) {
      if (mounted) {
        setState(() {
          _isLoadingOrphans = false;
          _errorMessage = AppStrings.get('error_occurred');
        });
      }
    }
  }

  @override
  void dispose() {
    _amountController.dispose();
    _notesController.dispose();
    super.dispose();
  }

  Future<void> _handleSubmit() async {
    if (!_formKey.currentState!.validate()) return;
    if (_orphan == null) return;

    setState(() {
      _isSubmitting = true;
      _errorMessage = null;
    });

    try {
      final repository = ref.read(sponsorshipRepositoryProvider);
      await repository.createSponsorship(
        orphanId: _orphan!.id,
        sponsorshipType: _sponsorshipType,
        monthlyAmount: double.parse(_amountController.text),
        notes: _notesController.text.isNotEmpty ? _notesController.text : null,
      );

      if (mounted) {
        ref.invalidate(homeProvider);
        ref.invalidate(mySponsorshipsProvider);
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(
            content: Text(AppStrings.get('sponsorship_created')),
            backgroundColor: AppColors.success,
            behavior: SnackBarBehavior.floating,
            shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
            margin: const EdgeInsets.fromLTRB(16, 0, 16, 16),
          ),
        );
        context.go('/my-sponsorships');
      }
    } on ApiException catch (e) {
      setState(() {
        _errorMessage = e.message;
        _isSubmitting = false;
      });
    } catch (e) {
      setState(() {
        _errorMessage = AppStrings.get('error_occurred');
        _isSubmitting = false;
      });
    }
  }

  void _onSponsorshipTypeChanged(String? value) {
    if (value == null) return;
    setState(() {
      _sponsorshipType = value;
    });
  }

  @override
  Widget build(BuildContext context) {
    final isDark = Theme.of(context).brightness == Brightness.dark;

    return Scaffold(
      appBar: AppBar(
        title: Text(AppStrings.get('new_sponsorship')),
        centerTitle: true,
      ),
      body: SingleChildScrollView(
        padding: const EdgeInsets.all(20),
        child: Form(
          key: _formKey,
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              _buildOrphanSection(isDark),
              const SizedBox(height: 24),
              if (_errorMessage != null) ...[
                _buildErrorBanner(isDark),
                const SizedBox(height: 20),
              ],
              Text(
                AppStrings.get('sponsorship_type'),
                style: TextStyle(
                  fontSize: 14,
                  fontWeight: FontWeight.w600,
                  color: isDark ? Colors.white : AppColors.neutral700,
                ),
              ),
              const SizedBox(height: 8),
              _buildSponsorshipTypeDropdown(isDark),
              const SizedBox(height: 20),
              Text(
                AppStrings.get('amount'),
                style: TextStyle(
                  fontSize: 14,
                  fontWeight: FontWeight.w600,
                  color: isDark ? Colors.white : AppColors.neutral700,
                ),
              ),
              const SizedBox(height: 8),
              _buildAmountField(isDark),
              const SizedBox(height: 20),
              Text(
                AppStrings.get('notes'),
                style: TextStyle(
                  fontSize: 14,
                  fontWeight: FontWeight.w600,
                  color: isDark ? Colors.white : AppColors.neutral700,
                ),
              ),
              const SizedBox(height: 8),
              _buildNotesField(isDark),
              const SizedBox(height: 32),
              _buildSubmitButton(isDark),
            ],
          ),
        ),
      ),
    );
  }

  Widget _buildOrphanSection(bool isDark) {
    if (_orphan != null) {
      return _buildOrphanCard(isDark);
    }
    if (_isLoadingOrphans) {
      return Container(
        width: double.infinity,
        padding: const EdgeInsets.all(20),
        decoration: BoxDecoration(
          color: isDark ? const Color(0xFF1E1E1E) : Colors.white,
          borderRadius: BorderRadius.circular(20),
          border: Border.all(color: isDark ? Colors.grey[800]! : AppColors.neutral200),
        ),
        child: Row(
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            const SizedBox(
              width: 20, height: 20,
              child: CircularProgressIndicator(strokeWidth: 2, color: AppColors.primary),
            ),
            const SizedBox(width: 12),
            Text(
              AppStrings.get('loading'),
              style: TextStyle(
                fontSize: 14,
                color: isDark ? Colors.grey[400] : AppColors.neutral600,
              ),
            ),
          ],
        ),
      );
    }
    if (_orphans.isEmpty) {
      return Container(
        width: double.infinity,
        padding: const EdgeInsets.all(20),
        decoration: BoxDecoration(
          color: isDark ? const Color(0xFF1E1E1E) : Colors.white,
          borderRadius: BorderRadius.circular(20),
          border: Border.all(color: isDark ? Colors.grey[800]! : AppColors.neutral200),
        ),
        child: Column(
          children: [
            Icon(Icons.people_outline, size: 40, color: isDark ? Colors.grey[500] : AppColors.neutral400),
            const SizedBox(height: 12),
            Text(
              AppStrings.get('no_orphans'),
              style: TextStyle(
                fontSize: 14,
                color: isDark ? Colors.grey[400] : AppColors.neutral600,
              ),
            ),
          ],
        ),
      );
    }
    return _buildOrphanDropdown(isDark);
  }

  Widget _buildOrphanCard(bool isDark) {
    return Container(
      width: double.infinity,
      padding: const EdgeInsets.all(16),
      decoration: BoxDecoration(
        gradient: LinearGradient(
          colors: [
            AppColors.primary.withOpacity(0.08),
            AppColors.primaryLight.withOpacity(0.04),
          ],
          begin: Alignment.topLeft,
          end: Alignment.bottomRight,
        ),
        borderRadius: BorderRadius.circular(20),
        border: Border.all(color: AppColors.primary.withOpacity(0.15)),
      ),
      child: Row(
        children: [
          Container(
            width: 56,
            height: 56,
            decoration: BoxDecoration(
              gradient: AppColors.primaryGradient,
              borderRadius: BorderRadius.circular(16),
            ),
            child: Center(
              child: Text(
                _orphan!.name.isNotEmpty ? _orphan!.name[0] : '?',
                style: const TextStyle(
                  fontSize: 22,
                  fontWeight: FontWeight.bold,
                  color: Colors.white,
                ),
              ),
            ),
          ),
          const SizedBox(width: 14),
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(
                  _orphan!.name,
                  style: TextStyle(
                    fontSize: 16,
                    fontWeight: FontWeight.w600,
                    color: isDark ? Colors.white : AppColors.neutral900,
                  ),
                ),
                const SizedBox(height: 4),
                Row(
                  children: [
                    Icon(Icons.cake_outlined, size: 13, color: isDark ? Colors.grey[400] : AppColors.neutral500),
                    const SizedBox(width: 4),
                    Text(
                      '${_orphan!.age} ${AppStrings.get('years_old')}',
                      style: TextStyle(fontSize: 13, color: isDark ? Colors.grey[400] : AppColors.neutral600),
                    ),
                    if (_orphan!.location != null) ...[
                      const SizedBox(width: 12),
                      Icon(Icons.location_on_outlined, size: 13, color: isDark ? Colors.grey[400] : AppColors.neutral500),
                      const SizedBox(width: 4),
                      Text(
                        _orphan!.location!,
                        style: TextStyle(fontSize: 13, color: isDark ? Colors.grey[400] : AppColors.neutral600),
                        overflow: TextOverflow.ellipsis,
                      ),
                    ],
                  ],
                ),
              ],
            ),
          ),
          Container(
            padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 6),
            decoration: BoxDecoration(
              color: AppColors.primary.withOpacity(0.1),
              borderRadius: BorderRadius.circular(8),
            ),
            child: Row(
              mainAxisSize: MainAxisSize.min,
              children: [
                const Icon(Icons.check, size: 12, color: AppColors.primary),
                const SizedBox(width: 4),
                Text(
                  AppStrings.get('select_orphan'),
                  style: const TextStyle(
                    fontSize: 11,
                    fontWeight: FontWeight.w600,
                    color: AppColors.primary,
                  ),
                ),
              ],
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildOrphanDropdown(bool isDark) {
    return Container(
      decoration: BoxDecoration(
        color: isDark ? const Color(0xFF1E1E1E) : Colors.white,
        borderRadius: BorderRadius.circular(16),
        border: Border.all(color: isDark ? Colors.grey[800]! : AppColors.neutral200),
      ),
      padding: const EdgeInsets.symmetric(horizontal: 16),
      child: DropdownButtonHideUnderline(
        child: DropdownButtonFormField<int>(
          value: _orphan?.id,
          hint: Text(
            AppStrings.get('select_orphan'),
            style: TextStyle(
              fontSize: 15,
              color: isDark ? Colors.grey[400] : AppColors.neutral500,
            ),
          ),
          icon: const Icon(Icons.keyboard_arrow_down_rounded, color: AppColors.primary),
          isExpanded: true,
          items: _orphans.map((orphan) {
            return DropdownMenuItem<int>(
              value: orphan.id,
              child: Row(
                children: [
                  Container(
                    width: 36,
                    height: 36,
                    decoration: BoxDecoration(
                      gradient: AppColors.primaryGradient,
                      borderRadius: BorderRadius.circular(10),
                    ),
                    child: Center(
                      child: Text(
                        orphan.name.isNotEmpty ? orphan.name[0] : '?',
                        style: const TextStyle(
                          fontSize: 16,
                          fontWeight: FontWeight.bold,
                          color: Colors.white,
                        ),
                      ),
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
                            fontSize: 15,
                            fontWeight: FontWeight.w500,
                            color: isDark ? Colors.white : AppColors.neutral900,
                          ),
                        ),
                        Text(
                          '${orphan.age} ${AppStrings.get('years_old')}${orphan.location != null ? ' - ${orphan.location}' : ''}',
                          style: TextStyle(
                            fontSize: 12,
                            color: isDark ? Colors.grey[500] : AppColors.neutral500,
                          ),
                        ),
                      ],
                    ),
                  ),
                ],
              ),
            );
          }).toList(),
          onChanged: (value) {
            if (value == null) return;
            setState(() {
              _orphan = _orphans.firstWhere((o) => o.id == value);
            });
          },
      validator: (value) {
        if (value == null) return AppStrings.get('required_field');
        return null;
      },
        ),
      ),
    );
  }

  Widget _buildSponsorshipTypeDropdown(bool isDark) {
    return Container(
      decoration: BoxDecoration(
        color: isDark ? const Color(0xFF1E1E1E) : Colors.white,
        borderRadius: BorderRadius.circular(16),
        border: Border.all(color: isDark ? Colors.grey[800]! : AppColors.neutral200),
      ),
      padding: const EdgeInsets.symmetric(horizontal: 16),
      child: DropdownButtonHideUnderline(
        child: DropdownButtonFormField<String>(
          value: _sponsorshipType,
          icon: const Icon(Icons.keyboard_arrow_down_rounded, color: AppColors.primary),
          isExpanded: true,
          items: const [
            DropdownMenuItem(value: 'financial', child: _TypeOption(icon: Icons.monetization_on, label: 'مالي')),
            DropdownMenuItem(value: 'educational', child: _TypeOption(icon: Icons.school, label: 'تعليمي')),
            DropdownMenuItem(value: 'medical', child: _TypeOption(icon: Icons.medical_services, label: 'طبي')),
          ],
          onChanged: _onSponsorshipTypeChanged,
          validator: (value) {
            if (value == null || value.isEmpty) return AppStrings.get('required_field');
            return null;
          },
        ),
      ),
    );
  }

  Widget _buildAmountField(bool isDark) {
    return TextFormField(
      controller: _amountController,
      keyboardType: TextInputType.number,
      textDirection: TextDirection.ltr,
      style: TextStyle(
        fontSize: 16,
        color: isDark ? Colors.white : AppColors.neutral900,
      ),
      decoration: InputDecoration(
        prefixIcon: Container(
          margin: const EdgeInsets.all(12),
          padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 6),
          decoration: BoxDecoration(
            color: AppColors.primary.withOpacity(0.1),
            borderRadius: BorderRadius.circular(8),
          ),
          child: Text(
            AppStrings.get('riyal'),
            style: const TextStyle(
              fontSize: 13,
              fontWeight: FontWeight.w600,
              color: AppColors.primary,
            ),
          ),
        ),
        hintText: AppStrings.get('amount_hint'),
        hintStyle: TextStyle(
          color: isDark ? Colors.grey[600] : AppColors.neutral400,
        ),
        filled: true,
        fillColor: isDark ? const Color(0xFF1E1E1E) : AppColors.neutral50,
        border: OutlineInputBorder(
          borderRadius: BorderRadius.circular(16),
          borderSide: BorderSide(color: isDark ? Colors.grey[800]! : AppColors.neutral200),
        ),
        enabledBorder: OutlineInputBorder(
          borderRadius: BorderRadius.circular(16),
          borderSide: BorderSide(color: isDark ? Colors.grey[800]! : AppColors.neutral200),
        ),
        focusedBorder: OutlineInputBorder(
          borderRadius: BorderRadius.circular(16),
          borderSide: const BorderSide(color: AppColors.primary, width: 2),
        ),
        errorBorder: OutlineInputBorder(
          borderRadius: BorderRadius.circular(16),
          borderSide: const BorderSide(color: AppColors.error),
        ),
        contentPadding: const EdgeInsets.symmetric(horizontal: 16, vertical: 16),
      ),
      validator: (value) {
        if (value == null || value.isEmpty) return AppStrings.get('required_field');
        final amount = double.tryParse(value);
        if (amount == null || amount <= 0) return AppStrings.get('invalid_amount');
        if (amount > 1000000) return AppStrings.get('amount_too_high');
        return null;
      },
    );
  }

  Widget _buildNotesField(bool isDark) {
    return TextFormField(
      controller: _notesController,
      maxLines: 4,
      textDirection: TextDirection.rtl,
      style: TextStyle(
        fontSize: 15,
        color: isDark ? Colors.white : AppColors.neutral900,
      ),
      decoration: InputDecoration(
        hintText: AppStrings.get('notes_hint'),
        hintStyle: TextStyle(
          color: isDark ? Colors.grey[600] : AppColors.neutral400,
        ),
        filled: true,
        fillColor: isDark ? const Color(0xFF1E1E1E) : AppColors.neutral50,
        border: OutlineInputBorder(
          borderRadius: BorderRadius.circular(16),
          borderSide: BorderSide(color: isDark ? Colors.grey[800]! : AppColors.neutral200),
        ),
        enabledBorder: OutlineInputBorder(
          borderRadius: BorderRadius.circular(16),
          borderSide: BorderSide(color: isDark ? Colors.grey[800]! : AppColors.neutral200),
        ),
        focusedBorder: OutlineInputBorder(
          borderRadius: BorderRadius.circular(16),
          borderSide: const BorderSide(color: AppColors.primary, width: 2),
        ),
        errorBorder: OutlineInputBorder(
          borderRadius: BorderRadius.circular(16),
          borderSide: const BorderSide(color: AppColors.error),
        ),
        contentPadding: const EdgeInsets.all(16),
      ),
    );
  }

  Widget _buildErrorBanner(bool isDark) {
    return Container(
      width: double.infinity,
      padding: const EdgeInsets.all(14),
      decoration: BoxDecoration(
        color: AppColors.error.withOpacity(0.08),
        borderRadius: BorderRadius.circular(14),
        border: Border.all(color: AppColors.error.withOpacity(0.2)),
      ),
      child: Row(
        children: [
          const Icon(Icons.error_outline, size: 20, color: AppColors.error),
          const SizedBox(width: 10),
          Expanded(
            child: Text(
              _errorMessage!,
              style: const TextStyle(
                fontSize: 14,
                color: AppColors.error,
              ),
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildSubmitButton(bool isDark) {
    return SizedBox(
      width: double.infinity,
      height: 54,
      child: DecoratedBox(
        decoration: BoxDecoration(
          borderRadius: BorderRadius.circular(16),
          gradient: (_orphan == null || _isSubmitting)
              ? null
              : AppColors.primaryGradient,
          boxShadow: (_orphan == null || _isSubmitting)
              ? null
              : [
                  BoxShadow(
                    color: AppColors.primary.withOpacity(0.3),
                    blurRadius: 12,
                    offset: const Offset(0, 6),
                  ),
                ],
        ),
        child: ElevatedButton(
          onPressed: (_orphan == null || _isSubmitting) ? null : _handleSubmit,
          style: ElevatedButton.styleFrom(
            backgroundColor: Colors.transparent,
            shadowColor: Colors.transparent,
            disabledBackgroundColor: isDark ? Colors.grey[800] : AppColors.neutral200,
            shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(16)),
          ),
          child: _isSubmitting
              ? Row(
                  mainAxisAlignment: MainAxisAlignment.center,
                  children: [
                    SizedBox(
                      width: 22, height: 22,
                      child: CircularProgressIndicator(
                        strokeWidth: 2.5,
                        color: isDark ? AppColors.primary : Colors.white,
                      ),
                    ),
                    const SizedBox(width: 12),
                    Text(
                      AppStrings.get('submitting'),
                      style: TextStyle(
                        fontSize: 16,
                        fontWeight: FontWeight.w600,
                        color: isDark ? AppColors.primary : Colors.white,
                      ),
                    ),
                  ],
                )
              : Row(
                  mainAxisAlignment: MainAxisAlignment.center,
                  children: [
                    const Icon(Icons.favorite, size: 22, color: Colors.white),
                    const SizedBox(width: 10),
                    Text(
                      AppStrings.get('submit'),
                      style: const TextStyle(
                        fontSize: 16,
                        fontWeight: FontWeight.w600,
                        color: Colors.white,
                      ),
                    ),
                  ],
                ),
        ),
      ),
    );
  }
}

class _TypeOption extends StatelessWidget {
  final IconData icon;
  final String label;

  const _TypeOption({required this.icon, required this.label});

  @override
  Widget build(BuildContext context) {
    final isDark = Theme.of(context).brightness == Brightness.dark;
    return Row(
      children: [
        Icon(icon, size: 20, color: AppColors.primary),
        const SizedBox(width: 10),
        Text(
          label,
          style: TextStyle(
            fontSize: 15,
            color: isDark ? Colors.white : AppColors.neutral900,
          ),
        ),
      ],
    );
  }
}
