<x-layout title="Notifications">
    <div class="flex items-center justify-between mb-4">
        <h1 class="text-2xl font-bold">Notifications</h1>
        @if (auth()->user()->unreadNotifications()->exists())
            <form action="{{ route('notifications.read-all') }}" method="POST">
                @csrf
                <x-ui.button size="sm">Mark all as read</x-ui.button>
            </form>
        @endif
    </div>

    <x-ui.flash-status class="mb-4" />

    @if ($notifications->isEmpty())
        <p>No notifications yet.</p>
    @else
        <div class="flex flex-col gap-3">
            @foreach ($notifications as $notification)
                <x-card>
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <p @class(['font-semibold' => $notification->unread()])>
                                Your idea was published: {{ $notification->data['description'] ?? '' }}
                            </p>
                            <p class="text-sm opacity-70">{{ $notification->created_at->diffForHumans() }}</p>
                        </div>
                        <div class="flex items-center gap-2 shrink-0">
                            @if ($notification->unread())
                                <x-ui.badge variant="primary" size="sm">New</x-ui.badge>
                            @endif
                            <x-ui.button size="sm" :href="route('notifications.show', $notification->id)">View</x-ui.button>
                        </div>
                    </div>
                </x-card>
            @endforeach
        </div>

        <div class="mt-4">
            {{ $notifications->links() }}
        </div>
    @endif
</x-layout>
