import 'dart:convert';
import 'package:http/http.dart' as http;
import 'package:shared_preferences/shared_preferences.dart';
import 'api_service.dart';

class AuthService {
  static String get baseUrl => ApiService.baseUrl;

  static Future<Map<String, dynamic>> login(String email, String password) async {
    try {
      final response = await http.post(
        Uri.parse('$baseUrl/login'),
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
        },
        body: jsonEncode({
          'email': email,
          'password': password,
        }),
      );

      if (response.statusCode == 200) {
        final data = jsonDecode(response.body);
        final token = data['authorisation']['token'];
        final prefs = await SharedPreferences.getInstance();
        await prefs.setString('jwt_token', token);
        return {'status': 'success', 'user': data['user']};
      } else {
        String errorMsg = 'Login failed';
        try {
          final data = jsonDecode(response.body);
          errorMsg = data['message'] ?? errorMsg;
        } catch (_) {
          // If response body is HTML (server crash), use a generic message
        }
        return {
          'status': 'error',
          'message': '[${response.statusCode}] $errorMsg'
        };
      }
    } catch (e) {
      print("Login error: $e");
      return {'status': 'error', 'message': 'Connection error: $e'};
    }
  }

  static Future<Map<String, dynamic>> register(String name, String email, String password) async {
    try {
      final response = await http.post(
        Uri.parse('$baseUrl/register'),
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
        },
        body: jsonEncode({
          'name': name,
          'email': email,
          'password': password,
          'password_confirmation': password, // Backend usually requires confirmation
        }),
      );

      if (response.statusCode == 201 || response.statusCode == 200) {
        final data = jsonDecode(response.body);
        return {'status': 'success', 'authorisation': data['authorisation']};
      } else {
        String errorMsg = 'Registration failed';
        Map<String, dynamic>? errors;
        try {
          final data = jsonDecode(response.body);
          errorMsg = data['message'] ?? errorMsg;
          errors = data['errors'];
        } catch (_) {}
        
        return {
          'status': 'error',
          'message': '[${response.statusCode}] $errorMsg',
          'errors': errors
        };
      }
    } catch (e) {
      return {'status': 'error', 'message': 'Connection error: $e'};
    }
  }

  static Future<String?> getToken() async {
    final prefs = await SharedPreferences.getInstance();
    return prefs.getString('jwt_token');
  }

  static Future<void> logout() async {
    final prefs = await SharedPreferences.getInstance();
    await prefs.remove('jwt_token');
  }

  static Future<bool> isFirstTime() async {
    final prefs = await SharedPreferences.getInstance();
    return prefs.getBool('onboarding_seen') ?? true;
  }

  static Future<void> setOnboardingComplete() async {
    final prefs = await SharedPreferences.getInstance();
    await prefs.setBool('onboarding_seen', false);
  }

  // --- Theme Management ---
  static const String _themeKey = 'theme_mode';

  static Future<String> getThemeMode() async {
    final prefs = await SharedPreferences.getInstance();
    return prefs.getString(_themeKey) ?? 'system';
  }

  static Future<void> setThemeMode(String mode) async {
    final prefs = await SharedPreferences.getInstance();
    await prefs.setString(_themeKey, mode);
  }
}
