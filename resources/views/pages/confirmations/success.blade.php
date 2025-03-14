<x-layouts.master title="Payment Successful">
    <div class="min-h-screen bg-gradient-to-br from-green-50 to-emerald-100 py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-xl mx-auto">
            <!-- Success Payment Card -->
            <div class="bg-white rounded-xl shadow-xl overflow-hidden">
                <!-- Success Header -->
                <div class="bg-green-500 px-6 py-4 relative">
                    <div class="absolute top-0 right-0 -mt-4 -mr-4 bg-white rounded-full p-2 shadow-lg">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-green-500" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <h1 class="text-2xl font-bold text-white">Payment Successful!</h1>
                    <p class="text-green-100">Your booking has been confirmed</p>
                </div>

                <!-- Booking Summary -->
                <div class="p-6">
                    <div class="flex items-center mb-6">
                        <div class="flex-shrink-0 bg-green-100 rounded-full p-3">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-green-600" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                            </svg>
                        </div>
                        <div class="ml-4">
                            <h2 class="text-lg font-semibold text-gray-800">Booking Confirmed</h2>
                            <p class="text-sm text-gray-600">Booking Code: <span
                                    class="font-mono font-medium">{{ $booking->booking_code }}</span></p>
                        </div>
                    </div>

                    <!-- Success Message -->
                    <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-green-400" xmlns="http://www.w3.org/2000/svg"
                                    viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd"
                                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                        clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm text-green-700">
                                    Thank you for your booking, <strong>{{ $booking->user->name }}</strong>. We've
                                    received your payment and your booking is now confirmed!
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
                            <span class="text-green-600">Rp {{ number_format($booking->total_price) }}</span>
                        </div>

                        <!-- Payment Status Badge -->
                        <div class="mt-3 flex justify-end">
                            <span
                                class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                <svg class="-ml-1 mr-1.5 h-2 w-2 text-green-400" fill="currentColor" viewBox="0 0 8 8">
                                    <circle cx="4" cy="4" r="3" />
                                </svg>
                                Paid
                            </span>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="space-y-3">
                        <a href="{{ route('my-booking') }}"
                            class="block w-full bg-green-500 hover:bg-green-600 text-white font-bold py-3 px-4 rounded-lg text-center transition duration-200">
                            View My Bookings
                        </a>
                        <a href="{{ route('home') }}"
                            class="block w-full bg-gray-200 hover:bg-gray-300 text-gray-800 font-medium py-3 px-4 rounded-lg text-center transition duration-200">
                            Back to Homepage
                        </a>
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-layouts.master>
