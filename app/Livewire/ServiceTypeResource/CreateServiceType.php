<?php

namespace App\Livewire\ServiceTypeResource;

use Flux\Flux;
use Livewire\Component;
use App\Models\ServiceType;

class CreateServiceType extends Component
{

    public $name, $price, $is_active = true; // Set default aktif

    // Livewire v3 menggunakan method `rules()` untuk validasi
    public function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'is_active' => 'boolean',
        ];
    }

    public function store()
    {
        $this->validate(); // Validasi sebelum menyimpan

        ServiceType::create([
            'name' => $this->name,
            'price' => $this->price,
            'is_active' => $this->is_active,
        ]);

        $this->resetForm(); // Reset input setelah simpan
        Flux::modal('create-service')->close();

        $this->dispatch('reloadServices');
    }

    public function resetForm()
    {
        $this->reset(['name', 'price', 'is_active']); // Reset form
    }

    public function render()
    {
        return view('livewire.service-type-resource.create-service-type');
    }
}
