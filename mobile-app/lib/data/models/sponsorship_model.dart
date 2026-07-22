import '../../core/localization/app_strings.dart';

class SponsorshipModel {
  final int id;
  final double monthlyAmount;
  final String sponsorshipType;
  final String status;
  final String? startDate;
  final String? endDate;
  final int? remainingDays;
  final String? sponsorName;
  final List<Map<String, dynamic>>? payments;
  final DateTime? createdAt;

  final OrphanInfo? orphan;

  const SponsorshipModel({
    required this.id,
    required this.monthlyAmount,
    required this.sponsorshipType,
    required this.status,
    this.startDate,
    this.endDate,
    this.remainingDays,
    this.sponsorName,
    this.payments,
    this.createdAt,
    this.orphan,
  });

  String? get orphanName => orphan?.fullName;
  String? get orphanImage => orphan?.imageUrl;
  String? get branchName => orphan?.branchName;
  int get orphanId => orphan?.id ?? 0;

  factory SponsorshipModel.fromJson(Map<String, dynamic> json) {
    OrphanInfo? orphan;
    if (json['orphan'] is Map<String, dynamic>) {
      orphan = OrphanInfo.fromJson(json['orphan'] as Map<String, dynamic>);
    }

    return SponsorshipModel(
      id: json['id'] is int
          ? json['id']
          : (json['id'] is double
              ? (json['id'] as double).toInt()
              : int.tryParse(json['id']?.toString() ?? '') ?? 0),
      monthlyAmount:
          (json['monthly_amount'] is num ? json['monthly_amount'] : 0.0)
              .toDouble(),
      sponsorshipType: json['sponsorship_type'] as String? ?? 'financial',
      status: json['sponsorship_status'] as String? ?? json['status'] as String? ?? 'active',
      startDate: json['sponsorship_start_date'] as String? ?? json['start_date'] as String?,
      endDate: json['sponsorship_end_date'] as String? ?? json['end_date'] as String?,
      remainingDays: json['remaining_days'] as int?,
      sponsorName: json['sponsor_name'] as String?,
      payments: json['payments'] is List
          ? List<Map<String, dynamic>>.from(json['payments'] as List)
          : null,
      createdAt: json['created_at'] != null
          ? DateTime.tryParse(json['created_at'].toString())
          : null,
      orphan: orphan,
    );
  }

  String get statusLabel {
    switch (status) {
      case 'active':
        return AppStrings.get('active_sponsorships');
      case 'inactive':
        return AppStrings.get('pending');
      case 'ended':
        return AppStrings.get('completed');
      default:
        return status;
    }
  }

  String get typeLabel {
    switch (sponsorshipType) {
      case 'financial':
        return AppStrings.get('financial');
      case 'educational':
        return AppStrings.get('educational');
      case 'medical':
        return AppStrings.get('medical');
      default:
        return sponsorshipType;
    }
  }
}

class OrphanInfo {
  final int id;
  final String fullName;
  final int? age;
  final String? gender;
  final String? imageUrl;
  final String? branchName;
  final String? healthStatus;

  const OrphanInfo({
    required this.id,
    required this.fullName,
    this.age,
    this.gender,
    this.imageUrl,
    this.branchName,
    this.healthStatus,
  });

  factory OrphanInfo.fromJson(Map<String, dynamic> json) {
    return OrphanInfo(
      id: json['id'] is int
          ? json['id']
          : (json['id'] is double
              ? (json['id'] as double).toInt()
              : int.tryParse(json['id']?.toString() ?? '') ?? 0),
      fullName: json['full_name'] as String? ?? '',
      age: json['age'] is int
          ? json['age']
          : int.tryParse(json['age']?.toString() ?? ''),
      gender: json['gender'] as String?,
      imageUrl: json['image_url'] as String?,
      branchName: json['branch_name'] as String?,
      healthStatus: json['health_status'] as String?,
    );
  }
}
