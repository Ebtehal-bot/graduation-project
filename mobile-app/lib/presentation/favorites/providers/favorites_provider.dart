import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:shared_preferences/shared_preferences.dart';
import 'dart:convert';

class FavoritesNotifier extends StateNotifier<List<int>> {
  FavoritesNotifier() : super([]);

  Future<void> loadFavorites() async {
    final prefs = await SharedPreferences.getInstance();
    final data = prefs.getString('favorite_orphans');
    if (data != null) {
      final list = jsonDecode(data) as List;
      state = list.map((e) => e as int).toList();
    }
  }

  Future<void> toggleFavorite(int orphanId) async {
    if (state.contains(orphanId)) {
      state = state.where((id) => id != orphanId).toList();
    } else {
      state = [...state, orphanId];
    }
    final prefs = await SharedPreferences.getInstance();
    await prefs.setString('favorite_orphans', jsonEncode(state));
  }

  bool isFavorite(int orphanId) => state.contains(orphanId);
}

final favoritesProvider = StateNotifierProvider<FavoritesNotifier, List<int>>((ref) {
  return FavoritesNotifier();
});
