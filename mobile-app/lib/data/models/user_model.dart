class UserModel {
  final int id;
  final String name;
  final String email;
  final String? role;
  final int? sponsorId;
  final String? phone;
  final String? address;
  final String? imageUrl;
  final DateTime? joinDate;
  final int totalSponsorships;
  final int activeSponsorships;
  final int sponsoredOrphans;
  final double totalDonations;

  const UserModel({
    required this.id,
    required this.name,
    required this.email,
    this.role,
    this.sponsorId,
    this.phone,
    this.address,
    this.imageUrl,
    this.joinDate,
    this.totalSponsorships = 0,
    this.activeSponsorships = 0,
    this.sponsoredOrphans = 0,
    this.totalDonations = 0.0,
  });

  factory UserModel.fromJson(Map<String, dynamic> json) {
    int? toInt(dynamic value) {
      if (value is int) return value;
      if (value is double) return value.toInt();
      return int.tryParse(value?.toString() ?? '');
    }

    return UserModel(
      id: json['id'] is int
          ? json['id']
          : (json['id'] is double
              ? (json['id'] as double).toInt()
              : int.tryParse(json['id']?.toString() ?? '') ?? 0),
      name: json['name'] as String? ?? json['full_name'] as String? ?? '',
      email: json['email'] as String? ?? json['email_address'] as String? ?? '',
      role: json['role'] as String?,
      sponsorId: toInt(json['sponsor_id']),
      phone: json['phone'] as String? ?? json['mobile'] as String? ?? json['phone_number'] as String?,
      address: json['address'] as String?,
      imageUrl: json['image_url'] as String? ?? json['avatar'] as String? ?? json['photo'] as String?,
      joinDate: json['join_date'] != null
          ? DateTime.tryParse(json['join_date'].toString())
          : json['created_at'] != null
              ? DateTime.tryParse(json['created_at'].toString())
              : null,
      totalSponsorships: toInt(json['total_sponsorships']) ?? 0,
      activeSponsorships: toInt(json['active_sponsorships']) ?? 0,
      sponsoredOrphans: toInt(json['sponsored_orphans']) ?? 0,
      totalDonations: (json['total_donations'] as num?)?.toDouble() ?? 0.0,
    );
  }
}
