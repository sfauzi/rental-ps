<div>
    <div class="max-w-6xl mx-auto p-6 shadow-md rounded-lg">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-lg font-semibold text-gray-700">List Service</h2>
        </div>
        <flux:modal.trigger name="create-service">
            <flux:button variant="primary">Create Service</flux:button>
        </flux:modal.trigger>

        @livewire('service-type-resource.create-service-type')
        @livewire('service-type-resource.edit-service-type')

        <flux:modal name="delete-service" class="min-w-[22rem]">
            <div class="space-y-6">
                <div>
                    <flux:heading size="lg">Delete class?</flux:heading>

                    <flux:subheading>
                        <p>You're about to delete this class.</p>
                        <p>This action cannot be reversed.</p>
                    </flux:subheading>
                </div>

                <div class="flex gap-2">
                    <flux:spacer />

                    <flux:modal.close>
                        <flux:button variant="ghost">Cancel</flux:button>
                    </flux:modal.close>

                    <flux:button type="submit" variant="danger" wire:click="destroy">Delete class</flux:button>
                </div>
            </div>
        </flux:modal>

        <div class="overflow-x-auto mt-[30px]">
            <table class="w-full border border-gray-300 rounded-lg">
                <thead>
                    <tr class="bg-gray-200 text-gray-700">
                        <th class="py-2 px-4 border">Service Name</th>
                        <th class="py-2 px-4 border">Price</th>
                        <th class="py-2 px-4 border">Is Active</th>
                        <th class="py-2 px-4 border">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($services as $service)
                        <tr class=" hover:bg-gray-100 transition">
                            <td class="py-2 px-4 border">{{ $service->name }}</td>
                            <td class="py-2 px-4 border">Rp {{ number_format($service->price, 0, ' ') }}/sesi</td>
                            <td class="py-2 px-4 border">
                                <span class="{{ $service->is_active ? 'text-green-600' : 'text-red-600' }}">
                                    {{ $service->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td class="py-2 px-4 border text-center">
                                <flux:button variant="primary" wire:click="edit('{{ $service->id }}')" size="sm">
                                    Edit
                                </flux:button>

                                <flux:button wire:click="delete('{{ $service->id }}')" size="sm">
                                    Delete
                                </flux:button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
