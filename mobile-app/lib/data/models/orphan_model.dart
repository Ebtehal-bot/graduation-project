import '../../core/localization/app_strings.dart';

class OrphanModel {
  final int id;
  final String name;
  final String? fileNumber;
  final String? photo;
  final String? gender;
  final String? religion;
  final String? nationality;
  final String? birthDate;
  final String? birthPlace;
  final int? age;
  final String? status;
  final String? addressGov;
  final String? addressDist;
  final String? addressVillage;
  final String? branchName;
  final int? branchId;
  final String? educationStatus;
  final String? schoolName;
  final String? academicLevel;
  final String? schoolPhone;
  final String? healthStatus;
  final String? talents;
  final String? quranMemorization;
  final String? fatherDeathCause;
  final String? fatherDeathDate;
  final String? motherName;
  final String? motherStatus;
  final String? motherJob;
  final String? guardianName;
  final String? guardianRelation;
  final String? guardianPhone;
  final bool isSponsored;
  final String? sponsorshipStatus;
  final Map<String, dynamic>? sponsorship;
  final List<Map<String, dynamic>>? sponsorships;
  final List<Map<String, dynamic>>? attachments;
  final Map<String, dynamic>? branch;
  final DateTime? createdAt;

  const OrphanModel({
    required this.id,
    required this.name,
    this.fileNumber,
    this.photo,
    this.gender,
    this.religion,
    this.nationality,
    this.birthDate,
    this.birthPlace,
    this.age,
    this.status,
    this.addressGov,
    this.addressDist,
    this.addressVillage,
    this.branchName,
    this.branchId,
    this.educationStatus,
    this.schoolName,
    this.academicLevel,
    this.schoolPhone,
    this.healthStatus,
    this.talents,
    this.quranMemorization,
    this.fatherDeathCause,
    this.fatherDeathDate,
    this.motherName,
    this.motherStatus,
    this.motherJob,
    this.guardianName,
    this.guardianRelation,
    this.guardianPhone,
    this.isSponsored = false,
    this.sponsorshipStatus,
    this.sponsorship,
    this.sponsorships,
    this.attachments,
    this.branch,
    this.createdAt,
  });

  factory OrphanModel.fromJson(Map<String, dynamic> json) {
    return OrphanModel(
      id: json['id'] is int
          ? json['id']
          : (json['id'] is double
              ? (json['id'] as double).toInt()
              : int.tryParse(json['id']?.toString() ?? '') ?? 0),
      name: json['name'] as String? ?? '',
      fileNumber: json['file_number'] as String?,
      photo: json['photo'] as String?,
      gender: json['gender'] as String?,
      religion: json['religion'] as String?,
      nationality: json['nationality'] as String?,
      birthDate: json['birth_date'] as String?,
      birthPlace: json['birth_place'] as String?,
      age: json['age'] is int
          ? json['age']
          : int.tryParse(json['age']?.toString() ?? ''),
      status: json['status'] as String?,
      addressGov: json['address_gov'] as String?,
      addressDist: json['address_dist'] as String?,
      addressVillage: json['address_village'] as String?,
      branchName: json['branch_name'] as String?,
      branchId: json['branch_id'] is int
          ? json['branch_id']
          : int.tryParse(json['branch_id']?.toString() ?? ''),
      educationStatus: json['education_status'] as String?,
      schoolName: json['school_name'] as String?,
      academicLevel: json['academic_level'] as String?,
      schoolPhone: json['school_phone'] as String?,
      healthStatus: json['health_status'] as String?,
      talents: json['talents'] as String?,
      quranMemorization: json['quran_memorization'] as String?,
      fatherDeathCause: json['father_death_cause'] as String?,
      fatherDeathDate: json['father_death_date'] as String?,
      motherName: json['mother_name'] as String?,
      motherStatus: json['mother_status'] as String?,
      motherJob: json['mother_job'] as String?,
      guardianName: json['guardian_name'] as String?,
      guardianRelation: json['guardian_relation'] as String?,
      guardianPhone: json['guardian_phone'] as String?,
      isSponsored: json['is_sponsored'] is bool
          ? json['is_sponsored'] as bool
          : json['is_sponsored'] is int
              ? (json['is_sponsored'] as int) == 1
              : false,
      sponsorshipStatus: json['sponsorship_status'] as String?,
      sponsorship: json['sponsorship'] is Map<String, dynamic>
          ? json['sponsorship'] as Map<String, dynamic>
          : null,
      sponsorships: json['sponsorships'] is List
          ? (json['sponsorships'] as List)
              .whereType<Map<String, dynamic>>()
              .toList()
          : null,
      attachments: json['attachments'] is List
          ? (json['attachments'] as List)
              .whereType<Map<String, dynamic>>()
              .toList()
          : null,
      branch: json['branch'] is Map<String, dynamic>
          ? json['branch'] as Map<String, dynamic>
          : json['branch'] is Map
              ? Map<String, dynamic>.from(json['branch'] as Map)
              : null,
      createdAt: json['created_at'] != null
          ? DateTime.tryParse(json['created_at'].toString())
          : null,
    );
  }

  String get statusLabel {
    switch (status) {
      case 'active':
        return AppStrings.get('sponsored');
      case 'needs_sponsorship':
        return AppStrings.get('needs_sponsorship');
      default:
        return status ?? '';
    }
  }

  String get genderLabel {
    switch (gender) {
      case 'male':
        return AppStrings.get('male');
      case 'female':
        return AppStrings.get('female');
      default:
        return gender ?? '';
    }
  }

  String? get location {
    if (addressGov != null && addressDist != null) {
      return '$addressGov - $addressDist';
    }
    return addressGov ?? addressDist ?? addressVillage;
  }
}
