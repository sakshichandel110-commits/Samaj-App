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
            <li class="breadcrumb-item active">Matrimony Posts</li>
          </ol>
        </nav>
        <h1 class="h3">Matrimony Posts</h1>
        <p class="text-muted mb-0">All matrimony posts created by community members.</p>
      </div>
    </div>

    {{-- Filters --}}
    <div class="card border-0 shadow mb-4">
      <div class="card-body py-2">
        <form method="GET" class="row g-2 align-items-end">
          <div class="col-md-4">
            <label class="form-label small mb-1">Search</label>
            <input type="text" name="search" value="{{ request('search') }}" class="form-control form-control-sm" placeholder="Name, occupation, bio...">
          </div>
          <div class="col-auto">
            <button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-search me-1"></i>Search</button>
            @if(request('search'))
              <a href="{{ route('admin.matrimony-posts.index') }}" class="btn btn-outline-secondary btn-sm ms-1">Clear</a>
            @endif
          </div>
        </form>
      </div>
    </div>

    {{-- Table --}}
    <div class="card border-0 shadow">
      <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Matrimony Posts <span class="badge bg-secondary ms-2">{{ $posts->total() }}</span></h5>
      </div>
      <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
          <thead class="table-light">
            <tr>
              <th>#</th>
              <th>Photo</th>
              <th>Name</th>
              <th>Age</th>
              <th>Occupation</th>
              <th>Posted By</th>
              <th>Likes</th>
              <th>Shares</th>
              <th>Posted</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            @forelse($posts as $post)
            <tr>
              <td>{{ $post->id }}</td>
              <td>
                @if($post->image_path)
                  <img src="{{ $post->image_path }}" alt="img" class="rounded-circle" style="width:40px;height:40px;object-fit:cover;">
                @else
                  <div class="rounded-circle bg-secondary d-inline-flex align-items-center justify-content-center text-white" style="width:40px;height:40px;font-size:14px;">
                    {{ strtoupper(substr($post->name ?? 'P', 0, 1)) }}
                  </div>
                @endif
              </td>
              <td class="fw-semibold">{{ $post->name ?? '—' }}</td>
              <td>{{ $post->age ?? '—' }}</td>
              <td>{{ $post->occupation ?? '—' }}</td>
              <td>{{ $post->user->name ?? ($post->user->email ?? '—') }}</td>
              <td><i class="fas fa-heart text-danger me-1"></i>{{ $post->likes_count ?? 0 }}</td>
              <td><i class="fas fa-share text-primary me-1"></i>{{ $post->shares_count ?? 0 }}</td>
              <td>{{ $post->created_at ? $post->created_at->format('d M Y') : '—' }}</td>
              <td class="d-flex gap-1">
                <a href="{{ route('admin.matrimony-posts.show', $post->id) }}" class="btn btn-sm btn-outline-primary"><i class="fas fa-eye"></i></a>
                <form method="POST" action="{{ route('admin.matrimony-posts.destroy', $post->id) }}" onsubmit="return confirm('Delete this post?')">
                  @csrf @method('DELETE')
                  <button type="submit" class="btn btn-sm btn-outline-danger"><i class="fas fa-trash"></i></button>
                </form>
              </td>
            </tr>
            @empty
            <tr><td colspan="10" class="text-center text-muted py-4">No matrimony posts found.</td></tr>
            @endforelse
          </tbody>
        </table>
      </div>
      @if($posts->hasPages())
      <div class="card-footer bg-white d-flex justify-content-between align-items-center">
        <small class="text-muted">Showing {{ $posts->firstItem() }}–{{ $posts->lastItem() }} of {{ $posts->total() }}</small>
        {{ $posts->links() }}
      </div>
      @endif
    </div>

  </div>
</div>
</x-layouts.app>
