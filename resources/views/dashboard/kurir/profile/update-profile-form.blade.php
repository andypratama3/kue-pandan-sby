<form method="POST" action="{{ route('kurir.profile.update') }}" enctype="multipart/form-data">
    @csrf
    @method('PUT')
    
    <div class="space-y-6">
        <!-- Profile Photo -->
        @if (Laravel\Jetstream\Jetstream::managesProfilePhotos())
        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-white">Photo</label>
            <div class="mt-2 flex items-center space-x-4">
                <img src="{{ Auth::user()->profile_photo_url }}" alt="{{ Auth::user()->name }}" class="rounded-full h-20 w-20 object-cover">
                <div>
                    <input type="file" name="photo" id="photo" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-brand-light file:text-brand-deep hover:file:bg-brand hover:file:text-white dark:text-gray-400">
                    @error('photo')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>
        @endif

        <!-- Name -->
        <div>
            <label for="name" class="block text-sm font-medium text-gray-700 dark:text-white">Name</label>
            <input type="text" name="name" id="name" value="{{ old('name', Auth::user()->name) }}" required 
                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-brand focus:ring-brand dark:bg-gray-600 dark:border-gray-500 dark:text-white dark:placeholder-gray-400">
            @error('name')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Email -->
        <div>
            <label for="email" class="block text-sm font-medium text-gray-700 dark:text-white">Email</label>
            <input type="email" name="email" id="email" value="{{ old('email', Auth::user()->email) }}" required 
                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-brand focus:ring-brand dark:bg-gray-600 dark:border-gray-500 dark:text-white dark:placeholder-gray-400">
            @error('email')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Save Button -->
        <div class="flex justify-end">
            <button type="submit" class="bg-brand hover:bg-brand-deep text-white font-medium py-2 px-4 rounded-md focus:outline-none focus:ring-2 focus:ring-brand focus:ring-offset-2">
                Save
            </button>
        </div>
    </div>
</form>
