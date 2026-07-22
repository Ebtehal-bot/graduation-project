import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:go_router/go_router.dart';
import 'package:cached_network_image/cached_network_image.dart';

import '../../../core/localization/app_strings.dart';
import '../../../core/theme/app_colors.dart';
import '../../../data/models/orphan_model.dart';
import '../../../data/repositories/orphans_repository.dart';
import '../../../shared/widgets/widgets.dart';
import '../providers/favorites_provider.dart';

class FavoritesScreen extends ConsumerStatefulWidget {
  const FavoritesScreen({super.key});

  @override
  ConsumerState<FavoritesScreen> createState() => _FavoritesScreenState();
}

class _FavoritesScreenState extends ConsumerState<FavoritesScreen> {
  List<OrphanModel>? _orphans;
  bool _isLoading = true;
  String? _error;

  @override
  void initState() {
    super.initState();
    WidgetsBinding.instance.addPostFrameCallback((_) {
      ref
          .read(favoritesProvider.notifier)
          .loadFavorites()
          .then((_) => _fetchFavoriteOrphans());
    });
  }

  Future<void> _fetchFavoriteOrphans() async {
    final favoriteIds = ref.read(favoritesProvider);
    if (favoriteIds.isEmpty) {
      setState(() {
        _orphans = [];
        _isLoading = false;
        _error = null;
      });
      return;
    }

    setState(() {
      _isLoading = true;
      _error = null;
    });

    try {
      final repository = ref.read(orphansRepositoryProvider);
      final data = await repository.getOrphans();
      final allOrphans = data
          .whereType<Map<String, dynamic>>()
          .map((e) => OrphanModel.fromJson(e))
          .toList();
      final filtered =
          allOrphans.where((o) => favoriteIds.contains(o.id)).toList();
      setState(() {
        _orphans = filtered;
        _isLoading = false;
      });
    } catch (e) {
      setState(() {
        _error = e.toString();
        _isLoading = false;
      });
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: Text(AppStrings.get('favorites')),
      ),
      body: _buildBody(),
    );
  }

  Widget _buildBody() {
    if (_isLoading) {
      return ListView.builder(
        padding: const EdgeInsets.all(16),
        itemCount: 3,
        itemBuilder: (_, __) => const ShimmerCard(),
      );
    }

    if (_error != null && _orphans == null) {
      return AppErrorState(
        message: _error,
        onRetry: _fetchFavoriteOrphans,
      );
    }

    final orphans = _orphans ?? [];

    if (orphans.isEmpty) {
      return AppEmptyState(
        icon: Icons.favorite_border,
        title: AppStrings.get('no_favorites'),
        subtitle: AppStrings.get('no_favorites_desc'),
      );
    }

    return RefreshIndicator(
      onRefresh: _fetchFavoriteOrphans,
      child: ListView.builder(
        padding: const EdgeInsets.all(16),
        itemCount: orphans.length,
        itemBuilder: (context, index) {
          final orphan = orphans[index];
          return _buildFavoriteCard(orphan);
        },
      ),
    );
  }

  Widget _buildFavoriteCard(OrphanModel orphan) {
    final isDark = Theme.of(context).brightness == Brightness.dark;

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
                tag: 'favorite_${orphan.id}',
                child: Container(
                  width: 60,
                  height: 60,
                  decoration: BoxDecoration(
                    borderRadius: BorderRadius.circular(14),
                    color: AppColors.primaryLight.withOpacity(0.2),
                  ),
                  child: orphan.photo != null
                      ? ClipRRect(
                          borderRadius: BorderRadius.circular(14),
                          child: CachedNetworkImage(
                            imageUrl: orphan.photo!,
                            fit: BoxFit.cover,
                            placeholder: (_, __) => _avatarPlaceholder(orphan),
                            errorWidget: (_, __, ___) => _avatarPlaceholder(orphan),
                          ),
                        )
                      : _avatarPlaceholder(orphan),
                ),
              ),
              const SizedBox(width: 14),
              Expanded(
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Text(
                      orphan.name,
                      style: TextStyle(
                        fontSize: 16,
                        fontWeight: FontWeight.w600,
                        color: isDark ? Colors.white : AppColors.neutral900,
                      ),
                    ),
                    const SizedBox(height: 4),
                    Text(
                      orphan.statusLabel,
                      style: const TextStyle(
                        fontSize: 13,
                        color: AppColors.neutral500,
                      ),
                    ),
                  ],
                ),
              ),
              IconButton(
                icon: const Icon(Icons.favorite, color: AppColors.error),
                onPressed: () {
                  ref
                      .read(favoritesProvider.notifier)
                      .toggleFavorite(orphan.id);
                  _fetchFavoriteOrphans();
                },
              ),
            ],
          ),
        ),
      ),
    );
  }

  Widget _avatarPlaceholder(OrphanModel orphan) {
    return Container(
      decoration: BoxDecoration(
        color: AppColors.primaryLight.withOpacity(0.2),
        borderRadius: BorderRadius.circular(14),
      ),
      alignment: Alignment.center,
      child: Text(
        orphan.name.isNotEmpty ? orphan.name[0].toUpperCase() : '?',
        style: const TextStyle(
          fontSize: 24,
          fontWeight: FontWeight.bold,
          color: AppColors.primary,
        ),
      ),
    );
  }
}
