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
    public $bookingDate;
    public $startTime;
    public $endTime;
    public $basePrice = 0;
    public $weekendSurcharge = 0;
    public $totalPrice = 0;
    public $duration = 0;

    protected $rules = [
        'serviceTypeId' => 'required|exists:service_types,id',
        'bookingDate' => 'required|date|after_or_equal:today',
        'startTime' => 'required',
        'endTime' => 'required|after:startTime',
    ];

    public function mount()
    {
        $this->serviceTypes = ServiceType::all();
    }

    public function updated($propertyName)
    {
        if (in_array($propertyName, ['serviceTypeId', 'bookingDate', 'startTime', 'endTime'])) {
            $this->calculatePrice();
        }
    }

    public function calculatePrice()
    {
        if (!$this->serviceTypeId || !$this->startTime || !$this->endTime) return;

        $serviceType = ServiceType::find($this->serviceTypeId);
        $this->basePrice = $serviceType ? $serviceType->price : 0;

        try {
            $start = Carbon::parse($this->startTime);
            $end = Carbon::parse($this->endTime);

            if ($end->lessThanOrEqualTo($start)) {
                $this->duration = 0;
                $this->totalPrice = 0;
                return;
            }

            $this->duration = $start->diffInHours($end);

            if ($this->duration == 0 && $start->diffInMinutes($end) > 0) {
                $this->duration = 1;
            }

            $this->weekendSurcharge = Carbon::parse($this->bookingDate)->isWeekend() ? 50000 : 0;
            $this->totalPrice = ($this->basePrice * $this->duration) + $this->weekendSurcharge;

            // **Simpan harga di session agar tetap konsisten saat redirect**
            session()->put('fixed_total_price', $this->totalPrice);
        } catch (\Exception $e) {
            $this->duration = 0;
            $this->totalPrice = 0;
        }
    }

    public function book()
    {
        $this->validate([
            'serviceTypeId' => 'required',
            'bookingDate' => 'required|date',
            'startTime' => 'required',
            'endTime' => 'required',
        ]);

        // **Ambil harga dari session agar tetap sama dengan yang ditampilkan**
        $totalPrice = session()->get('fixed_total_price', $this->totalPrice);

        // Create booking record
        $booking = ModelsBooking::create([
            'user_id' => auth()->user()->id,
            'service_type_id' => $this->serviceTypeId,
            'booking_date' => $this->bookingDate,
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
                'order_id' => $booking->id,
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


            // **Hapus session harga setelah redirect**
            session()->forget('fixed_total_price');
            
            // Redirect ke halaman pembayaran Midtrans
            return redirect($paymentUrl);
        } catch (\Exception $e) {
            // Hapus booking jika gagal membuat transaksi
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
