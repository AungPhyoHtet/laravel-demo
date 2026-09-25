<x-layout title="Notifications">
    <div class="flex items-center justify-between mt-6 mb-4">
        <h1 class="text-2xl font-bold">Notifications</h1>
        @if (auth()->user()->unreadNotifications()->exists())
            <form action="{{ route('notifications.read-all') }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-sm">Mark all as read</button>
            </form>
        @endif
    </div>

    @if (session('status'))
        <div class="alert alert-success mb-4">
            {{ session('status') }}
        </div>
    @endif

    @if ($notifications->isEmpty())
        <p>No notifications yet.</p>
    @else
        <div class="flex flex-col gap-3">
            @foreach ($notifications as $notification)
                <x-card class="text-left">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <p @class(['font-semibold' => $notification->unread()])>
                                Your idea was published: {{ $notification->data['description'] ?? '' }}
                            </p>
                            <p class="text-sm opacity-70">{{ $notification->created_at->diffForHumans() }}</p>
                        </div>
                        <div class="flex items-center gap-2 shrink-0">
                            @if ($notification->unread())
                                <span class="badge badge-primary badge-sm">New</span>
                            @endif
                            <a href="{{ route('notifications.show', $notification->id) }}" class="btn btn-sm">View</a>
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
