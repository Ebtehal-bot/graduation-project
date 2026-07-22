import 'package:flutter/foundation.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';

import '../../../data/models/sponsorship_model.dart';
import '../../../data/repositories/sponsorship_repository.dart';

class SponsorshipsListState {
  final List<SponsorshipModel> sponsorships;
  final bool isLoading;
  final String? error;

  const SponsorshipsListState({
    this.sponsorships = const [],
    this.isLoading = false,
    this.error,
  });

  SponsorshipsListState copyWith({
    List<SponsorshipModel>? sponsorships,
    bool? isLoading,
    String? error,
  }) {
    return SponsorshipsListState(
      sponsorships: sponsorships ?? this.sponsorships,
      isLoading: isLoading ?? this.isLoading,
      error: error,
    );
  }

  List<SponsorshipModel> get active =>
      sponsorships.where((s) => s.status == 'active').toList();
  List<SponsorshipModel> get pending =>
      sponsorships.where((s) => s.status == 'inactive').toList();
  List<SponsorshipModel> get completed => sponsorships
      .where((s) =>
          s.status == 'ended' ||
          s.status == 'completed' ||
          s.status == 'stopped')
      .toList();
}

class SponsorshipsNotifier extends StateNotifier<SponsorshipsListState> {
  final SponsorshipRepository _repository;

  SponsorshipsNotifier(this._repository) : super(const SponsorshipsListState());

  Future<void> loadSponsorships() async {
    state = state.copyWith(isLoading: true, error: null);
    try {
      debugPrint('[SponsorshipsNotifier] === loadSponsorships ===');
      final data = await _repository.getUserSponsorships();
      debugPrint('[SponsorshipsNotifier] Raw items: ${data.length}');
      final sponsorships = data.whereType<Map<String, dynamic>>().map((e) {
        final s = SponsorshipModel.fromJson(e);
        debugPrint(
            '[SponsorshipsNotifier] Parsed: id=${s.id} status=${s.status} orphan=${s.orphanName}');
        return s;
      }).toList();
      debugPrint('[SponsorshipsNotifier] Final count: ${sponsorships.length}');
      state = SponsorshipsListState(sponsorships: sponsorships);
    } catch (e) {
      debugPrint('[SponsorshipsNotifier] Error: $e');
      state = state.copyWith(isLoading: false, error: e.toString());
    }
  }

  Future<bool> createSponsorship({
    required int orphanId,
    required double monthlyAmount,
    required String sponsorshipType,
  }) async {
    try {
      await _repository.createSponsorship(
        orphanId: orphanId,
        monthlyAmount: monthlyAmount,
        sponsorshipType: sponsorshipType,
      );
      await loadSponsorships();
      return true;
    } catch (e) {
      rethrow;
    }
  }
}

final mySponsorshipsProvider =
    StateNotifierProvider<SponsorshipsNotifier, SponsorshipsListState>((ref) {
  return SponsorshipsNotifier(ref.watch(sponsorshipRepositoryProvider));
});

final sponsorshipDetailProvider =
    FutureProvider.family<SponsorshipModel, int>((ref, id) async {
  final repository = ref.watch(sponsorshipRepositoryProvider);
  final data = await repository.getSponsorshipDetails(id);
  return SponsorshipModel.fromJson(data);
});
