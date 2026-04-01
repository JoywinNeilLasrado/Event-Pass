import 'package:flutter/material.dart';
import 'package:url_launcher/url_launcher.dart';

class AboutScreen extends StatelessWidget {
  const AboutScreen({super.key});

  @override
  Widget build(BuildContext context) {
    final bool isDark = Theme.of(context).brightness == Brightness.dark;
    final Color textColor = isDark ? Colors.white : const Color(0xFF1E293B);
    final Color subTextColor = isDark ? Colors.white70 : Colors.black54;

    return Scaffold(
      backgroundColor: isDark ? const Color(0xFF0F172A) : Colors.white,
      appBar: AppBar(
        title: const Text('About App'),
        backgroundColor: Colors.transparent,
        elevation: 0,
        foregroundColor: textColor,
      ),
      body: SingleChildScrollView(
        padding: const EdgeInsets.all(24),
        child: Column(
          children: [
            const SizedBox(height: 20),
            // App Logo Placeholder
            Container(
              width: 100,
              height: 100,
              decoration: BoxDecoration(
                color: const Color(0xFF6366F1),
                borderRadius: BorderRadius.circular(24),
                boxShadow: [
                  BoxShadow(color: const Color(0xFF6366F1).withOpacity(0.3), blurRadius: 20, offset: const Offset(0, 10)),
                ],
              ),
              child: const Icon(Icons.bolt, color: Colors.white, size: 60),
            ),
            const SizedBox(height: 24),
            Text(
              'EventPass',
              style: TextStyle(fontSize: 28, fontWeight: FontWeight.bold, color: textColor),
            ),
            const SizedBox(height: 8),
            Text(
              'Version 1.0.0 (Build 1)',
              style: TextStyle(color: subTextColor, fontSize: 14),
            ),
            const SizedBox(height: 48),
            _buildAboutCard(
              context,
              'Description',
              'EventPass is a complete event management solution. From finding the best events near you to seamless check-ins for organizers, we make event handling easy and professional.',
              isDark,
            ),
            const SizedBox(height: 16),
            _buildAboutCard(
              context,
              'Developers',
              'Developed with ❤️ by the EventPass Team.\nLead Developer: Joywin Neil Lasrado',
              isDark,
            ),
            const SizedBox(height: 32),
            const Text('Connect with us', style: TextStyle(fontWeight: FontWeight.bold, fontSize: 16)),
            const SizedBox(height: 16),
            Row(
              mainAxisAlignment: MainAxisAlignment.center,
              children: [
                _buildSocialIcon(Icons.language, 'https://eventpass.com'),
                const SizedBox(width: 20),
                _buildSocialIcon(Icons.camera_alt_outlined, 'https://instagram.com/eventpass'),
                const SizedBox(width: 20),
                _buildSocialIcon(Icons.alternate_email, 'https://twitter.com/eventpass'),
              ],
            ),
            const SizedBox(height: 48),
            Text(
              '© 2026 EventPass Inc. All rights reserved.',
              style: TextStyle(color: subTextColor, fontSize: 12),
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildAboutCard(BuildContext context, String title, String content, bool isDark) {
    return Container(
      width: double.infinity,
      padding: const EdgeInsets.all(20),
      decoration: BoxDecoration(
        color: isDark ? const Color(0xFF1E293B) : const Color(0xFFF8FAFC),
        borderRadius: BorderRadius.circular(16),
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Text(title, style: const TextStyle(fontWeight: FontWeight.bold, fontSize: 14, color: Color(0xFF6366F1))),
          const SizedBox(height: 8),
          Text(content, style: const TextStyle(fontSize: 14, height: 1.5)),
        ],
      ),
    );
  }

  Widget _buildSocialIcon(IconData icon, String url) {
    return InkWell(
      onTap: () => launchUrl(Uri.parse(url)),
      child: Container(
        padding: const EdgeInsets.all(12),
        decoration: BoxDecoration(
          border: Border.all(color: Colors.grey.withOpacity(0.3)),
          shape: BoxShape.circle,
        ),
        child: Icon(icon, size: 24),
      ),
    );
  }
}
