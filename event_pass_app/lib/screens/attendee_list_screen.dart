import 'package:flutter/material.dart';
import '../services/api_service.dart';

class AttendeeListScreen extends StatefulWidget {
  final Map<String, dynamic> event;

  const AttendeeListScreen({super.key, required this.event});

  @override
  State<AttendeeListScreen> createState() => _AttendeeListScreenState();
}

class _AttendeeListScreenState extends State<AttendeeListScreen> {
  late Future<Map<String, dynamic>> _attendeesFuture;
  final TextEditingController _searchController = TextEditingController();
  String _searchQuery = '';

  @override
  void initState() {
    super.initState();
    _fetchAttendees();
  }

  void _fetchAttendees() {
    setState(() {
      _attendeesFuture = ApiService.getEventAttendees(widget.event['id'], search: _searchQuery);
    });
  }

  Future<void> _manualCheckIn(int bookingId) async {
    final response = await ApiService.checkInAttendee(bookingId);
    if (mounted) {
      if (response['message'] != null) {
        ScaffoldMessenger.of(context).showSnackBar(SnackBar(
          content: Text(response['message']),
          backgroundColor: Colors.green,
        ));
        _fetchAttendees(); // Refresh list
      } else {
        ScaffoldMessenger.of(context).showSnackBar(SnackBar(
          content: Text(response['error'] ?? 'Check-in failed'),
          backgroundColor: Colors.redAccent,
        ));
      }
    }
  }

  void _showBroadcastDialog() {
    final subjectController = TextEditingController();
    final messageController = TextEditingController();

    showDialog(
      context: context,
      builder: (context) => AlertDialog(
        title: const Text('Message All Attendees'),
        content: Column(
          mainAxisSize: MainAxisSize.min,
          children: [
            TextField(
              controller: subjectController,
              decoration: const InputDecoration(labelText: 'Subject'),
            ),
            const SizedBox(height: 12),
            TextField(
              controller: messageController,
              decoration: const InputDecoration(labelText: 'Message'),
              maxLines: 4,
            ),
          ],
        ),
        actions: [
          TextButton(onPressed: () => Navigator.pop(context), child: const Text('Cancel')),
          ElevatedButton(
            onPressed: () async {
              if (subjectController.text.isEmpty || messageController.text.isEmpty) return;
              final response = await ApiService.messageAttendees(
                widget.event['id'],
                subjectController.text,
                messageController.text,
              );
              if (mounted) {
                Navigator.pop(context);
                ScaffoldMessenger.of(context).showSnackBar(SnackBar(
                  content: Text(response['message'] ?? 'Message sent!'),
                ));
              }
            },
            child: const Text('Send Blast'),
          ),
        ],
      ),
    );
  }

  @override
  Widget build(BuildContext context) {
    final bool isDark = Theme.of(context).brightness == Brightness.dark;
    final Color textColor = isDark ? Colors.white : Colors.black87;
    final Color subTextColor = isDark ? Colors.white70 : Colors.black54;

    return Scaffold(
      appBar: AppBar(
        title: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            const Text('Attendees', style: TextStyle(fontWeight: FontWeight.bold)),
            Text(widget.event['title'] ?? '', style: TextStyle(fontSize: 12, color: subTextColor)),
          ],
        ),
        actions: [
          IconButton(
            icon: const Icon(Icons.campaign_outlined),
            onPressed: _showBroadcastDialog,
            tooltip: 'Message All',
          ),
        ],
      ),
      body: Column(
        children: [
          Padding(
            padding: const EdgeInsets.all(16.0),
            child: TextField(
              controller: _searchController,
              decoration: InputDecoration(
                hintText: 'Search name, email, or ID...',
                prefixIcon: const Icon(Icons.search),
                suffixIcon: IconButton(
                  icon: const Icon(Icons.clear),
                  onPressed: () {
                    _searchController.clear();
                    setState(() => _searchQuery = '');
                    _fetchAttendees();
                  },
                ),
                border: OutlineInputBorder(borderRadius: BorderRadius.circular(12)),
              ),
              onSubmitted: (value) {
                setState(() => _searchQuery = value);
                _fetchAttendees();
              },
            ),
          ),
          Expanded(
            child: FutureBuilder<Map<String, dynamic>>(
              future: _attendeesFuture,
              builder: (context, snapshot) {
                if (snapshot.connectionState == ConnectionState.waiting) {
                  return const Center(child: CircularProgressIndicator());
                }
                if (snapshot.hasError) {
                  return Center(child: Text('Error: ${snapshot.error}'));
                }

                final data = snapshot.data ?? {};
                final dynamic eventData = data['event'];
                final List bookings = eventData != null ? (eventData['bookings'] ?? []) : [];

                if (bookings.isEmpty) {
                  return const Center(child: Text('No attendees found matching your search.'));
                }

                return ListView.builder(
                  itemCount: bookings.length,
                  itemBuilder: (context, index) {
                    final booking = bookings[index];
                    final user = booking['user'] ?? {};
                    final bool isCheckedIn = booking['is_checked_in'] == 1 || booking['is_checked_in'] == true;

                    return ListTile(
                      leading: CircleAvatar(
                        backgroundColor: isCheckedIn ? Colors.green.withOpacity(0.1) : Colors.grey.withOpacity(0.1),
                        child: Icon(
                          isCheckedIn ? Icons.check_circle : Icons.person_outline,
                          color: isCheckedIn ? Colors.green : Colors.grey,
                        ),
                      ),
                      title: Text(user['name'] ?? 'Unknown User', style: TextStyle(fontWeight: FontWeight.bold, color: textColor)),
                      subtitle: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          Text(user['email'] ?? '', style: TextStyle(fontSize: 12, color: subTextColor)),
                          Text('Tier: ${booking['ticket_type']?['name'] ?? 'Free'} • ID: ${booking['id']}', style: TextStyle(fontSize: 11, color: subTextColor)),
                        ],
                      ),
                      trailing: isCheckedIn
                          ? const Icon(Icons.verified, color: Colors.green, size: 28)
                          : ElevatedButton(
                              onPressed: () => _manualCheckIn(booking['id']),
                              style: ElevatedButton.styleFrom(
                                backgroundColor: const Color(0xFF6366F1),
                                foregroundColor: Colors.white,
                                shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(8)),
                                padding: const EdgeInsets.symmetric(horizontal: 12),
                              ),
                              child: const Text('Check In', style: TextStyle(fontSize: 12)),
                            ),
                    );
                  },
                );
              },
            ),
          ),
        ],
      ),
    );
  }
}
