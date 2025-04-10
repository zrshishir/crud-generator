<x-layout>
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-2xl mx-auto">
            <h1 class="text-2xl font-bold mb-6">Create User</h1>

            <form action="{{ route('Users.store') }}" method="POST" class="space-y-4">
                @csrf
                                <div class="mb-4">
                    <label for="name" class="block text-sm font-medium text-gray-700">Name</label>
                    <input name="name" id="name" type="text" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" value="">
                    </input>
                    @error('name')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>                <div class="mb-4">
                    <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                    <input name="email" id="email" type="text" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" value="">
                    </input>
                    @error('email')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>                <div class="mb-4">
                    <label for="is_active" class="block text-sm font-medium text-gray-700">Is active</label>
                    <input name="is_active" id="is_active" type="text" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" value="">
                    </input>
                    @error('is_active')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>                <div class="mb-4">
                    <label for="is_admin" class="block text-sm font-medium text-gray-700">Is admin</label>
                    <input name="is_admin" id="is_admin" type="text" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" value="">
                    </input>
                    @error('is_admin')
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