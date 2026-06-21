<x-layouts.base>

    @php
        $routeName = request()->route()?->getName() ?? '';
        $isAdminRoute    = str_starts_with($routeName, 'admin.');
        $isAuthPage      = in_array($routeName, ['register', 'login', 'forgot-password', 'reset-password']);
        $isErrorPage     = in_array($routeName, ['404', '500']);
        $isProfileOrDash = in_array($routeName, ['profile', 'profile.update']);
    @endphp

    @if($isAdminRoute || $isProfileOrDash)

        {{-- Nav --}}
        @include('layouts.nav')
        {{-- SideNav --}}
        @include('layouts.sidenav')
        <main class="content">
            {{-- TopBar --}}
            @include('layouts.topbar')
            {{ $slot }}
            {{-- Footer --}}
            @include('layouts.footer')
        </main>

    @elseif($isAuthPage)

        {{ $slot }}
        @include('layouts.footer2')

    @elseif($isErrorPage)

        {{ $slot }}

    @endif

</x-layouts.base>