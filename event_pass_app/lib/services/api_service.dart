import 'dart:convert';
import 'dart:io';
import 'package:http/http.dart' as http;
import 'auth_service.dart';

class ApiService {
  // SET THIS TO true FOR LOCAL TESTING, false FOR CLOUD TESTING
  static const bool useLocal = true;

  static String get baseUrl {
    if (useLocal) {
      // Use 10.0.2.2 for Android Emulator, localhost for iOS/Web
      return 'http://10.0.2.2:8000/api';
    }
    return 'https://passage.viewdns.net/api';
  }

  static Future<Map<String, dynamic>> scanTicket(String qrData) async {
    final token = await AuthService.getToken();

    if (token == null) {
      return {'status': 'error', 'message': 'Not logged in'};
    }

    try {
      final response = await http.post(
        Uri.parse('$baseUrl/scan'),
        headers: {
          'Accept': 'application/json',
          'Authorization': 'Bearer $token',
        },
        body: {'qr_data': qrData},
      );

      return jsonDecode(response.body);
    } catch (e) {
      return {'status': 'error', 'message': 'Connection error: $e'};
    }
  }

  static Future<Map<String, dynamic>> getEvents({
    String search = '',
    String category = '',
  }) async {
    try {
      final queryParams = <String>[];
      if (search.isNotEmpty) queryParams.add('search=$search');
      if (category.isNotEmpty && category != 'All Categories') {
        queryParams.add('category=$category');
      }

      final queryString = queryParams.isNotEmpty
          ? '?${queryParams.join('&')}'
          : '';

      final response = await http.get(
        Uri.parse('$baseUrl/events$queryString'),
        headers: {'Accept': 'application/json'},
      );
      if (response.statusCode == 200) {
        return jsonDecode(response.body);
      } else {
        return {'status': 'error', 'message': 'Failed to load events'};
      }
    } catch (e) {
      return {'status': 'error', 'message': 'Connection error: $e'};
    }
  }

  static Future<Map<String, dynamic>> bookEvent({
    required int eventId,
    required int ticketTypeId,
    required int quantity,
    String? promoCode,
  }) async {
    try {
      final token = await AuthService.getToken();
      final response = await http.post(
        Uri.parse('$baseUrl/events/$eventId/book'),
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
          if (token != null) 'Authorization': 'Bearer $token',
        },
        body: jsonEncode({
          'ticket_type_id': ticketTypeId,
          'quantity': quantity,
          if (promoCode != null && promoCode.isNotEmpty)
            'promo_code': promoCode,
        }),
      );
      if (response.statusCode == 200) {
        return jsonDecode(response.body);
      } else {
        final data = jsonDecode(response.body);
        return {
          'status': 'error',
          'message': data['error'] ?? 'Booking failed',
        };
      }
    } catch (e) {
      return {'status': 'error', 'message': 'Connection error: $e'};
    }
  }

  static Future<Map<String, dynamic>> retryPayment(int bookingId) async {
    try {
      final token = await AuthService.getToken();
      final response = await http.post(
        Uri.parse('$baseUrl/bookings/$bookingId/pay'),
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
          if (token != null) 'Authorization': 'Bearer $token',
        },
      );

      if (response.statusCode == 200) {
        return jsonDecode(response.body);
      } else {
        final data = jsonDecode(response.body);
        return {
          'status': 'error',
          'message': data['error'] ?? 'Payment retry failed',
        };
      }
    } catch (e) {
      return {'status': 'error', 'message': e.toString()};
    }
  }

  static Future<Map<String, dynamic>> getMyTickets() async {
    try {
      final token = await AuthService.getToken();
      final response = await http.get(
        Uri.parse('$baseUrl/my-tickets'),
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
          if (token != null) 'Authorization': 'Bearer $token',
        },
      );
      if (response.statusCode == 200) {
        return jsonDecode(response.body);
      } else {
        return {'status': 'error', 'message': 'Failed to load tickets'};
      }
    } catch (e) {
      return {'status': 'error', 'message': 'Connection error: $e'};
    }
  }

  static Future<Map<String, dynamic>> getProfile() async {
    try {
      final token = await AuthService.getToken();
      final response = await http.get(
        Uri.parse('$baseUrl/profile'),
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
          if (token != null) 'Authorization': 'Bearer $token',
        },
      );
      if (response.statusCode == 200) {
        return jsonDecode(response.body);
      } else {
        return {'status': 'error', 'message': 'Failed to load profile'};
      }
    } catch (e) {
      return {'status': 'error', 'message': 'Connection error: $e'};
    }
  }

  static Future<Map<String, dynamic>> getDashboardStats() async {
    try {
      final token = await AuthService.getToken();
      final response = await http.get(
        Uri.parse('$baseUrl/dashboard'),
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
          if (token != null) 'Authorization': 'Bearer $token',
        },
      );
      if (response.statusCode == 200) {
        return jsonDecode(response.body);
      } else {
        return {
          'status': 'error',
          'message': 'Failed to load dashboard statistics',
        };
      }
    } catch (e) {
      return {'status': 'error', 'message': 'Connection error: $e'};
    }
  }

  static Future<Map<String, dynamic>> getEventAttendees(
    int eventId, {
    String search = '',
  }) async {
    try {
      final token = await AuthService.getToken();
      final query = search.isNotEmpty ? '?search=$search' : '';
      final response = await http.get(
        Uri.parse('$baseUrl/events/$eventId/attendees$query'),
        headers: {
          'Accept': 'application/json',
          if (token != null) 'Authorization': 'Bearer $token',
        },
      );
      return jsonDecode(response.body);
    } catch (e) {
      return {'status': 'error', 'message': 'Connection error: $e'};
    }
  }

  static Future<Map<String, dynamic>> checkInAttendee(int bookingId) async {
    try {
      final token = await AuthService.getToken();
      final response = await http.post(
        Uri.parse('$baseUrl/tickets/checkin/$bookingId'),
        headers: {
          'Accept': 'application/json',
          if (token != null) 'Authorization': 'Bearer $token',
        },
      );
      return jsonDecode(response.body);
    } catch (e) {
      return {'status': 'error', 'message': 'Connection error: $e'};
    }
  }

  static Future<Map<String, dynamic>> messageAttendees(
    int eventId,
    String subject,
    String message,
  ) async {
    try {
      final token = await AuthService.getToken();
      final response = await http.post(
        Uri.parse('$baseUrl/events/$eventId/message-attendees'),
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
          if (token != null) 'Authorization': 'Bearer $token',
        },
        body: jsonEncode({'subject': subject, 'message': message}),
      );
      return jsonDecode(response.body);
    } catch (e) {
      return {'status': 'error', 'message': 'Connection error: $e'};
    }
  }

  static Future<Map<String, dynamic>> getTeam() async {
    try {
      final token = await AuthService.getToken();
      final response = await http.get(
        Uri.parse('$baseUrl/team'),
        headers: {
          'Accept': 'application/json',
          if (token != null) 'Authorization': 'Bearer $token',
        },
      );
      return jsonDecode(response.body);
    } catch (e) {
      return {'status': 'error', 'message': 'Connection error: $e'};
    }
  }

  static Future<Map<String, dynamic>> addStaff(String email) async {
    try {
      final token = await AuthService.getToken();
      final response = await http.post(
        Uri.parse('$baseUrl/team/staff'),
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
          if (token != null) 'Authorization': 'Bearer $token',
        },
        body: jsonEncode({'email': email}),
      );
      return jsonDecode(response.body);
    } catch (e) {
      return {'status': 'error', 'message': 'Connection error: $e'};
    }
  }

  static Future<Map<String, dynamic>> removeStaff(int staffId) async {
    try {
      final token = await AuthService.getToken();
      final response = await http.delete(
        Uri.parse('$baseUrl/team/staff/$staffId'),
        headers: {
          'Accept': 'application/json',
          if (token != null) 'Authorization': 'Bearer $token',
        },
      );
      return jsonDecode(response.body);
    } catch (e) {
      return {'status': 'error', 'message': 'Connection error: $e'};
    }
  }

  static Future<Map<String, dynamic>> createEvent({
    required Map<String, dynamic> eventData,
    File? posterImage,
    List<File>? galleryImages,
  }) async {
    try {
      final token = await AuthService.getToken();
      var request = http.MultipartRequest('POST', Uri.parse('$baseUrl/events'));

      request.headers.addAll({
        'Accept': 'application/json',
        if (token != null) 'Authorization': 'Bearer $token',
      });

      // Simple fields
      request.fields['title'] = eventData['title'];
      request.fields['description'] = eventData['description'];
      request.fields['date'] = eventData['date'];
      request.fields['time'] = eventData['time'];
      request.fields['location'] = eventData['location'];
      request.fields['category_id'] = eventData['category_id'].toString();
      request.fields['is_featured'] = (eventData['is_featured'] ?? false)
          .toString();

      // Handle Tickets Array
      final List tickets = eventData['tickets'] ?? [];
      for (int i = 0; i < tickets.length; i++) {
        final ticket = tickets[i];
        request.fields['tickets[$i][name]'] = ticket['name'];
        request.fields['tickets[$i][price]'] = ticket['price'].toString();
        request.fields['tickets[$i][capacity]'] = ticket['capacity'].toString();
        if (ticket['description'] != null) {
          request.fields['tickets[$i][description]'] = ticket['description'];
        }
      }

      // Handle Poster Image
      if (posterImage != null) {
        request.files.add(
          await http.MultipartFile.fromPath('poster_image', posterImage.path),
        );
      }

      // Handle Gallery Images
      if (galleryImages != null && galleryImages.isNotEmpty) {
        for (var image in galleryImages) {
          request.files.add(
            await http.MultipartFile.fromPath('images[]', image.path),
          );
        }
      }

      final streamedResponse = await request.send();
      final response = await http.Response.fromStream(streamedResponse);

      return jsonDecode(response.body);
    } catch (e) {
      return {'status': 'error', 'message': 'Connection error: $e'};
    }
  }

  static Future<Map<String, dynamic>> updateEvent({
    required int eventId,
    required Map<String, dynamic> eventData,
    File? posterImage,
    List<File>? galleryImages,
  }) async {
    try {
      final token = await AuthService.getToken();
      // Laravel often requires POST with _method=PUT for multipart requests
      var request = http.MultipartRequest(
        'POST',
        Uri.parse('$baseUrl/events/$eventId'),
      );

      request.headers.addAll({
        'Accept': 'application/json',
        if (token != null) 'Authorization': 'Bearer $token',
      });

      request.fields['_method'] = 'PUT';

      // Simple fields
      request.fields['title'] = eventData['title'];
      request.fields['description'] = eventData['description'];
      request.fields['date'] = eventData['date'];
      request.fields['time'] = eventData['time'];
      request.fields['location'] = eventData['location'];
      request.fields['category_id'] = eventData['category_id'].toString();
      request.fields['is_featured'] = (eventData['is_featured'] ?? false)
          .toString();

      // Handle Tickets Array
      final List tickets = eventData['tickets'] ?? [];
      for (int i = 0; i < tickets.length; i++) {
        final ticket = tickets[i];
        if (ticket['id'] != null) {
          request.fields['tickets[$i][id]'] = ticket['id'].toString();
        }
        request.fields['tickets[$i][name]'] = ticket['name'];
        request.fields['tickets[$i][price]'] = ticket['price'].toString();
        request.fields['tickets[$i][capacity]'] = ticket['capacity'].toString();
        if (ticket['description'] != null) {
          request.fields['tickets[$i][description]'] = ticket['description'];
        }
      }

      // Handle Poster Image
      if (posterImage != null) {
        request.files.add(
          await http.MultipartFile.fromPath('poster_image', posterImage.path),
        );
      }

      // Handle Gallery Images
      if (galleryImages != null && galleryImages.isNotEmpty) {
        for (var image in galleryImages) {
          request.files.add(
            await http.MultipartFile.fromPath('images[]', image.path),
          );
        }
      }

      final streamedResponse = await request.send();
      final response = await http.Response.fromStream(streamedResponse);

      return jsonDecode(response.body);
    } catch (e) {
      return {'status': 'error', 'message': 'Connection error: $e'};
    }
  }

  static Future<Map<String, dynamic>> getWaitlist() async {
    try {
      final token = await AuthService.getToken();
      final response = await http.get(
        Uri.parse('$baseUrl/my-waitlist'),
        headers: {
          'Accept': 'application/json',
          if (token != null) 'Authorization': 'Bearer $token',
        },
      );
      return jsonDecode(response.body);
    } catch (e) {
      return {'status': 'error', 'message': 'Connection error: $e'};
    }
  }

  static Future<Map<String, dynamic>> joinWaitlist(
    int eventId,
    int ticketTypeId,
  ) async {
    try {
      final token = await AuthService.getToken();
      final response = await http.post(
        Uri.parse('$baseUrl/events/$eventId/waitlist'),
        headers: {
          'Accept': 'application/json',
          'Content-Type': 'application/json',
          if (token != null) 'Authorization': 'Bearer $token',
        },
        body: jsonEncode({'ticket_type_id': ticketTypeId}),
      );
      return jsonDecode(response.body);
    } catch (e) {
      return {'status': 'error', 'message': 'Connection error: $e'};
    }
  }

  static Future<Map<String, dynamic>> updateProfile({
    required String name,
    required String email,
    String? bio,
    File? profilePicture,
  }) async {
    try {
      final token = await AuthService.getToken();
      var request = http.MultipartRequest(
        'POST',
        Uri.parse('$baseUrl/profile'),
      );

      request.headers.addAll({
        'Accept': 'application/json',
        if (token != null) 'Authorization': 'Bearer $token',
      });

      request.fields['_method'] = 'PATCH';
      request.fields['name'] = name;
      request.fields['email'] = email;
      if (bio != null) request.fields['bio'] = bio;

      if (profilePicture != null) {
        request.files.add(
          await http.MultipartFile.fromPath(
            'profile_picture',
            profilePicture.path,
          ),
        );
      }

      final streamedResponse = await request.send();
      final response = await http.Response.fromStream(streamedResponse);

      return jsonDecode(response.body);
    } catch (e) {
      return {'status': 'error', 'message': 'Connection error: $e'};
    }
  }

  static Future<Map<String, dynamic>> validatePromoCode(
    int eventId,
    String code,
  ) async {
    try {
      final token = await AuthService.getToken();
      final response = await http.post(
        Uri.parse('$baseUrl/events/$eventId/promo-codes/validate'),
        headers: {
          'Accept': 'application/json',
          'Content-Type': 'application/json',
          if (token != null) 'Authorization': 'Bearer $token',
        },
        body: jsonEncode({'code': code}),
      );
      return jsonDecode(response.body);
    } catch (e) {
      return {'status': 'error', 'message': 'Connection error: $e'};
    }
  }

  static Future<Map<String, dynamic>> forgotPassword(String email) async {
    try {
      final response = await http.post(
        Uri.parse('$baseUrl/forgot-password'),
        headers: {
          'Accept': 'application/json',
          'Content-Type': 'application/json',
        },
        body: jsonEncode({'email': email}),
      );
      return jsonDecode(response.body);
    } catch (e) {
      return {'status': 'error', 'message': 'Connection error: $e'};
    }
  }

  static Future<Map<String, dynamic>> getNotifications() async {
    try {
      final token = await AuthService.getToken();
      final response = await http.get(
        Uri.parse('$baseUrl/notifications'),
        headers: {
          'Accept': 'application/json',
          if (token != null) 'Authorization': 'Bearer $token',
        },
      );
      return jsonDecode(response.body);
    } catch (e) {
      return {'status': 'error', 'message': 'Connection error: $e'};
    }
  }

  static Future<Map<String, dynamic>> markNotificationAsRead(
    int notificationId,
  ) async {
    try {
      final token = await AuthService.getToken();
      final response = await http.post(
        Uri.parse('$baseUrl/notifications/$notificationId/mark-read'),
        headers: {
          'Accept': 'application/json',
          if (token != null) 'Authorization': 'Bearer $token',
        },
      );
      return jsonDecode(response.body);
    } catch (e) {
      return {'status': 'error', 'message': 'Connection error: $e'};
    }
  }

  static Future<Map<String, dynamic>> getCategories() async {
    try {
      final response = await http.get(
        Uri.parse('$baseUrl/categories'),
        headers: {'Accept': 'application/json'},
      );
      return jsonDecode(response.body);
    } catch (e) {
      return {'status': 'error', 'message': 'Connection error: $e'};
    }
  }

  static Future<Map<String, dynamic>> upgradeBasic() async {
    try {
      final token = await AuthService.getToken();
      final response = await http.post(
        Uri.parse('$baseUrl/upgrade/basic'),
        headers: {
          'Accept': 'application/json',
          'Content-Type': 'application/json',
          if (token != null) 'Authorization': 'Bearer $token',
        },
      );
      return jsonDecode(response.body);
    } catch (e) {
      return {'status': 'error', 'message': 'Connection error: $e'};
    }
  }

  static Future<Map<String, dynamic>> upgradePro() async {
    try {
      final token = await AuthService.getToken();
      final response = await http.post(
        Uri.parse('$baseUrl/upgrade/pro'),
        headers: {
          'Accept': 'application/json',
          'Content-Type': 'application/json',
          if (token != null) 'Authorization': 'Bearer $token',
        },
      );
      return jsonDecode(response.body);
    } catch (e) {
      return {'status': 'error', 'message': 'Connection error: $e'};
    }
  }

  static Future<Map<String, dynamic>> cancelOrganizerRole() async {
    try {
      final token = await AuthService.getToken();
      final response = await http.post(
        Uri.parse('$baseUrl/upgrade/cancel'),
        headers: {
          'Accept': 'application/json',
          'Content-Type': 'application/json',
          if (token != null) 'Authorization': 'Bearer $token',
        },
      );
      return jsonDecode(response.body);
    } catch (e) {
      return {'status': 'error', 'message': 'Connection error: $e'};
    }
  }

  static Future<Map<String, dynamic>> getKycSetup() async {
    try {
      final token = await AuthService.getToken();
      final response = await http.get(
        Uri.parse('$baseUrl/kyc/setup'),
        headers: {
          'Accept': 'application/json',
          if (token != null) 'Authorization': 'Bearer $token',
        },
      );
      return jsonDecode(response.body);
    } catch (e) {
      return {'status': 'error', 'message': 'Connection error: $e'};
    }
  }

  static Future<Map<String, dynamic>> submitKyc(String businessDetails, String socialLinks) async {
    try {
      final token = await AuthService.getToken();
      final response = await http.post(
        Uri.parse('$baseUrl/kyc/submit'),
        headers: {
          'Accept': 'application/json',
          'Content-Type': 'application/json',
          if (token != null) 'Authorization': 'Bearer $token',
        },
        body: jsonEncode({
          'business_details': businessDetails,
          'social_links': socialLinks,
        }),
      );
      return jsonDecode(response.body);
    } catch (e) {
      return {'status': 'error', 'message': 'Connection error: $e'};
    }
  }

  static Future<Map<String, dynamic>> verifyPayment(String orderId) async {
    try {
      final token = await AuthService.getToken();
      final response = await http.get(
        Uri.parse('$baseUrl/payment/success?order_id=$orderId'),
        headers: {
          'Accept': 'application/json',
          if (token != null) 'Authorization': 'Bearer $token',
        },
      );
      
      final data = jsonDecode(response.body);
      data['statusCode'] = response.statusCode;
      return data;
    } catch (e) {
      return {'error': 'Connection error: $e'};
    }
  }
}
