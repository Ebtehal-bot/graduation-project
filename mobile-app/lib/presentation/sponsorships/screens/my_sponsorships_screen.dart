import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:go_router/go_router.dart';
import 'package:cached_network_image/cached_network_image.dart';

import '../../../core/localization/app_strings.dart';
import '../../../core/theme/app_colors.dart';
import '../../../data/models/sponsorship_model.dart';
import '../../../shared/widgets/widgets.dart';
import '../providers/sponsorships_provider.dart';

class MySponsorshipsScreen extends ConsumerStatefulWidget {
  const MySponsorshipsScreen({super.key});

  @override
  ConsumerState<MySponsorshipsScreen> createState() =>
      _MySponsorshipsScreenState();
}

class _MySponsorshipsScreenState extends ConsumerState<MySponsorshipsScreen>
    with SingleTickerProviderStateMixin {
  late TabController _tabController;

  @override
  void initState() {
    super.initState();
    _tabController = TabController(length: 3, vsync: this);
    WidgetsBinding.instance.addPostFrameCallback((_) {
      ref.read(mySponsorshipsProvider.notifier).loadSponsorships();
    });
  }

  @override
  void dispose() {
    _tabController.dispose();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    final state = ref.watch(mySponsorshipsProvider);
    final isDark = Theme.of(context).brightness == Brightness.dark;

    return Scaffold(
      appBar: AppBar(
        title: Text(AppStrings.get('nav_sponsorships')),
        bottom: TabBar(
          controller: _tabController,
          isScrollable: true,
          labelColor: AppColors.primary,
          unselectedLabelColor: isDark ? Colors.grey[400] : AppColors.neutral500,
          indicatorColor: AppColors.primary,
          tabs: [
            Tab(text: AppStrings.get('active_sponsorships')),
            Tab(text: AppStrings.get('completed_sponsorships')),
            Tab(text: AppStrings.get('history')),
          ],
        ),
      ),
      floatingActionButton: FloatingActionButton.extended(
        onPressed: () => context.push('/sponsorship-request'),
        backgroundColor: AppColors.primary,
        foregroundColor: Colors.white,
        icon: const Icon(Icons.add_rounded),
        label: Text(AppStrings.get('new_sponsorship')),
      ),
      body: state.isLoading
          ? _buildSkeletons()
          : state.error != null
              ? Center(
                  child: AppErrorState(
                    message: state.error,
                    onRetry: () => ref
                        .read(mySponsorshipsProvider.notifier)
                        .loadSponsorships(),
                  ),
                )
              : state.sponsorships.isEmpty
                  ? Center(
                      child: AppEmptyState(
                        icon: Icons.favorite_border_rounded,
                        title: AppStrings.get('no_sponsorships'),
                        subtitle: AppStrings.get('no_sponsorships_desc'),
                      ),
                    )
                  : TabBarView(
                      controller: _tabController,
                      children: [
                        _buildList(state.active, isDark),
                        _buildList(state.completed, isDark),
                        _buildList(state.sponsorships, isDark),
                      ],
                    ),
    );
  }

  Widget _buildList(List<SponsorshipModel> list, bool isDark) {
    if (list.isEmpty) {
      return Center(
        child: AppEmptyState(
          icon: Icons.inbox_rounded,
          title: AppStrings.get('no_items'),
        ),
      );
    }
    return RefreshIndicator(
      onRefresh: () =>
          ref.read(mySponsorshipsProvider.notifier).loadSponsorships(),
      child: ListView.builder(
        padding: const EdgeInsets.all(16),
        itemCount: list.length,
        itemBuilder: (_, i) => _buildSponsorshipCard(list[i], isDark),
      ),
    );
  }

  Widget _buildSponsorshipCard(SponsorshipModel sp, bool isDark) {
    return Card(
      margin: const EdgeInsets.only(bottom: 12),
      child: InkWell(
        onTap: () => context.push('/sponsorship-timeline/${sp.id}'),
        borderRadius: BorderRadius.circular(20),
        child: Padding(
          padding: const EdgeInsets.all(14),
          child: Row(
            children: [
              Container(
                width: 60,
                height: 60,
                decoration: BoxDecoration(
                  borderRadius: BorderRadius.circular(14),
                  color: AppColors.primaryLight.withOpacity(0.2),
                ),
                child: sp.orphanImage != null
                    ? ClipRRect(
                        borderRadius: BorderRadius.circular(14),
                        child: CachedNetworkImage(
                          imageUrl: sp.orphanImage!,
                          fit: BoxFit.cover,
                          placeholder: (_, __) => _avatarPlaceholder(sp),
                          errorWidget: (_, __, ___) => _avatarPlaceholder(sp),
                        ),
                      )
                    : _avatarPlaceholder(sp),
              ),
              const SizedBox(width: 14),
              Expanded(
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Text(
                      sp.orphanName ?? '---',
                      style: TextStyle(
                        fontSize: 15,
                        fontWeight: FontWeight.w600,
                        color: isDark ? Colors.white : AppColors.neutral900,
                      ),
                    ),
                    const SizedBox(height: 4),
                    Row(
                      children: [
                        _statusBadge(sp.status),
                        const SizedBox(width: 8),
                        Text(
                          '${sp.monthlyAmount.toStringAsFixed(0)} ${AppStrings.get('sar')}',
                          style: const TextStyle(
                            fontSize: 13,
                            fontWeight: FontWeight.w600,
                            color: AppColors.primary,
                          ),
                        ),
                      ],
                    ),
                    const SizedBox(height: 4),
                    Text(
                      '${AppStrings.get('start_date')}: ${sp.startDate ?? '---'}',
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
        ),
      ),
    );
  }

  Widget _avatarPlaceholder(SponsorshipModel sp) {
    return Container(
      decoration: BoxDecoration(
        color: AppColors.primaryLight.withOpacity(0.2),
        borderRadius: BorderRadius.circular(14),
      ),
      alignment: Alignment.center,
      child: Text(
        (sp.orphanName ?? '?').isNotEmpty
            ? sp.orphanName![0].toUpperCase()
            : '?',
        style: const TextStyle(
          fontSize: 22,
          fontWeight: FontWeight.bold,
          color: AppColors.primary,
        ),
      ),
    );
  }

  Widget _statusBadge(String status) {
    Color color;
    String labelKey;
    switch (status) {
      case 'active':
        color = AppColors.success;
        labelKey = 'active';
        break;
      case 'inactive':
      case 'pending':
        color = AppColors.warning;
        labelKey = 'pending';
        break;
      case 'ended':
      case 'completed':
      case 'stopped':
        color = AppColors.info;
        labelKey = 'completed';
        break;
      default:
        color = AppColors.neutral500;
        labelKey = status;
    }
    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 3),
      decoration: BoxDecoration(
        color: color.withOpacity(0.15),
        borderRadius: BorderRadius.circular(8),
      ),
      child: Text(
        AppStrings.get(labelKey),
        style: TextStyle(
          fontSize: 11,
          fontWeight: FontWeight.w600,
          color: color,
        ),
      ),
    );
  }

  Widget _buildSkeletons() {
    return ListView.builder(
      padding: const EdgeInsets.all(16),
      itemCount: 4,
      itemBuilder: (_, __) => const ShimmerCard(),
    );
  }
}
