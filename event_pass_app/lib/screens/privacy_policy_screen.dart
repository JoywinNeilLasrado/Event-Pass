import 'package:flutter/material.dart';

class PrivacyPolicyScreen extends StatelessWidget {
  const PrivacyPolicyScreen({super.key});

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: const Text('Privacy Policy')),
      body: SingleChildScrollView(
        padding: const EdgeInsets.all(24),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            const Text(
              'Privacy Policy',
              style: TextStyle(fontSize: 24, fontWeight: FontWeight.bold),
            ),
            const SizedBox(height: 16),
            const Text(
              'Last Updated: March 27, 2026',
              style: TextStyle(color: Colors.grey, fontStyle: FontStyle.italic),
            ),
            const SizedBox(height: 24),
            _buildSection(
              '1. Information We Collect',
              'We collect information you provide directly to us when you create an account, book a ticket, or contact us for support. This includes your name, email, and payment information.',
            ),
            _buildSection(
              '2. How We Use Information',
              'We use the information we collect to provide, maintain, and improve our services, to process your bookings, and to communicate with you about events.',
            ),
            _buildSection(
              '3. Sharing of Information',
              'We do not share your personal information with third parties except as described in this policy, such as with event organizers for the events you book.',
            ),
            _buildSection(
              '4. Your Choices',
              'You can update your account information at any time from the app settings. You can also contact us to request the deletion of your account.',
            ),
            _buildSection(
                '5. Security',
                'We take reasonable measures to protect your personal information from loss, theft, misuse, and unauthorized access.'),
            const SizedBox(height: 32),
            const Center(
              child: Text(
                '© 2026 EventPass. All rights reserved.',
                style: TextStyle(color: Colors.grey, fontSize: 12),
              ),
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildSection(String title, String content) {
    return Padding(
      padding: const EdgeInsets.only(bottom: 24),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Text(title, style: const TextStyle(fontSize: 18, fontWeight: FontWeight.bold)),
          const SizedBox(height: 8),
          Text(
            content,
            style: const TextStyle(fontSize: 14, height: 1.5, color: Colors.black87),
          ),
        ],
      ),
    );
  }
}
