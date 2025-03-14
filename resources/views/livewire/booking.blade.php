<div class="max-w-6xl mx-auto p-4">
    <div class="bg-white shadow-lg rounded-lg overflow-hidden " x-data="dateRangePicker()">
        <h2 class="text-xl font-semibold text-gray-800 bg-gray-50 p-6 border-b">Booking Service</h2>

        @if (session()->has('message'))
            <div class="bg-green-100 text-green-800 px-6 py-3 mx-6 mt-4 rounded-md">
                {{ session('message') }}
            </div>
        @endif

        @if (session()->has('error'))
            <div class="bg-red-100 text-red-800 px-6 py-3 mx-6 mt-4 rounded-md">
                {{ session('error') }}
            </div>
        @endif

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 p-6">
            <!-- Left Column: Booking Form (Takes 2/3 of the space) -->
            <div class="md:col-span-2 space-y-6">
                <form wire:submit.prevent="book" class="space-y-4">
                    <div>
                        <label for="serviceType" class="block text-gray-700 font-medium">Pilih Layanan:</label>
                        <select wire:model.live="serviceTypeId"
                            class="w-full border rounded-md px-3 py-2 focus:ring focus:ring-blue-300">
                            <option value="">-- Pilih --</option>
                            @foreach ($serviceTypes as $service)
                                <option value="{{ $service->id }}">
                                    {{ $service->name }} (Rp {{ number_format($service->price) }}/session)
                                </option>
                            @endforeach
                        </select>
                        @error('serviceTypeId')
                            <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- START: DATE RANGE PICKER -->
                    <div class="grid grid-cols-2 gap-y-6 gap-x-4 relative" @keydown.escape="closeDatepicker()"
                        @click.outside="closeDatepicker()">
                        <!-- Start Date -->
                        <div class="flex flex-col col-span-1 gap-3">
                            <label class="block text-gray-700 font-medium">
                                Tanggal Mulai:
                            </label>
                            <input readonly type="text"
                                class="w-full border rounded-md px-3 py-2 focus:ring focus:ring-blue-300"
                                placeholder="Pilih Tanggal" @click="endToShow = 'from'; init(); showDatepicker = true"
                                x-model="outputDateFromValue" x-on:change="updateLivewireDate('from')">
                            <input type="hidden" wire:model.live="startDate" x-model="hiddenDateFromValue" />
                            @error('startDate')
                                <span class="text-red-500 text-sm">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- End Date -->
                        <div class="flex flex-col col-span-1 gap-3">
                            <label class="block text-gray-700 font-medium">
                                Tanggal Selesai:
                            </label>
                            <input readonly type="text"
                                class="w-full border rounded-md px-3 py-2 focus:ring focus:ring-blue-300"
                                placeholder="Pilih Tanggal" @click="endToShow = 'to'; init(); showDatepicker = true"
                                x-model="outputDateToValue" x-on:change="updateLivewireDate('to')">
                            <input type="hidden" wire:model.live="endDate" x-model="hiddenDateToValue" />
                            @error('endDate')
                                <span class="text-red-500 text-sm">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- DATE PICKER DROPDOWN - Fixed positioning -->
                        <div x-show="showDatepicker" x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                            x-transition:leave="transition ease-in duration-150"
                            x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
                            class="fixed inset-0 z-[9999] overflow-y-auto" style="display: none;">

                            <!-- Backdrop overlay -->
                            <div class="fixed inset-0 bg-gray-500 bg-opacity-50 transition-opacity"
                                @click="closeDatepicker()"></div>

                            <!-- Modal container -->
                            <div class="flex items-center justify-center min-h-screen p-4 text-center">
                                <div
                                    class="relative bg-white rounded-lg shadow-xl max-w-md w-full p-6 mx-auto transform transition-all">
                                    <!-- Close button -->
                                    <button @click="closeDatepicker()" type="button"
                                        class="absolute top-3 right-3 text-gray-400 hover:text-gray-500">
                                        <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </button>

                                    <!-- Date picker title -->
                                    <div class="mb-4 text-left">
                                        <h3 class="text-lg font-medium text-gray-900">
                                            <span
                                                x-text="endToShow === 'from' ? 'Pilih Tanggal Mulai' : 'Pilih Tanggal Selesai'"></span>
                                        </h3>
                                    </div>

                                    <!-- Date picker content -->
                                    <div class="flex flex-col items-center">
                                        <div class="w-full mb-5">
                                            <div class="flex items-center justify-center gap-1">
                                                <button type="button"
                                                    class="inline-flex p-1 mr-2 transition duration-100 ease-in-out rounded-full cursor-pointer hover:bg-gray-200"
                                                    @click="if (month == 0) {year--; month=11;} else {month--;} getNoOfDays()">
                                                    <svg class="inline-flex w-6 h-6 text-gray-500" fill="none"
                                                        viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2" d="M15 19l-7-7 7-7" />
                                                    </svg>
                                                </button>
                                                <span x-text="MONTH_NAMES[month]"
                                                    class="text-base font-semibold text-gray-800"></span>
                                                <span x-text="year"
                                                    class="text-base font-semibold text-gray-800"></span>
                                                <button type="button"
                                                    class="inline-flex p-1 ml-2 transition duration-100 ease-in-out rounded-full cursor-pointer hover:bg-gray-200"
                                                    @click="if (month == 11) {year++; month=0;} else {month++;}; getNoOfDays()">
                                                    <svg class="inline-flex w-6 h-6 text-gray-500" fill="none"
                                                        viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2" d="M9 5l7 7-7 7" />
                                                    </svg>
                                                </button>
                                            </div>
                                        </div>

                                        <div class="flex flex-wrap w-full mb-3 -mx-1">
                                            <template x-for="(day, index) in DAYS" :key="index">
                                                <div style="width: 14.26%" class="px-1">
                                                    <div x-text="day"
                                                        class="text-sm font-medium text-center text-gray-800">
                                                    </div>
                                                </div>
                                            </template>
                                        </div>

                                        <div class="flex flex-wrap -mx-1">
                                            <template x-for="blankday in blankdays">
                                                <div style="width: 14.28%"
                                                    class="p-1 text-sm text-center border border-transparent">
                                                </div>
                                            </template>
                                            <template x-for="(date, dateIndex) in no_of_days" :key="dateIndex">
                                                <div style="width: 14.28%">
                                                    <div @click="!isPastDate(date) && getDateValue(date, false)"
                                                        @mouseover="!isPastDate(date) && getDateValue(date, true)"
                                                        x-text="date"
                                                        class="p-1 text-sm leading-loose text-center transition duration-100 ease-in-out"
                                                        :class="{
                                                            'font-bold': isToday(date) == true,
                                                            'bg-blue-500 text-white rounded-l-full': isDateFrom(date) ==
                                                                true,
                                                            'bg-blue-500 text-white rounded-r-full': isDateTo(date) ==
                                                                true,
                                                            'bg-blue-100': isInRange(date) == true,
                                                            'text-gray-400 bg-gray-100 cursor-not-allowed': isPastDate(
                                                                date) == true,
                                                            'cursor-pointer hover:bg-gray-200': isPastDate(date) ==
                                                                false
                                                        }">
                                                    </div>
                                                </div>
                                            </template>
                                        </div>

                                        <!-- Action buttons -->
                                        <div class="mt-5 flex justify-end space-x-3 border-t pt-4 w-full">
                                            <button type="button" @click="closeDatepicker()"
                                                class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-md hover:bg-gray-200">
                                                Batal
                                            </button>
                                            <button type="button" @click="closeDatepicker()"
                                                class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-md hover:bg-blue-700">
                                                Selesai
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- END DATE PICKER DROPDOWN -->
                    </div>
                    <!-- END: DATE RANGE PICKER -->

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="startTime" class="block text-gray-700 font-medium">Jam Mulai:</label>
                            <input type="time" id="startTime" wire:model.live="startTime"
                                class="w-full border rounded-md px-3 py-2 focus:ring focus:ring-blue-300"
                                min="08:00" max="20:00">
                            @error('startTime')
                                <span class="text-red-500 text-sm">{{ $message }}</span>
                            @enderror
                            <span class="text-xs text-gray-500 mt-1">Jam operasional: 08:00 - 20:00</span>
                        </div>

                        <div>
                            <label for="endTime" class="block text-gray-700 font-medium">Jam Selesai:</label>
                            <input type="time" id="endTime" wire:model.live="endTime"
                                class="w-full border rounded-md px-3 py-2 focus:ring focus:ring-blue-300"
                                min="09:00" max="21:00">
                            @error('endTime')
                                <span class="text-red-500 text-sm">{{ $message }}</span>
                            @enderror
                            <p class="text-xs text-gray-500 mt-1">
                                Durasi minimal: 1 jam
                            </p>
                        </div>
                    </div>

                    <!-- Real-time validation message for same-day bookings -->
                    @if ($startDate && $endDate && $startDate === $endDate && $startTime && $endTime)
                        @php
                            $start = \Carbon\Carbon::parse($startTime);
                            $end = \Carbon\Carbon::parse($endTime);
                            $valid = $end->gt($start);
                        @endphp

                        @if (!$valid)
                            <div class="mt-2 text-red-500 text-sm">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 inline-block mr-1"
                                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                                Untuk booking di tanggal yang sama, jam selesai harus setelah jam mulai.
                            </div>
                        @endif
                    @endif



                    <button type="submit" wire:click.prevent="book" wire:loading.attr="disabled" wire:target="book"
                        class="w-full bg-gradient-to-r from-blue-500 to-indigo-600 text-white py-3 rounded-lg font-semibold shadow-md hover:opacity-90 transition disabled:opacity-70 disabled:cursor-not-allowed">
                        <span wire:loading.remove wire:target="book">
                            Book Now
                        </span>
                        <span wire:loading wire:target="book" class="flex items-center justify-center">
                            <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg"
                                fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10"
                                    stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor"
                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                </path>
                            </svg>
                            Processing...
                        </span>
                    </button>
                </form>
            </div>

            <!-- Right Column: Booking Summary (Takes 1/3 of the space) -->
            <div class="md:col-span-1">
                <div class="bg-gray-50 p-6 rounded-lg shadow-inner h-full">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4 border-b pb-2">Ringkasan Booking</h3>

                    @if ($serviceTypeId && $startDate && $endDate && $startTime && $endTime)
                        <div class="space-y-3">

                            <div class="flex justify-between">
                                <span class="text-gray-600">Tanggal:</span>
                                <span class="font-medium">
                                    @if ($startDate === $endDate)
                                        {{ \Carbon\Carbon::parse($startDate)->format('d M Y') }}
                                    @else
                                        {{ \Carbon\Carbon::parse($startDate)->format('d M Y') }} -
                                        {{ \Carbon\Carbon::parse($endDate)->format('d M Y') }}
                                    @endif
                                </span>
                            </div>

                            <div class="flex justify-between">
                                <span class="text-gray-600">Waktu:</span>
                                <span class="font-medium">{{ \Carbon\Carbon::parse($startTime)->format('H:i') }} -
                                    {{ \Carbon\Carbon::parse($endTime)->format('H:i') }}</span>
                            </div>

                            <div class="flex justify-between">
                                <span class="text-gray-600">Total Hari:</span>
                                <span class="font-medium">{{ $totalDays }} Hari</span>
                            </div>

                            <div class="flex justify-between">
                                <span class="text-gray-600">Total Durasi:</span>
                                <span class="font-medium">{{ $duration }} Jam</span>
                            </div>

                            <div class="flex justify-between">
                                <span class="text-gray-600">Harga Dasar:</span>
                                <span class="font-medium">Rp {{ number_format($basePrice) }}/session</span>
                            </div>

                            @if ($weekendSurcharge > 0)
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Weekend Surcharge:</span>
                                    <span class="font-medium text-orange-600">Rp
                                        {{ number_format($weekendSurcharge) }}</span>
                                </div>
                            @endif

                            <div class="border-t pt-3 mt-3">
                                <div class="flex justify-between">
                                    <span class="text-gray-800 font-semibold">Total:</span>
                                    <span class="text-lg font-bold text-blue-600">Rp
                                        {{ number_format($totalPrice) }}</span>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="text-center py-8 text-gray-500">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto mb-3 text-gray-400"
                                fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <p>Silahkan isi detail booking untuk melihat ringkasan</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function dateRangePicker() {
        return {
            showDatepicker: false,
            dateFromYmd: '',
            dateToYmd: '',
            outputDateFromValue: '',
            outputDateToValue: '',
            hiddenDateFromValue: '',
            hiddenDateToValue: '',
            endToShow: '',
            month: '',
            year: '',
            no_of_days: [],
            blankdays: [],

            MONTH_NAMES: [
                'January', 'February', 'March', 'April', 'May', 'June',
                'July', 'August', 'September', 'October', 'November', 'December'
            ],
            DAYS: ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'],

            init() {
                const today = new Date();
                this.month = today.getMonth();
                this.year = today.getFullYear();
                this.getNoOfDays();
            },

            closeDatepicker() {
                this.showDatepicker = false;
            },

            isToday(date) {
                const today = new Date();
                const d = new Date(this.year, this.month, date);
                return today.toDateString() === d.toDateString();
            },

            isPastDate(date) {
                const today = new Date();
                today.setHours(0, 0, 0, 0); // Set time to beginning of day for accurate comparison
                const d = new Date(this.year, this.month, date);
                return d < today;
            },

            isDateFrom(date) {
                const d = new Date(this.year, this.month, date);
                return d.toDateString() === this.dateFromYmd;
            },

            isDateTo(date) {
                const d = new Date(this.year, this.month, date);
                return d.toDateString() === this.dateToYmd;
            },

            isInRange(date) {
                if (this.dateFromYmd && this.dateToYmd) {
                    const startDate = new Date(this.dateFromYmd);
                    const endDate = new Date(this.dateToYmd);
                    const d = new Date(this.year, this.month, date);

                    // Allow same-day selection to be highlighted
                    if (startDate.toDateString() === endDate.toDateString()) {
                        return false; // No in-between days to highlight for same day
                    }

                    return d > startDate && d < endDate;
                }
                return false;
            },

            getDateValue(date, isMouseOver) {
                const selectedDate = new Date(this.year, this.month, date);

                // Don't allow selection of past dates
                if (this.isPastDate(date)) {
                    return;
                }

                if (this.endToShow === 'from' && (!isMouseOver || !this.dateFromYmd)) {
                    this.dateFromYmd = selectedDate.toDateString();
                    this.outputDateFromValue = this.formatDateOutput(selectedDate);
                    this.hiddenDateFromValue = this.formatDateForLivewire(selectedDate);

                    // If we have an end date that's before the new start date, clear it
                    if (this.dateToYmd && selectedDate > new Date(this.dateToYmd)) {
                        this.dateToYmd = '';
                        this.outputDateToValue = '';
                        this.hiddenDateToValue = '';
                    }

                    if (!isMouseOver) {
                        this.updateLivewireDate('from');
                        this.endToShow = 'to';
                    }
                } else if (this.endToShow === 'to' && (!isMouseOver || !this.dateToYmd)) {
                    if (!this.dateFromYmd) {
                        this.endToShow = 'from';
                        return this.getDateValue(date, isMouseOver);
                    }

                    // Allow same day selection by using >=
                    if (selectedDate >= new Date(this.dateFromYmd)) {
                        this.dateToYmd = selectedDate.toDateString();
                        this.outputDateToValue = this.formatDateOutput(selectedDate);
                        this.hiddenDateToValue = this.formatDateForLivewire(selectedDate);

                        if (!isMouseOver) {
                            this.updateLivewireDate('to');
                            this.closeDatepicker();
                        }
                    }
                }
            },

            getNoOfDays() {
                let daysInMonth = new Date(this.year, this.month + 1, 0).getDate();
                let firstDay = new Date(this.year, this.month, 1);
                let paddingDays = firstDay.getDay();

                this.blankdays = Array(paddingDays).fill(null);
                this.no_of_days = Array.from({
                    length: daysInMonth
                }, (_, i) => i + 1);
            },

            formatDateOutput(date) {
                return date.toLocaleDateString('id-ID', {
                    day: 'numeric',
                    month: 'long',
                    year: 'numeric'
                });
            },

            formatDateForLivewire(date) {
                const year = date.getFullYear();
                const month = String(date.getMonth() + 1).padStart(2, '0');
                const day = String(date.getDate()).padStart(2, '0');
                return `${year}-${month}-${day}`;
            },

            updateLivewireDate(type) {
                // This function dispatches an event to Livewire to update the date
                if (type === 'from' && this.hiddenDateFromValue) {
                    this.$wire.set('startDate', this.hiddenDateFromValue);
                } else if (type === 'to' && this.hiddenDateToValue) {
                    this.$wire.set('endDate', this.hiddenDateToValue);
                }
            }
        };
    }
</script>
