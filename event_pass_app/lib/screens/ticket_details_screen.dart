import 'package:flutter/material.dart';
import 'package:qr_flutter/qr_flutter.dart';

class TicketDetailsScreen extends StatelessWidget {
  final Map<String, dynamic> booking;
  final String eventTitle;

  const TicketDetailsScreen({
    super.key,
    required this.booking,
    required this.eventTitle,
  });

  @override
  Widget build(BuildContext context) {
    final bool isDark = Theme.of(context).brightness == Brightness.dark;
    final Color bgColor = isDark ? const Color(0xFF0F172A) : const Color(0xFFF8FAFC);
    final Color cardColor = isDark ? const Color(0xFF1E293B) : Colors.white;
    final Color textColor = isDark ? Colors.white : const Color(0xFF1E293B);
    
    final String bookingId = booking['id'].toString();
    final String ticketType = booking['ticket_type']?['name'] ?? 'General Admission';
    final String eventDate = booking['event']?['date'] ?? 'TBA';
    final String eventTime = booking['event']?['time'] ?? 'TBA';
    final String location = booking['event']?['location'] ?? 'Venue TBA';

    return Scaffold(
      backgroundColor: bgColor,
      appBar: AppBar(
        title: const Text('Digital Ticket'),
        backgroundColor: Colors.transparent,
        elevation: 0,
      ),
      body: SingleChildScrollView(
        padding: const EdgeInsets.all(24),
        child: Column(
          children: [
            Container(
              decoration: BoxDecoration(
                color: cardColor,
                borderRadius: BorderRadius.circular(24),
                boxShadow: [
                  BoxShadow(
                    color: Colors.black.withOpacity(0.05),
                    blurRadius: 20,
                    offset: const Offset(0, 10),
                  )
                ],
              ),
              child: Column(
                children: [
                  // Event Header
                  Container(
                    width: double.infinity,
                    padding: const EdgeInsets.all(24),
                    decoration: const BoxDecoration(
                      color: Color(0xFF6366F1),
                      borderRadius: BorderRadius.vertical(top: Radius.circular(24)),
                    ),
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Text(
                          eventTitle,
                          style: const TextStyle(color: Colors.white, fontSize: 22, fontWeight: FontWeight.bold),
                        ),
                        const SizedBox(height: 8),
                        Row(
                          children: [
                            const Icon(Icons.location_on, color: Colors.white70, size: 16),
                            const SizedBox(width: 4),
                            Text(location, style: const TextStyle(color: Colors.white70)),
                          ],
                        ),
                      ],
                    ),
                  ),
                  
                  // QR Code
                  Padding(
                    padding: const EdgeInsets.symmetric(vertical: 40),
                    child: Column(
                      children: [
                        QrImageView(
                          data: bookingId,
                          version: QrVersions.auto,
                          size: 220.0,
                          backgroundColor: Colors.white,
                          padding: const EdgeInsets.all(16),
                        ),
                        const SizedBox(height: 16),
                        Text(
                          'BOOKING ID: #$bookingId',
                          style: TextStyle(
                            color: textColor.withOpacity(0.6),
                            fontWeight: FontWeight.w900,
                            letterSpacing: 2,
                            fontSize: 12,
                          ),
                        ),
                      ],
                    ),
                  ),
                  
                  // Perforated Line Effect
                  Row(
                    children: List.generate(
                      20,
                      (index) => Expanded(
                        child: Container(
                          margin: const EdgeInsets.symmetric(horizontal: 4),
                          height: 1,
                          color: Colors.grey.withOpacity(0.3),
                        ),
                      ),
                    ),
                  ),
                  
                  // Ticket Info Details
                  Padding(
                    padding: const EdgeInsets.all(24),
                    child: Column(
                      children: [
                        _buildInfoRow('Ticket Type', ticketType, textColor),
                        const SizedBox(height: 16),
                        Row(
                          children: [
                            Expanded(child: _buildInfoRow('Date', eventDate, textColor)),
                            Expanded(child: _buildInfoRow('Time', eventTime, textColor)),
                          ],
                        ),
                        const SizedBox(height: 24),
                        Container(
                          padding: const EdgeInsets.all(16),
                          decoration: BoxDecoration(
                            color: const Color(0xFFF1F5F9),
                            borderRadius: BorderRadius.circular(12),
                          ),
                          child: Row(
                            children: [
                              const Icon(Icons.info_outline, color: Color(0xFF475569)),
                              const SizedBox(width: 12),
                              Expanded(
                                child: Text(
                                  'Please have this QR code ready at the entrance for scanning.',
                                  style: TextStyle(color: const Color(0xFF475569), fontSize: 13),
                                ),
                              ),
                            ],
                          ),
                        ),
                      ],
                    ),
                  ),
                ],
              ),
            ),
            const SizedBox(height: 40),
            ElevatedButton.icon(
              onPressed: () {
                // Future: Share ticket as Image/PDF
              },
              icon: const Icon(Icons.share_outlined),
              label: const Text('Share Ticket'),
              style: ElevatedButton.styleFrom(
                backgroundColor: const Color(0xFF6366F1).withOpacity(0.1),
                foregroundColor: const Color(0xFF6366F1),
                elevation: 0,
                padding: const EdgeInsets.symmetric(horizontal: 40, vertical: 16),
                shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
              ),
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildInfoRow(String label, String value, Color textColor) {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Text(label, style: TextStyle(color: textColor.withOpacity(0.5), fontSize: 12, fontWeight: FontWeight.w600)),
        const SizedBox(height: 4),
        Text(value, style: TextStyle(color: textColor, fontSize: 16, fontWeight: FontWeight.bold)),
      ],
    );
  }
}
