<div class="navbar bg-base-100 shadow-sm">
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
            <a href="{{ route('login') }}" class="btn btn-outline btn-secondary">Log In</a>
            <a href="{{ route('register') }}" class="btn btn-secondary">Register</a>
        @endguest
        @auth
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="btn">Log Out</button>
            </form>
        @endauth
    </div>
</div>
