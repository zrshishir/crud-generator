<x-layout>
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-2xl mx-auto">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-2xl font-bold">Tag Details</h1>
                <div class="space-x-2">
                    <a href="{{ route('Tags.edit', $Tag) }}" class="bg-blue-500 text-white px-4 py-2 rounded">Edit</a>
                    <a href="{{ route('Tags.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded">Back to List</a>
                </div>
            </div>

            <div class="bg-white shadow-md rounded-lg p-6">
                                <div class="mb-4">
                    <h3 class="text-sm font-medium text-gray-500">name</h3>
                    <p class="mt-1">{{ $Tag->name }}</p>
                </div>
            </div>
        </div>
    </div>
</x-layout>