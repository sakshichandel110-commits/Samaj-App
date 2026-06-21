<x-layouts.app>
<div class="py-4">
  <div class="container-fluid">

    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center py-3">
      <div>
        <nav aria-label="breadcrumb">
          <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.announcements.index') }}">Announcements</a></li>
            <li class="breadcrumb-item active">#{{ $announcement->id }}</li>
          </ol>
        </nav>
        <h1 class="h3">Announcement Details</h1>
      </div>
      <div class="d-flex gap-2">
        <a href="{{ route('admin.announcements.index') }}" class="btn btn-outline-secondary btn-sm"><i class="fas fa-arrow-left me-1"></i>Back</a>
        <form method="POST" action="{{ route('admin.announcements.destroy', $announcement->id) }}" onsubmit="return confirm('Delete this announcement?')">
          @csrf @method('DELETE')
          <button type="submit" class="btn btn-danger btn-sm"><i class="fas fa-trash me-1"></i>Delete</button>
        </form>
      </div>
    </div>

    <div class="row">

      <div class="col-md-7 mb-4">
        <div class="card border-0 shadow">
          <div class="card-header bg-white border-bottom"><h5 class="mb-0">Announcement</h5></div>
          <div class="card-body">
            @if($announcement->image_path)
              <img src="{{ $announcement->image_path }}" alt="image" class="img-fluid rounded mb-3" style="max-height:300px;object-fit:cover;width:100%;">
            @endif
            @if($announcement->type)
              <span class="badge bg-info text-dark mb-2">{{ $announcement->type }}</span>
            @endif
            <h4 class="fw-bold">{{ $announcement->title }}</h4>
            <p class="text-muted">{{ $announcement->description ?: 'No description.' }}</p>

            <div class="d-flex gap-4 mt-3 pt-3 border-top">
              <div class="text-center">
                <i class="fas fa-heart text-danger fs-5"></i>
                <div class="fw-bold">{{ $announcement->likes_count ?? 0 }}</div>
                <small class="text-muted">Likes</small>
              </div>
              <div class="text-center">
                <i class="fas fa-share text-primary fs-5"></i>
                <div class="fw-bold">{{ $announcement->shares_count ?? 0 }}</div>
                <small class="text-muted">Shares</small>
              </div>
              <div class="text-center">
                <i class="fas fa-comments text-success fs-5"></i>
                <div class="fw-bold">{{ $announcement->reactions->count() }}</div>
                <small class="text-muted">Reactions</small>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="col-md-5 mb-4">
        <div class="card border-0 shadow mb-4">
          <div class="card-header bg-white border-bottom"><h5 class="mb-0">Info</h5></div>
          <div class="card-body">
            <table class="table table-borderless table-sm">
              <tr><th width="40%">ID</th><td>{{ $announcement->id }}</td></tr>
              <tr><th>Posted By</th><td>
                @if($announcement->user)
                  <a href="{{ route('admin.users.show', $announcement->user_id) }}">
                    {{ $announcement->user->name ?? $announcement->user->email }}
                  </a>
                @else —
                @endif
              </td></tr>
              <tr><th>Posted At</th><td>{{ $announcement->created_at ? $announcement->created_at->format('d M Y H:i') : '—' }}</td></tr>
              <tr><th>Updated At</th><td>{{ $announcement->updated_at ? $announcement->updated_at->format('d M Y H:i') : '—' }}</td></tr>
            </table>
          </div>
        </div>

        {{-- Reactions --}}
        <div class="card border-0 shadow">
          <div class="card-header bg-white border-bottom">
            <h5 class="mb-0">Reactions <span class="badge bg-secondary">{{ $announcement->reactions->count() }}</span></h5>
          </div>
          <div class="table-responsive" style="max-height:300px;overflow-y:auto;">
            <table class="table table-sm table-hover mb-0">
              <thead class="table-light"><tr><th>User</th><th>Liked</th><th>Shared</th></tr></thead>
              <tbody>
                @forelse($announcement->reactions as $r)
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
                </tr>
                @empty
                <tr><td colspan="3" class="text-muted text-center py-3">No reactions yet.</td></tr>
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
