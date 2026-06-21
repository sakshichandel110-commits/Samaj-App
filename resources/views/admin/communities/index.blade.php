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
            <li class="breadcrumb-item active">Communities</li>
          </ol>
        </nav>
        <h1 class="h3">Communities</h1>
        <p class="text-muted mb-0">All registered communities.</p>
      </div>
    </div>

    {{-- Search --}}
    <div class="card border-0 shadow mb-4">
      <div class="card-body py-2">
        <form method="GET" class="row g-2 align-items-end">
          <div class="col-md-4">
            <label class="form-label small mb-1">Search</label>
            <input type="text" name="search" value="{{ request('search') }}" class="form-control form-control-sm" placeholder="Name, code...">
          </div>
          <div class="col-auto">
            <button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-search me-1"></i>Search</button>
            @if(request('search'))
              <a href="{{ route('admin.communities.index') }}" class="btn btn-outline-secondary btn-sm ms-1">Clear</a>
            @endif
          </div>
        </form>
      </div>
    </div>

    {{-- Table --}}
    <div class="card border-0 shadow">
      <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center">
        <h5 class="mb-0">All Communities <span class="badge bg-secondary ms-2">{{ $communities->total() }}</span></h5>
      </div>
      <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
          <thead class="table-light">
            <tr>
              <th>#</th>
              <th>Logo</th>
              <th>Community Name</th>
              <th>Short Name</th>
              <th>Code</th>
              <th>Admin (Owner)</th>
              <th>Created</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            @forelse($communities as $community)
            <tr>
              <td>{{ $community->id }}</td>
              <td>
                @if($community->community_logo)
                  <img src="{{ $community->community_logo }}" alt="logo" class="rounded-circle" style="width:38px;height:38px;object-fit:cover;">
                @else
                  <div class="rounded-circle bg-secondary d-inline-flex align-items-center justify-content-center text-white" style="width:38px;height:38px;font-size:14px;">
                    {{ strtoupper(substr($community->community_short_name ?? 'C', 0, 1)) }}
                  </div>
                @endif
              </td>
              <td class="fw-semibold">{{ $community->community_name }}</td>
              <td>{{ $community->community_short_name }}</td>
              <td><code>{{ $community->code }}</code></td>
              <td>{{ $community->user->name ?? ($community->user->email ?? '—') }}</td>
              <td>{{ $community->created_at ? $community->created_at->format('d M Y') : '—' }}</td>
              <td>
                <a href="{{ route('admin.communities.show', $community->id) }}" class="btn btn-sm btn-outline-primary">
                  <i class="fas fa-eye me-1"></i>View
                </a>
              </td>
            </tr>
            @empty
            <tr>
              <td colspan="8" class="text-center text-muted py-4">No communities found.</td>
            </tr>
            @endforelse
          </tbody>
        </table>
      </div>
      @if($communities->hasPages())
      <div class="card-footer bg-white d-flex justify-content-between align-items-center">
        <small class="text-muted">Showing {{ $communities->firstItem() }}–{{ $communities->lastItem() }} of {{ $communities->total() }}</small>
        {{ $communities->links() }}
      </div>
      @endif
    </div>

  </div>
</div>
</x-layouts.app>
