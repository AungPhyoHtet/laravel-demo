<header class="bg-base-100 shadow-sm">
    <div class="navbar mx-auto max-w-3xl px-4">
        <div class="navbar-start">
            <div class="dropdown">
                <div tabindex="0" role="button" class="btn btn-ghost lg:hidden">
                    <svg aria-label="Menu" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h8m-8 6h16" />
                    </svg>
                </div>
                <ul tabindex="-1" class="menu menu-sm dropdown-content bg-base-100 rounded-box z-1 mt-3 w-52 p-2 shadow">
                    <li><a href="{{ route('home') }}" @class(['text-primary font-semibold' => request()->routeIs('home')])>Home</a></li>
                    <li><a href="{{ route('ideas.index') }}" @class(['text-primary font-semibold' => request()->routeIs('ideas.*')])>Ideas</a></li>
                    @can('access-admin')
                        <li><a href="{{ route('admin') }}" @class(['text-primary font-semibold' => request()->routeIs('admin')])>Admin</a></li>
                    @endcan
                </ul>
            </div>
            <a class="btn btn-ghost text-xl" href="{{ route('home') }}">Laravel Demo</a>
        </div>
        <div class="navbar-center hidden lg:flex">
            <ul class="menu menu-horizontal px-1">
                <li><a href="{{ route('home') }}" @class(['text-primary font-semibold' => request()->routeIs('home')])>Home</a></li>
                <li><a href="{{ route('ideas.index') }}" @class(['text-primary font-semibold' => request()->routeIs('ideas.*')])>Ideas</a></li>
                @can('access-admin')
                    <li><a href="{{ route('admin') }}" @class(['text-primary font-semibold' => request()->routeIs('admin')])>Admin</a></li>
                @endcan
            </ul>
        </div>
        <div class="navbar-end gap-2">
            @guest
                <x-ui.button variant="secondary" outline :href="route('login')">Log In</x-ui.button>
                <x-ui.button variant="secondary" :href="route('register')">Register</x-ui.button>
            @endguest
            @auth
                @php($unreadNotificationCount = auth()->user()->unreadNotifications()->count())
                <a href="{{ route('notifications.index') }}" class="btn btn-ghost btn-circle"
                    aria-label="Notifications{{ $unreadNotificationCount ? " ({$unreadNotificationCount} unread)" : '' }}">
                    <div class="indicator">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                        </svg>
                        @if ($unreadNotificationCount)
                            <span class="badge badge-xs badge-primary indicator-item">{{ $unreadNotificationCount }}</span>
                        @endif
                    </div>
                </a>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <x-ui.button>Log Out</x-ui.button>
                </form>
            @endauth
        </div>
    </div>
</header>
