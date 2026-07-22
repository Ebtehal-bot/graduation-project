class ApiEndpoints {
  ApiEndpoints._();

  static const String baseUrl = 'http://172.16.3.203:8000/api';

  static const String register = '/register';
  static const String login = '/login';
  static const String profile = '/profile';
  static const String logout = '/logout';
  static const String updateProfile = '/user/profile';
  static const String changePassword = '/user/change-password';

  static const String orphans = '/orphans';
  static String orphanDetails(int id) => '/orphans/$id';
  static const String orphanSearch = '/orphans/search';
  static const String availableOrphans = '/orphans/available';

  static const String sponsorships = '/sponsorships';
  static const String userSponsorships = '/user/sponsorships';
  static const String activeSponsorships = '/sponsorships/active';
  static String sponsorshipDetails(int id) => '/sponsorships/$id';
  static const String createSponsorship = '/sponsorships';

  static const String notifications = '/notifications';
  static String markNotificationRead(String id) => '/notifications/$id/read';
  static const String markAllNotificationsRead = '/notifications/read-all';
  static const String unreadNotificationsCount = '/notifications/unread-count';

  static const String dashboardStats = '/dashboard/stats';
  static const String dashboardRecentActivities = '/dashboard/recent-activities';
  static const String dashboardGrowthTrends = '/dashboard/growth-trends';

  static const String sponsorReports = '/reports/sponsor';
  static const String settings = '/settings';
}
