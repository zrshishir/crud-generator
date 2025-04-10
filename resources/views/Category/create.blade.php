<x-layout>
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-2xl mx-auto">
            <h1 class="text-2xl font-bold mb-6">Create Category</h1>

            <form action="{{ route('Categorys.store') }}" method="POST" class="space-y-4">
                @csrf
                                <div class="mb-4">
                    <label for="name" class="block text-sm font-medium text-gray-700">Name</label>
                    <input name="name" id="name" type="text" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" value="">
                    </input>
                    @error('name')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>                <div class="mb-4">
                    <label for="slug" class="block text-sm font-medium text-gray-700">Slug</label>
                    <input name="slug" id="slug" type="text" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" value="">
                    </input>
                    @error('slug')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>                <div class="mb-4">
                    <label for="parent_id" class="block text-sm font-medium text-gray-700">Parent id</label>
                    <input name="parent_id" id="parent_id" type="text" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" value="">
                    </input>
                    @error('parent_id')
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