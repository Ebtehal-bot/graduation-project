import 'package:flutter/foundation.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';

import '../../../data/models/orphan_model.dart';
import '../../../data/models/sponsorship_model.dart';
import '../../../data/repositories/orphans_repository.dart';
import '../../../data/repositories/sponsorship_repository.dart';

class OrphansListState {
  final List<OrphanModel> orphans;
  final bool isLoading;
  final String? error;

  const OrphansListState({
    this.orphans = const [],
    this.isLoading = false,
    this.error,
  });

  OrphansListState copyWith({
    List<OrphanModel>? orphans,
    bool? isLoading,
    String? error,
  }) {
    return OrphansListState(
      orphans: orphans ?? this.orphans,
      isLoading: isLoading ?? this.isLoading,
      error: error,
    );
  }
}

class OrphansNotifier extends StateNotifier<OrphansListState> {
  final OrphansRepository _repository;
  final SponsorshipRepository _sponsorshipRepository;
  List<OrphanModel> _allOrphans = [];
  String _searchQuery = '';
  String? _filterStatus;
  Set<int> _mySponsoredOrphanIds = {};

  OrphansNotifier(this._repository, this._sponsorshipRepository)
      : super(const OrphansListState());

  Future<void> _loadMySponsoredOrphanIds() async {
    try {
      final data = await _sponsorshipRepository.getUserSponsorships();
      final ids = <int>{};
      for (final item in data) {
        if (item is Map<String, dynamic>) {
          try {
            final sponsorship = SponsorshipModel.fromJson(item);
            if (sponsorship.orphanId > 0) {
              ids.add(sponsorship.orphanId);
            }
          } catch (e) {
            debugPrint('[OrphansNotifier] SKIP sponsorship item: $e');
          }
        }
      }
      _mySponsoredOrphanIds = ids;
      debugPrint(
          '[OrphansNotifier] Loaded ${ids.length} sponsored orphan IDs: $ids');
    } catch (e) {
      debugPrint(
          '[OrphansNotifier] Failed to load sponsored orphan IDs: $e');
      _mySponsoredOrphanIds = {};
    }
  }

  Future<void> loadOrphans() async {
    state = state.copyWith(isLoading: true, error: null);
    try {
      final data = await _repository.getOrphans();
      debugPrint('[OrphansNotifier] Repository returned ${data.length} items');
      final List<OrphanModel> parsed = [];
      int skippedNotMap = 0;
      int skippedParseError = 0;
      for (int i = 0; i < data.length; i++) {
        final e = data[i];
        if (e is! Map<String, dynamic>) {
          skippedNotMap++;
          debugPrint(
              '[OrphansNotifier] SKIP item $i (not a Map): ${e.runtimeType} value=$e');
          continue;
        }
        try {
          final model = OrphanModel.fromJson(e);
          parsed.add(model);
        } catch (itemError) {
          skippedParseError++;
          debugPrint('[OrphansNotifier] SKIP item $i: $itemError');
          debugPrint('[OrphansNotifier]   Item JSON: $e');
        }
      }
      debugPrint(
          '[OrphansNotifier] PARSING SUMMARY: raw=${data.length} parsed=${parsed.length} skippedNotMap=$skippedNotMap skippedParseError=$skippedParseError');
      _allOrphans = parsed;
      _searchQuery = '';
      _filterStatus = null;
      await _loadMySponsoredOrphanIds();
      _applyFilters();
      state = state.copyWith(isLoading: false);
    } catch (e, stack) {
      debugPrint('[OrphansNotifier] loadOrphans Error: $e\n$stack');
      state = state.copyWith(isLoading: false, error: e.toString());
    }
  }

  Future<void> loadAvailableOrphans() async {
    state = state.copyWith(isLoading: true, error: null);
    try {
      final data = await _repository.getAvailableOrphans();
      debugPrint(
          '[OrphansNotifier] loadAvailableOrphans raw items: ${data.length}');
      final List<OrphanModel> parsed = [];
      for (int i = 0; i < data.length; i++) {
        final e = data[i];
        if (e is! Map<String, dynamic>) {
          debugPrint(
              '[OrphansNotifier] Available item $i is not a Map: ${e.runtimeType}');
          continue;
        }
        try {
          final model = OrphanModel.fromJson(e);
          parsed.add(model);
        } catch (itemError) {
          debugPrint(
              '[OrphansNotifier] Skipping available item $i: $itemError');
        }
      }
      _allOrphans = parsed;
      _searchQuery = '';
      _filterStatus = null;
      await _loadMySponsoredOrphanIds();
      _applyFilters();
      state = state.copyWith(isLoading: false);
    } catch (e, stack) {
      debugPrint('[OrphansNotifier] loadAvailableOrphans Error: $e\n$stack');
      state = state.copyWith(isLoading: false, error: e.toString());
    }
  }

  Future<void> refreshOrphans() async {
    debugPrint('[OrphansNotifier] === refreshOrphans ===');
    await loadOrphans();
  }

  void _applyFilters() {
    List<OrphanModel> result = List.from(_allOrphans);

    if (_searchQuery.isNotEmpty) {
      result = result
          .where(
              (o) => o.name.toLowerCase().contains(_searchQuery.toLowerCase()))
          .toList();
    }

    if (_filterStatus == 'sponsored') {
      result = result
          .where((o) => _mySponsoredOrphanIds.contains(o.id))
          .toList();
    } else if (_filterStatus == 'unsponsored') {
      result = result.where((o) => !o.isSponsored).toList();
    } else {
      result = result.where((o) {
        return _mySponsoredOrphanIds.contains(o.id) || !o.isSponsored;
      }).toList();
    }

    debugPrint(
        '[OrphansNotifier] _applyFilters: search="$_searchQuery" filter="$_filterStatus" '
        'result=${result.length} of ${_allOrphans.length} mySponsored=${_mySponsoredOrphanIds.length}');
    state = state.copyWith(orphans: result);
  }

  void search(String query) {
    debugPrint(
        '[OrphansNotifier] search query="$query" _allOrphans=${_allOrphans.length}');
    _searchQuery = query;
    _applyFilters();
  }

  void filterByStatus(String? status) {
    debugPrint(
        '[OrphansNotifier] filterByStatus status=$status _allOrphans=${_allOrphans.length}');
    _filterStatus = status;
    _applyFilters();
  }

  void sortBy(String field) {
    debugPrint(
        '[OrphansNotifier] sortBy field=$field currentView=${state.orphans.length}');
    final sorted = List<OrphanModel>.from(state.orphans);
    switch (field) {
      case 'name':
        sorted.sort((a, b) => a.name.compareTo(b.name));
        break;
      case 'age':
        sorted.sort((a, b) => (a.age ?? 0).compareTo(b.age ?? 0));
        break;
      case 'newest':
        sorted.sort((a, b) {
          if (a.createdAt == null && b.createdAt == null) return 0;
          if (a.createdAt == null) return 1;
          if (b.createdAt == null) return -1;
          return b.createdAt!.compareTo(a.createdAt!);
        });
        break;
    }
    state = state.copyWith(orphans: sorted);
  }

  void resetFilters() {
    _searchQuery = '';
    _filterStatus = null;
    _applyFilters();
    debugPrint(
        '[OrphansNotifier] resetFilters: showing filtered ${state.orphans.length} orphans');
  }
}

final orphansProvider =
    StateNotifierProvider<OrphansNotifier, OrphansListState>((ref) {
  final notifier = OrphansNotifier(
    ref.watch(orphansRepositoryProvider),
    ref.watch(sponsorshipRepositoryProvider),
  );
  Future.microtask(() => notifier.loadOrphans());
  return notifier;
});

final orphanDetailsProvider =
    FutureProvider.family<OrphanModel, int>((ref, id) async {
  final repository = ref.watch(orphansRepositoryProvider);
  final data = await repository.getOrphanDetails(id);
  return OrphanModel.fromJson(data);
});
