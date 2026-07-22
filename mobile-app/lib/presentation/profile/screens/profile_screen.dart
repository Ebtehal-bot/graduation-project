import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:go_router/go_router.dart';
import 'package:cached_network_image/cached_network_image.dart';

import '../../../core/localization/app_strings.dart';
import '../../../core/theme/app_colors.dart';
import '../../../data/repositories/auth_repository.dart';
import '../../../shared/widgets/widgets.dart';
import '../../auth/providers/auth_provider.dart';
import '../providers/profile_provider.dart';

class ProfileScreen extends ConsumerStatefulWidget {
  const ProfileScreen({super.key});

  @override
  ConsumerState<ProfileScreen> createState() => _ProfileScreenState();
}

class _ProfileScreenState extends ConsumerState<ProfileScreen> {
  @override
  void initState() {
    super.initState();
    WidgetsBinding.instance.addPostFrameCallback((_) {
      ref.read(profileProvider.notifier).loadProfile();
      ref.read(authProvider.notifier).fetchProfile();
    });
  }

  @override
  Widget build(BuildContext context) {
    final profileState = ref.watch(profileProvider);
    final authState = ref.watch(authProvider);
    final isDark = Theme.of(context).brightness == Brightness.dark;

    return Scaffold(
      appBar: AppBar(
        title: Text(AppStrings.get('profile')),
        actions: [
          IconButton(
            icon: const Icon(Icons.settings_outlined),
            onPressed: () => context.push('/settings'),
          ),
        ],
      ),
      body: profileState.isLoading && profileState.user == null
          ? _buildSkeletons(isDark)
          : profileState.error != null && profileState.user == null
              ? Center(
                  child: AppErrorState(
                    message: profileState.error,
                    onRetry: () =>
                        ref.read(profileProvider.notifier).loadProfile(),
                  ),
                )
              : RefreshIndicator(
                  onRefresh: () async {
                    await ref.read(profileProvider.notifier).loadProfile();
                    await ref.read(authProvider.notifier).fetchProfile();
                  },
                  child: SingleChildScrollView(
                    physics: const AlwaysScrollableScrollPhysics(),
                    child: Column(
                      children: [
                        _buildHeader(profileState, authState, isDark),
                        const SizedBox(height: 24),
                        _buildMenuItems(context, isDark,
                            authState.user?.role?.toLowerCase() == 'admin'),
                        const SizedBox(height: 24),
                      ],
                    ),
                  ),
                ),
    );
  }

  Widget _buildHeader(
      ProfileState profile, AuthState auth, bool isDark) {
    final user = profile.user ?? auth.user;
    final displayName = user?.name ?? auth.userName ?? AppStrings.get('user');
    final email = user?.email ?? '';
    final initial =
        displayName.isNotEmpty ? displayName[0].toUpperCase() : '?';

    return Container(
      width: double.infinity,
      padding: const EdgeInsets.fromLTRB(16, 24, 16, 24),
      child: Column(
        children: [
          CircleAvatar(
            radius: 48,
            backgroundColor: AppColors.primaryLight.withOpacity(0.2),
            backgroundImage: user?.imageUrl != null
                ? CachedNetworkImageProvider(user!.imageUrl!)
                : null,
            child: user?.imageUrl == null
                ? Text(
                    initial,
                    style: const TextStyle(
                      fontSize: 36,
                      fontWeight: FontWeight.bold,
                      color: AppColors.primary,
                    ),
                  )
                : null,
          ),
          const SizedBox(height: 14),
          Text(
            displayName,
            style: TextStyle(
              fontSize: 22,
              fontWeight: FontWeight.bold,
              color: isDark ? Colors.white : AppColors.neutral900,
            ),
          ),
          const SizedBox(height: 4),
          Text(
            email,
            style: TextStyle(
              fontSize: 14,
              color: isDark ? Colors.grey[400] : AppColors.neutral500,
            ),
          ),
          if (user?.phone != null && user!.phone!.isNotEmpty)
            Padding(
              padding: const EdgeInsets.only(top: 2),
              child: Text(
                '📞 ${user.phone}',
                style: TextStyle(
                  fontSize: 13,
                  color: isDark ? Colors.grey[400] : AppColors.neutral500,
                ),
              ),
            ),
          if (user?.address != null && user!.address!.isNotEmpty)
            Padding(
              padding: const EdgeInsets.only(top: 2),
              child: Text(
                '📍 ${user.address}',
                style: TextStyle(
                  fontSize: 13,
                  color: isDark ? Colors.grey[400] : AppColors.neutral500,
                ),
              ),
            ),
          if (user?.joinDate != null)
            Padding(
              padding: const EdgeInsets.only(top: 4),
              child: Text(
                '${AppStrings.get('member_since')} ${_formatDate(user!.joinDate!)}',
                style: TextStyle(
                  fontSize: 12,
                  color: isDark ? Colors.grey[500] : AppColors.neutral400,
                ),
              ),
            ),
        ],
      ),
    );
  }

  Widget _buildMenuItems(BuildContext context, bool isDark, bool isAdmin) {
    return Padding(
      padding: const EdgeInsets.symmetric(horizontal: 16),
      child: Card(
        child: Column(
          children: [
            _menuItem(
              Icons.person_outline,
              AppStrings.get('edit_profile'),
              () => _showEditProfile(context),
              isDark,
            ),
            _menuItem(
              Icons.lock_outline,
              AppStrings.get('change_password'),
              () => _showChangePassword(context),
              isDark,
            ),
            if (isAdmin)
              _menuItem(
                Icons.notifications_outlined,
                AppStrings.get('notifications'),
                () => context.push('/notifications'),
                isDark,
              ),
            _menuItem(
              Icons.settings_outlined,
              AppStrings.get('settings'),
              () => context.push('/settings'),
              isDark,
            ),
            _menuItem(
              Icons.info_outline,
              AppStrings.get('about'),
              () => context.push('/about'),
              isDark,
            ),
            _menuItem(
              Icons.contact_support_outlined,
              AppStrings.get('contact'),
              () => context.push('/contact'),
              isDark,
            ),
            const Divider(height: 1),
            _menuItem(
              Icons.logout_rounded,
              AppStrings.get('logout'),
              () => _showLogoutDialog(context),
              isDark,
              isDestructive: true,
            ),
          ],
        ),
      ),
    );
  }

  Widget _menuItem(IconData icon, String label, VoidCallback onTap,
      bool isDark, {bool isDestructive = false}) {
    return ListTile(
      leading: Icon(
        icon,
        color: isDestructive
            ? AppColors.error
            : isDark
                ? Colors.grey[400]
                : AppColors.neutral600,
      ),
      title: Text(
        label,
        style: TextStyle(
          color: isDestructive
              ? AppColors.error
              : isDark
                  ? Colors.white
                  : AppColors.neutral900,
        ),
      ),
      trailing: Icon(
        Icons.arrow_forward_ios,
        size: 16,
        color: isDark ? Colors.grey[600] : AppColors.neutral400,
      ),
      onTap: onTap,
    );
  }

  void _showEditProfile(BuildContext context) {
    final user = ref.read(authProvider).user;
    final nameController = TextEditingController(text: user?.name ?? '');
    final emailController = TextEditingController(text: user?.email ?? '');
    final phoneController = TextEditingController(text: user?.phone ?? '');
    final addressController = TextEditingController(text: user?.address ?? '');

    showDialog(
      context: context,
      builder: (ctx) => AlertDialog(
        title: Text(AppStrings.get('edit_profile')),
        content: SingleChildScrollView(
          child: Column(
            mainAxisSize: MainAxisSize.min,
            children: [
              TextField(
                controller: nameController,
                decoration: InputDecoration(
                  labelText: AppStrings.get('name'),
                ),
              ),
              const SizedBox(height: 12),
              TextField(
                controller: emailController,
                decoration: InputDecoration(
                  labelText: AppStrings.get('email'),
                ),
                keyboardType: TextInputType.emailAddress,
              ),
              const SizedBox(height: 12),
              TextField(
                controller: phoneController,
                decoration: InputDecoration(
                  labelText: AppStrings.get('phone'),
                ),
                keyboardType: TextInputType.phone,
              ),
              const SizedBox(height: 12),
              TextField(
                controller: addressController,
                decoration: InputDecoration(
                  labelText: AppStrings.get('address_label'),
                ),
              ),
            ],
          ),
        ),
        actions: [
          TextButton(
            onPressed: () => Navigator.pop(ctx),
            child: Text(AppStrings.get('cancel')),
          ),
          ElevatedButton(
            onPressed: () async {
              try {
                await ref.read(authRepositoryProvider).updateProfile(
                      name: nameController.text,
                      email: emailController.text,
                      phone: phoneController.text,
                      address: addressController.text,
                    );
                if (ctx.mounted) Navigator.pop(ctx);
                await ref.read(profileProvider.notifier).loadProfile();
                await ref.read(authProvider.notifier).fetchProfile();
              } catch (e) {
                if (ctx.mounted) {
                  ScaffoldMessenger.of(ctx).showSnackBar(
                    SnackBar(content: Text('$e')),
                  );
                }
              }
            },
            child: Text(AppStrings.get('save')),
          ),
        ],
      ),
    );
  }

  void _showChangePassword(BuildContext context) {
    final currentController = TextEditingController();
    final newController = TextEditingController();
    final confirmController = TextEditingController();

    showDialog(
      context: context,
      builder: (ctx) => AlertDialog(
        title: Text(AppStrings.get('change_password')),
        content: Column(
          mainAxisSize: MainAxisSize.min,
          children: [
            TextField(
              controller: currentController,
              decoration: InputDecoration(labelText: AppStrings.get('current_password')),
              obscureText: true,
            ),
            const SizedBox(height: 12),
            TextField(
              controller: newController,
              decoration: InputDecoration(labelText: AppStrings.get('new_password')),
              obscureText: true,
            ),
            const SizedBox(height: 12),
            TextField(
              controller: confirmController,
              decoration: InputDecoration(labelText: AppStrings.get('confirm_password')),
              obscureText: true,
            ),
          ],
        ),
        actions: [
          TextButton(
            onPressed: () => Navigator.pop(ctx),
            child: Text(AppStrings.get('cancel')),
          ),
          ElevatedButton(
            onPressed: () async {
              try {
                await ref.read(authRepositoryProvider).changePassword(
                      currentPassword: currentController.text,
                      newPassword: newController.text,
                      newPasswordConfirmation: confirmController.text,
                    );
                if (ctx.mounted) Navigator.pop(ctx);
                if (ctx.mounted) {
                  ScaffoldMessenger.of(ctx).showSnackBar(
                    SnackBar(content: Text(AppStrings.get('password_changed'))),
                  );
                }
              } catch (e) {
                if (ctx.mounted) {
                  ScaffoldMessenger.of(ctx).showSnackBar(
                    SnackBar(content: Text('$e')),
                  );
                }
              }
            },
            child: Text(AppStrings.get('save')),
          ),
        ],
      ),
    );
  }

  void _showLogoutDialog(BuildContext context) {
    showDialog(
      context: context,
      builder: (ctx) => AlertDialog(
        title: Text(AppStrings.get('logout')),
        content: Text(AppStrings.get('logout_confirmation')),
        actions: [
          TextButton(
            onPressed: () => Navigator.pop(ctx),
            child: Text(AppStrings.get('cancel')),
          ),
          ElevatedButton(
            onPressed: () {
              Navigator.pop(ctx);
              ref.read(authProvider.notifier).logout();
              context.go('/login');
            },
            style: ElevatedButton.styleFrom(backgroundColor: AppColors.error),
            child: Text(AppStrings.get('logout')),
          ),
        ],
      ),
    );
  }

  Widget _buildSkeletons(bool isDark) {
    return SingleChildScrollView(
      child: Column(
        children: [
          const SizedBox(height: 40),
          const ShimmerPlaceholder(width: 96, height: 96, borderRadius: 48),
          const SizedBox(height: 16),
          const ShimmerPlaceholder(width: 150, height: 20),
          const SizedBox(height: 8),
          const ShimmerPlaceholder(width: 200, height: 16),
          const SizedBox(height: 24),
          Padding(
            padding: const EdgeInsets.symmetric(horizontal: 16),
            child: ShimmerLoading(
              child: Container(
                height: 300,
                decoration: BoxDecoration(
                  color: isDark ? Colors.grey[800] : Colors.white,
                  borderRadius: BorderRadius.circular(20),
                ),
              ),
            ),
          ),
        ],
      ),
    );
  }

  String _formatDate(DateTime dt) {
    return '${dt.year}/${dt.month}/${dt.day}';
  }

}
