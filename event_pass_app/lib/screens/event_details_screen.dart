import 'dart:io';
import 'package:flutter/material.dart';
import 'package:flutter_html/flutter_html.dart';
import 'package:url_launcher/url_launcher.dart';
import 'package:share_plus/share_plus.dart';
import '../services/api_service.dart';
import '../services/payment_service.dart';

class EventDetailsScreen extends StatefulWidget {
  final Map<String, dynamic> event;

  const EventDetailsScreen({super.key, required this.event});

  @override
  State<EventDetailsScreen> createState() => _EventDetailsScreenState();
}

class _EventDetailsScreenState extends State<EventDetailsScreen> {
  int _selectedTicketTypeId = -1;
  int _quantity = 1;
  final TextEditingController _promoController = TextEditingController();
  String? _appliedPromoCode;
  double _discountAmount = 0;
  String? _discountType;
  bool _isPromoLoading = false;
  late final PaymentService _paymentService;

  @override
  void initState() {
    super.initState();
    _paymentService = PaymentService();
    _paymentService.onPaymentSuccess = (orderId) {
       ScaffoldMessenger.of(context).showSnackBar(
         const SnackBar(content: Text('Payment Successful! 🎉'), backgroundColor: Colors.green),
       );
       Navigator.pop(context, true);
    };
    _paymentService.onPaymentFailure = (message) {
       ScaffoldMessenger.of(context).showSnackBar(
         SnackBar(content: Text('Payment Failed: $message'), backgroundColor: Colors.red),
       );
    };
  }

  void _openMap(String location) async {
    final query = Uri.encodeComponent(location);
    final googleMapsUrl = 'https://www.google.com/maps/search/?api=1&query=$query';
    final appleMapsUrl = 'https://maps.apple.com/?q=$query';

    try {
      if (Platform.isIOS) {
        if (await canLaunchUrl(Uri.parse(appleMapsUrl))) {
          await launchUrl(Uri.parse(appleMapsUrl));
        } else {
          await launchUrl(Uri.parse(googleMapsUrl), mode: LaunchMode.externalApplication);
        }
      } else {
        await launchUrl(Uri.parse(googleMapsUrl), mode: LaunchMode.externalApplication);
      }
    } catch (e) {
      if (mounted) {
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(content: Text('Could not open map: $e')),
        );
      }
    }
  }

  void _applyPromo() async {
    if (_promoController.text.isEmpty) return;
    
    setState(() => _isPromoLoading = true);
    final response = await ApiService.validatePromoCode(widget.event['id'], _promoController.text);
    setState(() => _isPromoLoading = false);

    if (response['valid'] == true) {
      setState(() {
        _appliedPromoCode = _promoController.text.toUpperCase();
        _discountAmount = double.tryParse(response['discount_amount'].toString()) ?? 0;
        _discountType = response['discount_type'];
      });
      if (mounted) ScaffoldMessenger.of(context).showSnackBar(const SnackBar(content: Text('Promo code applied! 🎉'), backgroundColor: Colors.green));
    } else {
      setState(() {
        _appliedPromoCode = null;
        _discountAmount = 0;
      });
      if (mounted) ScaffoldMessenger.of(context).showSnackBar(SnackBar(content: Text(response['message'] ?? 'Invalid promo code.')));
    }
  }

  void _bookTicket() async {
    if (_selectedTicketTypeId == -1) {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(content: Text('Please select a ticket type')),
      );
      return;
    }

    showDialog(
      context: context,
      barrierDismissible: false,
      builder: (context) => const Center(child: CircularProgressIndicator()),
    );

    final response = await ApiService.bookEvent(
      eventId: widget.event['id'],
      ticketTypeId: _selectedTicketTypeId,
      promoCode: _appliedPromoCode,
      quantity: _quantity,
    );

    if (mounted) {
      Navigator.pop(context); // Close loading dialog

      if (response['payment_session_id'] != null) {
        // REAL PAID TICKET FLOW
        _paymentService.startPayment(
          context: context,
          sessionId: response['payment_session_id'], 
          orderId: response['order_id'],
          env: response['env'] ?? 'sandbox',
        );
      } else if (response['bookings'] != null || response['message']?.contains('booked') == true) {
        // FREE TICKET FLOW
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(content: Text(response['message'] ?? 'Tickets booked successfully! 🎉'), backgroundColor: Colors.green),
        );
        Navigator.pop(context, true);
      } else {
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(content: Text(response['error'] ?? response['message'] ?? 'Booking failed')),
        );
      }
    }
  }


  void _joinWaitlist() async {
    if (_selectedTicketTypeId == -1) {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(content: Text('Please select a ticket type')),
      );
      return;
    }

    showDialog(
      context: context,
      barrierDismissible: false,
      builder: (context) => const Center(child: CircularProgressIndicator()),
    );

    final response = await ApiService.joinWaitlist(widget.event['id'], _selectedTicketTypeId);

    if (mounted) {
      Navigator.pop(context); // Close loading dialog
      if (response['status'] == 'success' || response['message']?.contains('waitlist') == true) {
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(content: Text(response['message'] ?? 'Joined waitlist successfully! ⏳'), backgroundColor: Colors.orange),
        );
        Navigator.pop(context, true);
      } else {
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(content: Text(response['message'] ?? 'Failed to join waitlist')),
        );
      }
    }
  }

  @override
  Widget build(BuildContext context) {
    final bool isDark = Theme.of(context).brightness == Brightness.dark;
    final Color bgColor = isDark ? const Color(0xFF0F172A) : Colors.white;
    final Color textColor = isDark ? Colors.white : const Color(0xFF1E293B);
    final Color subTextColor = isDark ? Colors.white70 : const Color(0xFF64748B);
    final Color cardColor = isDark ? const Color(0xFF1E293B) : const Color(0xFFF8FAFC);

    final String eventName = widget.event['title'] ?? 'Event Details';
    final String location = widget.event['location'] ?? 'Online';
    final String description = widget.event['description'] ?? '';
    final String posterUrl = widget.event['poster_image_url'] ?? '';
    final String date = widget.event['date'] ?? '';
    final String time = widget.event['time'] ?? '';
    final List ticketTypes = widget.event['ticket_types'] ?? [];

    bool isSoldOut = (widget.event['remaining'] != null && widget.event['remaining'] <= 0);
    if (_selectedTicketTypeId != -1) {
      final selectedType = ticketTypes.firstWhere((t) => t['id'] == _selectedTicketTypeId, orElse: () => null);
      if (selectedType != null) {
        isSoldOut = (selectedType['remaining'] ?? 0) <= 0;
      }
    }

    return Scaffold(
      backgroundColor: bgColor,
      body: CustomScrollView(
        slivers: [
          SliverAppBar(
            expandedHeight: 300,
            pinned: true,
            backgroundColor: const Color(0xFF6366F1),
            iconTheme: const IconThemeData(color: Colors.white),
            actions: [
              IconButton(
                icon: const Icon(Icons.share),
                onPressed: () {
                  final String shareText = 'Check out this event "$eventName" on EventPass!\n\nLocation: $location\nDate: $date\n\nBook your tickets now!';
                  Share.share(shareText);
                },
              ),
              const SizedBox(width: 8),
            ],
            flexibleSpace: FlexibleSpaceBar(
              background: posterUrl.isNotEmpty
                  ? Image.network(posterUrl, fit: BoxFit.cover, errorBuilder: (c, e, s) => Container(color: Colors.grey, child: const Icon(Icons.image, color: Colors.white, size: 50)))
                  : Container(color: Colors.grey, child: const Icon(Icons.image, color: Colors.white, size: 50)),
            ),
          ),
          SliverToBoxAdapter(
            child: Container(
              padding: const EdgeInsets.all(24),
              decoration: BoxDecoration(
                color: bgColor,
                borderRadius: const BorderRadius.vertical(top: Radius.circular(30)),
              ),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Row(
                    children: [
                      Container(
                        padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 6),
                        decoration: BoxDecoration(
                          color: const Color(0xFF6366F1).withOpacity(0.1),
                          borderRadius: BorderRadius.circular(20),
                        ),
                        child: Text(
                          widget.event['category']?['name']?.toString().toUpperCase() ?? 'EVENT',
                          style: const TextStyle(color: Color(0xFF6366F1), fontWeight: FontWeight.bold, fontSize: 12),
                        ),
                      ),
                      const Spacer(),
                      Row(
                        children: [
                          const Icon(Icons.star, color: Colors.amber, size: 20),
                          const SizedBox(width: 4),
                          Text('4.8', style: TextStyle(color: textColor, fontWeight: FontWeight.bold)),
                        ],
                      ),
                    ],
                  ),
                  const SizedBox(height: 16),
                  Text(eventName, style: TextStyle(fontSize: 28, fontWeight: FontWeight.bold, color: textColor)),
                  const SizedBox(height: 20),
                  Row(
                    children: [
                      _buildInfoItem(Icons.calendar_today, 'Date', date, cardColor, textColor, subTextColor),
                      const SizedBox(width: 16),
                      _buildInfoItem(Icons.access_time, 'Time', time, cardColor, textColor, subTextColor),
                    ],
                  ),
                  const SizedBox(height: 20),
                  Row(
                    children: [
                      const Icon(Icons.location_on, color: Color(0xFF6366F1), size: 24),
                      const SizedBox(width: 12),
                      Expanded(
                        child: Text(location, style: TextStyle(fontSize: 16, color: subTextColor, fontWeight: FontWeight.w500)),
                      ),
                      TextButton.icon(
                        onPressed: () => _openMap(location),
                        icon: const Icon(Icons.map_outlined, size: 18),
                        label: const Text('View on Map'),
                      ),
                    ],
                  ),
                  const SizedBox(height: 32),
                  Text('About Event', style: TextStyle(fontSize: 20, fontWeight: FontWeight.bold, color: textColor)),
                  const SizedBox(height: 12),
                  Html(data: description, style: {
                    "body": Style(color: subTextColor, fontSize: FontSize(16), lineHeight: LineHeight(1.5), margin: Margins.zero),
                  }),
                  const SizedBox(height: 32),
                  Text('Select Tickets', style: TextStyle(fontSize: 20, fontWeight: FontWeight.bold, color: textColor)),
                  const SizedBox(height: 16),
                  ...ticketTypes.map((ticket) {
                    final bool isSelected = _selectedTicketTypeId == ticket['id'];
                    final bool typeSoldOut = (ticket['remaining'] ?? 0) <= 0;
                    
                    return GestureDetector(
                      onTap: () {
                        setState(() => _selectedTicketTypeId = ticket['id']);
                      },
                      child: Container(
                        margin: const EdgeInsets.only(bottom: 12),
                        padding: const EdgeInsets.all(16),
                        decoration: BoxDecoration(
                          color: isSelected ? const Color(0xFF6366F1).withOpacity(0.05) : cardColor,
                          border: Border.all(color: isSelected ? const Color(0xFF6366F1) : Colors.transparent, width: 2),
                          borderRadius: BorderRadius.circular(16),
                        ),
                        child: Row(
                          children: [
                            Expanded(
                              child: Column(
                                crossAxisAlignment: CrossAxisAlignment.start,
                                children: [
                                  Text(ticket['name'], style: TextStyle(fontSize: 18, fontWeight: FontWeight.bold, color: textColor)),
                                  const SizedBox(height: 4),
                                  Text(
                                    typeSoldOut ? 'Sold Out' : '${ticket['remaining']} tickets left',
                                    style: TextStyle(color: typeSoldOut ? Colors.red : Colors.green, fontWeight: FontWeight.w500, fontSize: 13),
                                  ),
                                ],
                              ),
                            ),
                            Text(
                              double.parse(ticket['price'].toString()) == 0 ? 'FREE' : '₹${ticket['price']}',
                              style: const TextStyle(fontSize: 18, fontWeight: FontWeight.bold, color: Color(0xFF6366F1)),
                            ),
                          ],
                        ),
                      ),
                    );
                  }),
                  const SizedBox(height: 24),
                  // PROMO CODE SECTION
                  Container(
                    padding: const EdgeInsets.all(16),
                    decoration: BoxDecoration(
                      color: cardColor,
                      borderRadius: BorderRadius.circular(16),
                      border: Border.all(color: _appliedPromoCode != null ? Colors.green : Colors.transparent),
                    ),
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        const Text('Promo Code', style: TextStyle(fontWeight: FontWeight.bold)),
                        const SizedBox(height: 8),
                        Row(
                          children: [
                            Expanded(
                              child: TextField(
                                controller: _promoController,
                                decoration: const InputDecoration(
                                  hintText: 'Enter coupon code',
                                  prefixIcon: Icon(Icons.confirmation_number_outlined),
                                ),
                              ),
                            ),
                            const SizedBox(width: 12),
                            _isPromoLoading 
                              ? const SizedBox(width: 20, height: 20, child: CircularProgressIndicator(strokeWidth: 2))
                              : ElevatedButton(
                                  onPressed: _applyPromo,
                                  style: ElevatedButton.styleFrom(
                                    padding: const EdgeInsets.symmetric(horizontal: 24, vertical: 14),
                                  ),
                                  child: const Text('Apply'),
                                ),
                          ],
                        ),
                        if (_appliedPromoCode != null)
                          Padding(
                            padding: const EdgeInsets.only(top: 8.0),
                            child: Text(
                              'Discount Applied: -₹$_discountAmount${_discountType == 'percentage' ? '%' : ''}',
                              style: const TextStyle(color: Colors.green, fontWeight: FontWeight.bold),
                            ),
                          ),
                      ],
                    ),
                  ),
                  const SizedBox(height: 100), // Space for bottom bar
                ],
              ),
            ),
          ),
        ],
      ),
      bottomNavigationBar: Container(
        padding: const EdgeInsets.symmetric(horizontal: 24, vertical: 20),
        decoration: BoxDecoration(
          color: isDark ? const Color(0xFF1E293B) : Colors.white,
          boxShadow: [
            BoxShadow(
              color: Colors.black.withOpacity(isDark ? 0.3 : 0.08),
              blurRadius: 20,
              offset: const Offset(0, -5),
            )
          ],
        ),
        child: SafeArea(
          child: Row(
            children: [
              Container(
                height: 56,
                padding: const EdgeInsets.symmetric(horizontal: 12),
                decoration: BoxDecoration(
                  color: isDark ? Colors.white.withOpacity(0.05) : const Color(0xFFF1F5F9),
                  borderRadius: BorderRadius.circular(16),
                ),
                child: Row(
                  children: [
                    IconButton(
                      onPressed: () => setState(() => _quantity = _quantity > 1 ? _quantity - 1 : 1),
                      icon: Icon(Icons.remove, size: 20, color: textColor),
                    ),
                    Text('$_quantity', style: TextStyle(fontSize: 18, fontWeight: FontWeight.bold, color: textColor)),
                    IconButton(
                      onPressed: () => setState(() => _quantity++),
                      icon: Icon(Icons.add, size: 20, color: textColor),
                    ),
                  ],
                ),
              ),
              const SizedBox(width: 16),
              Expanded(
                child: ElevatedButton(
                  onPressed: isSoldOut ? _joinWaitlist : _bookTicket,
                  style: ElevatedButton.styleFrom(
                    backgroundColor: isSoldOut ? Colors.orange : const Color(0xFF6366F1),
                    padding: const EdgeInsets.symmetric(vertical: 16),
                    shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(16)),
                    elevation: 0,
                  ),
                  child: Text(
                    isSoldOut ? 'JOIN WAITLIST' : 'BOOK NOW',
                    style: const TextStyle(color: Colors.white, fontSize: 16, fontWeight: FontWeight.bold, letterSpacing: 1),
                  ),
                ),
              ),
            ],
          ),
        ),
      ),
    );
  }

  Widget _buildInfoItem(IconData icon, String label, String value, Color cardColor, Color textColor, Color subTextColor) {
    return Expanded(
      child: Container(
        padding: const EdgeInsets.all(16),
        decoration: BoxDecoration(color: cardColor, borderRadius: BorderRadius.circular(16)),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Row(children: [Icon(icon, color: const Color(0xFF6366F1), size: 16), const SizedBox(width: 8), Text(label, style: TextStyle(color: subTextColor, fontSize: 12))]),
            const SizedBox(height: 4),
            Text(value, style: TextStyle(color: textColor, fontWeight: FontWeight.bold, fontSize: 14)),
          ],
        ),
      ),
    );
  }
}
