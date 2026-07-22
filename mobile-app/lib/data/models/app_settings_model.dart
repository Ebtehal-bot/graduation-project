class AppSettingsModel {
  final String siteName;
  final String orgName;
  final String? siteLogo;
  final String? logoDark;
  final String? siteEmail;
  final String? sitePhone;
  final String? siteWhatsapp;
  final String appCurrency;

  const AppSettingsModel({
    required this.siteName,
    required this.orgName,
    this.siteLogo,
    this.logoDark,
    this.siteEmail,
    this.sitePhone,
    this.siteWhatsapp,
    this.appCurrency = 'YER',
  });

  factory AppSettingsModel.fromJson(Map<String, dynamic> json) {
    return AppSettingsModel(
      siteName: json['site_name'] as String? ?? 'نظام إدارة كفالة الأيتام',
      orgName: json['org_name'] as String? ?? 'نظام الأيتام',
      siteLogo: json['site_logo'] as String?,
      logoDark: json['logo_dark'] as String?,
      siteEmail: json['site_email'] as String?,
      sitePhone: json['site_phone'] as String?,
      siteWhatsapp: json['site_whatsapp'] as String?,
      appCurrency: json['app_currency'] as String? ?? 'YER',
    );
  }
}

