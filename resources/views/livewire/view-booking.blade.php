<div>
    <flux:modal name="view-booking" class="md:w-[650px]">
        <div class="space-y-6">
            <!-- Header Section -->
            <div>
                <flux:heading size="lg">Booking Details</flux:heading>
                <flux:subheading>View complete information about this booking.</flux:subheading>
            </div>

            <!-- Booking Information -->
            <div>
                @foreach ($booking as $item)
                    <div class="space-y-6">
                        <!-- Booking ID and Status -->
                        <div class="flex items-center justify-between">
                            <div>
                                <flux:badge
                                    color="{{ $item->payment_status === 'success' ? 'green' : ($item->payment_status === 'pending' ? 'yellow' : 'red') }}">
                                    {{ ucfirst($item->payment_status) }}
                                </flux:badge>
                                <div class="mt-2 text-sm text-gray-600">Booking Code: <span
                                        class="font-mono font-medium">{{ $item->booking_code }}</span></div>
                            </div>
                            <div class="text-sm text-gray-500">
                                Created: {{ $item->created_at->format('d M Y, H:i') }}
                            </div>
                        </div>

                        <!-- Customer & Service Info -->
                        {{-- <flux:card> --}}
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <flux:label>Customer</flux:label>
                                <div class="font-medium">{{ $item->user->name }}</div>
                                <div class="text-sm text-gray-600">{{ $item->user->email }}</div>
                            </div>
                            <div>
                                <flux:label>Service Type</flux:label>
                                <div class="font-medium">{{ $item->serviceType->name }}</div>
                                <div class="text-sm text-gray-600">Rp {{ number_format($item->base_price) }}/jam
                                </div>
                            </div>
                        </div>
                        {{-- </flux:card> --}}

                        <!-- Booking Details -->
                        {{-- <flux:card> --}}
                        <flux:label>Booking Schedule</flux:label>
                        <div class="grid grid-cols-2 gap-4 mt-2">
                            <div>
                                <div class="text-sm text-gray-600">Date</div>
                                <div class="font-medium">
                                    @if ($item->start_date === $item->end_date)
                                        {{ \Carbon\Carbon::parse($item->start_date)->format('d M Y') }}
                                    @else
                                        {{ \Carbon\Carbon::parse($item->start_date)->format('d M Y') }} -
                                        {{ \Carbon\Carbon::parse($item->end_date)->format('d M Y') }}
                                    @endif
                                </div>
                            </div>
                            <div>
                                <div class="text-sm text-gray-600">Time</div>
                                <div class="font-medium">
                                    {{ \Carbon\Carbon::parse($item->start_time)->format('H:i') }} -
                                    {{ \Carbon\Carbon::parse($item->end_time)->format('H:i') }}
                                </div>
                            </div>
                        </div>
                        {{-- </flux:card> --}}

                        <!-- Payment Information -->
                        {{-- <flux:card> --}}
                        <flux:label>Payment Information</flux:label>
                        <div class="space-y-3 mt-2">
                            <div class="flex justify-between items-center">
                                <span class="text-gray-600">Base Price</span>
                                <span>Rp {{ number_format($item->base_price) }}</span>
                            </div>
                            @if ($item->weekend_surcharge > 0)
                                <div class="flex justify-between items-center">
                                    <span class="text-gray-600">Weekend Surcharge</span>
                                    <span>Rp {{ number_format($item->weekend_surcharge) }}</span>
                                </div>
                            @endif
                            <div class="flex justify-between items-center pt-2 border-t">
                                <span class="font-semibold">Total Amount</span>
                                <span class="font-bold">Rp {{ number_format($item->total_price) }}</span>
                            </div>
                        </div>
                        {{-- </flux:card> --}}

                        <!-- Actions Based on Status -->
                        <div class="pt-2">
                            @if ($item->payment_status === 'success')
                                {{-- <div class="flex gap-2">
                                    <flux:button color="white" href="#">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                        </svg>
                                        Download Receipt
                                    </flux:button>
                                    <flux:spacer />
                                    <flux:button color="primary" href="{{ route('my-booking') }}">View All Bookings
                                    </flux:button>
                                </div> --}}
                            @elseif($item->payment_status === 'pending')
                                <div class="flex gap-2">
                                    <flux:button color="white" href="{{ route('contact') }}">Contact Support
                                    </flux:button>
                                    <flux:spacer />
                                    <flux:button color="warning" href="{{ $item->payment_url }}">Complete Payment
                                    </flux:button>
                                </div>
                            @else
                                <div class="flex gap-2">
                                    <flux:button color="white" href="{{ route('home') }}">Back to Home</flux:button>
                                    <flux:spacer />
                                    <flux:button color="danger" href="{{ route('contact') }}">Contact Support
                                    </flux:button>
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Footer Actions -->
            {{-- <div class="flex pt-2 border-t">
                <flux:spacer />
                <flux:button color="white" x-on:click="$dispatch('close-modal', 'view-booking')">Close</flux:button>
            </div> --}}
        </div>
    </flux:modal>
</div>
