import 'package:flutter/material.dart';
import 'package:url_launcher/url_launcher.dart';

class HelpSupportScreen extends StatelessWidget {
  const HelpSupportScreen({super.key});

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: const Text('Help & Support')),
      body: ListView(
        padding: const EdgeInsets.all(24),
        children: [
          const Icon(Icons.help_outline, size: 80, color: Color(0xFF6366F1)),
          const SizedBox(height: 16),
          const Text(
            'How can we help you?',
            textAlign: TextAlign.center,
            style: TextStyle(fontSize: 24, fontWeight: FontWeight.bold),
          ),
          const SizedBox(height: 32),
          _buildSupportItem(
            context,
            Icons.email_outlined,
            'Email Support',
            'support@eventpass.com',
            () => launchUrl(Uri.parse('mailto:support@eventpass.com')),
          ),
          _buildSupportItem(
            context,
            Icons.phone_outlined,
            'Call Us',
            '+1 (555) 123-4567',
            () => launchUrl(Uri.parse('tel:+15551234567')),
          ),
          _buildSupportItem(
            context,
            Icons.chat_bubble_outline,
            'Live Chat',
            'Chat with our team',
            () {}, // Implementation for chat
          ),
          const SizedBox(height: 32),
          const Text('Frequently Asked Questions', style: TextStyle(fontSize: 18, fontWeight: FontWeight.bold)),
          const SizedBox(height: 16),
          _buildFaqItem('How do I book a ticket?', 'Browse events, select a ticket type, and click "Book Now".'),
          _buildFaqItem('Can I cancel my booking?', 'Yes, you can cancel from the "My Tickets" section up to 24h before the event.'),
          _buildFaqItem('Is my payment secure?', 'Yes, we use industry-standard encryption and payment processors like Cashfree.'),
        ],
      ),
    );
  }

  Widget _buildSupportItem(BuildContext context, IconData icon, String title, String subtitle, VoidCallback onTap) {
    return Card(
      margin: const EdgeInsets.only(bottom: 16),
          shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
      child: ListTile(
        leading: Icon(icon, color: const Color(0xFF6366F1)),
        title: Text(title, style: const TextStyle(fontWeight: FontWeight.bold)),
        subtitle: Text(subtitle),
        onTap: onTap,
        trailing: const Icon(Icons.arrow_forward_ios, size: 16),
      ),
    );
  }

  Widget _buildFaqItem(String question, String answer) {
    return ExpansionTile(
      title: Text(question, style: const TextStyle(fontWeight: FontWeight.w500)),
      children: [
        Padding(
          padding: const EdgeInsets.all(16.0),
          child: Text(answer),
        ),
      ],
    );
  }
}
