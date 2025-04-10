<x-layout>
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-2xl mx-auto">
            <h1 class="text-2xl font-bold mb-6">Create Order</h1>

            <form action="{{ route('Orders.store') }}" method="POST" class="space-y-4">
                @csrf
                                <div class="mb-4">
                    <label for="order_number" class="block text-sm font-medium text-gray-700">Order number</label>
                    <input name="order_number" id="order_number" type="text" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" value="">
                    </input>
                    @error('order_number')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>                <div class="mb-4">
                    <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
                    <select name="status" id="status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                        <option value="pendin" >pendin</option>
                    
                    </select>
                    @error('status')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div class="flex justify-end">
                    <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">Create</button>
                </div>
            </form>
        </div>
    </div>
</x-layout>