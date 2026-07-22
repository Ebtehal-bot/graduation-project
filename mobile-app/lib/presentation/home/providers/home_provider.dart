import 'package:flutter/foundation.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';

import '../../../data/models/orphan_model.dart';
import '../../../data/models/dashboard_stats_model.dart';
import '../../../data/repositories/sponsorship_repository.dart';
import '../../../data/repositories/orphans_repository.dart';

class HomeState {
  final DashboardStatsModel? stats;
  final List<Map<String, dynamic>> recentActivities;
  final List<OrphanModel> recentOrphans;
  final bool isLoading;
  final String? error;

  const HomeState({
    this.stats,
    this.recentActivities = const [],
    this.recentOrphans = const [],
    this.isLoading = false,
    this.error,
  });

  HomeState copyWith({
    DashboardStatsModel? stats,
    List<Map<String, dynamic>>? recentActivities,
    List<OrphanModel>? recentOrphans,
    bool? isLoading,
    String? error,
  }) {
    return HomeState(
      stats: stats ?? this.stats,
      recentActivities: recentActivities ?? this.recentActivities,
      recentOrphans: recentOrphans ?? this.recentOrphans,
      isLoading: isLoading ?? this.isLoading,
      error: error,
    );
  }
}

class HomeNotifier extends StateNotifier<HomeState> {
  final SponsorshipRepository _repository;
  final OrphansRepository _orphansRepository;

  HomeNotifier(this._repository, this._orphansRepository)
      : super(const HomeState());

  DateTime _extractActivityDate(Map<String, dynamic> activity) {
    for (final key in ['created_at', 'timestamp', 'date', 'createdAt', 'datetime', 'updated_at']) {
      final val = activity[key];
      if (val is String) {
        final parsed = DateTime.tryParse(val);
        if (parsed != null) return parsed;
      }
      if (val is DateTime) return val;
      if (val is num) {
        try {
          return DateTime.fromMillisecondsSinceEpoch(val.toInt() * 1000);
        } catch (_) {}
      }
    }
    return DateTime.now();
  }

  Future<void> loadDashboard() async {
    state = state.copyWith(isLoading: true, error: null);
    debugPrint('[HomeNotifier] === DASHBOARD REFRESH STARTED ===');
    try {
      debugPrint('[HomeNotifier] === API CALLED ===');
      final statsData = await _repository.getDashboardStats();
      debugPrint('[HomeNotifier] === API RESPONSE RECEIVED ===');
      debugPrint('[HomeNotifier] Stats data keys: ${statsData.keys}');
      final stats = DashboardStatsModel.fromJson(statsData);

      debugPrint('[HomeNotifier] Dashboard values: totalOrphans=${stats.totalOrphans} sponsored=${stats.sponsoredOrphans} unsponsored=${stats.unsponsoredOrphans} active=${stats.activeSponsorships} pending=${stats.pendingSponsorships} completed=${stats.completedSponsorships}');

      debugPrint('[HomeNotifier] === RECENT ACTIVITIES API CALLED ===');
      List<Map<String, dynamic>> activities = [];
      try {
        final activitiesData = await _repository.getDashboardRecentActivities();
        debugPrint('[HomeNotifier] RECENT ACTIVITIES COUNT: ${activitiesData.length}');
        activities = activitiesData
            .whereType<Map<String, dynamic>>()
            .toList();
        activities.sort((a, b) {
          final dateA = _extractActivityDate(a);
          final dateB = _extractActivityDate(b);
          return dateB.compareTo(dateA);
        });
        debugPrint('[HomeNotifier] RECENT ACTIVITIES DISPLAYED: ${activities.length}');
      } catch (e) {
        debugPrint('[HomeNotifier] Activities fetch error: $e');
      }

      List<OrphanModel> orphans = [];
      try {
        final orphansData = await _orphansRepository.getAvailableOrphans();
        debugPrint('[HomeNotifier] Available orphans raw: ${orphansData.length}');
        orphans = orphansData
            .whereType<Map<String, dynamic>>()
            .map((e) => OrphanModel.fromJson(e))
            .take(5)
            .toList();
        debugPrint('[HomeNotifier] Recent orphans parsed: ${orphans.length}');
      } catch (e) {
        debugPrint('[HomeNotifier] Available orphans error: $e, trying getOrphans()');
        try {
          final orphansData = await _orphansRepository.getOrphans();
          debugPrint('[HomeNotifier] Orphans raw: ${orphansData.length}');
          orphans = orphansData
              .whereType<Map<String, dynamic>>()
              .map((e) => OrphanModel.fromJson(e))
              .take(5)
              .toList();
          debugPrint('[HomeNotifier] Recent orphans from fallback: ${orphans.length}');
        } catch (e2) {
          debugPrint('[HomeNotifier] Orphans fallback error: $e2');
        }
      }

      state = HomeState(
        stats: stats,
        recentActivities: activities,
        recentOrphans: orphans,
        isLoading: false,
      );
      debugPrint('[HomeNotifier] === STATE UPDATED ===');
      debugPrint('[HomeNotifier] Dashboard loaded: stats totalOrphans=${stats.totalOrphans} sponsored=${stats.sponsoredOrphans} unsponsored=${stats.unsponsoredOrphans} activities=${activities.length} orphans=${orphans.length}');
    } catch (e) {
      debugPrint('[HomeNotifier] loadDashboard error: $e');
      state = state.copyWith(
        isLoading: false,
        error: e.toString(),
      );
    }
  }
}

final homeProvider = StateNotifierProvider<HomeNotifier, HomeState>((ref) {
  return HomeNotifier(
    ref.watch(sponsorshipRepositoryProvider),
    ref.watch(orphansRepositoryProvider),
  );
});
