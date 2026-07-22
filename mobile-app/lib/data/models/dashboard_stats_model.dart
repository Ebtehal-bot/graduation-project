class DashboardStatsModel {
  final int totalOrphans;
  final int sponsoredOrphans;
  final int unsponsoredOrphans;
  final int totalSponsorships;
  final int activeSponsorships;
  final int pendingSponsorships;
  final int completedSponsorships;
  final int totalSponsors;
  final int expiringSoon;

  const DashboardStatsModel({
    required this.totalOrphans,
    required this.sponsoredOrphans,
    required this.unsponsoredOrphans,
    required this.totalSponsorships,
    required this.activeSponsorships,
    required this.pendingSponsorships,
    required this.completedSponsorships,
    required this.totalSponsors,
    required this.expiringSoon,
  });

  factory DashboardStatsModel.fromJson(Map<String, dynamic> json) {
    int toInt(dynamic value) {
      if (value is int) return value;
      if (value is double) return value.toInt();
      if (value is String) return int.tryParse(value) ?? 0;
      return 0;
    }

    final totalOrphans = toInt(json['total_orphans']);
    final sponsoredOrphans = toInt(json['sponsored_orphans']);

    int unsponsored;
    if (json.containsKey('unsponsored_orphans')) {
      unsponsored = toInt(json['unsponsored_orphans']);
    } else if (json.containsKey('unsponsored')) {
      unsponsored = toInt(json['unsponsored']);
    } else {
      unsponsored = totalOrphans - sponsoredOrphans;
    }

    int completed;
    if (json.containsKey('completed_sponsorships')) {
      completed = toInt(json['completed_sponsorships']);
    } else if (json.containsKey('expired_sponsorships')) {
      completed = toInt(json['expired_sponsorships']);
    } else if (json.containsKey('ended_sponsorships')) {
      completed = toInt(json['ended_sponsorships']);
    } else {
      completed = 0;
    }

    return DashboardStatsModel(
      totalOrphans: totalOrphans,
      sponsoredOrphans: sponsoredOrphans,
      unsponsoredOrphans: unsponsored,
      totalSponsorships: toInt(json['total_sponsorships']),
      activeSponsorships: toInt(json['active_sponsorships']),
      pendingSponsorships: toInt(json['pending_sponsorships']),
      completedSponsorships: completed,
      totalSponsors: toInt(json['total_sponsors']),
      expiringSoon: toInt(json['expiring_soon']),
    );
  }
}
