<x-layout>
    <div class="container mx-auto px-4 py-8">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold">Tasks</h1>
            <a href="{{ route('Tasks.create') }}" class="bg-blue-500 text-white px-4 py-2 rounded">Create New</a>
        </div>

        <div class="bg-white shadow-md rounded-lg overflow-hidden">
            <table class="min-w-full">
                <thead class="bg-gray-100">
                    <tr>
                        <td>{{ $Task->title }}</td>
                <td>{{ $Task->description }}</td>
                <td>{{ $Task->status }}</td>
                
                        <th class="px-6 py-3 text-left">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($Tasks as $Task)
                    <tr class="border-t">
                        <td>{{ $Task->title }}</td>
                <td>{{ $Task->description }}</td>
                <td>{{ $Task->status }}</td>
                
                        <td class="px-6 py-4">
                            <a href="{{ route('Tasks.edit', $Task) }}" class="text-blue-500 hover:text-blue-700 mr-3">Edit</a>
                            <form action="{{ route('Tasks.destroy', $Task) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-500 hover:text-red-700">Delete</button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</x-layout>