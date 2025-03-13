<div>
    <flux:modal name="create-service" class="w-full">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">Create Service</flux:heading>
                <flux:subheading>Make changes to your personal details.</flux:subheading>
            </div>

            <flux:input label="Name" wire:model="name" placeholder="Enter name" />

            <flux:input label="Price" wire:model="price" type="numeric" placeholder="Enter price" />


            <flux:switch wire:model.live="is_active" label="Enable is active" />

            <div class="flex">
                <flux:spacer />
                <flux:button type="submit" variant="primary" wire:click="store">Save</flux:button>
            </div>
        </div>
    </flux:modal>
</div>
