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
            <li class="breadcrumb-item active">Announcements</li>
          </ol>
        </nav>
        <h1 class="h3">Announcements</h1>
        <p class="text-muted mb-0">All announcements posted by community members.</p>
      </div>
    </div>

    {{-- Filters --}}
    <div class="card border-0 shadow mb-4">
      <div class="card-body py-2">
        <form method="GET" class="row g-2 align-items-end">
          <div class="col-md-4">
            <label class="form-label small mb-1">Search</label>
            <input type="text" name="search" value="{{ request('search') }}" class="form-control form-control-sm" placeholder="Title, description...">
          </div>
          @if($types->count())
          <div class="col-md-3">
            <label class="form-label small mb-1">Type</label>
            <select name="type" class="form-select form-select-sm">
              <option value="">All Types</option>
              @foreach($types as $type)
                <option value="{{ $type }}" {{ request('type')===$type?'selected':'' }}>{{ $type }}</option>
              @endforeach
            </select>
          </div>
          @endif
          <div class="col-auto">
            <button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-filter me-1"></i>Filter</button>
            @if(request()->hasAny(['search','type']))
              <a href="{{ route('admin.announcements.index') }}" class="btn btn-outline-secondary btn-sm ms-1">Clear</a>
            @endif
          </div>
        </form>
      </div>
    </div>

    {{-- Table --}}
    <div class="card border-0 shadow">
      <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Announcements <span class="badge bg-secondary ms-2">{{ $announcements->total() }}</span></h5>
      </div>
      <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
          <thead class="table-light">
            <tr>
              <th>#</th>
              <th>Title</th>
              <th>Type</th>
              <th>Author</th>
              <th>Likes</th>
              <th>Shares</th>
              <th>Image</th>
              <th>Posted</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            @forelse($announcements as $ann)
            <tr>
              <td>{{ $ann->id }}</td>
              <td class="fw-semibold" style="max-width:200px;">{{ Str::limit($ann->title, 50) }}</td>
              <td>{{ $ann->type ? '<span class="badge bg-info text-dark">'.e($ann->type).'</span>' : '—' }}</td>
              <td>{{ $ann->user->name ?? ($ann->user->email ?? '—') }}</td>
              <td><i class="fas fa-heart text-danger me-1"></i>{{ $ann->likes_count ?? 0 }}</td>
              <td><i class="fas fa-share text-primary me-1"></i>{{ $ann->shares_count ?? 0 }}</td>
              <td>
                @if($ann->image_path)
                  <img src="{{ $ann->image_path }}" alt="img" class="rounded" style="width:40px;height:40px;object-fit:cover;">
                @else
                  <span class="text-muted small">—</span>
                @endif
              </td>
              <td>{{ $ann->created_at ? $ann->created_at->format('d M Y') : '—' }}</td>
              <td class="d-flex gap-1">
                <a href="{{ route('admin.announcements.show', $ann->id) }}" class="btn btn-sm btn-outline-primary"><i class="fas fa-eye"></i></a>
                <form method="POST" action="{{ route('admin.announcements.destroy', $ann->id) }}" onsubmit="return confirm('Delete this announcement?')">
                  @csrf @method('DELETE')
                  <button type="submit" class="btn btn-sm btn-outline-danger"><i class="fas fa-trash"></i></button>
                </form>
              </td>
            </tr>
            @empty
            <tr><td colspan="9" class="text-center text-muted py-4">No announcements found.</td></tr>
            @endforelse
          </tbody>
        </table>
      </div>
      @if($announcements->hasPages())
      <div class="card-footer bg-white d-flex justify-content-between align-items-center">
        <small class="text-muted">Showing {{ $announcements->firstItem() }}–{{ $announcements->lastItem() }} of {{ $announcements->total() }}</small>
        {{ $announcements->links() }}
      </div>
      @endif
    </div>

  </div>
</div>
</x-layouts.app>
