<x-filament::widget>
    <x-filament::card>
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-xl font-bold" style="color: rgb(46, 125, 50);">
                {{ __('notifications.latest_notifications') }}
            </h2>
            @if($unreadCount > 0)
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-danger-100 text-danger-700">
                    {{ $unreadCount }} {{ __('notifications.unread') }}
                </span>
            @endif
        </div>

        <div class="space-y-3">
            @forelse($unreadNotifications as $notification)
                <div
                    wire:click="markAsRead('{{ $notification['id'] }}')"
                    class="flex items-start gap-3 p-3 rounded-lg bg-gray-50 dark:bg-gray-700/50 cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-600/50 transition-colors"
                >
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-900 dark:text-gray-100 truncate">
                            {{ $notification['title'] }}
                        </p>
                        <p class="text-sm text-gray-500 dark:text-gray-400 line-clamp-2">
                            {{ $notification['body'] }}
                        </p>
                        <p class="text-xs text-gray-400 mt-1">
                            {{ $notification['created_at'] }}
                        </p>
                    </div>
                </div>
            @empty
                <div class="text-center py-6">
                    <div class="text-gray-400 dark:text-gray-500">
                        <svg class="mx-auto h-12 w-12 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                        </svg>
                        <p class="text-sm">{{ __('notifications.no_unread') }}</p>
                    </div>
                </div>
            @endforelse
        </div>
    </x-filament::card>
</x-filament::widget>
