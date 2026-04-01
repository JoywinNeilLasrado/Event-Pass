import 'package:flutter/material.dart';
import 'package:mobile_scanner/mobile_scanner.dart';
import '../services/api_service.dart';
import '../services/auth_service.dart';
import 'login_screen.dart';

class ScannerScreen extends StatefulWidget {
  const ScannerScreen({super.key});

  @override
  State<ScannerScreen> createState() => _ScannerScreenState();
}

class _ScannerScreenState extends State<ScannerScreen> {
  final MobileScannerController cameraController = MobileScannerController();
  bool _isProcessing = false;

  void _handleBarcode(BarcodeCapture capture) async {
    if (_isProcessing) return;

    final List<Barcode> barcodes = capture.barcodes;
    if (barcodes.isEmpty) return;

    final String? code = barcodes.first.rawValue;
    if (code == null) return;

    setState(() => _isProcessing = true);
    cameraController.stop();

    // Show loading
    showDialog(
      context: context,
      barrierDismissible: false,
      builder: (context) => Center(
        child: Container(
          padding: const EdgeInsets.all(24),
          decoration: BoxDecoration(
            color: Theme.of(context).scaffoldBackgroundColor,
            borderRadius: BorderRadius.circular(16),
          ),
          child: const CircularProgressIndicator(color: Colors.black),
        ),
      ),
    );

    // Call API to verify ticket
    final response = await ApiService.scanTicket(code);

    if (!mounted) return;
    Navigator.pop(context); // Remove loading dialog

    // Display Result
    final bool isDark = Theme.of(context).brightness == Brightness.dark;
    final Color textColor = isDark ? Colors.white : Colors.black;

    await showDialog(
      context: context,
      builder: (context) => AlertDialog(
        backgroundColor: isDark ? const Color(0xFF111111) : Colors.white,
        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(16)),
        title: Text(
          response['status'] == 'success' ? 'Valid Ticket!' : 'Attention',
          style: TextStyle(
            color: response['status'] == 'success' ? Colors.green : Colors.redAccent,
            fontWeight: FontWeight.w800,
            fontSize: 24,
            letterSpacing: -0.5,
          )
        ),
        content: Text(
          response['message'] ?? 'Unknown response',
          style: TextStyle(fontWeight: FontWeight.w500, color: textColor),
        ),
        actions: [
          TextButton(
            onPressed: () {
              Navigator.pop(context);
              setState(() => _isProcessing = false);
              cameraController.start();
            },
            child: Text('Scan Next Ticket', style: TextStyle(color: textColor, fontWeight: FontWeight.bold)),
          )
        ],
      ),
    );
  }

  void _logout() async {
    await AuthService.logout();
    if (!mounted) return;
    Navigator.pushReplacement(
      context,
      MaterialPageRoute(builder: (context) => const LoginScreen()),
    );
  }

  @override
  Widget build(BuildContext context) {
    // Elegant Dark/Light Mode adaptivity
    final bool isDark = Theme.of(context).brightness == Brightness.dark;
    final Color bgColor = isDark ? Colors.black : const Color(0xFFFAFAFA);
    final Color textColor = isDark ? Colors.white : Colors.black;
    final Color borderColor = isDark ? Colors.white.withOpacity(0.1) : Colors.black.withOpacity(0.1);

    return Scaffold(
      backgroundColor: bgColor,
      body: SafeArea(
        child: Column(
          children: [
            // Custom Sleek Navigation Bar
            Padding(
              padding: const EdgeInsets.symmetric(horizontal: 16.0, vertical: 16.0),
              child: Row(
                children: [
                  IconButton(
                    icon: Icon(Icons.arrow_back_ios_new, color: textColor, size: 20),
                    onPressed: () => Navigator.pop(context),
                  ),
                  const SizedBox(width: 8),
                  Text(
                    'Passage.',
                    style: TextStyle(
                      fontSize: 28,
                      fontWeight: FontWeight.w900,
                      letterSpacing: -1.0,
                      color: textColor,
                    ),
                  ),
                  const Spacer(),
                  Container(
                    decoration: BoxDecoration(
                      color: isDark ? Colors.white.withOpacity(0.1) : Colors.black.withOpacity(0.05),
                      borderRadius: BorderRadius.circular(50),
                    ),
                    child: IconButton(
                      icon: Icon(Icons.logout, color: textColor, size: 20),
                      onPressed: _logout,
                      tooltip: 'Logout',
                    ),
                  )
                ],
              ),
            ),
            
            // Scanner View
            Expanded(
              child: Container(
                margin: const EdgeInsets.fromLTRB(24, 8, 24, 32),
                decoration: BoxDecoration(
                  color: Colors.black, // Camera backing should be dark
                  borderRadius: BorderRadius.circular(24),
                  border: Border.all(color: borderColor, width: 1),
                  boxShadow: [
                    BoxShadow(
                      color: Colors.black.withOpacity(0.1),
                      blurRadius: 30,
                      offset: const Offset(0, 10),
                    )
                  ],
                ),
                child: ClipRRect(
                  borderRadius: BorderRadius.circular(24),
                  child: Stack(
                    alignment: Alignment.center,
                    children: [
                      Padding(
                        padding: const EdgeInsets.all(8.0),
                        child: Center(
                          child: Text(
                            "Waiting for camera...", 
                            style: TextStyle(color: Colors.white.withOpacity(0.5))
                          ),
                        ),
                      ),
                      Positioned.fill(
                        child: MobileScanner(
                          controller: cameraController,
                          onDetect: _handleBarcode,
                          errorBuilder: (context, error) {
                            return Center(
                              child: Text(
                                "Camera permission denied.\nPlease enable it in your browser settings.",
                                textAlign: TextAlign.center,
                                style: const TextStyle(color: Colors.redAccent, fontWeight: FontWeight.w600),
                              ),
                            );
                          },
                        ),
                      ),
                      // Overlay minimal viewfinder graphic
                      Container(
                        width: 250,
                        height: 250,
                        decoration: BoxDecoration(
                          border: Border.all(color: Colors.white.withOpacity(0.5), width: 2),
                          borderRadius: BorderRadius.circular(16),
                        ),
                      ),
                      const Positioned(
                        bottom: 30,
                        child: Text(
                          'Align ticket within frame',
                          style: TextStyle(
                            color: Colors.white,
                            fontWeight: FontWeight.w600,
                            letterSpacing: 0.5,
                            shadows: [Shadow(color: Colors.black87, blurRadius: 4)],
                          ),
                        ),
                      )
                    ],
                  ),
                ),
              ),
            ),
          ],
        ),
      ),
    );
  }
}
