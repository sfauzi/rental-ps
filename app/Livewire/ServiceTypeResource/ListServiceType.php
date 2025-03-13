<?php

namespace App\Livewire\ServiceTypeResource;

use Flux\Flux;
use Livewire\Component;
use App\Models\ServiceType;
use Livewire\Attributes\On;

class ListServiceType extends Component


{
    public $services, $serviceId;

    public function mount()
    {
        $this->services  = ServiceType::all();
    }

    #[On("reloadServices")]
    public function reloadClassrooms()
    {
        $this->services  = ServiceType::all();
    }

    public function edit($id)
    {

        $this->dispatch("editService", $id);
    }

    public function delete($id)
    {
        $this->serviceId = $id;
        Flux::modal("delete-service")->show();
    }

    public function destroy()
    {

        ServiceType::find($this->serviceId)->delete();

        Flux::modal("delete-service")->close();
        $this->dispatch("reloadServices");
    }

    public function render()
    {
        return view('livewire.service-type-resource.list-service-type');
    }
}
