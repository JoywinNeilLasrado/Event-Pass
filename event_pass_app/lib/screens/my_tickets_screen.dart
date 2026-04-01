import 'package:flutter/material.dart';
import '../services/api_service.dart';
import '../services/auth_service.dart';
import '../services/payment_service.dart';
import 'login_screen.dart';
import 'ticket_details_screen.dart';

class MyTicketsScreen extends StatefulWidget {
  const MyTicketsScreen({super.key});

  @override
  State<MyTicketsScreen> createState() => _MyTicketsScreenState();
}

class _MyTicketsScreenState extends State<MyTicketsScreen> {
  late Future<Map<String, dynamic>> _ticketsFuture;
  
  late final PaymentService _paymentService;

  @override
  void initState() {
    super.initState();
    _ticketsFuture = ApiService.getMyTickets();
    _paymentService = PaymentService();
    _paymentService.onPaymentSuccess = (orderId) async {
      await ApiService.verifyPayment(orderId);
      
      if (mounted) {
        ScaffoldMessenger.of(context).showSnackBar(
          const SnackBar(content: Text('Payment Successful! 🎉'), backgroundColor: Colors.green),
        );
        setState(() {
          _ticketsFuture = ApiService.getMyTickets(); // Actually refresh the ticket list
        });
      }
    };
    _paymentService.onPaymentFailure = (message) {
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(content: Text('Payment Failed: $message'), backgroundColor: Colors.red),
      );
    };
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
    final bool isDark = Theme.of(context).brightness == Brightness.dark;
    final Color bgColor = isDark ? const Color(0xFF111713) : const Color(0xFFEAF5EF);
    final Color textColor = isDark ? Colors.white : const Color(0xFF1A1A1A);
    final Color subTextColor = isDark ? Colors.white70 : const Color(0xFF666666);
    final Color cardColor = isDark ? const Color(0xFF1A221C) : Colors.white;

    return Scaffold(
      backgroundColor: bgColor,
      appBar: AppBar(
        backgroundColor: Colors.transparent,
        elevation: 0,
        iconTheme: IconThemeData(color: textColor),
        title: Text('My Tickets', style: TextStyle(color: textColor, fontWeight: FontWeight.bold)),
      ),
      body: FutureBuilder<Map<String, dynamic>>(
        future: _ticketsFuture,
        builder: (context, snapshot) {
          if (snapshot.connectionState == ConnectionState.waiting) {
            return const Center(child: CircularProgressIndicator());
          }
          if (snapshot.hasError || (snapshot.data != null && snapshot.data!['status'] == 'error')) {
            return Center(
              child: Column(
                mainAxisAlignment: MainAxisAlignment.center,
                children: [
                  Icon(Icons.error_outline, size: 60, color: Colors.red.withOpacity(0.5)),
                  const SizedBox(height: 16),
                  Text('Failed to load tickets.', style: TextStyle(color: subTextColor, fontSize: 16)),
                  const SizedBox(height: 8),
                  Text('Your session may have expired.', style: TextStyle(color: subTextColor.withOpacity(0.7), fontSize: 13)),
                  const SizedBox(height: 24),
                  ElevatedButton(
                    onPressed: () => setState(() {}), // Simple retry logic
                    child: const Text('Try Again'),
                  ),
                  TextButton(
                    onPressed: _logout,
                    child: const Text('Logout', style: TextStyle(color: Colors.red)),
                  ),
                ],
              ),
            );
          }

          final data = snapshot.data ?? {};
          final dynamic rawGrouped = data['groupedBookings'];
          final Map<String, dynamic> groupedBookings = (rawGrouped is Map) ? Map<String, dynamic>.from(rawGrouped) : {};

          if (groupedBookings.isEmpty) {
            return Center(
              child: Padding(
                padding: const EdgeInsets.all(32.0),
                child: Column(
                  mainAxisSize: MainAxisSize.min,
                  children: [
                    Icon(Icons.airplane_ticket_outlined, size: 80, color: subTextColor.withOpacity(0.5)),
                    const SizedBox(height: 16),
                    Text('You have no tickets yet.', style: TextStyle(color: subTextColor, fontSize: 16)),
                  ],
                ),
              ),
            );
          }

          return ListView.builder(
            padding: const EdgeInsets.all(20),
            itemCount: groupedBookings.keys.length,
            itemBuilder: (context, index) {
              final String eventIdStr = groupedBookings.keys.elementAt(index);
              final List bookingsList = groupedBookings[eventIdStr] ?? [];
              if (bookingsList.isEmpty) return const SizedBox.shrink();

              final firstBooking = bookingsList.first;
              final event = firstBooking['event'];
              final title = event['title'] ?? 'Event';
              final location = event['location'] ?? 'Location TBA';
              
              final imageUrl = event['poster_image_url'] ?? 'https://images.unsplash.com/photo-1459749411175-04bf5292ceea?auto=format&fit=crop&q=80&w=200';

              return Container(
                margin: const EdgeInsets.only(bottom: 24),
                decoration: BoxDecoration(
                  color: cardColor,
                  borderRadius: BorderRadius.circular(20),
                  boxShadow: [BoxShadow(color: Colors.black.withOpacity(0.04), blurRadius: 20, offset: const Offset(0, 8))],
                ),
                clipBehavior: Clip.antiAlias,
                child: Column(
                  children: [
                    Row(
                      children: [
                        Container(
                          width: 100,
                          height: 120,
                          decoration: BoxDecoration(
                            image: DecorationImage(image: NetworkImage(imageUrl), fit: BoxFit.cover),
                          ),
                        ),
                        Expanded(
                          child: Padding(
                            padding: const EdgeInsets.all(16.0),
                            child: Column(
                              crossAxisAlignment: CrossAxisAlignment.start,
                              children: [
                                Text(title, style: TextStyle(color: textColor, fontSize: 18, fontWeight: FontWeight.bold), maxLines: 1, overflow: TextOverflow.ellipsis),
                                const SizedBox(height: 4),
                                Row(
                                  crossAxisAlignment: CrossAxisAlignment.start,
                                  children: [
                                    Icon(Icons.location_on, size: 14, color: subTextColor),
                                    const SizedBox(width: 4),
                                    Expanded(child: Text(location, style: TextStyle(color: subTextColor, fontSize: 13), maxLines: 2, overflow: TextOverflow.ellipsis)),
                                  ],
                                ),
                                const SizedBox(height: 12),
                                Container(
                                  padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 4),
                                  decoration: BoxDecoration(color: Colors.blue.withOpacity(0.1), borderRadius: BorderRadius.circular(12)),
                                  child: Text('${bookingsList.length} Ticket(s)', style: TextStyle(color: Colors.blue.shade700, fontSize: 12, fontWeight: FontWeight.bold)),
                                ),
                              ],
                            ),
                          ),
                        ),
                      ],
                    ),
                    Divider(color: subTextColor.withOpacity(0.1), height: 1),
                    ...bookingsList.map((booking) {
                      final bool isActive = ['paid', 'free'].contains(booking['payment_status']);
                      return InkWell(
                        onTap: isActive ? () {
                          Navigator.push(
                            context,
                            MaterialPageRoute(
                              builder: (context) => TicketDetailsScreen(
                                booking: booking,
                                eventTitle: title,
                              ),
                            ),
                          );
                        } : null,
                        child: Padding(
                          padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 12),
                          child: Row(
                            children: [
                              Icon(Icons.confirmation_number, color: isActive ? Colors.green : Colors.orange, size: 20),
                              const SizedBox(width: 12),
                              Expanded(
                                child: Column(
                                  crossAxisAlignment: CrossAxisAlignment.start,
                                  children: [
                                    Text(booking['ticket_type']?['name'] ?? 'General', style: TextStyle(color: textColor, fontWeight: FontWeight.bold)),
                                    const SizedBox(height: 2),
                                    Text('ID: ${booking['id']} • ${booking['payment_status']}', style: TextStyle(color: subTextColor, fontSize: 12)),
                                  ],
                                ),
                              ),
                              if (isActive)
                                Icon(Icons.qr_code, color: textColor)
                              else if (booking['payment_status'] == 'pending')
                                TextButton(
                                  onPressed: () => _handleRetryPayment(booking['id']),
                                  style: TextButton.styleFrom(
                                    backgroundColor: Colors.orange.withOpacity(0.1),
                                    foregroundColor: Colors.orange,
                                    padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 4),
                                    minimumSize: Size.zero,
                                    tapTargetSize: MaterialTapTargetSize.shrinkWrap,
                                    shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
                                  ),
                                  child: const Text('Pay Now', style: TextStyle(fontSize: 12, fontWeight: FontWeight.bold)),
                                )
                              else
                                const Icon(Icons.info_outline, color: Colors.orange, size: 20),
                            ],
                          ),
                        ),
                      );
                    }),
                  ],
                ),
              );
            },
          );
        },
      ),
    );
  }

  void _handleRetryPayment(int bookingId) async {
    showDialog(
      context: context,
      barrierDismissible: false,
      builder: (context) => const Center(child: CircularProgressIndicator()),
    );

    final response = await ApiService.retryPayment(bookingId);

    if (mounted) {
      Navigator.pop(context); // Close loading dialog

      if (response['payment_session_id'] != null) {
        _paymentService.startPayment(
          context: context,
          sessionId: response['payment_session_id'],
          orderId: response['order_id'],
          env: response['env'] ?? 'sandbox',
        );
      } else {
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(content: Text(response['message'] ?? 'Failed to initiate payment')),
        );
      }
    }
  }
}
