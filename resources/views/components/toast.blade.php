{{-- resources/views/components/toast.blade.php --}}
<div x-data="{
    show: false,
    message: '',
    type: 'success',
    showToast(event) {
        this.show = true;
        this.message = event.detail.message;
        this.type = event.detail.type || 'success';
        setTimeout(() => this.show = false, 5000);
    }
}" x-init="
    @if (session()->has('success'))
        show = true;
        message = '{{ session('success') }}';
        type = 'success';
        setTimeout(() => show = false, 5000);
    @elseif (session()->has('error'))
        show = true;
        message = '{{ session('error') }}';
        type = 'error';
        setTimeout(() => show = false, 5000);
    @endif
    window.addEventListener('show-toast', event => showToast(event));
" x-show="show" x-transition:enter="transform ease-out duration-300 transition"
    x-transition:enter-start="translate-y-2 opacity-0 sm:translate-y-0 sm:translate-x-2"
    x-transition:enter-end="translate-y-0 opacity-100 sm:translate-x-0"
    x-transition:leave="transition ease-in duration-100" x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
    class="fixed top-5 right-5 w-full max-w-xs p-4 text-gray-900 bg-white rounded-lg shadow-lg dark:bg-gray-800 dark:text-gray-300 z-[100]"
    role="alert" style="display: none;">

    <div class="flex items-center">
        {{-- Success Icon --}}
        <template x-if="type === 'success'">
            <div
                class="inline-flex items-center justify-center flex-shrink-0 w-8 h-8 text-brand bg-brand-light rounded-lg dark:bg-brand-deep dark:text-brand-light">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                    <path
                        d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5Zm3.707 8.207-4 4a1 1 0 0 1-1.414 0l-2-2a1 1 0 0 1 1.414-1.414L9 10.586l3.293-3.293a1 1 0 0 1 1.414 1.414Z" />
                </svg>
            </div>
        </template>

        {{-- Error Icon --}}
        <template x-if="type === 'error'">
            <div
                class="inline-flex items-center justify-center flex-shrink-0 w-8 h-8 text-red-500 bg-red-100 rounded-lg dark:bg-red-800 dark:text-red-200">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                    <path
                        d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5Zm3.707 11.793a1 1 0 1 1-1.414 1.414L10 11.414l-2.293 2.293a1 1 0 0 1-1.414-1.414L8.586 10 6.293 7.707a1 1 0 0 1 1.414-1.414L10 8.586l2.293-2.293a1 1 0 0 1 1.414 1.414L11.414 10l2.293 2.293Z" />
                </svg>
            </div>
        </template>

        <div class="ms-3 text-sm font-normal" x-text="message"></div>
        <button type="button" @click="show = false"
            class="ms-auto -mx-1.5 -my-1.5 bg-white text-gray-400 hover:text-gray-900 rounded-lg focus:ring-2 focus:ring-gray-300 p-1.5 hover:bg-gray-100 inline-flex items-center justify-center h-8 w-8 dark:text-gray-500 dark:hover:text-white dark:bg-gray-800 dark:hover:bg-gray-700"
            aria-label="Close">
            <span class="sr-only">Close</span>
            <svg class="w-3 h-3" fill="none" viewBox="0 0 14 14">
                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
            </svg>
        </button>
    </div>
</div>
