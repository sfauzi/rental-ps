<?php

namespace App\Livewire;

use App\Models\Booking;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class MyBooking extends Component
{

    public function view($id)
    {
        $this->dispatch("viewBooking", $id);
    }

    public function render()
    {
        $bookings = Booking::where('user_id', Auth::id())->orderBy('created_at', 'desc')->get();

        return view('livewire.my-booking', [
            'bookings' => $bookings,
        ])->layout('components.layouts.app');
    }
}
