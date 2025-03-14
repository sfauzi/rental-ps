<div>
    <div class="max-w-6xl mx-auto p-6 shadow-md rounded-lg">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-lg font-semibold text-gray-700">My Bookings</h2>
        </div>

        <div class="overflow-x-auto mt-[30px]">
            <table class="w-full border border-gray-300 rounded-lg">
                <thead>
                    <tr class="bg-gray-200 text-gray-700">
                        <th class="py-2 px-4 border">Booking Code</th>
                        <th class="py-2 px-4 border">Service</th>
                        <th class="py-2 px-4 border">Date</th>
                        <th class="py-2 px-4 border">Time</th>
                        <th class="py-2 px-4 border">Total Price</th>
                        <th class="py-2 px-4 border">Payment</th>
                        <th class="py-2 px-4 border">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($bookings as $booking)
                        <tr class="hover:bg-gray-100 transition">
                            <td class="py-2 px-4 border">{{ $booking->booking_code }}</td>
                            <td class="py-2 px-4 border">{{ $booking->serviceType->name }}</td>
                            <td class="py-2 px-4 border">{{ $booking->booking_date }}</td>
                            <td class="py-2 px-4 border">{{ $booking->start_time }}</td>
                            <td class="py-2 px-4 border text-green-600 font-semibold">
                                Rp {{ number_format($booking->total_price, 0, ',', '.') }}
                            </td>
                            <td class="py-2 px-4 border">
                                <span
                                    class="px-2 py-1 text-xs font-semibold rounded-full 
                                    @if ($booking->payment_status == 'success') bg-green-500 text-white 
                                    @elseif ($booking->payment_status == 'pending') bg-yellow-500 text-white 
                                    @else bg-red-500 text-white @endif">
                                    {{ ucfirst($booking->payment_status) }}
                                </span>
                            </td>
                            <td class="py-2 px-4 border text-center">
                                @if ($booking->payment_status === 'pending')
                                    <flux:button variant="primary" href="{{ $booking->payment_url }}" size="sm">
                                        Pay Now
                                    </flux:button>
                                @else
                                    <flux:button href="" size="sm">
                                        View
                                    </flux:button>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
