import 'dart:convert';

import 'package:dio/dio.dart';
import 'package:flutter/foundation.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';

import '../../core/constants/api_endpoints.dart';
import '../../core/errors/api_exception.dart';
import '../../core/network/dio_client.dart';

class SponsorshipRepository {
  final Dio _dio;

  SponsorshipRepository(this._dio);

  List<dynamic> _extractListWithFallback(dynamic body, [int depth = 0]) {
    if (body is List) {
      debugPrint(
          '[SponsorshipRepository] Found list at depth $depth: ${body.length} items');
      return body;
    }
    if (body is! Map<String, dynamic>) {
      debugPrint(
          '[SponsorshipRepository] Depth $depth: not a Map or List: ${body.runtimeType}');
      return [];
    }

    const possibleKeys = [
      'data', 'sponsorships', 'results', 'items', 'activities', 'activity',
      'records', 'list', 'rows', 'entries', 'response',
    ];

    for (final key in possibleKeys) {
      if (body.containsKey(key)) {
        final raw = body[key];
        if (raw is List) {
          debugPrint(
              '[SponsorshipRepository] Found list at depth $depth under key "$key": ${raw.length} items');
          return raw;
        }
      }
    }

    for (final entry in body.entries) {
      if (entry.value is Map) {
        final result = _extractListWithFallback(
            Map<String, dynamic>.from(entry.value as Map), depth + 1);
        if (result.isNotEmpty) {
          debugPrint(
              '[SponsorshipRepository] Found list at depth $depth through key "${entry.key}" (recursive): ${result.length} items');
          return result;
        }
      }
      if (entry.value is List) {
        final list = entry.value as List;
        if (list.isEmpty || list.first is Map<String, dynamic>) {
          debugPrint(
              '[SponsorshipRepository] Found list at depth $depth under key "${entry.key}": ${list.length} items');
          return list;
        }
      }
    }

    debugPrint(
        '[SponsorshipRepository] No list found at depth $depth. Keys: ${body.keys}');
    for (final k in body.keys) {
      debugPrint('[SponsorshipRepository]   key="$k" type=${body[k].runtimeType}');
    }
    return [];
  }

  Map<String, dynamic> _extractMapWithFallback(dynamic body) {
    if (body is Map<String, dynamic>) {
      for (final key in ['data', 'stats', 'dashboard', 'report', 'result']) {
        if (body.containsKey(key) && body[key] is Map) {
          final extracted = Map<String, dynamic>.from(body[key] as Map);
          if (extracted.length > 1 || extracted.isEmpty) {
            return extracted;
          }
          final innerKeys = extracted.keys.toList();
          if (innerKeys.length == 1 && extracted[innerKeys.first] is Map) {
            return _extractMapWithFallback(extracted);
          }
          return extracted;
        }
      }
      return body;
    }
    if (body is Map) {
      final result = Map<String, dynamic>.from(body);
      if (result.length == 1) {
        final key = result.keys.first;
        if (result[key] is Map) {
          return _extractMapWithFallback(result[key]);
        }
      }
      return result;
    }
    debugPrint(
        '[SponsorshipRepository] Unexpected type for map extraction: ${body.runtimeType}');
    return <String, dynamic>{};
  }

  Future<List<dynamic>> getUserSponsorships() async {
    try {
      debugPrint('[SponsorshipRepository] === REQUEST ===');
      debugPrint(
          '[SponsorshipRepository] GET ${ApiEndpoints.userSponsorships}');
      final response = await _dio.get(ApiEndpoints.userSponsorships);
      debugPrint(
          '[SponsorshipRepository] Status: ${response.statusCode} type: ${response.data.runtimeType}');
      debugPrint(
          '[SponsorshipRepository] Response: ${jsonEncode(response.data)}');
      return _extractListWithFallback(response.data);
    } on DioException catch (e) {
      throw ApiException.fromDioError(e);
    } catch (e) {
      throw ApiException(message: 'Unexpected error: $e');
    }
  }

  Future<Map<String, dynamic>> getSponsorshipDetails(int id) async {
    try {
      final response = await _dio.get(ApiEndpoints.sponsorshipDetails(id));
      final data = _extractMapWithFallback(response.data);
      return data;
    } on DioException catch (e) {
      throw ApiException.fromDioError(e);
    } catch (e) {
      throw ApiException(message: 'Unexpected error: $e');
    }
  }

  Future<Map<String, dynamic>> createSponsorship({
    required int orphanId,
    required double monthlyAmount,
    required String sponsorshipType,
    String? notes,
  }) async {
    try {
      final response = await _dio.post(
        ApiEndpoints.createSponsorship,
        data: {
          'orphan_id': orphanId,
          'monthly_amount': monthlyAmount,
          'sponsorship_type': sponsorshipType,
          if (notes != null && notes.isNotEmpty) 'notes': notes,
        },
      );
      return response.data is Map<String, dynamic>
          ? response.data as Map<String, dynamic>
          : <String, dynamic>{};
    } on DioException catch (e) {
      throw ApiException.fromDioError(e);
    } catch (e) {
      throw ApiException(message: 'Unexpected error: $e');
    }
  }

  Future<List<dynamic>> getActiveSponsorships() async {
    try {
      debugPrint(
          '[SponsorshipRepository] GET ${ApiEndpoints.activeSponsorships}');
      final response = await _dio.get(ApiEndpoints.activeSponsorships);
      debugPrint(
          '[SponsorshipRepository] Status: ${response.statusCode} type: ${response.data.runtimeType}');
      debugPrint(
          '[SponsorshipRepository] Response: ${jsonEncode(response.data)}');
      return _extractListWithFallback(response.data);
    } on DioException catch (e) {
      throw ApiException.fromDioError(e);
    } catch (e) {
      throw ApiException(message: 'Unexpected error: $e');
    }
  }

  Future<Map<String, dynamic>> getSponsorReports() async {
    try {
      debugPrint('[SponsorshipRepository] GET ${ApiEndpoints.sponsorReports}');
      final response = await _dio.get(ApiEndpoints.sponsorReports);
      debugPrint(
          '[SponsorshipRepository] Status: ${response.statusCode} type: ${response.data.runtimeType}');
      debugPrint(
          '[SponsorshipRepository] Response: ${jsonEncode(response.data)}');
      return _extractMapWithFallback(response.data);
    } on DioException catch (e) {
      throw ApiException.fromDioError(e);
    } catch (e) {
      throw ApiException(message: 'Unexpected error: $e');
    }
  }

  Future<Map<String, dynamic>> getDashboardStats() async {
    try {
      debugPrint('[SponsorshipRepository] GET ${ApiEndpoints.dashboardStats}');
      final response = await _dio.get(ApiEndpoints.dashboardStats);
      debugPrint(
          '[SponsorshipRepository] Status: ${response.statusCode} type: ${response.data.runtimeType}');
      debugPrint(
          '[SponsorshipRepository] Response: ${jsonEncode(response.data)}');
      return _extractMapWithFallback(response.data);
    } on DioException catch (e) {
      throw ApiException.fromDioError(e);
    } catch (e) {
      throw ApiException(message: 'Unexpected error: $e');
    }
  }

  Future<List<dynamic>> getDashboardRecentActivities() async {
    try {
      debugPrint(
          '[SponsorshipRepository] GET ${ApiEndpoints.dashboardRecentActivities}');
      final response = await _dio.get(ApiEndpoints.dashboardRecentActivities);
      debugPrint(
          '[SponsorshipRepository] Status: ${response.statusCode} type: ${response.data.runtimeType}');
      debugPrint(
          '[SponsorshipRepository] Response: ${jsonEncode(response.data)}');
      return _extractListWithFallback(response.data);
    } on DioException catch (e) {
      throw ApiException.fromDioError(e);
    } catch (e) {
      throw ApiException(message: 'Unexpected error: $e');
    }
  }
}

final sponsorshipRepositoryProvider = Provider<SponsorshipRepository>((ref) {
  return SponsorshipRepository(ref.watch(dioProvider));
});
