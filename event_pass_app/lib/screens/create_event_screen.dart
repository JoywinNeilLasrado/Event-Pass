import 'dart:io';
import 'package:flutter/material.dart';
import 'package:image_picker/image_picker.dart';
import 'package:intl/intl.dart';
import '../services/api_service.dart';

class CreateEventScreen extends StatefulWidget {
  final dynamic event;
  const CreateEventScreen({super.key, this.event});

  @override
  State<CreateEventScreen> createState() => _CreateEventScreenState();
}

class _CreateEventScreenState extends State<CreateEventScreen> {
  int _currentStep = 0;
  bool _isLoading = false;
  final _formKey = GlobalKey<FormState>();

  // Form Fields
  final TextEditingController _titleController = TextEditingController();
  final TextEditingController _descriptionController = TextEditingController();
  final TextEditingController _locationController = TextEditingController();
  DateTime? _selectedDate;
  TimeOfDay? _selectedTime;
  int? _selectedCategoryId;
  File? _posterImage;
  final List<File> _galleryImages = [];
  
  // Ticket Data
  List<Map<String, dynamic>> _tickets = [
    {'name': 'General Admission', 'price': 0.0, 'capacity': 100, 'description': ''}
  ];

  List<dynamic> _categories = [];

  bool get isEditing => widget.event != null;

  @override
  void initState() {
    super.initState();
    _fetchCategories();
    if (isEditing) {
      _initializeEditMode();
    }
  }

  void _initializeEditMode() {
    final event = widget.event;
    _titleController.text = event['title'] ?? '';
    _descriptionController.text = event['description'] ?? '';
    _locationController.text = event['location'] ?? '';
    
    if (event['date'] != null) {
      _selectedDate = DateTime.parse(event['date']);
    }
    
    if (event['time'] != null) {
      final parts = event['time'].toString().split(':');
      _selectedTime = TimeOfDay(hour: int.parse(parts[0]), minute: int.parse(parts[1]));
    }
    
    _selectedCategoryId = event['category_id'];
    
    final List ticketTypes = event['ticket_types'] ?? [];
    if (ticketTypes.isNotEmpty) {
      _tickets = ticketTypes.map((t) => {
        'id': t['id'],
        'name': t['name'] ?? '',
        'price': double.tryParse(t['price'].toString()) ?? 0.0,
        'capacity': int.tryParse(t['capacity'].toString()) ?? 0,
        'description': t['description'] ?? '',
      }).toList();
    }
  }

  Future<void> _fetchCategories() async {
    final response = await ApiService.getCategories();
    if (response['categories'] != null) {
      setState(() {
        _categories = response['categories'];
        if (!isEditing && _categories.isNotEmpty && _selectedCategoryId == null) {
          _selectedCategoryId = _categories[0]['id'];
        }
      });
    }
  }

  Future<void> _pickPoster() async {
    final picker = ImagePicker();
    final pickedFile = await picker.pickImage(source: ImageSource.gallery);
    if (pickedFile != null) {
      setState(() => _posterImage = File(pickedFile.path));
    }
  }

  Future<void> _pickGalleryImages() async {
    final picker = ImagePicker();
    final pickedFiles = await picker.pickMultiImage();
    if (pickedFiles.isNotEmpty) {
      setState(() {
        _galleryImages.addAll(pickedFiles.map((f) => File(f.path)));
      });
    }
  }

  Future<void> _selectDate() async {
    final date = await showDatePicker(
      context: context,
      initialDate: _selectedDate ?? DateTime.now().add(const Duration(days: 1)),
      firstDate: DateTime.now().subtract(const Duration(days: 365)), // Allow past dates when editing
      lastDate: DateTime.now().add(const Duration(days: 730)),
    );
    if (date != null) setState(() => _selectedDate = date);
  }

  Future<void> _selectTime() async {
    final time = await showTimePicker(
      context: context,
      initialTime: _selectedTime ?? const TimeOfDay(hour: 18, minute: 0),
    );
    if (time != null) setState(() => _selectedTime = time);
  }

  void _addTicketType() {
    setState(() {
      _tickets.add({'name': '', 'price': 0.0, 'capacity': 50, 'description': ''});
    });
  }

  void _removeTicketType(int index) {
    if (_tickets.length > 1) {
      setState(() => _tickets.removeAt(index));
    }
  }

  Future<void> _submit() async {
    if (!_formKey.currentState!.validate()) return;
    if (_selectedDate == null || _selectedTime == null) {
      ScaffoldMessenger.of(context).showSnackBar(const SnackBar(content: Text('Please select date and time')));
      return;
    }

    setState(() => _isLoading = true);

    final eventData = {
      'title': _titleController.text,
      'description': _descriptionController.text,
      'location': _locationController.text,
      'date': DateFormat('yyyy-MM-dd').format(_selectedDate!),
      'time': '${_selectedTime!.hour.toString().padLeft(2, '0')}:${_selectedTime!.minute.toString().padLeft(2, '0')}',
      'category_id': _selectedCategoryId,
      'tickets': _tickets,
      'is_featured': widget.event?['is_featured'] ?? false,
    };

    Map<String, dynamic> response;
    if (isEditing) {
      response = await ApiService.updateEvent(
        eventId: widget.event['id'],
        eventData: eventData,
        posterImage: _posterImage,
        galleryImages: _galleryImages,
      );
    } else {
      response = await ApiService.createEvent(
        eventData: eventData,
        posterImage: _posterImage,
        galleryImages: _galleryImages,
      );
    }

    setState(() => _isLoading = false);

    if (mounted) {
      if (response['status'] == 'success' || response['event'] != null || response['message'] != null) {
        ScaffoldMessenger.of(context).showSnackBar(SnackBar(content: Text(isEditing ? 'Event updated successfully!' : 'Event created successfully!'), backgroundColor: Colors.green));
        Navigator.pop(context, true);
      } else {
        ScaffoldMessenger.of(context).showSnackBar(SnackBar(content: Text(response['message'] ?? response['error'] ?? 'Failed to save event'), backgroundColor: Colors.redAccent));
      }
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: Text(isEditing ? 'Edit Event' : 'Create New Event')),
      body: _isLoading 
        ? const Center(child: Column(mainAxisAlignment: MainAxisAlignment.center, children: [CircularProgressIndicator(), SizedBox(height: 16), Text('Processing your event...')]))
        : Form(
            key: _formKey,
            child: Stepper(
              type: StepperType.horizontal,
              currentStep: _currentStep,
              onStepTapped: (step) => setState(() => _currentStep = step),
              onStepContinue: () {
                if (_currentStep < 2) {
                  setState(() => _currentStep++);
                } else {
                  _submit();
                }
              },
              onStepCancel: () {
                if (_currentStep > 0) {
                  setState(() => _currentStep--);
                }
              },
              controlsBuilder: (context, details) {
                return Padding(
                  padding: const EdgeInsets.only(top: 24.0),
                  child: Row(
                    children: [
                      Expanded(
                        child: ElevatedButton(
                          onPressed: details.onStepContinue,
                          style: ElevatedButton.styleFrom(
                            backgroundColor: const Color(0xFF6366F1),
                            foregroundColor: Colors.white,
                            padding: const EdgeInsets.symmetric(vertical: 16),
                            shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
                          ),
                          child: Text(_currentStep == 2 ? (isEditing ? 'Update Event' : 'Launch Event') : 'Continue'),
                        ),
                      ),
                      if (_currentStep > 0) ...[
                        const SizedBox(width: 12),
                        Expanded(
                          child: OutlinedButton(
                            onPressed: details.onStepCancel,
                            style: OutlinedButton.styleFrom(
                              padding: const EdgeInsets.symmetric(vertical: 16),
                              shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
                            ),
                            child: const Text('Back'),
                          ),
                        ),
                      ],
                    ],
                  ),
                );
              },
              steps: [
                _buildGeneralInfoStep(),
                _buildDateTimeStep(),
                _buildTicketsStep(),
              ],
            ),
          ),
    );
  }

  Step _buildGeneralInfoStep() {
    return Step(
      title: const Text('Basic', style: TextStyle(fontSize: 12)),
      isActive: _currentStep == 0,
      content: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          const Text('Event Details', style: TextStyle(fontSize: 18, fontWeight: FontWeight.bold)),
          const SizedBox(height: 16),
          TextFormField(
            controller: _titleController,
            decoration: const InputDecoration(labelText: 'Event Title', prefixIcon: Icon(Icons.title), border: OutlineInputBorder()),
            validator: (v) => v!.isEmpty ? 'Title is required' : null,
          ),
          const SizedBox(height: 16),
          DropdownButtonFormField<int>(
            initialValue: _selectedCategoryId,
            decoration: const InputDecoration(labelText: 'Category', prefixIcon: Icon(Icons.category_outlined), border: OutlineInputBorder()),
            items: _categories.map((c) => DropdownMenuItem<int>(value: c['id'], child: Text(c['name']))).toList(),
            onChanged: (v) => setState(() => _selectedCategoryId = v),
          ),
          const SizedBox(height: 16),
          TextFormField(
            controller: _descriptionController,
            decoration: const InputDecoration(labelText: 'Description', alignLabelWithHint: true, border: OutlineInputBorder()),
            maxLines: 4,
            validator: (v) => v!.isEmpty ? 'Description is required' : null,
          ),
          const SizedBox(height: 24),
          Text(isEditing ? 'Update Event Poster (Optional)' : 'Event Poster', style: const TextStyle(fontWeight: FontWeight.bold)),
          const SizedBox(height: 8),
          GestureDetector(
            onTap: _pickPoster,
            child: Container(
              height: 150,
              width: double.infinity,
              decoration: BoxDecoration(
                color: Colors.grey.withOpacity(0.1),
                borderRadius: BorderRadius.circular(12),
                border: Border.all(color: Colors.grey.withOpacity(0.3)),
              ),
              child: _posterImage != null
                  ? ClipRRect(borderRadius: BorderRadius.circular(12), child: Image.file(_posterImage!, fit: BoxFit.cover))
                  : (isEditing && widget.event['poster_image_url'] != null)
                      ? ClipRRect(borderRadius: BorderRadius.circular(12), child: Image.network(widget.event['poster_image_url'], fit: BoxFit.cover))
                      : const Column(mainAxisAlignment: MainAxisAlignment.center, children: [Icon(Icons.image_outlined, size: 40, color: Colors.grey), Text('Upload Poster Image')]),
            ),
          ),
        ],
      ),
    );
  }

  Step _buildDateTimeStep() {
    return Step(
      title: const Text('Venue', style: TextStyle(fontSize: 12)),
      isActive: _currentStep == 1,
      content: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          const Text('Where & When', style: TextStyle(fontSize: 18, fontWeight: FontWeight.bold)),
          const SizedBox(height: 16),
          TextFormField(
            controller: _locationController,
            decoration: const InputDecoration(labelText: 'Venue Location', prefixIcon: Icon(Icons.location_on_outlined), border: OutlineInputBorder()),
            validator: (v) => v!.isEmpty ? 'Location is required' : null,
          ),
          const SizedBox(height: 16),
          Row(
            children: [
              Expanded(
                child: ListTile(
                  title: const Text('Date'),
                  subtitle: Text(_selectedDate == null ? 'Select Date' : DateFormat('EEE, MMM d, yyyy').format(_selectedDate!)),
                  leading: const Icon(Icons.calendar_today_outlined),
                  onTap: _selectDate,
                  tileColor: Colors.grey.withOpacity(0.05),
                  shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
                ),
              ),
            ],
          ),
          const SizedBox(height: 12),
          Row(
            children: [
              Expanded(
                child: ListTile(
                  title: const Text('Time'),
                  subtitle: Text(_selectedTime == null ? 'Select Time' : _selectedTime!.format(context)),
                  leading: const Icon(Icons.access_time),
                  onTap: _selectTime,
                  tileColor: Colors.grey.withOpacity(0.05),
                  shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
                ),
              ),
            ],
          ),
          const SizedBox(height: 24),
          const Text('Gallery Images (Optional)', style: TextStyle(fontWeight: FontWeight.bold)),
          const SizedBox(height: 8),
          SingleChildScrollView(
            scrollDirection: Axis.horizontal,
            child: Row(
              children: [
                GestureDetector(
                  onTap: _pickGalleryImages,
                  child: Container(
                    width: 80,
                    height: 80,
                    decoration: BoxDecoration(color: Colors.grey.withOpacity(0.1), borderRadius: BorderRadius.circular(12), border: Border.all(color: Colors.grey.withOpacity(0.3))),
                    child: const Icon(Icons.add_a_photo_outlined, color: Colors.grey),
                  ),
                ),
                ..._galleryImages.map((img) => Padding(
                  padding: const EdgeInsets.only(left: 8.0),
                  child: ClipRRect(borderRadius: BorderRadius.circular(12), child: Image.file(img, width: 80, height: 80, fit: BoxFit.cover)),
                )),
              ],
            ),
          ),
        ],
      ),
    );
  }

  Step _buildTicketsStep() {
    return Step(
      title: const Text('Tickets', style: TextStyle(fontSize: 12)),
      isActive: _currentStep == 2,
      content: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Row(
            mainAxisAlignment: MainAxisAlignment.spaceBetween,
            children: [
              const Text('Ticket Types', style: TextStyle(fontSize: 18, fontWeight: FontWeight.bold)),
              IconButton(onPressed: _addTicketType, icon: const Icon(Icons.add_circle, color: Colors.indigo)),
            ],
          ),
          const SizedBox(height: 8),
          ..._tickets.asMap().entries.map((entry) {
            final idx = entry.key;
            final ticket = entry.value;
            return Card(
              margin: const EdgeInsets.only(bottom: 16),
              child: Padding(
                padding: const EdgeInsets.all(12.0),
                child: Column(
                  children: [
                    Row(
                      children: [
                        Expanded(child: TextFormField(initialValue: ticket['name'], decoration: const InputDecoration(labelText: 'Ticket Name'), onChanged: (v) => ticket['name'] = v)),
                        IconButton(onPressed: () => _removeTicketType(idx), icon: const Icon(Icons.delete_outline, color: Colors.redAccent)),
                      ],
                    ),
                    Row(
                      children: [
                        Expanded(child: TextFormField(initialValue: ticket['price'].toString(), decoration: const InputDecoration(labelText: 'Price (₹)'), keyboardType: TextInputType.number, onChanged: (v) => ticket['price'] = double.tryParse(v) ?? 0.0)),
                        const SizedBox(width: 12),
                        Expanded(child: TextFormField(initialValue: ticket['capacity'].toString(), decoration: const InputDecoration(labelText: 'Capacity'), keyboardType: TextInputType.number, onChanged: (v) => ticket['capacity'] = int.tryParse(v) ?? 0)),
                      ],
                    ),
                  ],
                ),
              ),
            );
          }),
        ],
      ),
    );
  }
}
