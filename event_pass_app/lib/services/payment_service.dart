import 'package:flutter/material.dart';
import '../screens/payment_webview_screen.dart';

class PaymentService {
  void Function(String)? onPaymentSuccess;
  void Function(String)? onPaymentFailure;

  PaymentService();

  Future<void> startPayment({
    required BuildContext context,
    required String sessionId,
    required String orderId,
    required String env,
  }) async {
    try {
      final result = await Navigator.push<String>(
        context,
        MaterialPageRoute(
          builder: (context) => PaymentWebViewScreen(
            sessionId: sessionId,
            orderId: orderId,
            env: env,
          ),
        ),
      );

      if (result == 'success') {
        if (onPaymentSuccess != null) {
          onPaymentSuccess!(orderId);
        }
      } else {
        if (onPaymentFailure != null) {
          onPaymentFailure!(result ?? 'Payment Cancelled');
        }
      }
    } catch (e) {
      if (onPaymentFailure != null) {
        onPaymentFailure!(e.toString());
      }
    }
  }
}
