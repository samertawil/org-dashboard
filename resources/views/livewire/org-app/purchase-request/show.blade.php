<div class="flex flex-col gap-6">
    <div class="bg-white dark:bg-zinc-800 rounded-lg border border-zinc-200 dark:border-zinc-700 shadow-sm p-6">
        <flux:heading level="2" size="lg" class="mb-4">{{ __('Purchase Requisition Details') }} #{{ $purchaseRequisition->request_number }}</flux:heading>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
            <div>
                <span class="block text-sm font-medium text-gray-500">{{ __('Request Date') }}</span>
                <span class="block text-lg">{{ $purchaseRequisition->request_date ? $purchaseRequisition->request_date->format('Y-m-d') : '-' }}</span>
            </div>
            <div>
                <span class="block text-sm font-medium text-gray-500">{{ __('Status') }}</span>
                <span class="block text-lg">{{ $purchaseRequisition->status->status_name ?? '-' }}</span>
            </div>
             <div>
                <span class="block text-sm font-medium text-gray-500">{{ __('Suggested Vendors') }}</span>
                <div class="flex flex-wrap gap-2">
                    @forelse($purchaseRequisition->suggestedVendors as $vendor)
                         <flux:badge size="sm">{{ $vendor->name }}</flux:badge>
                    @empty
                        <span>-</span>
                    @endforelse
                </div>
            </div>
            <div class="md:col-span-2">
                <span class="block text-sm font-medium text-gray-500">{{ __('Description') }}</span>
                <p class="mt-1 text-gray-900 dark:text-gray-100">{{ $purchaseRequisition->description }}</p>
            </div>
        </div>

        <flux:heading level="3" size="md" class="mb-2">{{ __('Items') }}</flux:heading>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-900">
                    <tr>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Item Name') }}</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Quantity') }}</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Unit') }}</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Unit Price') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($purchaseRequisition->items as $item)
                        <tr>
                            <td class="px-4 py-2">{{ $item->item_name }}</td>
                            <td class="px-4 py-2">{{ $item->quantity }}</td>
                            <td class="px-4 py-2">{{ $item->unit->status_name ?? '-' }}</td>
                            <td class="px-4 py-2">{{ $item->unit_price }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-4 py-2 text-center text-gray-500">{{ __('No items found') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
