<x-layout>
    <div class="container mx-auto px-4 py-8">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold">Posts</h1>
            <a href="{{ route('Posts.create') }}" class="bg-blue-500 text-white px-4 py-2 rounded">Create New</a>
        </div>

        <div class="bg-white shadow-md rounded-lg overflow-hidden">
            <table class="min-w-full">
                <thead class="bg-gray-100">
                    <tr>
                        <td>{{ $Post->title }}</td>
                <td>{{ $Post->content }}</td>
                
                        <th class="px-6 py-3 text-left">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($Posts as $Post)
                    <tr class="border-t">
                        <td>{{ $Post->title }}</td>
                <td>{{ $Post->content }}</td>
                
                        <td class="px-6 py-4">
                            <a href="{{ route('Posts.edit', $Post) }}" class="text-blue-500 hover:text-blue-700 mr-3">Edit</a>
                            <form action="{{ route('Posts.destroy', $Post) }}" method="POST" class="inline">
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