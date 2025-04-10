<x-layout>
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-2xl mx-auto">
            <h1 class="text-2xl font-bold mb-6">Create Article</h1>

            <form action="{{ route('Articles.store') }}" method="POST" class="space-y-4">
                @csrf
                                <div class="mb-4">
                    <label for="title" class="block text-sm font-medium text-gray-700">Title</label>
                    <input name="title" id="title" type="text" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" value="">
                    </input>
                    @error('title')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>                <div class="mb-4">
                    <label for="content" class="block text-sm font-medium text-gray-700">Content</label>
                    <textarea name="content" id="content" rows="4" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" value="">
                    </textarea>
                    @error('content')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>                <div class="mb-4">
                    <label for="excerpt" class="block text-sm font-medium text-gray-700">Excerpt</label>
                    <textarea name="excerpt" id="excerpt" rows="4" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" value="">
                    </textarea>
                    @error('excerpt')
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