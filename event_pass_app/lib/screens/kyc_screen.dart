import 'package:flutter/material.dart';
import '../services/api_service.dart';

class KycScreen extends StatefulWidget {
  const KycScreen({super.key});

  @override
  State<KycScreen> createState() => _KycScreenState();
}

class _KycScreenState extends State<KycScreen> {
  final _formKey = GlobalKey<FormState>();
  final _businessController = TextEditingController();
  final _socialsController = TextEditingController();
  bool _isLoading = false;

  Future<void> _submitKyc() async {
    if (!_formKey.currentState!.validate()) return;
    
    setState(() => _isLoading = true);
    
    final result = await ApiService.submitKyc(
      _businessController.text.trim(),
      _socialsController.text.trim(),
    );
    
    if (mounted) {
      setState(() => _isLoading = false);
      if (result['status'] == 'error' || result['error'] != null) {
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(content: Text(result['message'] ?? result['error'] ?? 'Submission failed'), backgroundColor: Colors.red),
        );
      } else {
        ScaffoldMessenger.of(context).showSnackBar(
          const SnackBar(content: Text('KYC Submitted successfully!'), backgroundColor: Colors.green),
        );
        Navigator.pop(context, true);
      }
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: const Text('Complete KYC')),
      body: SingleChildScrollView(
        padding: const EdgeInsets.all(16),
        child: Form(
          key: _formKey,
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              const Text('Please provide your details below to become an organizer.', style: TextStyle(fontSize: 16)),
              const SizedBox(height: 24),
              TextFormField(
                controller: _businessController,
                decoration: const InputDecoration(
                  labelText: 'Business Details',
                  hintText: 'Enter company name, registration details etc.',
                  border: OutlineInputBorder(),
                ),
                maxLines: 4,
                validator: (v) => v!.trim().isEmpty ? 'Please enter business details' : null,
              ),
              const SizedBox(height: 16),
              TextFormField(
                controller: _socialsController,
                decoration: const InputDecoration(
                  labelText: 'Social Links (Optional)',
                  hintText: 'Website, LinkedIn, Twitter etc.',
                  border: OutlineInputBorder(),
                ),
                maxLines: 2,
              ),
              const SizedBox(height: 24),
              SizedBox(
                width: double.infinity,
                child: ElevatedButton(
                  style: ElevatedButton.styleFrom(
                    backgroundColor: const Color(0xFF6366F1),
                    foregroundColor: Colors.white,
                    padding: const EdgeInsets.symmetric(vertical: 16),
                  ),
                  onPressed: _isLoading ? null : _submitKyc,
                  child: _isLoading ? const CircularProgressIndicator(color: Colors.white) : const Text('Submit Application'),
                ),
              ),
            ],
          ),
        ),
      ),
    );
  }
}
