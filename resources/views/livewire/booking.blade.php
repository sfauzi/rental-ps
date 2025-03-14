<div class="max-w-lg mx-auto bg-white shadow-lg rounded-lg p-6">
    <h2 class="text-xl font-semibold text-gray-800 mb-4">Booking Service</h2>

    @if (session()->has('message'))
        <div class="bg-green-100 text-green-800 px-4 py-2 rounded-md mb-4">
            {{ session('message') }}
        </div>
    @endif

    <form wire:submit.prevent="book" class="space-y-4">
        <div>
            <label for="serviceType" class="block text-gray-700 font-medium">Pilih Layanan:</label>
            <select wire:model.live="serviceTypeId"
                class="w-full border rounded-md px-3 py-2 focus:ring focus:ring-blue-300">
                <option value="">-- Pilih --</option>
                @foreach ($serviceTypes as $service)
                    <option value="{{ $service->id }}">
                        {{ $service->name }} (Rp {{ number_format($service->price) }}/jam)
                    </option>
                @endforeach
            </select>
            @error('serviceTypeId')
                <span class="text-red-500 text-sm">{{ $message }}</span>
            @enderror
        </div>

        <div>
            <label for="bookingDate" class="block text-gray-700 font-medium">Tanggal Booking:</label>
            <input type="date" wire:model.live="bookingDate"
                class="w-full border rounded-md px-3 py-2 focus:ring focus:ring-blue-300">
            @error('bookingDate')
                <span class="text-red-500 text-sm">{{ $message }}</span>
            @enderror
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label for="startTime" class="block text-gray-700 font-medium">Jam Mulai:</label>
                <input type="time" wire:model.live="startTime"
                    class="w-full border rounded-md px-3 py-2 focus:ring focus:ring-blue-300">
                @error('startTime')
                    <span class="text-red-500 text-sm">{{ $message }}</span>
                @enderror
            </div>

            <div>
                <label for="endTime" class="block text-gray-700 font-medium">Jam Selesai:</label>
                <input type="time" wire:model.live="endTime"
                    class="w-full border rounded-md px-3 py-2 focus:ring focus:ring-blue-300">
                @error('endTime')
                    <span class="text-red-500 text-sm">{{ $message }}</span>
                @enderror
            </div>
        </div>

        <div class="bg-gray-100 p-4 rounded-md shadow-inner">
            <p class="text-gray-700"><strong>Durasi Booking:</strong> {{ $duration }} Jam</p>
            <p class="text-gray-700"><strong>Base Price per Jam:</strong> Rp {{ number_format($basePrice) }}</p>
            <p class="text-gray-700"><strong>Weekend Surcharge:</strong> Rp {{ number_format($weekendSurcharge) }}</p>
            <p class="text-lg font-semibold text-gray-900"><strong>Total Price:</strong> Rp
                {{ number_format($totalPrice) }}</p>
        </div>

        <button type="submit" wire:click.prevent="book"
            class="w-full bg-gradient-to-r from-blue-500 to-indigo-600 text-white py-2 rounded-lg font-semibold shadow-md hover:opacity-90 transition">
            Book Now
        </button>
    </form>
</div>
