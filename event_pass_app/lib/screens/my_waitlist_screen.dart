import 'package:flutter/material.dart';
import 'package:intl/intl.dart';
import '../services/api_service.dart';
import 'event_details_screen.dart';

class MyWaitlistScreen extends StatefulWidget {
  const MyWaitlistScreen({super.key});

  @override
  State<MyWaitlistScreen> createState() => _MyWaitlistScreenState();
}

class _MyWaitlistScreenState extends State<MyWaitlistScreen> {
  late Future<Map<String, dynamic>> _waitlistFuture;

  @override
  void initState() {
    super.initState();
    _waitlistFuture = ApiService.getWaitlist();
  }

  @override
  Widget build(BuildContext context) {
    final bool isDark = Theme.of(context).brightness == Brightness.dark;
    final Color textColor = isDark ? Colors.white : const Color(0xFF1E293B);
    final Color subTextColor = isDark ? Colors.white70 : Colors.black54;
    final Color cardColor = isDark ? const Color(0xFF1E293B) : Colors.white;

    return Scaffold(
      backgroundColor: isDark ? const Color(0xFF0F172A) : const Color(0xFFF8FAFC),
      appBar: AppBar(
        title: const Text('My Waitlist', style: TextStyle(fontWeight: FontWeight.bold)),
        backgroundColor: Colors.transparent,
        elevation: 0,
        foregroundColor: textColor,
      ),
      body: FutureBuilder<Map<String, dynamic>>(
        future: _waitlistFuture,
        builder: (context, snapshot) {
          if (snapshot.connectionState == ConnectionState.waiting) {
            return const Center(child: CircularProgressIndicator());
          }

          if (snapshot.hasError || (snapshot.hasData && snapshot.data!['status'] == 'error')) {
            return Center(
              child: Column(
                mainAxisAlignment: MainAxisAlignment.center,
                children: [
                  const Icon(Icons.error_outline, size: 48, color: Colors.red),
                  const SizedBox(height: 16),
                  Text(snapshot.data?['message'] ?? 'Failed to load waitlist', style: TextStyle(color: textColor)),
                  TextButton(
                    onPressed: () => setState(() => _waitlistFuture = ApiService.getWaitlist()),
                    child: const Text('Retry'),
                  ),
                ],
              ),
            );
          }

          final waitlists = List<dynamic>.from(snapshot.data?['waitlists'] ?? []);

          if (waitlists.isEmpty) {
            return Center(
              child: Column(
                mainAxisAlignment: MainAxisAlignment.center,
                children: [
                  Icon(Icons.hourglass_empty_rounded, size: 64, color: subTextColor.withOpacity(0.5)),
                  const SizedBox(height: 16),
                  Text('Your waitlist is empty', style: TextStyle(color: textColor, fontSize: 18, fontWeight: FontWeight.w600)),
                  const SizedBox(height: 8),
                  Text('Join a waitlist for sold-out events!', style: TextStyle(color: subTextColor)),
                ],
              ),
            );
          }

          return RefreshIndicator(
            onRefresh: () async {
              setState(() => _waitlistFuture = ApiService.getWaitlist());
            },
            child: ListView.builder(
              padding: const EdgeInsets.all(16),
              itemCount: waitlists.length,
              itemBuilder: (context, index) {
                final entry = waitlists[index];
                final event = entry['event'] ?? {};
                final ticketType = entry['ticket_type'] ?? {};
                final status = entry['status'] ?? 'pending';

                return GestureDetector(
                  onTap: () {
                    Navigator.push(
                      context,
                      MaterialPageRoute(builder: (context) => EventDetailsScreen(event: event)),
                    );
                  },
                  child: Container(
                    margin: const EdgeInsets.only(bottom: 16),
                    padding: const EdgeInsets.all(16),
                    decoration: BoxDecoration(
                      color: cardColor,
                      borderRadius: BorderRadius.circular(16),
                      boxShadow: [
                        BoxShadow(color: Colors.black.withOpacity(0.05), blurRadius: 10, offset: const Offset(0, 4)),
                      ],
                    ),
                    child: Row(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        ClipRRect(
                          borderRadius: BorderRadius.circular(12),
                          child: Image.network(
                            event['poster_image_url'] ?? 'https://images.unsplash.com/photo-1459749411175-04bf5292ceea?auto=format&fit=crop&q=80&w=200',
                            width: 80,
                            height: 80,
                            fit: BoxFit.cover,
                            errorBuilder: (c, e, s) => Container(width: 80, height: 80, color: Colors.grey.shade200, child: const Icon(Icons.image)),
                          ),
                        ),
                        const SizedBox(width: 16),
                        Expanded(
                          child: Column(
                            crossAxisAlignment: CrossAxisAlignment.start,
                            children: [
                              Text(
                                event['title'] ?? 'Event',
                                style: TextStyle(color: textColor, fontSize: 16, fontWeight: FontWeight.bold),
                                maxLines: 1,
                                overflow: TextOverflow.ellipsis,
                              ),
                              const SizedBox(height: 4),
                              Row(
                                children: [
                                  Icon(Icons.calendar_today_outlined, size: 12, color: subTextColor),
                                  const SizedBox(width: 4),
                                  Text(
                                    event['date'] != null ? DateFormat('MMM d, yyyy').format(DateTime.parse(event['date'])) : 'TBA',
                                    style: TextStyle(color: subTextColor, fontSize: 12),
                                  ),
                                ],
                              ),
                              const SizedBox(height: 8),
                              Container(
                                padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 4),
                                decoration: BoxDecoration(
                                  color: const Color(0xFF6366F1).withOpacity(0.1),
                                  borderRadius: BorderRadius.circular(8),
                                ),
                                child: Text(
                                  ticketType['name'] ?? 'General',
                                  style: const TextStyle(color: Color(0xFF6366F1), fontSize: 10, fontWeight: FontWeight.bold),
                                ),
                              ),
                            ],
                          ),
                        ),
                        Container(
                          padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 6),
                          decoration: BoxDecoration(
                            color: Colors.orange.withOpacity(0.1),
                            borderRadius: BorderRadius.circular(20),
                          ),
                          child: Text(
                            status.toUpperCase(),
                            style: const TextStyle(color: Colors.orange, fontSize: 10, fontWeight: FontWeight.bold),
                          ),
                        ),
                      ],
                    ),
                  ),
                );
              },
            ),
          );
        },
      ),
    );
  }
}
