import 'package:flutter/material.dart';
import '../services/auth_service.dart';
import '../main.dart';
import 'about_screen.dart';

class SettingsScreen extends StatefulWidget {
  const SettingsScreen({super.key});

  @override
  State<SettingsScreen> createState() => _SettingsScreenState();
}

class _SettingsScreenState extends State<SettingsScreen> {
  bool _isDarkMode = false;
  bool _notificationsEnabled = true;
  bool _emailUpdates = true;

  @override
  void initState() {
    super.initState();
    _loadSettings();
  }

  Future<void> _loadSettings() async {
    final mode = await AuthService.getThemeMode();
    setState(() {
      _isDarkMode = mode == 'dark';
    });
  }

  void _toggleTheme(bool value) async {
    setState(() {
      _isDarkMode = value;
    });
    final mode = value ? 'dark' : 'light';
    await AuthService.setThemeMode(mode);
    themeNotifier.value = value ? ThemeMode.dark : ThemeMode.light;
  }

  @override
  Widget build(BuildContext context) {
    final bool isDark = Theme.of(context).brightness == Brightness.dark;
    final Color textColor = isDark ? Colors.white : const Color(0xFF1E293B);

    return Scaffold(
      backgroundColor: isDark ? const Color(0xFF0F172A) : const Color(0xFFF8FAFC),
      appBar: AppBar(
        title: const Text('Settings', style: TextStyle(fontWeight: FontWeight.bold)),
        backgroundColor: Colors.transparent,
        elevation: 0,
        foregroundColor: textColor,
      ),
      body: ListView(
        padding: const EdgeInsets.all(24),
        children: [
          _buildSectionHeader('Appearance'),
          _buildSettingsTile(
            Icons.dark_mode_outlined,
            'Dark Mode',
            'Switch between light and dark themes',
            Switch(
              value: _isDarkMode,
              onChanged: _toggleTheme,
              activeThumbColor: const Color(0xFF6366F1),
            ),
          ),
          const SizedBox(height: 32),
          _buildSectionHeader('Notifications'),
          _buildSettingsTile(
            Icons.notifications_active_outlined,
            'Push Notifications',
            'Receive alerts for upcoming events',
            Switch(
              value: _notificationsEnabled,
              onChanged: (val) => setState(() => _notificationsEnabled = val),
              activeThumbColor: const Color(0xFF6366F1),
            ),
          ),
          _buildSettingsTile(
            Icons.email_outlined,
            'Email Updates',
            'Get newsletters and special offers',
            Switch(
              value: _emailUpdates,
              onChanged: (val) => setState(() => _emailUpdates = val),
              activeThumbColor: const Color(0xFF6366F1),
            ),
          ),
          const SizedBox(height: 32),
          _buildSectionHeader('System'),
          _buildSettingsTile(
            Icons.language_outlined,
            'Language',
            'English (Default)',
            const Icon(Icons.arrow_forward_ios, size: 16),
            onTap: () {},
          ),
          _buildSettingsTile(
            Icons.info_outline,
            'About App',
            'Version, License, and Socials',
            const Icon(Icons.arrow_forward_ios, size: 16),
            onTap: () {
              Navigator.push(context, MaterialPageRoute(builder: (c) => const AboutScreen()));
            },
          ),
          _buildSettingsTile(
            Icons.storage_outlined,
            'Clear Cache',
            'Free up some space',
            const SizedBox(),
            onTap: () {
              ScaffoldMessenger.of(context).showSnackBar(const SnackBar(content: Text('Cache cleared!')));
            },
          ),
        ],
      ),
    );
  }

  Widget _buildSectionHeader(String title) {
    return Padding(
      padding: const EdgeInsets.only(bottom: 16),
      child: Text(
        title.toUpperCase(),
        style: const TextStyle(
          color: Colors.grey,
          fontSize: 12,
          fontWeight: FontWeight.bold,
          letterSpacing: 1.2,
        ),
      ),
    );
  }

  Widget _buildSettingsTile(IconData icon, String title, String subtitle, Widget trailing, {VoidCallback? onTap}) {
    final bool isDark = Theme.of(context).brightness == Brightness.dark;
    final Color cardColor = isDark ? const Color(0xFF1E293B) : Colors.white;

    return Container(
      margin: const EdgeInsets.only(bottom: 12),
      decoration: BoxDecoration(
        color: cardColor,
        borderRadius: BorderRadius.circular(16),
        boxShadow: [
          BoxShadow(color: Colors.black.withOpacity(0.03), blurRadius: 10, offset: const Offset(0, 4)),
        ],
      ),
      child: ListTile(
        onTap: onTap,
        leading: Container(
          padding: const EdgeInsets.all(8),
          decoration: BoxDecoration(color: const Color(0xFF6366F1).withOpacity(0.1), shape: BoxShape.circle),
          child: Icon(icon, color: const Color(0xFF6366F1), size: 20),
        ),
        title: Text(title, style: const TextStyle(fontWeight: FontWeight.bold)),
        subtitle: Text(subtitle, style: const TextStyle(fontSize: 12, color: Colors.grey)),
        trailing: trailing,
      ),
    );
  }
}
