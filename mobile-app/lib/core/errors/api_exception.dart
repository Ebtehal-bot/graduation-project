import 'package:dio/dio.dart';

class ApiException implements Exception {
  final String message;
  final int? statusCode;
  final dynamic response;

  const ApiException({
    required this.message,
    this.statusCode,
    this.response,
  });

  @override
  String toString() {
    return 'ApiException($statusCode): $message';
  }

  factory ApiException.fromDioError(DioException error) {
    switch (error.type) {
      case DioExceptionType.connectionTimeout:
      case DioExceptionType.sendTimeout:
      case DioExceptionType.receiveTimeout:
        return const ApiException(
          message: 'انتهت مهلة الاتصال. تحقق من اتصالك بالإنترنت',
          statusCode: 0,
        );
      case DioExceptionType.badResponse:
        final statusCode = error.response?.statusCode;
        final responseData = error.response?.data;
        String message = 'حدث خطأ غير متوقع';

        if (responseData is Map<String, dynamic>) {
          if (responseData['message'] is String) {
            message = responseData['message'] as String;
          }

          if (statusCode == 422 && responseData['errors'] is Map) {
            final errors = responseData['errors'] as Map;
            final firstError = errors.values.firstWhere(
              (v) => v is List && v.isNotEmpty,
              orElse: () => <String>[],
            );
            if (firstError is List && firstError.isNotEmpty) {
              message = firstError.first.toString();
            }
          }

          if (statusCode == 403) {
            message = responseData['message'] as String? ?? 'ليس لديك صلاحية للوصول إلى هذا المورد';
          }
        }

        return ApiException(
          message: message,
          statusCode: statusCode,
          response: responseData,
        );
      case DioExceptionType.cancel:
        return const ApiException(
          message: 'تم إلغاء الطلب',
          statusCode: 0,
        );
      case DioExceptionType.connectionError:
        return const ApiException(
          message: 'لا يوجد اتصال بالإنترنت. تحقق من اتصالك وحاول مرة أخرى',
          statusCode: 0,
        );
      case DioExceptionType.badCertificate:
        return const ApiException(
          message: 'خطأ في شهادة الأمان',
          statusCode: 0,
        );
      case DioExceptionType.unknown:
      default:
        final errMsg = error.message ?? 'خطأ غير متوقع';
        if (errMsg.contains('SocketException') || errMsg.contains('Failed host lookup')) {
          return const ApiException(
            message: 'تعذر الاتصال بالخادم. تأكد من تشغيل الخادم',
            statusCode: 0,
          );
        }
        return ApiException(
          message: errMsg,
          statusCode: 0,
        );
    }
  }
}
