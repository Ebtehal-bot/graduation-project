import 'package:dio/dio.dart';
import 'package:flutter/foundation.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';

import '../constants/api_endpoints.dart';
import 'session_manager.dart';
import 'token_storage.dart';

final dioProvider = Provider<Dio>((ref) {
  final tokenStorage = ref.watch(tokenStorageProvider);

  final dio = Dio(
    BaseOptions(
      baseUrl: ApiEndpoints.baseUrl,
      connectTimeout: const Duration(seconds: 20),
      receiveTimeout: const Duration(seconds: 20),
      sendTimeout: const Duration(seconds: 15),
      headers: {
        'Accept': 'application/json',
        'Content-Type': 'application/json',
      },
    ),
  );

  dio.interceptors.add(
    InterceptorsWrapper(
      onRequest: (options, handler) async {
        final token = await tokenStorage.getToken();
        if (token != null && token.isNotEmpty) {
          options.headers['Authorization'] = 'Bearer $token';
        }
        handler.next(options);
      },
      onError: (error, handler) async {
        if (error.response?.statusCode == 401) {
          await tokenStorage.clearAll();
          try {
            ref.read(sessionExpiredProvider.notifier).state = true;
          } catch (_) {}
          handler.next(error);
          return;
        }

        if (error.response?.statusCode == 403) {
          handler.next(error);
          return;
        }

        if (error.type == DioExceptionType.connectionTimeout ||
            error.type == DioExceptionType.receiveTimeout ||
            error.type == DioExceptionType.connectionError) {
          final retryCount = error.requestOptions.headers['_retry_count'] as int? ?? 0;
          if (retryCount < 2) {
            try {
              error.requestOptions.headers['_retry_count'] = retryCount + 1;
              await Future.delayed(Duration(seconds: 1 * (retryCount + 1)));
              final response = await dio.fetch(error.requestOptions);
              handler.resolve(response);
              return;
            } catch (_) {}
          }
        }

        handler.next(error);
      },
    ),
  );

  dio.interceptors.add(
    LogInterceptor(
      request: true,
      requestHeader: false,
      requestBody: true,
      responseHeader: false,
      responseBody: false,
      error: true,
      logPrint: (obj) => debugPrint('[DIO] $obj'),
    ),
  );

  return dio;
});
