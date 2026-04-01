import 'package:flutter/material.dart';
import '../services/api_service.dart';
import 'payment_webview_screen.dart';
import 'kyc_screen.dart';

class UpgradeScreen extends StatefulWidget {
  const UpgradeScreen({super.key});

  @override
  State<UpgradeScreen> createState() => _UpgradeScreenState();
}

class _UpgradeScreenState extends State<UpgradeScreen> {
  bool _isLoading = false;

  void _upgradeBasic() async {
    setState(() => _isLoading = true);
    final result = await ApiService.upgradeBasic();
    setState(() => _isLoading = false);

    if (mounted) {
      if (result['status'] == 'error' || result['error'] != null) {
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(content: Text(result['message'] ?? result['error'] ?? 'Upgrade failed'), backgroundColor: Colors.red),
        );
      } else {
        ScaffoldMessenger.of(context).showSnackBar(
          const SnackBar(content: Text('Plan selected! Please complete KYC.'), backgroundColor: Colors.green),
        );
        if (result['kyc_status'] == 'pending_submission' || result['is_organizer'] == null) {
          final kycSuccess = await Navigator.push(context, MaterialPageRoute(builder: (c) => const KycScreen()));
          if (kycSuccess == true) {
            Navigator.pop(context, true);
          }
        } else {
           Navigator.pop(context, true);
        }
      }
    }
  }

  void _upgradePro() async {
    setState(() => _isLoading = true);
    final result = await ApiService.upgradePro();
    setState(() => _isLoading = false);

    if (mounted) {
      if (result['status'] == 'error' || result['error'] != null) {
        ScaffoldMessenger.of(context).showSnackBar(
           SnackBar(content: Text(result['message'] ?? result['error'] ?? 'Failed to initialize payment'), backgroundColor: Colors.red),
        );
      } else if (result['paymentSessionId'] != null) {
        var payResult = await Navigator.push(
          context,
          MaterialPageRoute(
            builder: (context) => PaymentWebViewScreen(
              sessionId: result['paymentSessionId'],
              orderId: result['orderId'],
              env: result['env'] ?? 'sandbox',
            ),
          ),
        );
        if (payResult == 'success') {
          await ApiService.verifyPayment(result['orderId']);
          if (mounted) Navigator.pop(context, true);
        }
      }
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: const Text('Upgrade Plan')),
      body: _isLoading 
        ? const Center(child: CircularProgressIndicator()) 
        : ListView(
            padding: const EdgeInsets.all(16),
            children: [
              _buildPlanCard(
                title: 'Basic Plan',
                price: 'Free',
                features: ['Standard features', 'Manual KYC approval', 'Limited Support'],
                onTap: _upgradeBasic,
                isPro: false,
              ),
              const SizedBox(height: 16),
              _buildPlanCard(
                title: 'Pro Plan',
                price: '₹500 / event',
                features: ['Instant Approval', 'Unlimited events', 'Priority Support', 'Advanced Analytics'],
                onTap: _upgradePro,
                isPro: true,
              ),
            ],
          ),
    );
  }

  Widget _buildPlanCard({required String title, required String price, required List<String> features, required VoidCallback onTap, required bool isPro}) {
    return Card(
      elevation: isPro ? 8 : 2,
      shape: RoundedRectangleBorder(
        borderRadius: BorderRadius.circular(16),
        side: isPro ? const BorderSide(color: Color(0xFF6366F1), width: 2) : BorderSide.none,
      ),
      child: Padding(
        padding: const EdgeInsets.all(24),
        child: Column(
          children: [
            if (isPro)
               Container(
                 padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 4),
                 decoration: BoxDecoration(color: const Color(0xFF6366F1).withOpacity(0.1), borderRadius: BorderRadius.circular(12)),
                 child: const Text('RECOMMENDED', style: TextStyle(color: Color(0xFF6366F1), fontSize: 12, fontWeight: FontWeight.bold)),
               ),
            const SizedBox(height: 16),
            Text(title, style: const TextStyle(fontSize: 24, fontWeight: FontWeight.bold)),
            const SizedBox(height: 8),
            Text(price, style: const TextStyle(fontSize: 20, color: Colors.grey)),
            const SizedBox(height: 24),
            ...features.map((f) => Padding(
              padding: const EdgeInsets.only(bottom: 8),
              child: Row(children: [const Icon(Icons.check, color: Colors.green, size: 20), const SizedBox(width: 8), Expanded(child: Text(f))]),
            )),
            const SizedBox(height: 24),
            SizedBox(
              width: double.infinity,
              child: ElevatedButton(
                style: ElevatedButton.styleFrom(
                  backgroundColor: isPro ? const Color(0xFF6366F1) : Colors.grey.shade200,
                  foregroundColor: isPro ? Colors.white : Colors.black87,
                  padding: const EdgeInsets.symmetric(vertical: 16),
                ),
                onPressed: onTap,
                child: Text(isPro ? 'Upgrade to Pro' : 'Choose Basic'),
              ),
            ),
          ],
        ),
      ),
    );
  }
}
