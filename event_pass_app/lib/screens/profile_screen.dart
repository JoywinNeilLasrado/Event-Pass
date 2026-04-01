import 'package:flutter/material.dart';
import '../services/auth_service.dart';
import '../services/api_service.dart';
import 'login_screen.dart';
import 'edit_profile_screen.dart';
import 'my_waitlist_screen.dart';
import 'notifications_screen.dart';
import 'help_support_screen.dart';
import 'privacy_policy_screen.dart';
import 'staff_management_screen.dart';
import 'settings_screen.dart';
import 'scanner_screen.dart';
import 'upgrade_screen.dart';
import 'main_screen.dart';

class ProfileScreen extends StatefulWidget {
  const ProfileScreen({super.key});

  @override
  State<ProfileScreen> createState() => _ProfileScreenState();
}

class _ProfileScreenState extends State<ProfileScreen> {
  Map<String, dynamic>? _user;
  bool _isLoading = true;

  @override
  void initState() {
    super.initState();
    _loadProfile();
  }

  Future<void> _loadProfile() async {
    final profile = await ApiService.getProfile();
    if (mounted) {
      setState(() {
        _user = profile['user'];
        _isLoading = false;
      });
    }
  }

  void _logout() async {
    await AuthService.logout();
    if (mounted) {
      Navigator.of(context, rootNavigator: true).pushAndRemoveUntil(
        MaterialPageRoute(builder: (context) => const LoginScreen()),
        (route) => false,
      );
    }
  }

  @override
  Widget build(BuildContext context) {
    if (_isLoading) return const Scaffold(body: Center(child: CircularProgressIndicator()));

    final bool isDark = Theme.of(context).brightness == Brightness.dark;
    final Color textColor = isDark ? Colors.white : Colors.black;
    final Color subTextColor = isDark ? Colors.white70 : Colors.black54;

    final String name = _user?['name'] ?? 'User';
    final String email = _user?['email'] ?? 'Not available';
    final String avatarUrl = _user?['profile_picture_url'] ?? '';
    final bool isOrganizer = _user?['is_organizer'] == 1 || _user?['is_organizer'] == true;
    final bool isStaff = _user?['employer_id'] != null;
    final String kycStatus = _user?['kyc_status'] ?? 'none';
    final bool isPending = kycStatus == 'pending';
    final String role = isOrganizer ? 'ORGANIZER' : (isStaff ? 'STAFF' : (isPending ? 'PENDING APPROVAL' : 'ATTENDEE'));
    final Color roleColor = isOrganizer ? Colors.purple : (isStaff ? Colors.teal : (isPending ? Colors.orange : Colors.blue));

    return Scaffold(
      backgroundColor: isDark ? const Color(0xFF0F172A) : Colors.white,
      appBar: AppBar(
        title: const Text('Profile'),
        actions: [
          IconButton(onPressed: _logout, icon: const Icon(Icons.logout)),
        ],
      ),
      body: SingleChildScrollView(
        padding: const EdgeInsets.all(24),
        child: Column(
          children: [
            Stack(
              children: [
                CircleAvatar(
                  radius: 50,
                  backgroundImage: avatarUrl.isNotEmpty ? NetworkImage(avatarUrl) : null,
                  child: avatarUrl.isEmpty ? const Icon(Icons.person, size: 50) : null,
                ),
                Positioned(
                  bottom: 0,
                  right: 0,
                  child: Container(
                    decoration: const BoxDecoration(color: Color(0xFF6366F1), shape: BoxShape.circle),
                    child: IconButton(
                      icon: const Icon(Icons.edit, color: Colors.white, size: 20),
                      onPressed: () async {
                        final result = await Navigator.push(context, MaterialPageRoute(builder: (c) => EditProfileScreen(user: _user!)));
                        if (result == true) _loadProfile();
                      },
                    ),
                  ),
                ),
              ],
            ),
            const SizedBox(height: 16),
            Text(name, style: TextStyle(fontSize: 24, fontWeight: FontWeight.bold, color: textColor)),
            Text(email, style: TextStyle(color: subTextColor)),
            const SizedBox(height: 8),
            Container(
              padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 4),
              decoration: BoxDecoration(
                color: roleColor.withOpacity(0.1),
                borderRadius: BorderRadius.circular(20),
              ),
              child: Text(
                role,
                style: TextStyle(
                  color: roleColor,
                  fontSize: 10,
                  fontWeight: FontWeight.bold,
                  letterSpacing: 1.2,
                ),
              ),
            ),
            const SizedBox(height: 32),
            _buildProfileItem(context, Icons.notifications_outlined, 'Notifications', () => Navigator.push(context, MaterialPageRoute(builder: (c) => const NotificationsScreen()))),
            _buildProfileItem(context, Icons.history, 'My Waitlist', () => Navigator.push(context, MaterialPageRoute(builder: (c) => const MyWaitlistScreen()))),
            if (isOrganizer || isStaff)
              _buildProfileItem(context, Icons.qr_code_scanner, 'Ticket Scanner', () => Navigator.push(context, MaterialPageRoute(builder: (c) => const ScannerScreen()))),
            _buildProfileItem(context, Icons.settings_outlined, 'App Settings', () => Navigator.push(context, MaterialPageRoute(builder: (c) => const SettingsScreen()))),
            if (isOrganizer && !isStaff)
              _buildProfileItem(context, Icons.group_outlined, 'Manage Team', () => Navigator.push(context, MaterialPageRoute(builder: (c) => const StaffManagementScreen()))),
            const Divider(height: 32),
            _buildProfileItem(context, Icons.help_outline, 'Help & Support', () => Navigator.push(context, MaterialPageRoute(builder: (c) => const HelpSupportScreen()))),
            _buildProfileItem(context, Icons.privacy_tip_outlined, 'Privacy Policy', () => Navigator.push(context, MaterialPageRoute(builder: (c) => const PrivacyPolicyScreen()))),
            const SizedBox(height: 32),
            if (!isOrganizer && !isStaff)
              SizedBox(
                width: double.infinity,
                child: ElevatedButton.icon(
                  style: ElevatedButton.styleFrom(
                    backgroundColor: const Color(0xFF6366F1),
                    foregroundColor: Colors.white,
                    padding: const EdgeInsets.symmetric(vertical: 16),
                    shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
                  ),
                  onPressed: () async {
                    final upgraded = await Navigator.push(context, MaterialPageRoute(builder: (c) => const UpgradeScreen()));
                    if (upgraded == true) {
                      Navigator.pushAndRemoveUntil(context, MaterialPageRoute(builder: (c) => const MainScreen()), (route) => false);
                    }
                  },
                  icon: const Icon(Icons.rocket_launch),
                  label: const Text('Upgrade to Organizer', style: TextStyle(fontSize: 16, fontWeight: FontWeight.bold)),
                ),
              ),
            if (isOrganizer && !isStaff)
              SizedBox(
                width: double.infinity,
                child: OutlinedButton.icon(
                  style: OutlinedButton.styleFrom(
                    foregroundColor: Colors.red,
                    side: const BorderSide(color: Colors.red),
                    padding: const EdgeInsets.symmetric(vertical: 16),
                    shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
                  ),
                  onPressed: _confirmCancelRole,
                  icon: const Icon(Icons.cancel_outlined),
                  label: const Text('Cancel Organizer Role', style: TextStyle(fontSize: 16, fontWeight: FontWeight.bold)),
                ),
              ),
            const SizedBox(height: 16),
            TextButton(
              onPressed: _logout,
              child: const Text('Logout', style: TextStyle(color: Colors.red, fontWeight: FontWeight.bold)),
            ),
          ],
        ),
      ),
    );
  }

  void _confirmCancelRole() {
    showDialog(
      context: context,
      builder: (dialogContext) => AlertDialog(
        title: const Text('Cancel Organizer Role?'),
        content: const Text('Are you sure you want to revert to a standard user? You will lose access to the Dashboard and event creation features.'),
        actions: [
          TextButton(onPressed: () => Navigator.pop(dialogContext), child: const Text('Keep Role')),
          TextButton(
            onPressed: () async {
              Navigator.pop(dialogContext);
              setState(() => _isLoading = true);
              final res = await ApiService.cancelOrganizerRole();
              
              if (!mounted) return;
              
              if (res['status'] != 'error') {
                ScaffoldMessenger.of(context).showSnackBar(const SnackBar(content: Text('Role successfully cancelled.'), backgroundColor: Colors.green));
                Navigator.pushAndRemoveUntil(context, MaterialPageRoute(builder: (c) => const MainScreen()), (route) => false);
              } else {
                setState(() => _isLoading = false);
                ScaffoldMessenger.of(context).showSnackBar(SnackBar(content: Text(res['message'] ?? 'Failed to cancel role.'), backgroundColor: Colors.red));
              }
            },
            child: const Text('Yes, Cancel', style: TextStyle(color: Colors.red)),
          ),
        ],
      ),
    );
  }

  Widget _buildProfileItem(BuildContext context, IconData icon, String title, VoidCallback onTap) {
    return ListTile(
      leading: Icon(icon, color: const Color(0xFF6366F1)),
      title: Text(title, style: const TextStyle(fontWeight: FontWeight.w500)),
      trailing: const Icon(Icons.arrow_forward_ios, size: 16),
      onTap: onTap,
    );
  }
}
