<form method="POST" action="{{ route('kurir.profile.password') }}">
    @csrf
    @method('PUT')
    
    <div class="space-y-6">
        <!-- Current Password -->
        <div>
            <label for="current_password" class="block text-sm font-medium text-gray-700 dark:text-white">Current Password</label>
            <input type="password" name="current_password" id="current_password" required 
                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-brand focus:ring-brand dark:bg-gray-600 dark:border-gray-500 dark:text-white dark:placeholder-gray-400">
            @error('current_password')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- New Password -->
        <div>
            <label for="password" class="block text-sm font-medium text-gray-700 dark:text-white">New Password</label>
            <input type="password" name="password" id="password" required 
                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-brand focus:ring-brand dark:bg-gray-600 dark:border-gray-500 dark:text-white dark:placeholder-gray-400">
            @error('password')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Confirm Password -->
        <div>
            <label for="password_confirmation" class="block text-sm font-medium text-gray-700 dark:text-white">Confirm Password</label>
            <input type="password" name="password_confirmation" id="password_confirmation" required 
                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-brand focus:ring-brand dark:bg-gray-600 dark:border-gray-500 dark:text-white dark:placeholder-gray-400">
        </div>

        <!-- Save Button -->
        <div class="flex justify-end">
            <button type="submit" class="bg-brand hover:bg-brand-deep text-white font-medium py-2 px-4 rounded-md focus:outline-none focus:ring-2 focus:ring-brand focus:ring-offset-2">
                Update Password
            </button>
        </div>
    </div>
</form>
