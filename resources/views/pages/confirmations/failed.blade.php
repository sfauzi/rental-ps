<x-layouts.master title="Payment Failed">
    <div class="min-h-screen bg-gradient-to-br from-red-50 to-pink-100 py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-xl mx-auto">
            <!-- Failed Payment Card -->
            <div class="bg-white rounded-xl shadow-xl overflow-hidden">
                <!-- Failed Header -->
                <div class="bg-red-500 px-6 py-4 relative">
                    <div class="absolute top-0 right-0 -mt-4 -mr-4 bg-white rounded-full p-2 shadow-lg">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-red-500" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <h1 class="text-2xl font-bold text-white">Payment Failed</h1>
                    <p class="text-red-100">There was a problem processing your payment</p>
                </div>

                <!-- Booking Summary -->
                <div class="p-6">
                    <div class="flex items-center mb-6">
                        <div class="flex-shrink-0 bg-red-100 rounded-full p-3">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-red-600" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                        </div>
                        <div class="ml-4">
                            <h2 class="text-lg font-semibold text-gray-800">Transaction Failed</h2>
                            <p class="text-sm text-gray-600">Booking Code: <span
                                    class="font-mono font-medium">{{ $booking->booking_code }}</span></p>
                        </div>
                    </div>

                    <!-- Error Message -->
                    <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-red-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                                    fill="currentColor">
                                    <path fill-rule="evenodd"
                                        d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                        clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm text-red-700">
                                    Sorry <strong>{{ $booking->user->name }}</strong>, your payment could not be
                                    processed. This may be due to insufficient funds, an expired card, or a temporary
                                    issue with the payment gateway.
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- User & Service Info -->
                    <div class="bg-gray-50 rounded-lg p-4 mb-4">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <p class="text-xs text-gray-500 uppercase">Customer</p>
                                <p class="font-medium">{{ $booking->user->name }}</p>
                                <p class="text-sm text-gray-600">{{ $booking->user->email }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 uppercase">Service Type</p>
                                <p class="font-medium">{{ $booking->serviceType->name }}</p>
                                <p class="text-sm text-gray-600">Rp {{ number_format($booking->base_price) }}/jam</p>
                            </div>
                        </div>
                    </div>

                    <!-- Booking Details -->
                    <div class="space-y-3 mb-6">
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">Booking Date</span>
                            <span class="font-medium">
                                @if ($booking->start_date === $booking->end_date)
                                    {{ \Carbon\Carbon::parse($booking->start_date)->format('d M Y') }}
                                @else
                                    {{ \Carbon\Carbon::parse($booking->start_date)->format('d M Y') }} -
                                    {{ \Carbon\Carbon::parse($booking->end_date)->format('d M Y') }}
                                @endif
                            </span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">Time</span>
                            <span class="font-medium">{{ \Carbon\Carbon::parse($booking->start_time)->format('H:i') }} -
                                {{ \Carbon\Carbon::parse($booking->end_time)->format('H:i') }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">Duration</span>
                            <span class="font-medium">
                                @php
                                    $startDate = \Carbon\Carbon::parse($booking->start_date);
                                    $endDate = \Carbon\Carbon::parse($booking->end_date);
                                    $totalDays = $startDate->diffInDays($endDate) + 1;

                                    $start = \Carbon\Carbon::parse($booking->start_time);
                                    $end = \Carbon\Carbon::parse($booking->end_time);
                                    $hoursPerDay = $end->diffInHours($start);
                                    if ($hoursPerDay == 0 && $end->diffInMinutes($start) > 0) {
                                        $hoursPerDay = 1;
                                    }
                                @endphp
                                {{ $totalDays }} day(s), {{ $hoursPerDay }} hour(s) per day
                            </span>
                        </div>
                    </div>

                    <!-- Payment Details -->
                    <div class="border-t border-dashed pt-4 mb-6">
                        <div class="flex justify-between items-center mb-2">
                            <span class="text-gray-600">Base Price</span>
                            <span>Rp {{ number_format($booking->base_price * ($hoursPerDay * $totalDays)) }}</span>
                        </div>
                        @if ($booking->weekend_surcharge > 0)
                            <div class="flex justify-between items-center mb-2">
                                <span class="text-gray-600">Weekend Surcharge</span>
                                <span class="text-orange-600">Rp
                                    {{ number_format($booking->weekend_surcharge) }}</span>
                            </div>
                        @endif
                        <div class="flex justify-between items-center text-lg font-bold mt-3">
                            <span>Total Amount</span>
                            <span class="text-gray-800">Rp {{ number_format($booking->total_price) }}</span>
                        </div>

                        <!-- Payment Status Badge -->
                        <div class="mt-3 flex justify-end">
                            <span
                                class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800">
                                <svg class="-ml-1 mr-1.5 h-2 w-2 text-red-400" fill="currentColor" viewBox="0 0 8 8">
                                    <circle cx="4" cy="4" r="3" />
                                </svg>
                                Failed
                            </span>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="space-y-3">
                        <a href="{{ $booking->payment_url }}"
                            class="block w-full bg-red-500 hover:bg-red-600 text-white font-bold py-3 px-4 rounded-lg text-center transition duration-200">
                            Try Payment Again
                        </a>
                        <div class="grid grid-cols-2 gap-3">
                            <a href="https://wa.me/6288229877220?text=Hello,%20I%20need%20to%20cancel%20my%20booking."
                                class="block w-full bg-gray-200 hover:bg-gray-300 text-gray-800 font-medium py-3 px-4 rounded-lg text-center transition duration-200">
                                Contact Support
                            </a>
                            <a href="{{ route('home') }}"
                                class="block w-full bg-gray-200 hover:bg-gray-300 text-gray-800 font-medium py-3 px-4 rounded-lg text-center transition duration-200">
                                Back to Homepage
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Troubleshooting Tips -->
            <div class="bg-white p-5 rounded-lg shadow-md mt-6">
                <h3 class="font-medium text-gray-800 mb-2">Common Payment Issues:</h3>
                <ul class="text-sm text-gray-600 space-y-2 list-disc list-inside">
                    <li>Ensure your card has sufficient funds</li>
                    <li>Check if your card isn't expired or blocked for online transactions</li>
                    <li>Verify your billing address matches what's on file with your bank</li>
                    <li>Try a different payment method if available</li>
                    <li>If problems persist, contact your bank or our support team</li>
                </ul>
            </div>
        </div>
    </div>
</x-layouts.master>
