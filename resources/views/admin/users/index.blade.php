<x-layouts.app>
<div class="py-4">
  <div class="container-fluid">

    @if(session('success'))
      <div class="alert alert-success alert-dismissible fade show"><i class="fas fa-check-circle me-2"></i>{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
    @endif

    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center py-3">
      <div>
        <nav aria-label="breadcrumb">
          <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">Users</li>
          </ol>
        </nav>
        <h1 class="h3">All Users</h1>
        <p class="text-muted mb-0">All registered users in the system.</p>
      </div>
    </div>

    {{-- Filters --}}
    <div class="card border-0 shadow mb-4">
      <div class="card-body py-2">
        <form method="GET" class="row g-2 align-items-end">
          <div class="col-md-4">
            <label class="form-label small mb-1">Search</label>
            <input type="text" name="search" value="{{ request('search') }}" class="form-control form-control-sm" placeholder="Name, email, mobile...">
          </div>
          <div class="col-md-2">
            <label class="form-label small mb-1">Type</label>
            <select name="user_type" class="form-select form-select-sm">
              <option value="">All Types</option>
              <option value="admin" {{ request('user_type')=='admin'?'selected':'' }}>Admin</option>
              <option value="member" {{ request('user_type')=='member'?'selected':'' }}>Member</option>
            </select>
          </div>
          <div class="col-md-2">
            <label class="form-label small mb-1">Status</label>
            <select name="status" class="form-select form-select-sm">
              <option value="">All Statuses</option>
              <option value="pending" {{ request('status')=='pending'?'selected':'' }}>Pending</option>
              <option value="accepted" {{ request('status')=='accepted'?'selected':'' }}>Accepted</option>
              <option value="rejected" {{ request('status')=='rejected'?'selected':'' }}>Rejected</option>
            </select>
          </div>
          <div class="col-auto">
            <button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-filter me-1"></i>Filter</button>
            @if(request()->hasAny(['search','user_type','status']))
              <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary btn-sm ms-1">Clear</a>
            @endif
          </div>
        </form>
      </div>
    </div>

    {{-- Table --}}
    <div class="card border-0 shadow">
      <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Users <span class="badge bg-secondary ms-2">{{ $users->total() }}</span></h5>
      </div>
      <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
          <thead class="table-light">
            <tr>
              <th>#</th>
              <th>Name</th>
              <th>Email</th>
              <th>Mobile</th>
              <th>Type</th>
              <th>Community</th>
              <th>Status</th>
              <th>Registered</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            @forelse($users as $user)
            <tr>
              <td>{{ $user->id }}</td>
              <td class="fw-semibold">{{ $user->name ?? '—' }}</td>
              <td>{{ $user->email ?? '—' }}</td>
              <td>{{ $user->mobile ?? '—' }}</td>
              <td>
                <span class="badge bg-{{ $user->user_type==='admin'?'danger':'primary' }}">
                  {{ ucfirst($user->user_type ?? 'member') }}
                </span>
              </td>
              <td>{{ $user->community->community_name ?? '—' }}</td>
              <td>
                @php $s = $user->status ?? 'pending'; @endphp
                <span class="badge bg-{{ $s==='accepted'?'success':($s==='rejected'?'danger':'warning') }}">
                  {{ ucfirst($s) }}
                </span>
              </td>
              <td>{{ $user->created_at ? $user->created_at->format('d M Y') : '—' }}</td>
              <td>
                <a href="{{ route('admin.users.show', $user->id) }}" class="btn btn-sm btn-outline-primary">
                  <i class="fas fa-eye me-1"></i>View
                </a>
              </td>
            </tr>
            @empty
            <tr><td colspan="9" class="text-center text-muted py-4">No users found.</td></tr>
            @endforelse
          </tbody>
        </table>
      </div>
      @if($users->hasPages())
      <div class="card-footer bg-white d-flex justify-content-between align-items-center">
        <small class="text-muted">Showing {{ $users->firstItem() }}–{{ $users->lastItem() }} of {{ $users->total() }}</small>
        {{ $users->links() }}
      </div>
      @endif
    </div>

  </div>
</div>
</x-layouts.app>
