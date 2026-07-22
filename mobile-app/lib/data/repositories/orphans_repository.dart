import 'dart:convert';

import 'package:dio/dio.dart';
import 'package:flutter/foundation.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';

import '../../core/constants/api_endpoints.dart';
import '../../core/errors/api_exception.dart';
import '../../core/network/dio_client.dart';

class OrphansRepository {
  final Dio _dio;

  OrphansRepository(this._dio);

  List<dynamic> _extractDataList(dynamic body, [int depth = 0]) {
    if (body is List) {
      debugPrint(
          '[OrphansRepository] Found list at depth $depth: ${body.length} items');
      return body;
    }
    if (body is! Map<String, dynamic>) {
      debugPrint(
          '[OrphansRepository] Depth $depth: not a Map or List: ${body.runtimeType}');
      return [];
    }

    const possibleKeys = [
      'data', 'orphans', 'results', 'items', 'records',
      'list', 'record', 'response', 'orphan', 'children', 'students',
      'rows', 'entries',
    ];

    // Phase 1: Check all known keys at current level
    for (final key in possibleKeys) {
      if (body.containsKey(key)) {
        final raw = body[key];
        if (raw is List) {
          debugPrint(
              '[OrphansRepository] Found list at depth $depth under key "$key": ${raw.length} items');
          return raw;
        }
      }
    }

    // Phase 2: Recursively search any Map value under any key (any nesting depth)
    for (final entry in body.entries) {
      if (entry.value is Map) {
        final result = _extractDataList(
            Map<String, dynamic>.from(entry.value as Map), depth + 1);
        if (result.isNotEmpty) {
          debugPrint(
              '[OrphansRepository] Found list at depth $depth through key "${entry.key}" (recursive): ${result.length} items');
          return result;
        }
      }
      if (entry.value is List) {
        final list = entry.value as List;
        if (list.isEmpty || list.first is Map<String, dynamic>) {
          debugPrint(
              '[OrphansRepository] Found list at depth $depth under key "${entry.key}": ${list.length} items');
          return list;
        }
      }
    }

    debugPrint(
        '[OrphansRepository] WARNING at depth $depth: No list found. Keys: ${body.keys}');
    for (final k in body.keys) {
      debugPrint(
          '[OrphansRepository]   key="$k" type=${body[k].runtimeType}');
    }
    return [];
  }

  Map<String, dynamic>? _extractPaginationMeta(Map<String, dynamic> body) {
    if (body.containsKey('meta') && body['meta'] is Map<String, dynamic>) {
      return body['meta'] as Map<String, dynamic>;
    }
    if (body.containsKey('links') && body['links'] is Map<String, dynamic>) {
      return body['links'] as Map<String, dynamic>;
    }
    for (final key in ['data', 'orphans', 'results']) {
      if (body.containsKey(key) && body[key] is Map<String, dynamic>) {
        final nested = body[key] as Map<String, dynamic>;
        if (nested.containsKey('meta') &&
            nested['meta'] is Map<String, dynamic>) {
          debugPrint('[OrphansRepository] Found nested meta under "$key.meta"');
          return nested['meta'] as Map<String, dynamic>;
        }
      }
    }
    if (body.containsKey('current_page') || body.containsKey('last_page')) {
      debugPrint(
          '[OrphansRepository] Found raw pagination fields at top level');
      return body;
    }
    return null;
  }

  Future<List<dynamic>> _fetchAllPages(String endpoint,
      List<dynamic> firstPageData, Map<String, dynamic>? meta) async {
    if (meta == null) return firstPageData;

    final currentPage = meta['current_page'] as int? ?? 1;
    final lastPage = meta['last_page'] as int? ?? 1;
    final total = meta['total'] as int?;

    debugPrint('[OrphansRepository] Pagination: page=$currentPage/$lastPage '
        'total=$total items=${firstPageData.length}');

    if (lastPage <= 1) return firstPageData;

    final List<dynamic> allItems = List.from(firstPageData);

    for (int page = 2; page <= lastPage; page++) {
      try {
        final response = await _dio.get(endpoint, queryParameters: {
          'page': page,
        });
        final pageData = _extractDataList(response.data);
        debugPrint(
            '[OrphansRepository] Fetched page $page: ${pageData.length} items');
        allItems.addAll(pageData);
      } catch (e) {
        debugPrint('[OrphansRepository] Failed to fetch page $page: $e');
        break;
      }
    }

    debugPrint(
        '[OrphansRepository] Total items after pagination: ${allItems.length}');
    return allItems;
  }

  Future<List<dynamic>> getOrphans() async {
    try {
      debugPrint('[OrphansRepository] === REQUEST ===');
      debugPrint('[OrphansRepository] GET ${ApiEndpoints.orphans}');
      final response = await _dio.get(ApiEndpoints.orphans);
      debugPrint('[OrphansRepository] === RESPONSE ===');
      debugPrint('[OrphansRepository] Status: ${response.statusCode}');
      debugPrint(
          '[OrphansRepository] Response type: ${response.data.runtimeType}');
      debugPrint(
          '[OrphansRepository] Full response body: ${jsonEncode(response.data)}');

      final extracted = _extractDataList(response.data);
      debugPrint(
          '[OrphansRepository] Extracted items count: ${extracted.length}');

      if (response.data is Map<String, dynamic>) {
        final meta =
            _extractPaginationMeta(response.data as Map<String, dynamic>);
        if (meta != null) {
          debugPrint(
              '[OrphansRepository] Pagination meta found, fetching all pages...');
          return _fetchAllPages(ApiEndpoints.orphans, extracted, meta);
        }
      }

      return extracted;
    } on DioException catch (e) {
      debugPrint('[OrphansRepository] DioException: ${e.message} '
          'type=${e.type} response=${e.response?.data}');
      throw ApiException.fromDioError(e);
    } catch (e) {
      debugPrint('[OrphansRepository] Unexpected error: $e');
      throw ApiException(message: 'Unexpected error: $e');
    }
  }

  Future<List<dynamic>> getAvailableOrphans() async {
    try {
      debugPrint('[OrphansRepository] === REQUEST ===');
      debugPrint('[OrphansRepository] GET ${ApiEndpoints.availableOrphans}');
      final response = await _dio.get(ApiEndpoints.availableOrphans);
      debugPrint('[OrphansRepository] === RESPONSE ===');
      debugPrint('[OrphansRepository] Status: ${response.statusCode}');
      debugPrint(
          '[OrphansRepository] Response type: ${response.data.runtimeType}');
      debugPrint(
          '[OrphansRepository] Full response body: ${jsonEncode(response.data)}');

      final extracted = _extractDataList(response.data);
      debugPrint(
          '[OrphansRepository] Extracted available orphans count: ${extracted.length}');

      if (response.data is Map<String, dynamic>) {
        final meta =
            _extractPaginationMeta(response.data as Map<String, dynamic>);
        if (meta != null) {
          return _fetchAllPages(ApiEndpoints.availableOrphans, extracted, meta);
        }
      }

      return extracted;
    } on DioException catch (e) {
      debugPrint('[OrphansRepository] Available DioException: ${e.message} '
          'type=${e.type} response=${e.response?.data}');
      throw ApiException.fromDioError(e);
    } catch (e) {
      debugPrint('[OrphansRepository] Unexpected error: $e');
      throw ApiException(message: 'Unexpected error: $e');
    }
  }

  Future<Map<String, dynamic>> getOrphanDetails(int id) async {
    try {
      final response = await _dio.get(ApiEndpoints.orphanDetails(id));
      debugPrint('[OrphansRepository] GET ${ApiEndpoints.orphanDetails(id)} '
          'status=${response.statusCode} type=${response.data.runtimeType}');
      debugPrint(
          '[OrphansRepository] Details body: ${jsonEncode(response.data)}');
      if (response.data is Map<String, dynamic>) {
        final body = response.data as Map<String, dynamic>;
        final data = body['data'];
        if (data is Map<String, dynamic>) {
          return data;
        }
        if (body['orphan'] is Map<String, dynamic>) {
          return body['orphan'] as Map<String, dynamic>;
        }
        if (body['record'] is Map<String, dynamic>) {
          return body['record'] as Map<String, dynamic>;
        }
        return body;
      }
      if (response.data is List) {
        final list = response.data as List;
        if (list.isNotEmpty && list.first is Map<String, dynamic>) {
          return list.first as Map<String, dynamic>;
        }
      }
      debugPrint(
          '[OrphansRepository] Unexpected response type for details: ${response.data.runtimeType}');
      return <String, dynamic>{};
    } on DioException catch (e) {
      debugPrint('[OrphansRepository] DioException: ${e.message}');
      throw ApiException.fromDioError(e);
    }
  }

  Future<List<dynamic>> searchOrphans({
    String? query,
    String? gender,
    String? status,
    int? branchId,
  }) async {
    try {
      final response = await _dio.get(
        ApiEndpoints.orphanSearch,
        queryParameters: {
          if (query != null && query.isNotEmpty) 'q': query,
          if (gender != null) 'gender': gender,
          if (status != null) 'status': status,
          if (branchId != null) 'branch_id': branchId,
        },
      );
      debugPrint(
          '[OrphansRepository] Search status=${response.statusCode} type=${response.data.runtimeType}');
      final extracted = _extractDataList(response.data);
      debugPrint(
          '[OrphansRepository] Search results count: ${extracted.length}');
      return extracted;
    } on DioException catch (e) {
      debugPrint('[OrphansRepository] Search DioException: ${e.message}');
      throw ApiException.fromDioError(e);
    }
  }
}

final orphansRepositoryProvider = Provider<OrphansRepository>((ref) {
  return OrphansRepository(ref.watch(dioProvider));
});
