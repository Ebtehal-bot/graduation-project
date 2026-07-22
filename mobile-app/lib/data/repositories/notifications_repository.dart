import 'dart:convert';

import 'package:dio/dio.dart';
import 'package:flutter/foundation.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';

import '../../core/constants/api_endpoints.dart';
import '../../core/errors/api_exception.dart';
import '../../core/network/dio_client.dart';

class NotificationsRepository {
  final Dio _dio;

  NotificationsRepository(this._dio);

  List<dynamic> _extractDataList(dynamic body, [int depth = 0]) {
    if (body is List) {
      debugPrint(
          '[NotificationsRepository] Found list at depth $depth: ${body.length} items');
      return body;
    }
    if (body is! Map<String, dynamic>) {
      debugPrint(
          '[NotificationsRepository] Depth $depth: not a Map or List: ${body.runtimeType}');
      return [];
    }

    const possibleKeys = [
      'notifications', 'data', 'results', 'items', 'records',
      'list', 'record', 'response', 'notification',
    ];

    for (final key in possibleKeys) {
      if (body.containsKey(key)) {
        final raw = body[key];
        if (raw is List) {
          debugPrint(
              '[NotificationsRepository] Found list at depth $depth under key "$key": ${raw.length} items');
          return raw;
        }
      }
    }

    for (final entry in body.entries) {
      if (entry.value is Map) {
        final result = _extractDataList(
            Map<String, dynamic>.from(entry.value as Map), depth + 1);
        if (result.isNotEmpty) {
          debugPrint(
              '[NotificationsRepository] Found list at depth $depth through key "${entry.key}" (recursive): ${result.length} items');
          return result;
        }
      }
      if (entry.value is List) {
        final list = entry.value as List;
        if (list.isNotEmpty && list.first is Map<String, dynamic>) {
          debugPrint(
              '[NotificationsRepository] Found non-empty list at depth $depth under key "${entry.key}": ${list.length} items');
          return list;
        }
      }
    }

    debugPrint(
        '[NotificationsRepository] WARNING at depth $depth: No list found. Keys: ${body.keys}');
    for (final k in body.keys) {
      debugPrint(
          '[NotificationsRepository]   key="$k" type=${body[k].runtimeType}');
    }
    return [];
  }

  Future<List<dynamic>> getNotifications() async {
    try {
      debugPrint('[NotificationsRepository] === REQUEST ===');
      debugPrint('[NotificationsRepository] GET ${ApiEndpoints.notifications}');
      final response = await _dio.get(ApiEndpoints.notifications);
      debugPrint('[NotificationsRepository] === RESPONSE ===');
      debugPrint('[NotificationsRepository] Status: ${response.statusCode}');
      debugPrint('[NotificationsRepository] Response type: ${response.data.runtimeType}');
      debugPrint('[NotificationsRepository] Full response: ${jsonEncode(response.data)}');

      final extracted = _extractDataList(response.data);
      debugPrint(
          '[NotificationsRepository] Extracted items count: ${extracted.length}');
      if (extracted.isNotEmpty && extracted.first is Map<String, dynamic>) {
        debugPrint('[NotificationsRepository] First item keys: '
            '${(extracted.first as Map<String, dynamic>).keys}');
      }
      return extracted;
    } on DioException catch (e) {
      debugPrint('[NotificationsRepository] DioException: ${e.message} '
          'type=${e.type} response=${e.response?.data}');
      throw ApiException.fromDioError(e);
    } catch (e) {
      debugPrint('[NotificationsRepository] Unexpected error: $e');
      throw ApiException(message: 'Unexpected error: $e');
    }
  }

  Future<int> getUnreadCount() async {
    try {
      final response = await _dio.get(ApiEndpoints.unreadNotificationsCount);
      final raw = response.data;
      final body = (raw is Map<String, dynamic>) ? raw : <String, dynamic>{};
      debugPrint('[NotificationsRepository] Unread count response: $body');
      if (body['data'] is int) return body['data'];
      if (body['count'] is int) return body['count'];
      if (body['unread_count'] is int) return body['unread_count'];
      return int.tryParse(body['data']?.toString() ?? '') ??
          int.tryParse(body['count']?.toString() ?? '') ??
          int.tryParse(body['unread_count']?.toString() ?? '') ??
          0;
    } on DioException catch (_) {
      return 0;
    }
  }

  Future<void> markAsRead(String notificationId) async {
    try {
      debugPrint('[NotificationsRepository] Mark as read: $notificationId');
      await _dio.post(ApiEndpoints.markNotificationRead(notificationId));
    } on DioException catch (e) {
      if (e.response?.statusCode == 405) {
        try {
          await _dio.put(ApiEndpoints.markNotificationRead(notificationId));
          return;
        } catch (_) {}
      }
      throw ApiException.fromDioError(e);
    }
  }

  Future<void> markAllAsRead() async {
    try {
      debugPrint('[NotificationsRepository] Mark all as read');
      await _dio.post(ApiEndpoints.markAllNotificationsRead);
    } on DioException catch (e) {
      if (e.response?.statusCode == 405) {
        try {
          await _dio.put(ApiEndpoints.markAllNotificationsRead);
          return;
        } catch (_) {}
      }
      throw ApiException.fromDioError(e);
    }
  }
}

final notificationsRepositoryProvider =
    Provider<NotificationsRepository>((ref) {
  return NotificationsRepository(ref.watch(dioProvider));
});
