<x-layouts.master title="Payment Failed">
    <div class="flex flex-col items-center justify-center min-h-screen bg-red-100">
        <div class="bg-white p-8 rounded-lg shadow-md text-center">
            <h1 class="text-2xl font-bold text-red-600">Payment Failed</h1>
            <p class="text-gray-600 mt-2">Sorry <strong>{{ $booking->user->name }}</strong>, your transaction of
                <strong>${{ number_format($booking->total_price, 0, ' ') }}</strong> could not be processed. Please try
                again.
            </p>
            @if ($booking->payment_status === 'success')
                <!-- Button for successful payment -->
                <button class="w-full bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-4 rounded"
                    onclick="window.location.href='';">
                    Lihat Pesananku
                </button>
            @elseif ($booking->payment_status === 'pending')
                <!-- Button for pending payment -->
                <button class="w-full bg-yellow-500 hover:bg-yellow-600 text-white font-bold py-2 px-4 rounded"
                    onclick="window.location.href='{{ $booking->payment_url }}';">
                    Pay Now
                </button>
            @elseif ($booking->payment_status === 'cancelled')
                <!-- Button for cancelled payment -->
                <button class="w-full bg-red-500 hover:bg-red-600 text-white font-bold py-2 px-4 rounded"
                    onclick="window.location.href='/';">
                    Contact Us
                </button>
            @endif

        </div>
    </div>

</x-layouts.master>
