import 'package:flutter/material.dart';
import 'package:flutter_staggered_animations/flutter_staggered_animations.dart';
import '../services/api_service.dart';
import 'event_details_screen.dart';
import 'filter_bottom_sheet.dart';

class ExploreScreen extends StatefulWidget {
  const ExploreScreen({super.key});

  @override
  State<ExploreScreen> createState() => _ExploreScreenState();
}

class _ExploreScreenState extends State<ExploreScreen> {
  bool _isMapView = false;
  final TextEditingController _searchController = TextEditingController();
  List<dynamic> _allEvents = [];
  List<dynamic> _filteredEvents = [];
  List<dynamic> _categoriesData = [];
  bool _isLoading = true;

  Map<String, dynamic> _currentFilters = {
    'start_date': null,
    'end_date': null,
    'min_price': 0.0,
    'max_price': 10000.0,
    'category': 'All',
  };

  @override
  void initState() {
    super.initState();
    _fetchEvents();
  }

  Future<void> _fetchEvents() async {
    setState(() => _isLoading = true);
    final response = await ApiService.getEvents();
    if (mounted) {
      setState(() {
        final dynamic eventsData = response['events'];
        _categoriesData = response['categories'] ?? [];
        _allEvents = (eventsData != null && eventsData is Map) ? (eventsData['data'] ?? []) : [];
        _applyFilters();
        _isLoading = false;
      });
    }
  }

  void _applyFilters() {
    setState(() {
      _filteredEvents = _allEvents.where((event) {
        final title = (event['title'] ?? '').toString().toLowerCase();
        final location = (event['location'] ?? '').toString().toLowerCase();
        final query = _searchController.text.toLowerCase();
        
        // Search query filter (checks title and location)
        if (query.isNotEmpty && !title.contains(query) && !location.contains(query)) return false;

        // Category filter (Case-insensitive)
        final category = _currentFilters['category'];
        final eventCategory = event['category']?['name']?.toString().toLowerCase();
        if (category != 'All' && eventCategory != category.toLowerCase()) return false;

        // Price filter
        final ticketTypes = event['ticket_types'] as List? ?? [];
        if (ticketTypes.isNotEmpty) {
          final minEventPrice = ticketTypes.map((t) => double.tryParse(t['price'].toString()) ?? 0).reduce((a, b) => a < b ? a : b);
          if (minEventPrice < _currentFilters['min_price'] || minEventPrice > _currentFilters['max_price']) return false;
        }

        return true;
      }).toList();
    });
  }

  void _showFilterSheet() {
    List<String> dropDownItems = ['All'];
    dropDownItems.addAll(_categoriesData.map((c) => c['name'].toString().isEmpty ? '' : c['name'].toString()[0].toUpperCase() + c['name'].toString().substring(1).toLowerCase()));

    showModalBottomSheet(
      context: context,
      isScrollControlled: true,
      backgroundColor: Colors.transparent,
      builder: (context) => FilterBottomSheet(
        initialFilters: _currentFilters,
        categories: dropDownItems,
        onApply: (filters) {
          setState(() {
            _currentFilters = filters;
            _applyFilters();
          });
        },
      ),
    );
  }

  @override
  Widget build(BuildContext context) {
    final bool isDark = Theme.of(context).brightness == Brightness.dark;
    final Color bgColor = isDark ? const Color(0xFF0F172A) : const Color(0xFFF8FAFC);
    final Color textColor = isDark ? Colors.white : const Color(0xFF1E293B);

    return Scaffold(
      backgroundColor: bgColor,
      appBar: AppBar(
        title: const Text('Explore Events', style: TextStyle(fontWeight: FontWeight.bold)),
        backgroundColor: Colors.transparent,
        elevation: 0,
        foregroundColor: textColor,
        actions: [
          IconButton(
            icon: Icon(_isMapView ? Icons.list : Icons.map_outlined),
            onPressed: () => setState(() => _isMapView = !_isMapView),
          ),
          const SizedBox(width: 8),
        ],
      ),
      body: Column(
        children: [
          Padding(
            padding: const EdgeInsets.symmetric(horizontal: 24, vertical: 8),
            child: Row(
              children: [
                Expanded(
                  child: Container(
                    decoration: BoxDecoration(
                      color: isDark ? const Color(0xFF1E293B) : Colors.white,
                      borderRadius: BorderRadius.circular(16),
                      boxShadow: [BoxShadow(color: Colors.black.withOpacity(0.05), blurRadius: 10, offset: const Offset(0, 4))],
                    ),
                    child: TextField(
                      controller: _searchController,
                      onChanged: (v) => _applyFilters(),
                      decoration: InputDecoration(
                        hintText: 'Search events, artists...',
                        prefixIcon: const Icon(Icons.search, color: Color(0xFF6366F1)),
                        border: InputBorder.none,
                        contentPadding: const EdgeInsets.symmetric(vertical: 14),
                      ),
                    ),
                  ),
                ),
                const SizedBox(width: 12),
                GestureDetector(
                  onTap: _showFilterSheet,
                  child: Container(
                    padding: const EdgeInsets.all(12),
                    decoration: BoxDecoration(
                      color: const Color(0xFF6366F1),
                      borderRadius: BorderRadius.circular(16),
                    ),
                    child: const Icon(Icons.tune, color: Colors.white),
                  ),
                ),
              ],
            ),
          ),
          Expanded(
            child: _isLoading 
              ? const Center(child: CircularProgressIndicator())
              : _isMapView ? _buildMapView() : _buildListView(),
          ),
        ],
      ),
    );
  }

  Widget _buildListView() {
    if (_filteredEvents.isEmpty) {
      return Center(
        child: Column(
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            Icon(Icons.search_off, size: 64, color: Colors.grey.withOpacity(0.5)),
            const SizedBox(height: 16),
            const Text('No events match your filters', style: TextStyle(color: Colors.grey, fontSize: 16)),
          ],
        ),
      );
    }

    return ListView.builder(
      padding: const EdgeInsets.all(24),
      itemCount: _filteredEvents.length,
      itemBuilder: (context, index) {
        final event = _filteredEvents[index];
        return AnimationConfiguration.staggeredList(
          position: index,
          duration: const Duration(milliseconds: 375),
          child: SlideAnimation(
            verticalOffset: 50.0,
            child: FadeInAnimation(
              child: _buildEventCard(event),
            ),
          ),
        );
      },
    );
  }

  Widget _buildMapView() {
    // Beautiful Simulated Map View
    final bool isDark = Theme.of(context).brightness == Brightness.dark;
    return Stack(
      children: [
        // Map Placeholder Background
        Container(
          width: double.infinity,
          height: double.infinity,
          color: isDark ? const Color(0xFF1E293B) : Colors.blue.shade50,
          child: Opacity(
            opacity: 0.3,
            child: Icon(Icons.map, size: 200, color: isDark ? Colors.white10 : Colors.blue.shade200),
          ),
        ),
        // Simulated Markers
        ..._filteredEvents.asMap().entries.map((entry) {
          final index = entry.key;
          final event = entry.value;
          // Fixed pseudo-random positions for demo
          final double left = 50 + (index * 70 % 300);
          final double top = 100 + (index * 110 % 400);

          return Positioned(
            left: left,
            top: top,
            child: GestureDetector(
              onTap: () => _showEventPreview(event),
              child: Column(
                children: [
                  Container(
                    padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 6),
                    decoration: BoxDecoration(
                      color: const Color(0xFF6366F1),
                      borderRadius: BorderRadius.circular(20),
                      boxShadow: [BoxShadow(color: Colors.black26, blurRadius: 4)],
                    ),
                    child: Text(
                      '₹${(event['ticket_types'] as List?)?.first['price'] ?? 0}',
                      style: const TextStyle(color: Colors.white, fontSize: 10, fontWeight: FontWeight.bold),
                    ),
                  ),
                  const Icon(Icons.location_on, color: Color(0xFF6366F1), size: 30),
                ],
              ),
            ),
          );
        }),
        
        Positioned(
          bottom: 24,
          left: 24,
          right: 24,
          child: Container(
            padding: const EdgeInsets.all(16),
            decoration: BoxDecoration(
              color: isDark ? const Color(0xFF0F172A) : Colors.white,
              borderRadius: BorderRadius.circular(20),
              boxShadow: [BoxShadow(color: Colors.black12, blurRadius: 20)],
            ),
            child: Row(
              children: [
                const Icon(Icons.my_location, color: Color(0xFF6366F1)),
                const SizedBox(width: 12),
                const Text('Showing events near you', style: TextStyle(fontWeight: FontWeight.bold)),
                const Spacer(),
                const Text('4.2 km', style: TextStyle(color: Colors.grey, fontSize: 12)),
              ],
            ),
          ),
        ),
      ],
    );
  }

  void _showEventPreview(Map<String, dynamic> event) {
    showModalBottomSheet(
      context: context,
      backgroundColor: Colors.transparent,
      builder: (context) => Container(
        margin: const EdgeInsets.all(24),
        padding: const EdgeInsets.all(16),
        decoration: BoxDecoration(
          color: Theme.of(context).cardColor,
          borderRadius: BorderRadius.circular(24),
        ),
        child: Row(
          children: [
            ClipRRect(
              borderRadius: BorderRadius.circular(16),
              child: Image.network(
                event['poster_image'] != null 
                  ? 'https://passage.viewdns.net/storage/${event['poster_image']}' 
                  : 'https://images.unsplash.com/photo-1459749411175-04bf5292ceea?auto=format&fit=crop&q=80&w=800',
                width: 80, 
                height: 80, 
                fit: BoxFit.cover,
                errorBuilder: (c, e, s) => Container(width: 80, height: 80, color: Colors.grey.shade300, child: const Icon(Icons.image_not_supported)),
              ),
            ),
            const SizedBox(width: 16),
            Expanded(
              child: Column(
                mainAxisSize: MainAxisSize.min,
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Text(event['title'], style: const TextStyle(fontWeight: FontWeight.bold, fontSize: 16), maxLines: 1),
                  const SizedBox(height: 4),
                  Text(event['location'], style: const TextStyle(color: Colors.grey, fontSize: 12)),
                  const SizedBox(height: 8),
                  Text('Starting from ₹${(event['ticket_types'] as List?)?.first['price'] ?? 0}', style: const TextStyle(color: Color(0xFF6366F1), fontWeight: FontWeight.bold)),
                ],
              ),
            ),
            IconButton(
              onPressed: () => Navigator.push(context, MaterialPageRoute(builder: (c) => EventDetailsScreen(event: event))),
              icon: const Icon(Icons.arrow_forward_ios, size: 16),
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildEventCard(Map<String, dynamic> event) {
    final bool isDark = Theme.of(context).brightness == Brightness.dark;
    final Color cardColor = isDark ? const Color(0xFF1E293B) : Colors.white;

    return GestureDetector(
      onTap: () => Navigator.push(context, MaterialPageRoute(builder: (c) => EventDetailsScreen(event: event))),
      child: Container(
        margin: const EdgeInsets.only(bottom: 20),
        decoration: BoxDecoration(
          color: cardColor,
          borderRadius: BorderRadius.circular(24),
          boxShadow: [BoxShadow(color: Colors.black.withOpacity(0.05), blurRadius: 10, offset: const Offset(0, 4))],
        ),
        child: Column(
          children: [
            ClipRRect(
              borderRadius: const BorderRadius.vertical(top: Radius.circular(24)),
              child: Image.network(
                event['poster_image_url'] ?? 'https://images.unsplash.com/photo-1459749411175-04bf5292ceea?auto=format&fit=crop&q=80&w=800',
                height: 160,
                width: double.infinity,
                fit: BoxFit.cover,
                errorBuilder: (c, e, s) => Container(height: 160, width: double.infinity, color: Colors.grey.shade300, child: const Icon(Icons.image_not_supported)),
              ),
            ),
            Padding(
              padding: const EdgeInsets.all(16),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Row(
                    children: [
                      Container(
                        padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 4),
                        decoration: BoxDecoration(color: const Color(0xFF6366F1).withOpacity(0.1), borderRadius: BorderRadius.circular(8)),
                        child: Text(event['category']?['name'] ?? 'Music', style: const TextStyle(color: Color(0xFF6366F1), fontSize: 10, fontWeight: FontWeight.bold)),
                      ),
                      const Spacer(),
                      const Icon(Icons.star, color: Colors.amber, size: 16),
                      const SizedBox(width: 4),
                      const Text('4.5', style: TextStyle(fontWeight: FontWeight.bold)),
                    ],
                  ),
                  const SizedBox(height: 12),
                  Text(event['title'], style: const TextStyle(fontSize: 18, fontWeight: FontWeight.bold)),
                  const SizedBox(height: 8),
                  Row(
                    children: [
                      const Icon(Icons.location_on_outlined, size: 16, color: Colors.grey),
                      const SizedBox(width: 4),
                      Expanded(child: Text(event['location'] ?? 'Location', style: const TextStyle(color: Colors.grey, fontSize: 13), overflow: TextOverflow.ellipsis)),
                    ],
                  ),
                ],
              ),
            ),
          ],
        ),
      ),
    );
  }
}
