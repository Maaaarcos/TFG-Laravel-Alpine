 @if(session('message'))
    <div x-data="{ isVisible: true }"
         x-init="setTimeout(() => isVisible = false, 5000); setTimeout(() => { isVisible = false; $wire.clearSessionMessage() }, 5500)"
         x-show.transition.duration.1000ms="isVisible"
         class="rounded-md p-4 mt-2 mb-4 {{ session('message_type') == 'success' ? 'bg-green-50' : 'bg-red-50' }}"
    >
        <div class="flex">
            <div class="flex-shrink-0">
                @if(session('message_type') == 'success')
                    <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd"
                              d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                              clip-rule="evenodd"/>
                    </svg>
                @else
                    <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd"
                              d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                              clip-rule="evenodd"/>
                    </svg>
                @endif
            </div>
            <div class="ml-3">
                <p class="text-sm leading-5 font-medium {{ session('message_type') == 'success' ? 'text-green-800' : 'text-red-800' }}">
                    {{ session('message') }}
                </p>
            </div>
        </div>
    </div>
@endif 
