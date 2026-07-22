import 'package:flutter/foundation.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';

import '../../../core/constants/api_endpoints.dart';
import '../../auth/providers/auth_provider.dart';
import '../../../data/models/notification_model.dart';
import '../../../data/repositories/notifications_repository.dart';

class NotificationState {
  final List<NotificationModel> notifications;
  final bool isLoading;
  final String? error;
  final int unreadCount;

  const NotificationState({
    this.notifications = const [],
    this.isLoading = false,
    this.error,
    this.unreadCount = 0,
  });

  NotificationState copyWith({
    List<NotificationModel>? notifications,
    bool? isLoading,
    String? error,
    int? unreadCount,
  }) {
    return NotificationState(
      notifications: notifications ?? this.notifications,
      isLoading: isLoading ?? this.isLoading,
      error: error,
      unreadCount: unreadCount ?? this.unreadCount,
    );
  }
}

class NotificationNotifier extends StateNotifier<NotificationState> {
  final NotificationsRepository _repository;
  final Ref _ref;

  NotificationNotifier(this._repository, this._ref)
      : super(const NotificationState());

  Future<void> loadNotifications() async {
    state = state.copyWith(isLoading: true, error: null);
    try {
      final authState = _ref.read(authProvider);
      debugPrint('[NotificationNotifier] === loadNotifications ===');
      debugPrint('[NotificationNotifier] Auth: userId=${authState.userId} '
          'userName=${authState.userName} '
          'isAuthenticated=${authState.isAuthenticated} token=${authState.token?.substring(0, 10)}...');
      debugPrint('[NotificationNotifier] API: GET ${ApiEndpoints.notifications}');

      final data = await _repository.getNotifications();
      debugPrint('[NotificationNotifier] Raw items from API: ${data.length}');

      final List<NotificationModel> allNotifications = [];
      for (int i = 0; i < data.length; i++) {
        final e = data[i];
        if (e is! Map<String, dynamic>) {
          debugPrint('[NotificationNotifier] Skipping item $i: not a Map (${e.runtimeType})');
          continue;
        }
        try {
          final parsed = NotificationModel.fromJson(e);
          allNotifications.add(parsed);
          if (i < 3) {
            debugPrint('[NotificationNotifier] Parsed notification #$i: id=${parsed.id} type=${parsed.type} title=${parsed.title}');
          }
        } catch (itemError) {
          debugPrint('[NotificationNotifier] Skipping notification item $i: $itemError');
        }
      }
      debugPrint('[NotificationNotifier] Parsed total: ${allNotifications.length}');
      if (allNotifications.isNotEmpty) {
        debugPrint('[NotificationNotifier] Types breakdown: ${allNotifications.fold<Map<String, int>>({}, (map, n) { map[n.type] = (map[n.type] ?? 0) + 1; return map; })}');
      }

      final unread = allNotifications.where((n) => !n.isRead).length;
      debugPrint('[NotificationNotifier] Final state: ${allNotifications.length} notifications, $unread unread');

      state = NotificationState(
        notifications: allNotifications,
        unreadCount: unread,
        isLoading: false,
      );
    } catch (e, stack) {
      debugPrint('[NotificationNotifier] loadNotifications Error: $e\n$stack');
      state = state.copyWith(
        isLoading: false,
        error: e.toString(),
      );
    }
  }

  Future<void> loadUnreadCount() async {
    try {
      final count = await _repository.getUnreadCount();
      debugPrint('[NotificationNotifier] loadUnreadCount: $count');
      state = state.copyWith(unreadCount: count);
    } catch (e) {
      debugPrint('[NotificationNotifier] loadUnreadCount error: $e');
    }
  }

  Future<void> markAsRead(String notificationId) async {
    debugPrint(
        '[NotificationNotifier] markAsRead: $notificationId');
    final updated = state.notifications.map((n) {
      if (n.id == notificationId) {
        return NotificationModel(
          id: n.id,
          title: n.title,
          body: n.body,
          type: n.type,
          isRead: true,
          createdAt: n.createdAt,
        );
      }
      return n;
    }).toList();
    state = state.copyWith(
      notifications: updated,
      unreadCount: updated.where((n) => !n.isRead).length,
    );

    try {
      await _repository.markAsRead(notificationId);
    } catch (e) {
      debugPrint(
          '[NotificationNotifier] markAsRead API error: $e');
    }
  }

  Future<void> markAllAsRead() async {
    debugPrint('[NotificationNotifier] markAllAsRead');
    final updated = state.notifications.map((n) {
      return NotificationModel(
        id: n.id,
        title: n.title,
        body: n.body,
        type: n.type,
        isRead: true,
        createdAt: n.createdAt,
      );
    }).toList();
    state = state.copyWith(
      notifications: updated,
      unreadCount: 0,
    );

    try {
      await _repository.markAllAsRead();
    } catch (e) {
      debugPrint(
          '[NotificationNotifier] markAllAsRead API error: $e');
    }
  }
}

final notificationsProvider =
    StateNotifierProvider<NotificationNotifier, NotificationState>((ref) {
  final notifier = NotificationNotifier(ref.watch(notificationsRepositoryProvider), ref);
  Future.microtask(() => notifier.loadNotifications());
  return notifier;
});

final unreadCountProvider = Provider<int>((ref) {
  return ref.watch(notificationsProvider).unreadCount;
});
