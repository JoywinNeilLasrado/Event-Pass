import 'package:flutter/material.dart';
import 'login_screen.dart';
import '../services/auth_service.dart';

class OnboardingScreen extends StatefulWidget {
  const OnboardingScreen({super.key});

  @override
  State<OnboardingScreen> createState() => _OnboardingScreenState();
}

class _OnboardingScreenState extends State<OnboardingScreen> {
  final PageController _pageController = PageController();
  int _currentPage = 0;

  final List<Map<String, String>> _onboardingData = [
    {
      'title': 'Discover Events',
      'description': 'Find the hottest concerts, workshops, and meetups happening right now.',
      'icon': 'search',
    },
    {
      'title': 'Seamless Booking',
      'description': 'Secure your spot in seconds with our fast and secure checkout system.',
      'icon': 'confirmation_number',
    },
    {
      'title': 'Easy Access',
      'description': 'No more paper tickets. Your digital QR codes are always in your pocket.',
      'icon': 'qr_code',
    },
  ];

  void _onFinish() async {
    await AuthService.setOnboardingComplete();
    if (mounted) {
      Navigator.pushReplacement(
        context,
        MaterialPageRoute(builder: (context) => const LoginScreen()),
      );
    }
  }

  @override
  Widget build(BuildContext context) {
    final bool isDark = Theme.of(context).brightness == Brightness.dark;
    final Color bgColor = isDark ? const Color(0xFF0F172A) : Colors.white;
    final Color textColor = isDark ? Colors.white : const Color(0xFF1E293B);
    final Color subTextColor = isDark ? Colors.white70 : const Color(0xFF64748B);

    return Scaffold(
      backgroundColor: bgColor,
      body: SafeArea(
        child: Column(
          children: [
            Expanded(
              child: PageView.builder(
                controller: _pageController,
                itemCount: _onboardingData.length,
                onPageChanged: (index) => setState(() => _currentPage = index),
                itemBuilder: (context, index) => _buildSlide(index, isDark, textColor, subTextColor),
              ),
            ),
            _buildBottomControls(isDark, textColor),
          ],
        ),
      ),
    );
  }

  Widget _buildSlide(int index, bool isDark, Color textColor, Color subTextColor) {
    final data = _onboardingData[index];
    IconData icon;
    switch (data['icon']) {
      case 'search': icon = Icons.search_outlined; break;
      case 'confirmation_number': icon = Icons.confirmation_number_outlined; break;
      case 'qr_code': icon = Icons.qr_code_2_outlined; break;
      default: icon = Icons.event;
    }

    return Padding(
      padding: const EdgeInsets.all(40.0),
      child: Column(
        mainAxisAlignment: MainAxisAlignment.center,
        children: [
          Container(
            padding: const EdgeInsets.all(40),
            decoration: BoxDecoration(
              color: const Color(0xFF6366F1).withOpacity(0.1),
              shape: BoxShape.circle,
            ),
            child: Icon(icon, size: 100, color: const Color(0xFF6366F1)),
          ),
          const SizedBox(height: 60),
          Text(
            data['title']!,
            textAlign: TextAlign.center,
            style: TextStyle(fontSize: 28, fontWeight: FontWeight.w900, color: textColor),
          ),
          const SizedBox(height: 20),
          Text(
            data['description']!,
            textAlign: TextAlign.center,
            style: TextStyle(fontSize: 16, color: subTextColor, height: 1.5),
          ),
        ],
      ),
    );
  }

  Widget _buildBottomControls(bool isDark, Color textColor) {
    return Padding(
      padding: const EdgeInsets.all(40.0),
      child: Row(
        mainAxisAlignment: MainAxisAlignment.spaceBetween,
        children: [
          Row(
            children: List.generate(
              _onboardingData.length,
              (index) => Container(
                margin: const EdgeInsets.only(right: 8),
                width: _currentPage == index ? 24 : 8,
                height: 8,
                decoration: BoxDecoration(
                  color: _currentPage == index ? const Color(0xFF6366F1) : (isDark ? Colors.white24 : const Color(0xFFCBD5E1)),
                  borderRadius: BorderRadius.circular(4),
                ),
              ),
            ),
          ),
          ElevatedButton(
            onPressed: () {
              if (_currentPage == _onboardingData.length - 1) {
                _onFinish();
              } else {
                _pageController.nextPage(
                  duration: const Duration(milliseconds: 300),
                  curve: Curves.easeInOut,
                );
              }
            },
            style: ElevatedButton.styleFrom(
              backgroundColor: isDark ? Colors.white : const Color(0xFF1E293B),
              foregroundColor: isDark ? Colors.black : Colors.white,
              padding: const EdgeInsets.symmetric(horizontal: 32, vertical: 16),
              shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
              elevation: 0,
            ),
            child: Text(_currentPage == _onboardingData.length - 1 ? 'Get Started' : 'Next'),
          ),
        ],
      ),
    );
  }
}
