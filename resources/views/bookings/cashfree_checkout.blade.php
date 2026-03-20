<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Redirecting to Payment...</title>
    <script src="https://sdk.cashfree.com/js/v3/cashfree.js"></script>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 flex items-center justify-center min-h-screen">
    <div class="text-center">
        <div class="inline-block animate-spin rounded-full border-4 border-solid border-indigo-600 border-r-transparent align-[-0.125em] h-12 w-12 mb-4"></div>
        <h2 class="text-xl font-semibold text-gray-800">Redirecting to Secure Checkout...</h2>
        <p class="text-gray-500 mt-2">Please do not refresh or close this page.</p>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const cashfree = Cashfree({
                mode: "{{ $env === 'production' ? 'production' : 'sandbox' }}"
            });
            cashfree.checkout({
                paymentSessionId: "{{ $paymentSessionId }}",
                redirectTarget: "_self"
            });
        });
    </script>
</body>
</html>
