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
            <li class="breadcrumb-item active">Members</li>
          </ol>
        </nav>
        <h1 class="h3">Members</h1>
        <p class="text-muted mb-0">All submitted member verification records.</p>
      </div>
    </div>

    {{-- Filters --}}
    <div class="card border-0 shadow mb-4">
      <div class="card-body py-2">
        <form method="GET" class="row g-2 align-items-end">
          <div class="col-md-4">
            <label class="form-label small mb-1">Search</label>
            <input type="text" name="search" value="{{ request('search') }}" class="form-control form-control-sm" placeholder="Name, mobile, email, city...">
          </div>
          <div class="col-md-3">
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
            @if(request()->hasAny(['search','status']))
              <a href="{{ route('admin.members.index') }}" class="btn btn-outline-secondary btn-sm ms-1">Clear</a>
            @endif
          </div>
        </form>
      </div>
    </div>

    {{-- Table --}}
    <div class="card border-0 shadow">
      <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Member Records <span class="badge bg-secondary ms-2">{{ $members->total() }}</span></h5>
      </div>
      <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
          <thead class="table-light">
            <tr>
              <th>#</th>
              <th>Full Name</th>
              <th>Gender</th>
              <th>Mobile</th>
              <th>City</th>
              <th>Community</th>
              <th>Status</th>
              <th>Submitted</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            @forelse($members as $member)
            <tr>
              <td>{{ $member->id }}</td>
              <td class="fw-semibold">{{ $member->full_name }}</td>
              <td>{{ ucfirst($member->gender ?? '—') }}</td>
              <td>{{ $member->mobile_number ?? '—' }}</td>
              <td>{{ $member->city ?? '—' }}</td>
              <td>{{ $member->user->community->community_name ?? '—' }}</td>
              <td>
                @php $s = $member->user->status ?? 'pending'; @endphp
                <span class="badge bg-{{ $s==='accepted'?'success':($s==='rejected'?'danger':'warning') }}">
                  {{ ucfirst($s) }}
                </span>
              </td>
              <td>{{ $member->created_at ? $member->created_at->format('d M Y') : '—' }}</td>
              <td>
                <a href="{{ route('admin.members.show', $member->id) }}" class="btn btn-sm btn-outline-primary">
                  <i class="fas fa-eye me-1"></i>View
                </a>
              </td>
            </tr>
            @empty
            <tr><td colspan="9" class="text-center text-muted py-4">No member records found.</td></tr>
            @endforelse
          </tbody>
        </table>
      </div>
      @if($members->hasPages())
      <div class="card-footer bg-white d-flex justify-content-between align-items-center">
        <small class="text-muted">Showing {{ $members->firstItem() }}–{{ $members->lastItem() }} of {{ $members->total() }}</small>
        {{ $members->links() }}
      </div>
      @endif
    </div>

  </div>
</div>
</x-layouts.app>
