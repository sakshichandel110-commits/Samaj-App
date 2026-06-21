<x-layouts.app>
<div class="py-4">
  <div class="container-fluid">

    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center py-3">
      <div>
        <nav aria-label="breadcrumb">
          <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.communities.index') }}">Communities</a></li>
            <li class="breadcrumb-item active">{{ $community->community_name }}</li>
          </ol>
        </nav>
        <h1 class="h3">Community Details</h1>
      </div>
      <a href="{{ route('admin.communities.index') }}" class="btn btn-outline-secondary btn-sm">
        <i class="fas fa-arrow-left me-1"></i>Back
      </a>
    </div>

    <div class="row">
      {{-- Community Info --}}
      <div class="col-md-5 mb-4">
        <div class="card border-0 shadow h-100">
          <div class="card-header bg-white border-bottom"><h5 class="mb-0">Community Info</h5></div>
          <div class="card-body">
            <div class="text-center mb-4">
              @if($community->community_logo)
                <img src="{{ $community->community_logo }}" class="rounded-circle border" style="width:80px;height:80px;object-fit:cover;" alt="logo">
              @else
                <div class="rounded-circle bg-primary d-inline-flex align-items-center justify-content-center text-white fw-bold" style="width:80px;height:80px;font-size:28px;">
                  {{ strtoupper(substr($community->community_short_name ?? 'C', 0, 1)) }}
                </div>
              @endif
            </div>
            <table class="table table-borderless table-sm">
              <tr><th width="40%">ID</th><td>{{ $community->id }}</td></tr>
              <tr><th>Name</th><td class="fw-semibold">{{ $community->community_name }}</td></tr>
              <tr><th>Short Name</th><td>{{ $community->community_short_name }}</td></tr>
              <tr><th>Code</th><td><code class="fs-6">{{ $community->code }}</code></td></tr>
              <tr><th>Description</th><td>{{ $community->community_description ?: '—' }}</td></tr>
              <tr><th>Admin Designation</th><td>{{ $community->admin_designation ?: '—' }}</td></tr>
              <tr><th>Owner</th><td>
                @if($community->user)
                  <a href="{{ route('admin.users.show', $community->user_id) }}">
                    {{ $community->user->name ?? $community->user->email }}
                  </a>
                @else —
                @endif
              </td></tr>
              <tr><th>Created</th><td>{{ $community->created_at ? $community->created_at->format('d M Y H:i') : '—' }}</td></tr>
            </table>
          </div>
        </div>
      </div>

      {{-- Members of this community --}}
      <div class="col-md-7 mb-4">
        <div class="card border-0 shadow">
          <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Members <span class="badge bg-secondary">{{ $members->total() }}</span></h5>
          </div>
          <div class="table-responsive">
            <table class="table table-hover align-middle table-sm mb-0">
              <thead class="table-light">
                <tr>
                  <th>#</th>
                  <th>Name</th>
                  <th>Mobile</th>
                  <th>Status</th>
                  <th>Action</th>
                </tr>
              </thead>
              <tbody>
                @forelse($members as $u)
                <tr>
                  <td>{{ $u->id }}</td>
                  <td>{{ $u->name ?? ($u->email ?? '—') }}</td>
                  <td>{{ $u->mobile ?? '—' }}</td>
                  <td>
                    @php $s = $u->status ?? 'pending'; @endphp
                    <span class="badge bg-{{ $s==='accepted'?'success':($s==='rejected'?'danger':'warning') }}">{{ ucfirst($s) }}</span>
                  </td>
                  <td>
                    <a href="{{ route('admin.users.show', $u->id) }}" class="btn btn-xs btn-outline-primary" style="font-size:11px;padding:2px 8px;">View</a>
                  </td>
                </tr>
                @empty
                <tr><td colspan="5" class="text-center text-muted py-3">No members yet.</td></tr>
                @endforelse
              </tbody>
            </table>
          </div>
          @if($members->hasPages())
          <div class="card-footer bg-white">{{ $members->links() }}</div>
          @endif
        </div>
      </div>
    </div>

  </div>
</div>
</x-layouts.app>
