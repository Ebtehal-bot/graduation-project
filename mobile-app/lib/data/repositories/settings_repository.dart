import 'package:dio/dio.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';

import '../../core/constants/api_endpoints.dart';
import '../../core/errors/api_exception.dart';
import '../../core/network/dio_client.dart';
import '../models/app_settings_model.dart';

class SettingsRepository {
  final Dio _dio;

  SettingsRepository(this._dio);

  Future<AppSettingsModel> getSettings() async {
    try {
      final response = await _dio.get(ApiEndpoints.settings);
      final body = response.data;
      if (body is Map<String, dynamic>) {
        if (body['status'] == true && body['data'] is Map<String, dynamic>) {
          return AppSettingsModel.fromJson(body['data'] as Map<String, dynamic>);
        }
        if (body['data'] is Map<String, dynamic>) {
          return AppSettingsModel.fromJson(body['data'] as Map<String, dynamic>);
        }
        if (body.containsKey('site_name') || body.containsKey('org_name')) {
          return AppSettingsModel.fromJson(body);
        }
      }
      return const AppSettingsModel(
        siteName: 'منصة كفيل لرعاية وكفالة الأيتام',
        orgName: 'منصة كفيل',
      );
    } on DioException catch (e) {
      throw ApiException.fromDioError(e);
    } catch (e) {
      throw ApiException(message: 'Unexpected error: $e');
    }
  }
}

final settingsRepositoryProvider = Provider<SettingsRepository>((ref) {
  return SettingsRepository(ref.watch(dioProvider));
});
