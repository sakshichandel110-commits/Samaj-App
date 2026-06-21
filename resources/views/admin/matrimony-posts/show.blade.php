<x-layouts.app>
<div class="py-4">
  <div class="container-fluid">

    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center py-3">
      <div>
        <nav aria-label="breadcrumb">
          <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.matrimony-posts.index') }}">Matrimony Posts</a></li>
            <li class="breadcrumb-item active">#{{ $post->id }}</li>
          </ol>
        </nav>
        <h1 class="h3">Matrimony Post Details</h1>
      </div>
      <div class="d-flex gap-2">
        <a href="{{ route('admin.matrimony-posts.index') }}" class="btn btn-outline-secondary btn-sm"><i class="fas fa-arrow-left me-1"></i>Back</a>
        <form method="POST" action="{{ route('admin.matrimony-posts.destroy', $post->id) }}" onsubmit="return confirm('Delete this post?')">
          @csrf @method('DELETE')
          <button type="submit" class="btn btn-danger btn-sm"><i class="fas fa-trash me-1"></i>Delete</button>
        </form>
      </div>
    </div>

    <div class="row">

      <div class="col-md-5 mb-4">
        <div class="card border-0 shadow">
          <div class="card-header bg-white border-bottom"><h5 class="mb-0">Post Details</h5></div>
          <div class="card-body">
            @if($post->image_path)
              <div class="text-center mb-3">
                <img src="{{ $post->image_path }}" alt="photo" class="rounded-circle border" style="width:100px;height:100px;object-fit:cover;">
              </div>
            @endif
            <table class="table table-borderless table-sm">
              <tr><th width="40%">ID</th><td>{{ $post->id }}</td></tr>
              <tr><th>Name</th><td class="fw-semibold">{{ $post->name ?? '—' }}</td></tr>
              <tr><th>Age</th><td>{{ $post->age ?? '—' }} years</td></tr>
              <tr><th>Occupation</th><td>{{ $post->occupation ?? '—' }}</td></tr>
              <tr><th>Bio</th><td>{{ $post->bio ?? '—' }}</td></tr>
              <tr><th>Likes</th><td><i class="fas fa-heart text-danger me-1"></i>{{ $post->likes_count ?? 0 }}</td></tr>
              <tr><th>Shares</th><td><i class="fas fa-share text-primary me-1"></i>{{ $post->shares_count ?? 0 }}</td></tr>
              <tr><th>Posted By</th><td>
                @if($post->user)
                  <a href="{{ route('admin.users.show', $post->user_id) }}">
                    {{ $post->user->name ?? $post->user->email }}
                  </a>
                @else —
                @endif
              </td></tr>
              <tr><th>Posted At</th><td>{{ $post->created_at ? $post->created_at->format('d M Y H:i') : '—' }}</td></tr>
            </table>
          </div>
        </div>
      </div>

      <div class="col-md-7 mb-4">
        <div class="card border-0 shadow">
          <div class="card-header bg-white border-bottom">
            <h5 class="mb-0">Reactions <span class="badge bg-secondary">{{ $post->reactions->count() }}</span></h5>
          </div>
          <div class="table-responsive">
            <table class="table table-sm table-hover mb-0">
              <thead class="table-light"><tr><th>User</th><th>Liked</th><th>Shared</th><th>Date</th></tr></thead>
              <tbody>
                @forelse($post->reactions as $r)
                <tr>
                  <td>{{ $r->user->name ?? ($r->user->email ?? '—') }}</td>
                  <td>
                    @if($r->liked)
                      <span class="badge bg-danger">❤ Yes</span>
                    @else
                      <span class="text-muted small">No</span>
                    @endif
                  </td>
                  <td>
                    @if($r->shared)
                      <span class="badge bg-primary">↗ Yes</span>
                    @else
                      <span class="text-muted small">No</span>
                    @endif
                  </td>
                  <td>{{ $r->created_at ? $r->created_at->format('d M Y') : '—' }}</td>
                </tr>
                @empty
                <tr><td colspan="4" class="text-muted text-center py-3">No reactions yet.</td></tr>
                @endforelse
              </tbody>
            </table>
          </div>
        </div>
      </div>

    </div>
  </div>
</div>
</x-layouts.app>
