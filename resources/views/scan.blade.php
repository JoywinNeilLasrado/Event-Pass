<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Scan Tickets') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-[#111] overflow-hidden shadow-xl sm:rounded-3xl border border-gray-200 dark:border-white/10 p-6 md:p-10 text-center">
                
                <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">QR Ticket Scanner</h3>
                <p class="text-gray-500 dark:text-gray-400 mb-8 max-w-lg mx-auto">
                    Point your camera at an attendee's digital or printed Passage ticket. The scanner will automatically verify the signature and checking status.
                </p>

                <div class="max-w-md mx-auto relative group">
                    <style>
                        #reader__dashboard_section_csr span { display: none !important; }
                        #reader__scan_region { background: black; }
                    </style>
                    <div class="absolute -inset-1 bg-gradient-to-r from-purple-600 to-indigo-600 rounded-2xl blur opacity-25 group-hover:opacity-40 transition duration-1000 group-hover:duration-200"></div>
                    <div class="relative bg-white dark:bg-black rounded-xl border border-gray-200 dark:border-white/10 overflow-hidden shadow-2xl">
                        <!-- Camera Feed Container -->
                        <div id="reader" width="100%"></div>
                    </div>
                </div>

                <div class="mt-8 text-sm font-medium text-gray-400 dark:text-gray-500 flex items-center justify-center gap-2">
                    <svg class="w-5 h-5 animate-pulse text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                    Waiting for camera permissions...
                </div>

            </div>
        </div>
    </div>

    <!-- Include the HTML5 QR Code library -->
    <script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            
            // Debug check for the user's browser
            if (window.isSecureContext === false) {
                alert("Browser Security Block: You must establish an HTTPS secure connection. Please type https:// before your domain in the URL bar.");
            } else if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
                alert("Browser Security Block: Camera access is disabled by this specific browser. Please open this link directly in standard Google Chrome (not inside WhatsApp/Instagram/Facebook).");
            }

            function onScanSuccess(decodedText, decodedResult) {
                if(decodedText.includes('tickets/verify')) {
                    window.location.href = decodedText;
                } else {
                    alert("Invalid QR Code scanned. This is not a Passage ticket.");
                }
            }

            function onScanFailure(error) {
                // Keep scanning
            }

            let html5QrcodeScanner = new Html5QrcodeScanner(
                "reader",
                { fps: 10, qrbox: {width: 250, height: 250} },
                /* verbose= */ false);
                
            html5QrcodeScanner.render(onScanSuccess, onScanFailure);
        });
    </script>
</x-app-layout>
