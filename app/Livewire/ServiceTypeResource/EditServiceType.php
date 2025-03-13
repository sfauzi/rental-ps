<?php

namespace App\Livewire\ServiceTypeResource;

use Flux\Flux;
use Livewire\Component;
use App\Models\ServiceType;
use Livewire\Attributes\On;

class EditServiceType extends Component
{
    public $name, $price, $is_active, $serviceId;

    #[On("editService")]
    public function editService($id)
    {

        $service = ServiceType::find($id);

        $this->serviceId = $service->id;
        $this->name = $service->name;
        $this->price = $service->price;
        $this->is_active = $service->is_active;


        Flux::modal('edit-service')->show();
    }

    public function update()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'is_active' => 'boolean',
        ]);

        // Update data di database
        ServiceType::where('id', $this->serviceId)->update([
            'name' => $this->name,
            'price' => $this->price,
            'is_active' => $this->is_active,
        ]);

        
        Flux::modal('edit-service')->close();
        $this->dispatch('reloadServices');
    }

    public function render()
    {
        return view('livewire.service-type-resource.edit-service-type');
    }
}
