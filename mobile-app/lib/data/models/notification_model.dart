import 'dart:convert';

import 'package:flutter/foundation.dart';

class NotificationModel {
  final String id;
  final String title;
  final String body;
  final String type;
  final bool isRead;
  final DateTime? createdAt;

  const NotificationModel({
    required this.id,
    required this.title,
    required this.body,
    this.type = 'info',
    this.isRead = false,
    this.createdAt,
  });

  factory NotificationModel.fromJson(Map<String, dynamic> json) {
    debugPrint('[NotificationModel] === PARSING NOTIFICATION ===');
    debugPrint('[NotificationModel] Full JSON keys: ${json.keys}');
    debugPrint('[NotificationModel] id=${json['id']} type=${json['type']} '
        'created_at=${json['created_at']} read_at=${json['read_at']} '
        'is_read=${json['is_read']} (${json['is_read'].runtimeType})');

    String title = json['title'] as String? ?? '';
    String body = json['body'] as String? ?? '';

    if (title.isEmpty && json['data'] != null) {
      if (json['data'] is Map<String, dynamic>) {
        final data = json['data'] as Map<String, dynamic>;
        debugPrint('[NotificationModel] data is Map with keys: ${data.keys}');
        title = data['title'] as String? ?? '';
        body = data['body'] as String? ?? '';
        if (title.isEmpty) title = data['message'] as String? ?? '';
        if (title.isEmpty) title = data['subject'] as String? ?? '';
      } else if (json['data'] is String) {
        final dataStr = json['data'] as String;
        final preview = dataStr.length > 100 ? dataStr.substring(0, 100) : dataStr;
        debugPrint('[NotificationModel] data is String, attempting to decode: $preview');
        try {
          final decoded = jsonDecode(dataStr) as Map<String, dynamic>;
          title = decoded['title'] as String? ?? '';
          body = decoded['body'] as String? ?? '';
          if (title.isEmpty) title = decoded['message'] as String? ?? '';
          if (title.isEmpty) title = decoded['subject'] as String? ?? '';
        } catch (e) {
          debugPrint('[NotificationModel] Failed to decode data string: $e');
        }
      } else {
        debugPrint('[NotificationModel] data is unexpected type: '
            '${json['data'].runtimeType}');
      }
    }

    bool isRead = false;
    final isReadRaw = json['is_read'];
    if (isReadRaw is bool) {
      isRead = isReadRaw;
    } else if (isReadRaw is int) {
      isRead = isReadRaw == 1;
    } else if (isReadRaw is String) {
      isRead = isReadRaw == '1' || isReadRaw.toLowerCase() == 'true';
    } else if (json['read_at'] is String && (json['read_at'] as String).isNotEmpty) {
      isRead = true;
    }

    String rawType = json['type'] as String? ?? 'info';
    if (rawType.contains('\\')) {
      final className = rawType.split('\\').last.toLowerCase();
      if (className.contains('sponsorship')) {
        rawType = 'sponsorship';
      } else if (className.contains('payment')) {
        rawType = 'payment';
      } else if (className.contains('orphan')) {
        rawType = 'orphan';
      } else if (className.contains('system')) {
        rawType = 'system';
      }
    } else {
      final lower = rawType.toLowerCase();
      if (lower.contains('sponsorship') || lower.contains('sponsor')) {
        rawType = 'sponsorship';
      } else if (lower.contains('payment') || lower.contains('donation')) {
        rawType = 'payment';
      } else if (lower.contains('orphan') || lower.contains('child')) {
        rawType = 'orphan';
      } else if (lower.contains('system') || lower.contains('info') || lower.contains('general')) {
        rawType = 'system';
      }
    }

    DateTime? createdAt;
    if (json['created_at'] != null) {
      createdAt = DateTime.tryParse(json['created_at'].toString());
    }

    final bodyPreview = body.length > 50 ? body.substring(0, 50) : body;
    debugPrint('[NotificationModel] RESULT: id=${json['id']} '
        'title=$title body=$bodyPreview '
        'type=$rawType isRead=$isRead createdAt=$createdAt');

    return NotificationModel(
      id: json['id']?.toString() ?? '',
      title: title,
      body: body,
      type: rawType,
      isRead: isRead,
      createdAt: createdAt,
    );
  }
}
