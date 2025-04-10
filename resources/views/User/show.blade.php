<x-layout>
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-2xl mx-auto">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-2xl font-bold">User Details</h1>
                <div class="space-x-2">
                    <a href="{{ route('Users.edit', $User) }}" class="bg-blue-500 text-white px-4 py-2 rounded">Edit</a>
                    <a href="{{ route('Users.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded">Back to List</a>
                </div>
            </div>

            <div class="bg-white shadow-md rounded-lg p-6">
                                <div class="mb-4">
                    <h3 class="text-sm font-medium text-gray-500">name</h3>
                    <p class="mt-1">{{ $User->name }}</p>
                </div>                <div class="mb-4">
                    <h3 class="text-sm font-medium text-gray-500">email</h3>
                    <p class="mt-1">{{ $User->email }}</p>
                </div>                <div class="mb-4">
                    <h3 class="text-sm font-medium text-gray-500">is_active</h3>
                    <p class="mt-1">{{ $User->is_active }}</p>
                </div>                <div class="mb-4">
                    <h3 class="text-sm font-medium text-gray-500">is_admin</h3>
                    <p class="mt-1">{{ $User->is_admin }}</p>
                </div>
            </div>
        </div>
    </div>
</x-layout>