<?php

namespace App\Livewire;

use Carbon\Carbon;
use Midtrans\Snap;
use Livewire\Component;
use App\Models\ServiceType;
use Illuminate\Support\Facades\Auth;
use App\Models\Booking as ModelsBooking;

class Booking extends Component
{
    public $serviceTypes;
    public $serviceTypeId;
    public $startDate;
    public $endDate;
    public $startTime;
    public $endTime;
    public $basePrice = 0;
    public $weekendSurcharge = 0;
    public $totalPrice = 0;
    public $duration = 0;
    public $totalDays = 0;

    protected $rules = [
        'serviceTypeId' => 'required|exists:service_types,id',
        'startDate' => 'required|date|after_or_equal:today',
        'endDate' => 'required|date|after_or_equal:startDate', // Allow same date
        'startTime' => 'required',
        'endTime' => 'required',
    ];

    // Custom validation for ensuring endTime is after startTime when dates are the same
    protected function getValidationAttributes()
    {
        return [
            'startTime' => 'jam mulai',
            'endTime' => 'jam selesai',
        ];
    }

    public function mount()
    {
        $this->serviceTypes = ServiceType::all();

        $this->calculatePrice();
    }

    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);

        // Add special validation for time when dates are the same
        if (in_array($propertyName, ['startTime', 'endTime']) && $this->startDate && $this->endDate && $this->startDate === $this->endDate) {
            $this->validateTimeOnSameDay();
        }

        if (in_array($propertyName, ['serviceTypeId', 'startDate', 'endDate', 'startTime', 'endTime'])) {
            $this->calculatePrice();
        }
    }

    protected function validateTimeOnSameDay()
    {
        if (!$this->startTime || !$this->endTime) return;

        $start = Carbon::parse($this->startTime);
        $end = Carbon::parse($this->endTime);

        if ($this->startDate === $this->endDate && $end->lessThanOrEqualTo($start)) {
            $this->addError('endTime', 'Jam selesai harus setelah jam mulai untuk pemesanan di hari yang sama.');
        }
    }

    public function calculatePrice()
    {
        if (!$this->serviceTypeId || !$this->startTime || !$this->endTime || !$this->startDate || !$this->endDate) return;

        $serviceType = ServiceType::find($this->serviceTypeId);
        $this->basePrice = $serviceType ? $serviceType->price : 0;

        try {
            // Calculate hours per day - improved time calculation
            $start = Carbon::parse($this->startTime);
            $end = Carbon::parse($this->endTime);

            if ($end->lessThanOrEqualTo($start) && $this->startDate === $this->endDate) {
                $this->duration = 0;
                $this->totalPrice = 0;
                return;
            }

            // Calculate exact hours difference to fix time boundary issues
            $startMinutes = ($start->hour * 60) + $start->minute;
            $endMinutes = ($end->hour * 60) + $end->minute;
            $diffMinutes = $endMinutes - $startMinutes;

            // Convert minutes to hours, rounding up to the nearest hour if there are partial hours
            $hoursPerDay = ceil($diffMinutes / 60);

            // Ensure we have at least 1 hour if there's any time difference
            if ($hoursPerDay == 0 && $diffMinutes > 0) {
                $hoursPerDay = 1;
            }

            // Calculate total days
            $startDateObj = Carbon::parse($this->startDate);
            $endDateObj = Carbon::parse($this->endDate);
            $this->totalDays = $startDateObj->diffInDays($endDateObj) + 1; // Including both start and end days

            // Calculate total duration in hours
            $this->duration = $hoursPerDay * $this->totalDays;

            // Calculate weekend surcharge
            $this->weekendSurcharge = 0;
            $currentDate = $startDateObj->copy();

            // Loop through each day in the date range to check for weekends
            for ($i = 0; $i < $this->totalDays; $i++) {
                if ($currentDate->isWeekend()) {
                    $this->weekendSurcharge += 50000; // Add surcharge for each weekend day
                }
                $currentDate->addDay();
            }

            // Calculate total price
            $this->totalPrice = ($this->basePrice * $this->duration) + $this->weekendSurcharge;

            // Store price in session for consistency
            session()->put('fixed_total_price', $this->totalPrice);
        } catch (\Exception $e) {
            $this->duration = 0;
            $this->totalDays = 0;
            $this->totalPrice = 0;
        }
    }

    public function book()
    {
        $this->validate();

        // Additional validation for same day bookings
        if ($this->startDate === $this->endDate) {
            $this->validateTimeOnSameDay();
            if ($this->getErrorBag()->has('endTime')) {
                return;
            }
        }

        // Get price from session to maintain consistency
        $totalPrice = session()->get('fixed_total_price', $this->totalPrice);

        // Generate booking_code with the format DHO + datetime + 3-digit random number
        $dateTime = now()->format('YmdHis'); // Format as YYYYMMDDHHMMSS
        $randomNumber = rand(100, 999); // Generate a random 3-digit number
        $bookingCode = 'DHO' . $dateTime . $randomNumber;

        // Create booking record with new date fields
        $booking = ModelsBooking::create([
            'user_id' => auth()->user()->id,
            'service_type_id' => $this->serviceTypeId,
            'booking_code' => $bookingCode,
            'start_date' => $this->startDate,
            'end_date' => $this->endDate,
            'start_time' => $this->startTime,
            'end_time' => $this->endTime,
            'base_price' => $this->basePrice,
            'weekend_surcharge' => $this->weekendSurcharge,
            'total_price' => $totalPrice,
            'payment_status' => 'pending',
            'payment_url' => '',
        ]);

        // Configure Midtrans
        \Midtrans\Config::$serverKey = config('services.midtrans.serverKey');
        \Midtrans\Config::$isProduction = config('services.midtrans.isProduction');
        \Midtrans\Config::$isSanitized = config('services.midtrans.isSanitized');
        \Midtrans\Config::$is3ds = config('services.midtrans.is3ds');

        // Midtrans transaction parameters
        $midtransParams = [
            'transaction_details' => [
                'order_id' => $booking->booking_code,
                'gross_amount' => (int) $this->totalPrice,
            ],
            'customer_details' => [
                'email' => auth()->user()->email,
            ],
            'enabled_payments' => ['gopay', 'bank_transfer'],
            'vtweb' => []
        ];

        try {
            // Get Snap Payment URL
            $paymentUrl = \Midtrans\Snap::createTransaction($midtransParams)->redirect_url;

            // Update booking with payment URL
            $booking->update(['payment_url' => $paymentUrl]);

            // Clear session price after redirect
            session()->forget('fixed_total_price');

            // Redirect to Midtrans payment page
            return redirect($paymentUrl);
        } catch (\Exception $e) {
            // Delete booking if transaction creation fails
            $booking->delete();
            session()->flash('error', 'Terjadi kesalahan saat memproses pembayaran.');
            return null;
        }
    }

    public function render()
    {
        return view('livewire.booking')->layout('components.layouts.master');
    }
}
