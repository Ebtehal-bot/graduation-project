class RouteNames {
  RouteNames._();

  static const String splash = '/splash';
  static const String login = '/login';

  // Shell routes (bottom navigation)
  static const String home = '/home';
  static const String orphans = '/orphans';
  static const String mySponsorships = '/my-sponsorships';
  static const String profile = '/profile';

  // Auth
  static const String register = '/register';

  // Pushed routes
  static const String orphanDetails = '/orphans/:orphanId';
  static const String sponsorshipRequest = '/sponsorship-request';
  static const String notifications = '/notifications';
  static const String settings = '/settings';
  static const String favorites = '/favorites';
  static const String reports = '/reports';
  static const String sponsorshipTimeline = '/sponsorship-timeline/:sponsorshipId';
  static const String about = '/about';
  static const String contact = '/contact';
}
