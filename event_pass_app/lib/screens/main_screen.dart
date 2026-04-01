import 'package:flutter/material.dart';
import 'events_screen.dart';
import 'my_tickets_screen.dart';
import 'dashboard_screen.dart';
import 'scanner_screen.dart';
import 'explore_screen.dart';
import 'profile_screen.dart';
import '../services/api_service.dart';

class MainScreen extends StatefulWidget {
  const MainScreen({super.key});

  @override
  State<MainScreen> createState() => _MainScreenState();
}

class _MainScreenState extends State<MainScreen> {
  int _selectedIndex = 0;
  bool _isOrganizer = false;
  bool _isStaff = false;
  bool _isLoading = true;

  @override
  void initState() {
    super.initState();
    _checkRole();
    // Fallback: ensure loading does not hang indefinitely
    Future.delayed(const Duration(seconds: 20), () {
      if (mounted && _isLoading) {
        print('FALLBACK TIMER: forcing _isLoading = false');
        setState(() => _isLoading = false);
      }
    });
  }

  Future<void> _checkRole() async {
    try {
      final profile = await ApiService.getProfile().timeout(
        const Duration(seconds: 15),
        onTimeout: () => {'user': {}},
      );
      print('===== PROFILE RESPONSE =====');
      print(profile);
      print('is_organizer value: ${profile['user']?['is_organizer']}');
      print('is_organizer type: ${profile['user']?['is_organizer']?.runtimeType}');
      print('employer_id value: ${profile['user']?['employer_id']}');
      print('============================');
      if (mounted) {
        setState(() {
          final user = profile['user'] ?? {};
          _isOrganizer = (user['is_organizer'] == 1 || user['is_organizer'] == true);
          _isStaff = (user['employer_id'] != null);
          print('_isOrganizer set to: $_isOrganizer');
          print('_isStaff set to: $_isStaff');
        });
      }
    } catch (e) {
      print('_checkRole ERROR: $e');
      if (mounted) {
        setState(() {
          _isOrganizer = false;
          _isStaff = false;
        });
      }
    } finally {
      if (mounted) {
        setState(() {
          _isLoading = false;
        });
      }
    }
  }

  List<Widget> _getPages() {
    return [
      const EventsScreen(),
      const ExploreScreen(),
      const MyTicketsScreen(),
      if (_isOrganizer) const DashboardScreen(),
      if (_isOrganizer || _isStaff) const ScannerScreen(),
      const ProfileScreen(),
    ];
  }

  @override
  Widget build(BuildContext context) {
    if (_isLoading) {
      return const Scaffold(
        body: Center(child: CircularProgressIndicator()),
      );
    }

    final bool isDark = Theme.of(context).brightness == Brightness.dark;
    final Color navColor = isDark ? const Color(0xFF111713) : Colors.white;

    return Scaffold(
      body: IndexedStack(
        index: _selectedIndex,
        children: _getPages(),
      ),
      bottomNavigationBar: Container(
        decoration: BoxDecoration(
          boxShadow: [
            BoxShadow(
              color: Colors.black.withOpacity(0.05),
              blurRadius: 10,
              offset: const Offset(0, -2),
            ),
          ],
        ),
        child: BottomNavigationBar(
          currentIndex: _selectedIndex,
          onTap: (index) => setState(() => _selectedIndex = index),
          type: BottomNavigationBarType.fixed,
          backgroundColor: navColor,
          selectedItemColor: const Color(0xFF6366F1),
          unselectedItemColor: isDark ? Colors.white54 : Colors.grey,
          showSelectedLabels: true,
          showUnselectedLabels: true,
          items: [
            const BottomNavigationBarItem(icon: Icon(Icons.home_outlined), activeIcon: Icon(Icons.home), label: 'Home'),
            const BottomNavigationBarItem(icon: Icon(Icons.explore_outlined), activeIcon: Icon(Icons.explore), label: 'Explore'),
            const BottomNavigationBarItem(icon: Icon(Icons.confirmation_number_outlined), activeIcon: Icon(Icons.confirmation_number), label: 'Tickets'),
            if (_isOrganizer)
              const BottomNavigationBarItem(icon: Icon(Icons.dashboard_outlined), activeIcon: Icon(Icons.dashboard), label: 'Dashboard'),
            if (_isOrganizer || _isStaff)
              const BottomNavigationBarItem(icon: Icon(Icons.qr_code_scanner), activeIcon: Icon(Icons.qr_code_scanner), label: 'Scan'),
            const BottomNavigationBarItem(icon: Icon(Icons.person_outline), activeIcon: Icon(Icons.person), label: 'Profile'),
          ],
        ),
      ),
    );
  }
}
