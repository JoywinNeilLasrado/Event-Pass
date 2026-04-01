import 'package:flutter/material.dart';
import '../services/api_service.dart';

class StaffManagementScreen extends StatefulWidget {
  const StaffManagementScreen({super.key});

  @override
  State<StaffManagementScreen> createState() => _StaffManagementScreenState();
}

class _StaffManagementScreenState extends State<StaffManagementScreen> {
  late Future<Map<String, dynamic>> _staffFuture;
  final TextEditingController _emailController = TextEditingController();
  bool _isAdding = false;

  @override
  void initState() {
    super.initState();
    _fetchStaff();
  }

  void _fetchStaff() {
    setState(() {
      _staffFuture = ApiService.getTeam();
    });
  }

  Future<void> _inviteStaff() async {
    final email = _emailController.text.trim();
    if (email.isEmpty) return;

    setState(() => _isAdding = true);
    final response = await ApiService.addStaff(email);
    setState(() => _isAdding = false);

    if (mounted) {
      if (response['message'] != null) {
        _emailController.clear();
        ScaffoldMessenger.of(context).showSnackBar(SnackBar(content: Text(response['message']), backgroundColor: Colors.green));
        _fetchStaff();
      } else {
        ScaffoldMessenger.of(context).showSnackBar(SnackBar(content: Text(response['error'] ?? 'Failed to add staff'), backgroundColor: Colors.redAccent));
      }
    }
  }

  Future<void> _removeStaff(int staffId) async {
    final response = await ApiService.removeStaff(staffId);
    if (mounted) {
      if (response['message'] != null) {
        ScaffoldMessenger.of(context).showSnackBar(SnackBar(content: Text(response['message'])));
        _fetchStaff();
      } else {
        ScaffoldMessenger.of(context).showSnackBar(SnackBar(content: Text(response['error'] ?? 'Failed to remove staff'), backgroundColor: Colors.redAccent));
      }
    }
  }

  @override
  Widget build(BuildContext context) {
    final isDark = Theme.of(context).brightness == Brightness.dark;
    final Color textColor = isDark ? Colors.white : Colors.black87;
    final Color subTextColor = isDark ? Colors.white70 : Colors.black54;

    return Scaffold(
      appBar: AppBar(
        title: const Text('Manage Team'),
      ),
      body: Column(
        children: [
          Container(
            padding: const EdgeInsets.all(20),
            decoration: BoxDecoration(
              color: isDark ? const Color(0xFF1E293B) : const Color(0xFFF8FAFC),
              border: Border(bottom: BorderSide(color: isDark ? Colors.white10 : Colors.black12)),
            ),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text('Add Staff Member', style: TextStyle(fontSize: 16, fontWeight: FontWeight.bold, color: textColor)),
                const SizedBox(height: 16),
                Row(
                  children: [
                    Expanded(
                      child: TextField(
                        controller: _emailController,
                        decoration: const InputDecoration(
                          hintText: 'User email address...',
                          prefixIcon: Icon(Icons.email_outlined),
                        ),
                        keyboardType: TextInputType.emailAddress,
                      ),
                    ),
                    const SizedBox(width: 12),
                    ElevatedButton(
                      onPressed: _isAdding ? null : _inviteStaff,
                      style: ElevatedButton.styleFrom(
                        padding: const EdgeInsets.symmetric(vertical: 16, horizontal: 20),
                      ),
                      child: _isAdding ? const SizedBox(width: 20, height: 20, child: CircularProgressIndicator(color: Colors.white, strokeWidth: 2)) : const Text('Add'),
                    ),
                  ],
                ),
                const SizedBox(height: 12),
                Text(
                  'User must be already registered on Event-Pass to be added as staff.',
                  style: TextStyle(fontSize: 12, color: subTextColor),
                ),
              ],
            ),
          ),
          Expanded(
            child: FutureBuilder<Map<String, dynamic>>(
              future: _staffFuture,
              builder: (context, snapshot) {
                if (snapshot.connectionState == ConnectionState.waiting) {
                  return const Center(child: CircularProgressIndicator());
                }
                if (snapshot.hasError) {
                  return Center(child: Text('Error: ${snapshot.error}'));
                }

                final List staffList = snapshot.data?['staff'] ?? [];

                if (staffList.isEmpty) {
                  return Center(
                    child: Column(
                      mainAxisAlignment: MainAxisAlignment.center,
                      children: [
                        Icon(Icons.group_off_outlined, size: 64, color: subTextColor.withOpacity(0.5)),
                        const SizedBox(height: 16),
                        Text('Your staff roster is empty.', style: TextStyle(color: subTextColor)),
                      ],
                    ),
                  );
                }

                return ListView.builder(
                  padding: const EdgeInsets.all(16),
                  itemCount: staffList.length,
                  itemBuilder: (context, index) {
                    final staff = staffList[index];
                    return Card(
                      margin: const EdgeInsets.only(bottom: 12),
                      shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
                      child: ListTile(
                        leading: CircleAvatar(
                          backgroundColor: Colors.indigo.withOpacity(0.1),
                          child: Text(staff['name']?[0]?.toUpperCase() ?? 'U', style: const TextStyle(color: Colors.indigo, fontWeight: FontWeight.bold)),
                        ),
                        title: Text(staff['name'] ?? 'Unknown', style: TextStyle(fontWeight: FontWeight.bold, color: textColor)),
                        subtitle: Text(staff['email'] ?? '', style: TextStyle(fontSize: 12, color: subTextColor)),
                        trailing: IconButton(
                          icon: const Icon(Icons.person_remove_outlined, color: Colors.redAccent, size: 20),
                          onPressed: () {
                            showDialog(
                              context: context,
                              builder: (context) => AlertDialog(
                                title: const Text('Remove Staff?'),
                                content: Text('Are you sure you want to detach ${staff['name']} from your team?'),
                                actions: [
                                  TextButton(onPressed: () => Navigator.pop(context), child: const Text('Cancel')),
                                  TextButton(
                                    onPressed: () {
                                      Navigator.pop(context);
                                      _removeStaff(staff['id']);
                                    },
                                    child: const Text('Remove', style: TextStyle(color: Colors.redAccent)),
                                  ),
                                ],
                              ),
                            );
                          },
                        ),
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
