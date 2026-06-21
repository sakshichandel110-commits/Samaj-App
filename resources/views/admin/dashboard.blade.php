<x-layouts.app>
<div class="py-4">
  <div class="container-fluid">

    {{-- Flash messages --}}
    @if(session('success'))
      <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>
    @endif

    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center py-3">
      <div class="d-block mb-4 mb-md-0">
        <nav aria-label="breadcrumb">
          <ol class="breadcrumb">
            <li class="breadcrumb-item active">Admin Panel</li>
          </ol>
        </nav>
        <h1 class="h3">Dashboard</h1>
        <p class="mb-0 text-muted">Welcome to the Samaj Admin Panel.</p>
      </div>
    </div>

    {{-- Stats Cards --}}
    <div class="row">

      <div class="col-12 col-sm-6 col-xl-3 mb-4">
        <div class="card border-0 shadow">
          <div class="card-body d-flex align-items-center">
            <div class="icon icon-shape icon-md rounded-circle bg-primary text-white me-4 d-flex align-items-center justify-content-center" style="width:50px;height:50px;">
              <i class="fas fa-users"></i>
            </div>
            <div>
              <p class="text-muted mb-0 small">Total Users</p>
              <h4 class="mb-0 fw-bold">{{ $stats['total_users'] }}</h4>
              <small class="text-success">Admin: {{ $stats['admin_users'] }} &nbsp;|&nbsp; Member: {{ $stats['member_users'] }}</small>
            </div>
          </div>
        </div>
      </div>

      <div class="col-12 col-sm-6 col-xl-3 mb-4">
        <div class="card border-0 shadow">
          <div class="card-body d-flex align-items-center">
            <div class="icon icon-shape icon-md rounded-circle bg-warning text-dark me-4 d-flex align-items-center justify-content-center" style="width:50px;height:50px;">
              <i class="fas fa-user-clock"></i>
            </div>
            <div>
              <p class="text-muted mb-0 small">Pending Members</p>
              <h4 class="mb-0 fw-bold">{{ $stats['pending_members'] }}</h4>
              <small class="text-success">Accepted: {{ $stats['accepted_members'] }}</small>
            </div>
          </div>
          <a href="{{ route('admin.members.index') }}?status=pending" class="stretched-link"></a>
        </div>
      </div>

      <div class="col-12 col-sm-6 col-xl-3 mb-4">
        <div class="card border-0 shadow">
          <div class="card-body d-flex align-items-center">
            <div class="icon icon-shape icon-md rounded-circle bg-info text-white me-4 d-flex align-items-center justify-content-center" style="width:50px;height:50px;">
              <i class="fas fa-people-group"></i>
            </div>
            <div>
              <p class="text-muted mb-0 small">Communities</p>
              <h4 class="mb-0 fw-bold">{{ $stats['total_communities'] }}</h4>
              <small class="text-muted">Registered</small>
            </div>
          </div>
          <a href="{{ route('admin.communities.index') }}" class="stretched-link"></a>
        </div>
      </div>

      <div class="col-12 col-sm-6 col-xl-3 mb-4">
        <div class="card border-0 shadow">
          <div class="card-body d-flex align-items-center">
            <div class="icon icon-shape icon-md rounded-circle bg-success text-white me-4 d-flex align-items-center justify-content-center" style="width:50px;height:50px;">
              <i class="fas fa-bullhorn"></i>
            </div>
            <div>
              <p class="text-muted mb-0 small">Announcements</p>
              <h4 class="mb-0 fw-bold">{{ $stats['total_announcements'] }}</h4>
              <small class="text-muted">Total posted</small>
            </div>
          </div>
          <a href="{{ route('admin.announcements.index') }}" class="stretched-link"></a>
        </div>
      </div>

      <div class="col-12 col-sm-6 col-xl-3 mb-4">
        <div class="card border-0 shadow">
          <div class="card-body d-flex align-items-center">
            <div class="icon icon-shape icon-md rounded-circle bg-danger text-white me-4 d-flex align-items-center justify-content-center" style="width:50px;height:50px;">
              <i class="fas fa-heart"></i>
            </div>
            <div>
              <p class="text-muted mb-0 small">Matrimony Posts</p>
              <h4 class="mb-0 fw-bold">{{ $stats['total_posts'] }}</h4>
              <small class="text-muted">Total posted</small>
            </div>
          </div>
          <a href="{{ route('admin.matrimony-posts.index') }}" class="stretched-link"></a>
        </div>
      </div>

      <div class="col-12 col-sm-6 col-xl-3 mb-4">
        <div class="card border-0 shadow">
          <div class="card-body d-flex align-items-center">
            <div class="icon icon-shape icon-md rounded-circle bg-secondary text-white me-4 d-flex align-items-center justify-content-center" style="width:50px;height:50px;">
              <i class="fas fa-id-card"></i>
            </div>
            <div>
              <p class="text-muted mb-0 small">Verified Members</p>
              <h4 class="mb-0 fw-bold">{{ $stats['total_members'] }}</h4>
              <small class="text-muted">Submitted verification</small>
            </div>
          </div>
          <a href="{{ route('admin.members.index') }}" class="stretched-link"></a>
        </div>
      </div>

    </div>

    {{-- Quick Navigation --}}
    <div class="row mt-2">
      <div class="col-12">
        <div class="card border-0 shadow">
          <div class="card-header bg-white border-bottom">
            <h5 class="mb-0">Quick Navigation</h5>
          </div>
          <div class="card-body">
            <div class="row g-3">
              <div class="col-6 col-md-3">
                <a href="{{ route('admin.communities.index') }}" class="btn btn-outline-primary w-100">
                  <i class="fas fa-people-group me-2"></i>Communities
                </a>
              </div>
              <div class="col-6 col-md-3">
                <a href="{{ route('admin.members.index') }}?status=pending" class="btn btn-outline-warning w-100">
                  <i class="fas fa-user-clock me-2"></i>Pending Members
                </a>
              </div>
              <div class="col-6 col-md-3">
                <a href="{{ route('admin.announcements.index') }}" class="btn btn-outline-success w-100">
                  <i class="fas fa-bullhorn me-2"></i>Announcements
                </a>
              </div>
              <div class="col-6 col-md-3">
                <a href="{{ route('admin.matrimony-posts.index') }}" class="btn btn-outline-danger w-100">
                  <i class="fas fa-heart me-2"></i>Matrimony Posts
                </a>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

  </div>
</div>
</x-layouts.app>
