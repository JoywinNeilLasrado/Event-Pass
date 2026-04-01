import 'package:flutter/material.dart';
import 'event_details_screen.dart';
import 'login_screen.dart';
import '../services/auth_service.dart';
import '../services/api_service.dart';

class EventsScreen extends StatefulWidget {
  const EventsScreen({super.key});

  @override
  State<EventsScreen> createState() => _EventsScreenState();
}

class _EventsScreenState extends State<EventsScreen> {
  Future<Map<String, dynamic>> _eventsFuture = ApiService.getEvents();

  String _searchQuery = '';
  String _selectedCategory = 'All Categories';
  final TextEditingController _searchController = TextEditingController();

  String? _getCategoryImageUrl(Map<String, dynamic> category) {
    if (category['image_path'] != null &&
        category['image_path'].toString().isNotEmpty &&
        category['image_path'].toString() != '0') {
      String path = category['image_path'].toString();
      if (path.startsWith('http')) {
        if (ApiService.useLocal && path.contains('127.0.0.1')) {
          path = path.replaceAll('127.0.0.1', '10.0.2.2');
        }
        return path;
      }
      return '${ApiService.baseUrl.replaceAll('/api', '')}/storage/$path';
    }
    return null;
  }
  
  Widget _buildFallbackIcon(String catName, bool isDark) {
    IconData icon;
    switch (catName.toLowerCase()) {
      case 'sports': icon = Icons.sports_basketball; break;
      case 'music': icon = Icons.music_note; break;
      case 'art': icon = Icons.palette; break;
      case 'business': icon = Icons.business_center; break;
      case 'competition': icon = Icons.emoji_events; break;
      case 'culture': icon = Icons.account_balance; break;
      case 'education': icon = Icons.school; break;
      case 'lifestyle': icon = Icons.local_cafe; break;
      default: icon = Icons.category; break;
    }
    return Container(
      width: 72,
      height: 72,
      decoration: BoxDecoration(
        color: isDark ? const Color(0xFF1E293B) : Colors.grey.shade200,
        shape: BoxShape.circle,
      ),
      child: Icon(icon, color: isDark ? Colors.white70 : Colors.black54, size: 32),
    );
  }

  String _capitalize(String s) =>
      s.isEmpty ? '' : s[0].toUpperCase() + s.substring(1).toLowerCase();

  void _fetchEvents() {
    setState(() {
      _eventsFuture = ApiService.getEvents(
        search: _searchQuery,
        category: _selectedCategory,
      );
    });
  }

  @override
  void initState() {
    super.initState();
  }

  @override
  Widget build(BuildContext context) {
    final bool isDark = Theme.of(context).brightness == Brightness.dark;
    final Color bgColor =
        isDark ? const Color(0xFF111713) : const Color(0xFFEAF5EF);
    final Color textColor = isDark ? Colors.white : const Color(0xFF1A1A1A);
    final Color subTextColor =
        isDark ? Colors.white70 : const Color(0xFF666666);
    final Color cardColor = isDark ? const Color(0xFF1A221C) : Colors.white;

    return Scaffold(
      backgroundColor: bgColor,
      appBar: AppBar(
        backgroundColor: Colors.transparent,
        elevation: 0,
        title: Text(
          'Passage.',
          style: TextStyle(
            color: textColor,
            fontWeight: FontWeight.w900,
            fontSize: 24,
            letterSpacing: -1.0,
          ),
        ),
        actions: [
          IconButton(
            icon: Icon(Icons.logout, color: textColor),
            onPressed: () async {
              await AuthService.logout();
              if (mounted) {
                Navigator.pushAndRemoveUntil(
                  context,
                  MaterialPageRoute(builder: (context) => const LoginScreen()),
                  (route) => false,
                );
              }
            },
          ),
          const SizedBox(width: 8),
        ],
      ),
      body: RefreshIndicator(
        onRefresh: () async {
          _fetchEvents();
        },
        child: FutureBuilder<Map<String, dynamic>>(
          future: _eventsFuture,
          builder: (context, snapshot) {
            final bool isLoading = snapshot.connectionState == ConnectionState.waiting;
            final data = snapshot.data ?? {};

            if (snapshot.hasError || (snapshot.data != null && snapshot.data!['status'] == 'error')) {
              return SingleChildScrollView(
                physics: const AlwaysScrollableScrollPhysics(),
                child: Center(
                  child: Padding(
                    padding: const EdgeInsets.only(top: 100),
                    child: Text(
                      snapshot.data?['message'] ?? 'Error fetching data. (${snapshot.error})',
                      style: TextStyle(color: subTextColor),
                    ),
                  ),
                ),
              );
            }

            final List categoriesData = data['categories'] ?? [];
            final dynamic eventsData = data['events'];
            final List events = (eventsData != null && eventsData is Map)
                ? (eventsData['data'] ?? [])
                : [];

            List<String> dropDownItems = ['All Categories'];
            dropDownItems.addAll(
                categoriesData.map((c) => _capitalize(c['name'].toString())));

            return SingleChildScrollView(
              padding: const EdgeInsets.symmetric(horizontal: 20.0),
              physics: const AlwaysScrollableScrollPhysics(),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  const SizedBox(height: 16),
                  Text(
                    'Upcoming Events',
                    style: TextStyle(
                      fontSize: 28,
                      fontWeight: FontWeight.bold,
                      color: textColor,
                      letterSpacing: -0.5,
                    ),
                  ),
                  const SizedBox(height: 8),
                  Text(
                    'Discover and book the best exclusive events around you.',
                    style: TextStyle(
                      fontSize: 15,
                      color: subTextColor,
                      height: 1.4,
                    ),
                  ),
                  const SizedBox(height: 32),

                  // Categories List
                  if (categoriesData.isNotEmpty)
                    SizedBox(
                      height: 110,
                      child: ListView.builder(
                        scrollDirection: Axis.horizontal,
                        clipBehavior: Clip.none,
                        itemCount: categoriesData.length,
                        itemBuilder: (context, index) {
                          final category = categoriesData[index];
                          final catName = _capitalize(category['name'].toString());
                          final isSelected = _selectedCategory == catName;

                          return GestureDetector(
                            onTap: () {
                              setState(() {
                                _selectedCategory =
                                    isSelected ? 'All Categories' : catName;
                                _searchQuery = '';
                                _searchController.clear();
                                _fetchEvents();
                              });
                            },
                            child: Padding(
                              padding: const EdgeInsets.only(right: 20.0),
                              child: Column(
                                children: [
                                  _getCategoryImageUrl(category) != null
                                      ? Container(
                                          width: 72,
                                          height: 72,
                                          decoration: BoxDecoration(
                                            shape: BoxShape.circle,
                                            border: Border.all(
                                              color: isSelected
                                                  ? const Color(0xFF6366F1)
                                                  : (isDark
                                                      ? Colors.white.withOpacity(0.1)
                                                      : Colors.white),
                                              width: isSelected ? 3 : 4,
                                            ),
                                            boxShadow: [
                                              BoxShadow(
                                                color: Colors.black.withOpacity(
                                                    isDark ? 0.4 : 0.05),
                                                blurRadius: 10,
                                                offset: const Offset(0, 4),
                                              ),
                                            ],
                                            image: DecorationImage(
                                              image: NetworkImage(
                                                _getCategoryImageUrl(category)!,
                                              ),
                                              fit: BoxFit.cover,
                                              onError: (exception, stackTrace) {}, // prevents fatal crash
                                            ),
                                          ),
                                        )
                                      : Container(
                                          decoration: BoxDecoration(
                                            shape: BoxShape.circle,
                                            border: Border.all(
                                              color: isSelected
                                                  ? const Color(0xFF6366F1)
                                                  : (isDark
                                                      ? Colors.white.withOpacity(0.1)
                                                      : Colors.white),
                                              width: isSelected ? 3 : 4,
                                            ),
                                            boxShadow: [
                                              BoxShadow(
                                                color: Colors.black.withOpacity(
                                                    isDark ? 0.4 : 0.05),
                                                blurRadius: 10,
                                                offset: const Offset(0, 4),
                                              ),
                                            ],
                                          ),
                                          child: _buildFallbackIcon(catName, isDark),
                                        ),
                                  const SizedBox(height: 8),
                                  Text(
                                    catName,
                                    style: TextStyle(
                                      fontSize: 13,
                                      fontWeight: isSelected
                                          ? FontWeight.bold
                                          : FontWeight.w600,
                                      color: isSelected
                                          ? const Color(0xFF6366F1)
                                          : textColor.withOpacity(0.8),
                                    ),
                                  ),
                                ],
                              ),
                            ),
                          );
                        },
                      ),
                    ),

                  const SizedBox(height: 32),

                  // Search Bar
                  Container(
                    decoration: BoxDecoration(
                      color: cardColor,
                      borderRadius: BorderRadius.circular(24),
                      boxShadow: [
                        BoxShadow(
                          color: Colors.black.withOpacity(isDark ? 0.2 : 0.04),
                          blurRadius: 24,
                          offset: const Offset(0, 8),
                        ),
                      ],
                    ),
                    padding: const EdgeInsets.all(20.0),
                    child: Column(
                      children: [
                        TextField(
                          controller: _searchController,
                          onSubmitted: (value) {
                            _searchQuery = value;
                            _fetchEvents();
                          },
                          decoration: InputDecoration(
                            hintText: 'Search events...',
                            prefixIcon: Icon(Icons.search, color: subTextColor),
                          ),
                          style: TextStyle(color: textColor),
                        ),
                        const SizedBox(height: 16),
                        Row(
                          children: [
                            Expanded(
                              child: Container(
                                padding:
                                    const EdgeInsets.symmetric(horizontal: 16),
                                decoration: BoxDecoration(
                                  color: isDark
                                      ? Colors.white.withOpacity(0.05)
                                      : const Color(0xFFF8FAFC),
                                  borderRadius: BorderRadius.circular(12),
                                ),
                                child: DropdownButtonHideUnderline(
                                  child: DropdownButton<String>(
                                    isExpanded: true,
                                    value: _selectedCategory == ""
                                        ? 'All Categories'
                                        : (dropDownItems
                                                .contains(_selectedCategory)
                                            ? _selectedCategory
                                            : 'All Categories'),
                                    icon: Icon(
                                      Icons.keyboard_arrow_down,
                                      size: 20,
                                      color: subTextColor,
                                    ),
                                    style: TextStyle(
                                      fontSize: 14,
                                      color: textColor,
                                      fontWeight: FontWeight.w500,
                                    ),
                                    dropdownColor: isDark
                                        ? const Color(0xFF1E293B)
                                        : Colors.white,
                                    items: dropDownItems.map((String value) {
                                      return DropdownMenuItem<String>(
                                        value: value,
                                        child: Text(
                                          value,
                                          overflow: TextOverflow.ellipsis,
                                        ),
                                      );
                                    }).toList(),
                                    onChanged: (val) {
                                      if (val != null) {
                                        setState(() {
                                          _selectedCategory =
                                              val == 'All Categories'
                                                  ? ''
                                                  : val;
                                          _fetchEvents();
                                        });
                                      }
                                    },
                                  ),
                                ),
                              ),
                            ),
                            const SizedBox(width: 12),
                            ElevatedButton(
                              onPressed: () {
                                _searchQuery = _searchController.text;
                                _fetchEvents();
                              },
                              style: ElevatedButton.styleFrom(
                                padding: const EdgeInsets.symmetric(
                                  horizontal: 20,
                                  vertical: 14,
                                ),
                              ),
                              child: const Text(
                                'Search',
                                style: TextStyle(fontWeight: FontWeight.bold),
                              ),
                            ),
                          ],
                        ),
                      ],
                    ),
                  ),

                  const SizedBox(height: 32),

                  // Events List Inner
                  if (isLoading && !snapshot.hasData)
                    const Center(
                      child: Padding(
                        padding: EdgeInsets.all(32.0),
                        child: CircularProgressIndicator(),
                      ),
                    ),

                  if (events.isEmpty && !isLoading)
                    Center(
                      child: Padding(
                        padding: const EdgeInsets.all(32.0),
                        child: Text(
                          'No upcoming events found.',
                          style: TextStyle(color: subTextColor, fontSize: 16),
                        ),
                      ),
                    )
                  else
                    Column(
                      children: events.map<Widget>((event) {
                        String imageUrl = event['poster_image_url'] ??
                            'https://images.unsplash.com/photo-1459749411175-04bf5292ceea?auto=format&fit=crop&q=80&w=800';

                        if (ApiService.useLocal &&
                            imageUrl.contains('127.0.0.1')) {
                          imageUrl =
                              imageUrl.replaceAll('127.0.0.1', '10.0.2.2');
                        }

                        return GestureDetector(
                          onTap: () {
                            Navigator.push(
                              context,
                              MaterialPageRoute(
                                builder: (context) =>
                                    EventDetailsScreen(event: event),
                              ),
                            );
                          },
                          child: Container(
                            margin: const EdgeInsets.only(bottom: 24),
                            decoration: BoxDecoration(
                              color: cardColor,
                              borderRadius: BorderRadius.circular(24),
                              boxShadow: [
                                BoxShadow(
                                  color: Colors.black.withOpacity(
                                    isDark ? 0.3 : 0.04,
                                  ),
                                  blurRadius: 24,
                                  offset: const Offset(0, 8),
                                ),
                              ],
                            ),
                            clipBehavior: Clip.antiAlias,
                            child: Column(
                              crossAxisAlignment: CrossAxisAlignment.start,
                              children: [
                                Image.network(
                                  imageUrl,
                                  height: 180,
                                  width: double.infinity,
                                  fit: BoxFit.cover,
                                  errorBuilder: (c, e, s) => Container(
                                    height: 180,
                                    width: double.infinity,
                                    color: Colors.grey.shade300,
                                    child: const Icon(Icons.image_not_supported),
                                  ),
                                ),
                                Padding(
                                  padding: const EdgeInsets.all(24),
                                  child: Column(
                                    crossAxisAlignment:
                                        CrossAxisAlignment.start,
                                    children: [
                                      Row(
                                        children: [
                                          Container(
                                            padding: const EdgeInsets.symmetric(
                                              horizontal: 12,
                                              vertical: 6,
                                            ),
                                            decoration: BoxDecoration(
                                              color: isDark
                                                  ? const Color(0xFF1E2F23)
                                                  : Colors.green.shade50,
                                              borderRadius:
                                                  BorderRadius.circular(20),
                                            ),
                                            child: Text(
                                              _capitalize(
                                                event['category'] != null
                                                    ? event['category']['name']
                                                        .toString()
                                                    : 'Event',
                                              ),
                                              style: TextStyle(
                                                color: isDark
                                                    ? Colors.green.shade400
                                                    : Colors.green.shade700,
                                                fontSize: 12,
                                                fontWeight: FontWeight.w800,
                                                letterSpacing: 0.5,
                                              ),
                                            ),
                                          ),
                                          const Spacer(),
                                          Text(
                                            event['date'] != null
                                                ? event['date']
                                                    .toString()
                                                    .split('T')
                                                    .first
                                                : '',
                                            style: TextStyle(
                                              color: subTextColor,
                                              fontSize: 13,
                                              fontWeight: FontWeight.w700,
                                            ),
                                          ),
                                        ],
                                      ),
                                      const SizedBox(height: 16),
                                      Text(
                                        event['title'] ?? 'Untitled Event',
                                        style: TextStyle(
                                          fontSize: 22,
                                          fontWeight: FontWeight.w900,
                                          color: textColor,
                                          letterSpacing: -0.5,
                                        ),
                                      ),
                                      const SizedBox(height: 8),
                                      Text(
                                        event['description'] ?? '',
                                        maxLines: 2,
                                        overflow: TextOverflow.ellipsis,
                                        style: TextStyle(
                                          fontSize: 15,
                                          color: subTextColor,
                                          height: 1.5,
                                        ),
                                      ),
                                    ],
                                  ),
                                ),
                              ],
                            ),
                          ),
                        );
                      }).toList(),
                    ),
                  const SizedBox(height: 48),
                ],
              ),
            );
          },
        ),
      ),
    );
  }
}
