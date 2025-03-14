<?php

namespace App\Livewire;

use Flux\Flux;
use App\Models\Booking;
use Livewire\Component;
use Livewire\Attributes\On;

class ViewBooking extends Component
{
    public $booking = [];
    #[On("viewBooking")]
    public function viewTask($id)
    {

        $this->booking = Booking::where('booking_code', $id)->latest()->get();

        Flux::modal('view-booking')->show();
    }
    public function render()
    {
        return view('livewire.view-booking');
    }
}
