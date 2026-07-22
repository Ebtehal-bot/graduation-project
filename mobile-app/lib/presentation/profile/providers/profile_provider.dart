import 'package:flutter_riverpod/flutter_riverpod.dart';

import '../../../data/models/user_model.dart';
import '../../../data/repositories/auth_repository.dart';

class ProfileState {
  final UserModel? user;
  final int activeSponsorships;
  final int totalSponsorships;
  final int sponsoredOrphans;
  final double totalDonations;
  final bool isLoading;
  final String? error;

  const ProfileState({
    this.user,
    this.activeSponsorships = 0,
    this.totalSponsorships = 0,
    this.sponsoredOrphans = 0,
    this.totalDonations = 0.0,
    this.isLoading = false,
    this.error,
  });

  ProfileState copyWith({
    UserModel? user,
    int? activeSponsorships,
    int? totalSponsorships,
    int? sponsoredOrphans,
    double? totalDonations,
    bool? isLoading,
    String? error,
  }) {
    return ProfileState(
      user: user ?? this.user,
      activeSponsorships: activeSponsorships ?? this.activeSponsorships,
      totalSponsorships: totalSponsorships ?? this.totalSponsorships,
      sponsoredOrphans: sponsoredOrphans ?? this.sponsoredOrphans,
      totalDonations: totalDonations ?? this.totalDonations,
      isLoading: isLoading ?? this.isLoading,
      error: error,
    );
  }
}

class ProfileNotifier extends StateNotifier<ProfileState> {
  final AuthRepository _authRepository;

  ProfileNotifier(this._authRepository)
      : super(const ProfileState());

  Future<void> loadProfile() async {
    state = state.copyWith(isLoading: true, error: null);
    try {
      final profileData = await _authRepository.getProfile();
      final user = UserModel.fromJson(profileData);

      state = ProfileState(
        user: user,
        activeSponsorships: user.activeSponsorships,
        totalSponsorships: user.totalSponsorships,
        sponsoredOrphans: user.sponsoredOrphans,
        totalDonations: user.totalDonations,
        isLoading: false,
      );
    } catch (e) {
      state = state.copyWith(isLoading: false, error: e.toString());
    }
  }
}

final profileProvider =
    StateNotifierProvider<ProfileNotifier, ProfileState>((ref) {
  return ProfileNotifier(
    ref.watch(authRepositoryProvider),
  );
});
