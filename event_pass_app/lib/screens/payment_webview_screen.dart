import 'package:flutter/material.dart';
import 'package:webview_flutter/webview_flutter.dart';

class PaymentWebViewScreen extends StatefulWidget {
  final String sessionId;
  final String orderId;
  final String env;

  const PaymentWebViewScreen({
    super.key,
    required this.sessionId,
    required this.orderId,
    required this.env,
  });

  @override
  State<PaymentWebViewScreen> createState() => _PaymentWebViewScreenState();
}

class _PaymentWebViewScreenState extends State<PaymentWebViewScreen> {
  late final WebViewController _controller;
  bool _isLoading = true;

  @override
  void initState() {
    super.initState();
    
    final String htmlString = '''
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Redirecting to Payment...</title>
    <script src="https://sdk.cashfree.com/js/v3/cashfree.js"></script>
</head>
<body style="background-color: #f9fafb; display: flex; align-items: center; justify-content: center; height: 100vh; font-family: sans-serif; margin: 0;">
    <div style="text-align: center;">
        <h2 style="color: #374151; font-size: 20px;">Loading Secure Checkout...</h2>
        <p style="color: #6b7280; margin-top: 8px;">Please wait...</p>
    </div>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const cashfree = Cashfree({
                mode: "${widget.env == 'production' ? 'production' : 'sandbox'}"
            });
            cashfree.checkout({
                paymentSessionId: "${widget.sessionId}",
                redirectTarget: "_self"
            });
        });
    </script>
</body>
</html>
''';

    _controller = WebViewController()
      ..setJavaScriptMode(JavaScriptMode.unrestricted)
      ..setNavigationDelegate(
        NavigationDelegate(
          onPageStarted: (url) {
            setState(() => _isLoading = true);
          },
          onPageFinished: (url) {
            setState(() => _isLoading = false);
            
            // Check for Success or Failure based on redirect URLs
            if (url.contains('/payment/success') || url.contains('order_status=PAID')) {
              Navigator.pop(context, 'success');
            } else if (url.contains('/payment/cancel') || url.contains('order_status=CANCELLED')) {
              Navigator.pop(context, 'cancelled');
            }
          },
          onNavigationRequest: (request) {
            if (request.url.contains('payment/success') || request.url.contains('order_status=PAID')) {
              Navigator.pop(context, 'success');
              return NavigationDecision.prevent;
            }
            if (request.url.contains('payment/cancel') || request.url.contains('order_status=CANCELLED')) {
              Navigator.pop(context, 'cancelled');
              return NavigationDecision.prevent;
            }
            return NavigationDecision.navigate;
          },
        ),
      )
      ..loadHtmlString(htmlString, baseUrl: 'https://passage.viewdns.net/');
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Text('Secure Payment'),
        backgroundColor: const Color(0xFF6366F1),
        foregroundColor: Colors.white,
        leading: IconButton(
          icon: const Icon(Icons.close),
          onPressed: () => Navigator.pop(context, 'cancelled'),
        ),
      ),
      body: Stack(
        children: [
          WebViewWidget(controller: _controller),
          if (_isLoading)
            const Center(
              child: CircularProgressIndicator(color: Color(0xFF6366F1)),
            ),
        ],
      ),
    );
  }
}
