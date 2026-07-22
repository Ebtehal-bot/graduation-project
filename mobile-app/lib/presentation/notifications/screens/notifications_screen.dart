import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';

import '../../../core/localization/app_strings.dart';
import '../../../core/theme/app_colors.dart';
import '../../../data/models/notification_model.dart';
import '../../../shared/widgets/widgets.dart';
import '../providers/notifications_provider.dart';

class NotificationsScreen extends ConsumerStatefulWidget {
  const NotificationsScreen({super.key});

  @override
  ConsumerState<NotificationsScreen> createState() =>
      _NotificationsScreenState();
}

class _NotificationsScreenState extends ConsumerState<NotificationsScreen>
    with WidgetsBindingObserver {
  @override
  void initState() {
    super.initState();
    WidgetsBinding.instance.addObserver(this);
  }

  @override
  void dispose() {
    WidgetsBinding.instance.removeObserver(this);
    super.dispose();
  }

  @override
  void didChangeAppLifecycleState(AppLifecycleState lifecycle) {
    if (lifecycle == AppLifecycleState.resumed) {
      debugPrint('[NotificationsScreen] App resumed, refreshing notifications');
      ref.read(notificationsProvider.notifier).loadNotifications();
    }
  }

  @override
  Widget build(BuildContext context) {
    final state = ref.watch(notificationsProvider);
    final isDark = Theme.of(context).brightness == Brightness.dark;

    return Scaffold(
      appBar: AppBar(
        title: Text(AppStrings.get('notifications')),
        actions: [
          if (state.unreadCount > 0)
            TextButton(
              onPressed: () =>
                  ref.read(notificationsProvider.notifier).markAllAsRead(),
              child: Text(
                AppStrings.get('mark_all_read'),
                style: const TextStyle(color: AppColors.primary),
              ),
            ),
        ],
      ),
      body: state.isLoading && state.notifications.isEmpty
          ? _buildSkeletons(isDark)
          : RefreshIndicator(
              onRefresh: () => ref
                  .read(notificationsProvider.notifier)
                  .loadNotifications(),
              child: state.error != null && state.notifications.isEmpty
                  ? SingleChildScrollView(
                      physics: const AlwaysScrollableScrollPhysics(),
                      child: AppErrorState(
                        message: state.error,
                        onRetry: () => ref
                            .read(notificationsProvider.notifier)
                            .loadNotifications(),
                      ),
                    )
                  : state.notifications.isEmpty
                      ? SingleChildScrollView(
                          physics: const AlwaysScrollableScrollPhysics(),
                          child: Center(
                            child: AppEmptyState(
                              icon: Icons.notifications_none_rounded,
                              title: AppStrings.get('no_notifications'),
                              subtitle:
                                  AppStrings.get('no_notifications_desc'),
                              actionLabel: AppStrings.get('retry'),
                              onAction: () => ref
                                  .read(notificationsProvider.notifier)
                                  .loadNotifications(),
                            ),
                          ),
                        )
                      : _buildList(state, isDark),
            ),
    );
  }

  Widget _buildList(NotificationState state, bool isDark) {
    final grouped = _groupByDate(state.notifications);

    return ListView(
      padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 8),
      children: grouped.entries.map((entry) {
        return Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Padding(
              padding: const EdgeInsets.only(top: 12, bottom: 8),
              child: Text(
                entry.key,
                style: TextStyle(
                  fontSize: 14,
                  fontWeight: FontWeight.w600,
                  color: isDark ? Colors.grey[400] : AppColors.neutral500,
                ),
              ),
            ),
            ...entry.value.map((n) => _buildNotificationCard(n, isDark)),
          ],
        );
      }).toList(),
    );
  }

  Widget _buildNotificationCard(NotificationModel notif, bool isDark) {
    return AppNotificationCard(
      title: notif.title,
      subtitle: notif.body,
      time: _formatTime(notif.createdAt),
      icon: _notifIcon(notif.type),
      iconColor: _notifColor(notif.type),
      isUnread: !notif.isRead,
      onTap: () {
        if (!notif.isRead) {
          ref
              .read(notificationsProvider.notifier)
              .markAsRead(notif.id);
        }
      },
    );
  }

  Map<String, List<NotificationModel>> _groupByDate(
      List<NotificationModel> notifications) {
    final now = DateTime.now();
    final today = DateTime(now.year, now.month, now.day);
    final yesterday = today.subtract(const Duration(days: 1));

    final Map<String, List<NotificationModel>> grouped = {};

    for (final n in notifications) {
      String key;
      if (n.createdAt == null) {
        key = AppStrings.get('older');
      } else {
        final date = DateTime(
          n.createdAt!.year,
          n.createdAt!.month,
          n.createdAt!.day,
        );
        if (date == today) {
          key = AppStrings.get('today');
        } else if (date == yesterday) {
          key = AppStrings.get('yesterday');
        } else {
          key = '${n.createdAt!.year}/${n.createdAt!.month}/${n.createdAt!.day}';
        }
      }
      grouped.putIfAbsent(key, () => []);
      grouped[key]!.add(n);
    }

    return grouped;
  }

  String _formatTime(DateTime? dt) {
    if (dt == null) return '';
    final now = DateTime.now();
    final diff = now.difference(dt);
    if (diff.inMinutes < 1) return AppStrings.get('now');
    if (diff.inHours < 1) {
      return AppStrings.get('minutes_ago').replaceAll('%d', '${diff.inMinutes}');
    }
    if (diff.inDays < 1) {
      return AppStrings.get('hours_ago').replaceAll('%d', '${diff.inHours}');
    }
    if (diff.inDays < 7) {
      return AppStrings.get('days_ago').replaceAll('%d', '${diff.inDays}');
    }
    return '${dt.year}/${dt.month}/${dt.day}';
  }

  IconData _notifIcon(String type) {
    switch (type) {
      case 'sponsorship':
        return Icons.favorite_rounded;
      case 'orphan':
        return Icons.child_care_rounded;
      case 'payment':
        return Icons.payment_rounded;
      case 'system':
        return Icons.info_outline_rounded;
      default:
        return Icons.notifications_rounded;
    }
  }

  Color _notifColor(String type) {
    switch (type) {
      case 'sponsorship':
        return AppColors.accent;
      case 'orphan':
        return AppColors.primary;
      case 'payment':
        return AppColors.success;
      case 'system':
        return AppColors.info;
      default:
        return AppColors.secondary;
    }
  }

  Widget _buildSkeletons(bool isDark) {
    return ListView.builder(
      padding: const EdgeInsets.all(16),
      itemCount: 5,
      itemBuilder: (_, __) => const Padding(
        padding: EdgeInsets.only(bottom: 8),
        child: ShimmerCard(),
      ),
    );
  }
}
