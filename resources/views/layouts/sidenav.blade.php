<nav id="sidebarMenu" class="sidebar d-lg-block bg-gray-800 text-white collapse" data-simplebar>
  <div class="sidebar-inner px-2 pt-3">

    {{-- Mobile user card --}}
    <div class="user-card d-flex d-md-none align-items-center justify-content-between justify-content-md-center pb-4">
      <div class="d-flex align-items-center">
        <div class="d-block">
          <h2 class="h5 mb-3">{{ auth()->user()->name ?? 'Admin User' }}</h2>
          <a href="{{ route('login') }}" class="btn btn-secondary btn-sm d-inline-flex align-items-center">
            <svg class="icon icon-xxs me-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
            </svg>
            Sign Out
          </a>
        </div>
      </div>
      <div class="collapse-close d-md-none">
        <a href="#sidebarMenu" data-bs-toggle="collapse" data-bs-target="#sidebarMenu" aria-controls="sidebarMenu" aria-expanded="true" aria-label="Toggle navigation">
          <svg class="icon icon-xs" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
          </svg>
        </a>
      </div>
    </div>

    <ul class="nav flex-column pt-3 pt-md-0">

      {{-- Brand --}}
      <li class="nav-item">
        <a href="{{ route('admin.dashboard') }}" class="nav-link d-flex align-items-center">
          <span class="sidebar-icon me-3">
            <svg class="icon icon-sm" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
              <path d="M10.394 2.08a1 1 0 00-.788 0l-7 3a1 1 0 000 1.84L5.25 8.051a.999.999 0 01.356-.257l4-1.714a1 1 0 11.788 1.838L7.667 9.088l1.94.831a1 1 0 00.787 0l7-3a1 1 0 000-1.838l-7-3z" />
              <path d="M3.31 9.397L5 10.12v4.102a8.969 8.969 0 00-1.05-.174 1 1 0 01-.89-.89 11.115 11.115 0 01.25-3.762zM9.3 16.573A9.026 9.026 0 007 14.935v-3.957l1.818.78a3 3 0 002.364 0l5.508-2.361a11.026 11.026 0 01.25 3.762 1 1 0 01-.89.89 8.968 8.968 0 00-5.35 2.524 1 1 0 01-1.4 0zM6 18a1 1 0 001-1v-2.065a8.935 8.935 0 00-2-.712V17a1 1 0 001 1z" />
            </svg>
          </span>
          <span class="mt-1 ms-1 sidebar-text fw-bold" style="font-size:1rem;">Samaj Admin</span>
        </a>
      </li>

      {{-- Dashboard --}}
      <li class="nav-item {{ Request::is('admin/dashboard') ? 'active' : '' }}">
        <a href="{{ route('admin.dashboard') }}" class="nav-link">
          <span class="sidebar-icon">
            <svg class="icon icon-xs me-2" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
              <path d="M2 10a8 8 0 018-8v8h8a8 8 0 11-16 0z"></path>
              <path d="M12 2.252A8.014 8.014 0 0117.748 8H12V2.252z"></path>
            </svg>
          </span>
          <span class="sidebar-text">Dashboard</span>
        </a>
      </li>

      {{-- Divider --}}
      <li role="separator" class="dropdown-divider mt-3 mb-2 border-gray-700"></li>
      <li class="nav-item"><span class="nav-link text-uppercase" style="font-size:0.65rem;letter-spacing:0.1em;color:#94a3b8;pointer-events:none;">Community</span></li>

      {{-- Communities --}}
      <li class="nav-item {{ Request::is('admin/communities*') ? 'active' : '' }}">
        <a href="{{ route('admin.communities.index') }}" class="nav-link">
          <span class="sidebar-icon">
            <svg class="icon icon-xs me-2" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
              <path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v1h8v-1zM6 8a2 2 0 11-4 0 2 2 0 014 0zM16 18v-1a5.972 5.972 0 00-.75-2.906A3.005 3.005 0 0119 15v1h-3zM4.75 12.094A5.973 5.973 0 004 15v1H1v-1a3 3 0 013.75-2.906z"></path>
            </svg>
          </span>
          <span class="sidebar-text">Communities</span>
        </a>
      </li>

      {{-- Members --}}
      <li class="nav-item {{ Request::is('admin/members*') ? 'active' : '' }}">
        <a href="{{ route('admin.members.index') }}" class="nav-link">
          <span class="sidebar-icon">
            <svg class="icon icon-xs me-2" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
              <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path>
            </svg>
          </span>
          <span class="sidebar-text">Members</span>
          @php $pending = \App\Models\User::where('status','pending')->count(); @endphp
          @if($pending > 0)
            <span class="badge bg-warning text-dark ms-auto">{{ $pending }}</span>
          @endif
        </a>
      </li>

      {{-- Divider --}}
      <li role="separator" class="dropdown-divider mt-3 mb-2 border-gray-700"></li>
      <li class="nav-item"><span class="nav-link text-uppercase" style="font-size:0.65rem;letter-spacing:0.1em;color:#94a3b8;pointer-events:none;">Content</span></li>

      {{-- Announcements --}}
      <li class="nav-item {{ Request::is('admin/announcements*') ? 'active' : '' }}">
        <a href="{{ route('admin.announcements.index') }}" class="nav-link">
          <span class="sidebar-icon">
            <svg class="icon icon-xs me-2" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
              <path fill-rule="evenodd" d="M18 3a1 1 0 00-1.447-.894L8.763 6H5a3 3 0 000 6h.28l1.771 5.316A1 1 0 008 18h1a1 1 0 001-1v-4.382l6.553 3.276A1 1 0 0018 15V3z" clip-rule="evenodd"></path>
            </svg>
          </span>
          <span class="sidebar-text">Announcements</span>
        </a>
      </li>

      {{-- Matrimony Posts --}}
      <li class="nav-item {{ Request::is('admin/matrimony-posts*') ? 'active' : '' }}">
        <a href="{{ route('admin.matrimony-posts.index') }}" class="nav-link">
          <span class="sidebar-icon">
            <svg class="icon icon-xs me-2" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
              <path fill-rule="evenodd" d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" clip-rule="evenodd"></path>
            </svg>
          </span>
          <span class="sidebar-text">Matrimony Posts</span>
        </a>
      </li>

      {{-- Divider --}}
      <li role="separator" class="dropdown-divider mt-3 mb-2 border-gray-700"></li>
      <li class="nav-item"><span class="nav-link text-uppercase" style="font-size:0.65rem;letter-spacing:0.1em;color:#94a3b8;pointer-events:none;">Users</span></li>

      {{-- Users --}}
      <li class="nav-item {{ Request::is('admin/users*') ? 'active' : '' }}">
        <a href="{{ route('admin.users.index') }}" class="nav-link">
          <span class="sidebar-icon">
            <svg class="icon icon-xs me-2" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
              <path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z"></path>
            </svg>
          </span>
          <span class="sidebar-text">All Users</span>
        </a>
      </li>

      {{-- Divider --}}
      <li role="separator" class="dropdown-divider mt-4 mb-3 border-gray-700"></li>

      {{-- Profile link --}}
      <li class="nav-item">
        <a href="{{ route('profile') }}" class="nav-link d-flex align-items-center">
          <span class="sidebar-icon">
            <svg class="icon icon-xs me-2" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
              <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-6-3a2 2 0 11-4 0 2 2 0 014 0zm-2 4a5 5 0 00-4.546 2.916A5.986 5.986 0 0010 16a5.986 5.986 0 004.546-2.084A5 5 0 0010 11z" clip-rule="evenodd"></path>
            </svg>
          </span>
          <span class="sidebar-text">My Profile</span>
        </a>
      </li>

    </ul>
  </div>
</nav>