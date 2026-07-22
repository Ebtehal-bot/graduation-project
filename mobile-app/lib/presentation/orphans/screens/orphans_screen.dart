import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:go_router/go_router.dart';
import 'package:cached_network_image/cached_network_image.dart';

import '../../../core/localization/app_strings.dart';
import '../../../core/theme/app_colors.dart';
import '../../../data/models/orphan_model.dart';
import '../../../shared/widgets/widgets.dart';
import '../providers/orphans_provider.dart';

class OrphansScreen extends ConsumerStatefulWidget {
  const OrphansScreen({super.key});

  @override
  ConsumerState<OrphansScreen> createState() => _OrphansScreenState();
}

class _OrphansScreenState extends ConsumerState<OrphansScreen>
    with WidgetsBindingObserver {
  final _searchController = TextEditingController();
  String _selectedStatus = 'all';

  @override
  void initState() {
    super.initState();
    WidgetsBinding.instance.addObserver(this);
  }

  @override
  void dispose() {
    WidgetsBinding.instance.removeObserver(this);
    _searchController.dispose();
    super.dispose();
  }

  @override
  void didChangeAppLifecycleState(AppLifecycleState lifecycle) {
    if (lifecycle == AppLifecycleState.resumed) {
      debugPrint('[OrphansScreen] App resumed, refreshing orphans');
      ref.read(orphansProvider.notifier).refreshOrphans();
    }
  }

  @override
  Widget build(BuildContext context) {
    final state = ref.watch(orphansProvider);
    final isDark = Theme.of(context).brightness == Brightness.dark;

    debugPrint('[OrphansScreen] build: orphans=${state.orphans.length} '
        'isLoading=${state.isLoading} error=${state.error != null}');

    return Scaffold(
      appBar: AppBar(
        title: Text(AppStrings.get('nav_orphans')),
        actions: [
          PopupMenuButton<String>(
            icon: const Icon(Icons.sort_rounded),
            onSelected: (value) {
              ref.read(orphansProvider.notifier).sortBy(value);
            },
            itemBuilder: (_) => [
              PopupMenuItem(
                value: 'newest',
                child: Text(AppStrings.get('newest_first')),
              ),
              PopupMenuItem(
                value: 'name',
                child: Text(AppStrings.get('sort_by_name')),
              ),
              PopupMenuItem(
                value: 'age',
                child: Text(AppStrings.get('sort_by_age')),
              ),
            ],
          ),
        ],
      ),
      body: Column(
        children: [
          _buildSearchAndFilters(isDark),
          Expanded(
            child: state.isLoading
                ? _buildSkeletons(isDark)
                : RefreshIndicator(
                    onRefresh: () => ref
                        .read(orphansProvider.notifier)
                        .refreshOrphans(),
                    child: state.error != null
                        ? SingleChildScrollView(
                            physics: const AlwaysScrollableScrollPhysics(),
                            child: AppErrorState(
                              message: state.error,
                              onRetry: () => ref
                                  .read(orphansProvider.notifier)
                                  .refreshOrphans(),
                            ),
                          )
                        : state.orphans.isEmpty
                            ? SingleChildScrollView(
                                physics:
                                    const AlwaysScrollableScrollPhysics(),
                                child: Center(
                                  child: AppEmptyState(
                                    icon: Icons.child_care_rounded,
                                    title:
                                        AppStrings.get('no_orphans_found'),
                                    subtitle: AppStrings.get(
                                        'no_orphans_found_desc'),
                                    actionLabel:
                                        AppStrings.get('retry'),
                                    onAction: () => ref
                                        .read(orphansProvider.notifier)
                                        .refreshOrphans(),
                                  ),
                                ),
                              )
                            : ListView.builder(
                                padding: const EdgeInsets.symmetric(
                                    horizontal: 16, vertical: 8),
                                itemCount: state.orphans.length,
                                itemBuilder: (context, index) {
                                  return _buildOrphanCard(
                                      state.orphans[index], isDark);
                                },
                              ),
                  ),
          ),
        ],
      ),
    );
  }

  Widget _buildSearchAndFilters(bool isDark) {
    return Padding(
      padding: const EdgeInsets.fromLTRB(16, 12, 16, 8),
      child: Column(
        children: [
          TextField(
            controller: _searchController,
            decoration: InputDecoration(
              hintText: AppStrings.get('search_orphans'),
              prefixIcon: const Icon(Icons.search_rounded),
              suffixIcon: _searchController.text.isNotEmpty
                  ? IconButton(
                      icon: const Icon(Icons.clear),
                      onPressed: () {
                        _searchController.clear();
                        setState(() => _selectedStatus = 'all');
                        ref.read(orphansProvider.notifier).resetFilters();
                      },
                    )
                  : null,
            ),
            onChanged: (value) {
              setState(() {});
              ref.read(orphansProvider.notifier).search(value);
            },
          ),
          const SizedBox(height: 12),
          SizedBox(
            height: 38,
            child: ListView(
              scrollDirection: Axis.horizontal,
              children: [
                _filterChip(
                  AppStrings.get('all'),
                  _selectedStatus == 'all',
                  () {
                    setState(() {
                      _selectedStatus = 'all';
                      _searchController.clear();
                    });
                    ref.read(orphansProvider.notifier).filterByStatus(null);
                  },
                ),
                const SizedBox(width: 8),
                _filterChip(
                  AppStrings.get('sponsored'),
                  _selectedStatus == 'sponsored',
                  () {
                    setState(() {
                      _selectedStatus = 'sponsored';
                      _searchController.clear();
                    });
                    ref
                        .read(orphansProvider.notifier)
                        .filterByStatus('sponsored');
                  },
                ),
                const SizedBox(width: 8),
                _filterChip(
                  AppStrings.get('needs_sponsorship'),
                  _selectedStatus == 'unsponsored',
                  () {
                    setState(() {
                      _selectedStatus = 'unsponsored';
                      _searchController.clear();
                    });
                    ref
                        .read(orphansProvider.notifier)
                        .filterByStatus('unsponsored');
                  },
                ),
              ],
            ),
          ),
        ],
      ),
    );
  }

  Widget _filterChip(String label, bool selected, VoidCallback onTap) {
    return GestureDetector(
      onTap: onTap,
      child: Container(
        padding: const EdgeInsets.symmetric(horizontal: 16),
        decoration: BoxDecoration(
          color: selected
              ? AppColors.primary
              : Theme.of(context).brightness == Brightness.dark
                  ? AppColors.darkCard
                  : AppColors.neutral100,
          borderRadius: BorderRadius.circular(20),
        ),
        alignment: Alignment.center,
        child: Text(
          label,
          style: TextStyle(
            fontSize: 13,
            fontWeight: selected ? FontWeight.w600 : FontWeight.normal,
            color: selected ? Colors.white : AppColors.neutral600,
          ),
        ),
      ),
    );
  }

  Widget _buildOrphanCard(OrphanModel orphan, bool isDark) {
    return Card(
      margin: const EdgeInsets.only(bottom: 12),
      child: InkWell(
        onTap: () => context.push('/orphans/${orphan.id}'),
        borderRadius: BorderRadius.circular(20),
        child: Padding(
          padding: const EdgeInsets.all(12),
          child: Row(
            children: [
              Hero(
                tag: 'orphan_${orphan.id}',
                child: Container(
                  width: 72,
                  height: 72,
                  decoration: BoxDecoration(
                    borderRadius: BorderRadius.circular(16),
                    color: AppColors.primaryLight.withOpacity(0.2),
                  ),
                  child: orphan.photo != null
                      ? ClipRRect(
                          borderRadius: BorderRadius.circular(16),
                          child: CachedNetworkImage(
                            imageUrl: orphan.photo!,
                            fit: BoxFit.cover,
                            placeholder: (_, __) => _orphanAvatar(orphan),
                            errorWidget: (_, __, ___) => _orphanAvatar(orphan),
                          ),
                        )
                      : _orphanAvatar(orphan),
                ),
              ),
              const SizedBox(width: 14),
              Expanded(
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Text(
                      orphan.name,
                      maxLines: 1,
                      overflow: TextOverflow.ellipsis,
                      style: TextStyle(
                        fontSize: 16,
                        fontWeight: FontWeight.w600,
                        color: isDark ? Colors.white : AppColors.neutral900,
                      ),
                    ),
                    const SizedBox(height: 6),
                    Wrap(
                      spacing: 8,
                      runSpacing: 4,
                      children: [
                        _infoChip(Icons.person_outline, orphan.genderLabel),
                        if (orphan.age != null)
                          _infoChip(Icons.cake_outlined,
                              '${orphan.age} ${AppStrings.get('years_old')}'),
                      ],
                    ),
                    const SizedBox(height: 6),
                    Wrap(
                      spacing: 8,
                      runSpacing: 4,
                      children: [
                        if (orphan.branchName != null &&
                            orphan.branchName!.isNotEmpty)
                          _infoChip(
                              Icons.location_on_outlined, orphan.branchName!),
                        _buildStatusBadge(orphan),
                      ],
                    ),
                  ],
                ),
              ),
              if (!orphan.isSponsored)
                Container(
                  margin: const EdgeInsets.only(right: 8),
                  padding:
                      const EdgeInsets.symmetric(horizontal: 8, vertical: 4),
                  decoration: BoxDecoration(
                    color: AppColors.primary.withOpacity(0.1),
                    borderRadius: BorderRadius.circular(8),
                  ),
                  child: Text(
                    AppStrings.get('sponsor_now'),
                    style: const TextStyle(
                      fontSize: 11,
                      fontWeight: FontWeight.w600,
                      color: AppColors.primary,
                    ),
                  ),
                ),
              Icon(
                Icons.arrow_forward_ios,
                size: 14,
                color: isDark ? Colors.grey[600] : AppColors.neutral400,
              ),
            ],
          ),
        ),
      ),
    );
  }

  Widget _orphanAvatar(OrphanModel orphan) {
    return Container(
      decoration: BoxDecoration(
        color: AppColors.primaryLight.withOpacity(0.2),
        borderRadius: BorderRadius.circular(16),
      ),
      alignment: Alignment.center,
      child: Text(
        orphan.name.isNotEmpty ? orphan.name[0].toUpperCase() : '?',
        style: const TextStyle(
          fontSize: 28,
          fontWeight: FontWeight.bold,
          color: AppColors.primary,
        ),
      ),
    );
  }

  Widget _infoChip(IconData icon, String label) {
    return Row(
      mainAxisSize: MainAxisSize.min,
      children: [
        Icon(icon, size: 14, color: AppColors.neutral500),
        const SizedBox(width: 4),
        ConstrainedBox(
          constraints: const BoxConstraints(maxWidth: 200),
          child: Text(
            label,
            maxLines: 1,
            overflow: TextOverflow.ellipsis,
            style: const TextStyle(
              fontSize: 12,
              color: AppColors.neutral500,
            ),
          ),
        ),
      ],
    );
  }

  Widget _buildStatusBadge(OrphanModel orphan) {
    final isSponsored = orphan.isSponsored;
    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 4),
      decoration: BoxDecoration(
        color: isSponsored
            ? AppColors.success.withOpacity(0.15)
            : AppColors.warning.withOpacity(0.15),
        borderRadius: BorderRadius.circular(12),
      ),
      child: Text(
        isSponsored
            ? AppStrings.get('sponsored')
            : AppStrings.get('needs_sponsorship'),
        style: TextStyle(
          fontSize: 11,
          fontWeight: FontWeight.w600,
          color: isSponsored ? AppColors.success : AppColors.warning,
        ),
      ),
    );
  }

  Widget _buildSkeletons(bool isDark) {
    return ListView.builder(
      padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 8),
      itemCount: 5,
      itemBuilder: (_, __) => const ShimmerCard(),
    );
  }
}
