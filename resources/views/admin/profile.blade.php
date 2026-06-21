<x-layouts.app>
<div class="py-4">
  <div class="container-fluid">

    {{-- Success message --}}
    @if(session('success'))
      <div class="alert alert-success alert-dismissible fade show">
        <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>
    @endif

    {{-- Validation errors --}}
    @if($errors->any())
      <div class="alert alert-danger alert-dismissible fade show">
        <i class="fas fa-exclamation-circle me-2"></i>
        <strong>Please fix the following errors:</strong>
        <ul class="mb-0 mt-1">
          @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
          @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>
    @endif

    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center py-3">
      <div>
        <nav aria-label="breadcrumb">
          <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">My Profile</li>
          </ol>
        </nav>
        <h1 class="h3">My Profile</h1>
        <p class="text-muted mb-0">Update your admin account information.</p>
      </div>
    </div>

    <div class="row">

      {{-- Profile Card --}}
      <div class="col-md-4 mb-4">
        <div class="card border-0 shadow text-center">
          <div class="card-body py-4">
            {{-- Avatar --}}
            @if($user->profile_image)
              <img src="{{ asset('storage/' . $user->profile_image) }}"
                   class="rounded-circle border mb-3"
                   style="width:90px;height:90px;object-fit:cover;" alt="Profile">
            @else
              <div class="rounded-circle bg-primary d-inline-flex align-items-center justify-content-center text-white fw-bold mb-3"
                   style="width:90px;height:90px;font-size:32px;">
                {{ strtoupper(substr($user->name ?? 'A', 0, 1)) }}
              </div>
            @endif

            <h4 class="fw-bold mb-0">{{ $user->name ?? 'Admin User' }}</h4>
            <p class="text-muted small mb-1">{{ $user->profession_designation ?? 'Administrator' }}</p>
            <span class="badge bg-danger">{{ ucfirst($user->user_type ?? 'admin') }}</span>

            <hr>
            <div class="text-start">
              <p class="mb-1 small"><i class="fas fa-envelope me-2 text-muted"></i>{{ $user->email ?? '—' }}</p>
              <p class="mb-1 small"><i class="fas fa-phone me-2 text-muted"></i>{{ $user->mobile ?? '—' }}</p>
              <p class="mb-1 small"><i class="fas fa-map-marker-alt me-2 text-muted"></i>{{ $user->address ?? '—' }}</p>
              @if($user->community)
                <p class="mb-0 small"><i class="fas fa-people-group me-2 text-muted"></i>{{ $user->community->community_name }}</p>
              @endif
            </div>
          </div>
        </div>
      </div>

      {{-- Edit Form --}}
      <div class="col-md-8 mb-4">
        <div class="card border-0 shadow">
          <div class="card-header bg-white border-bottom">
            <h5 class="mb-0">Edit Profile</h5>
          </div>
          <div class="card-body">
            <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data">
              @csrf

              <div class="row mb-3">
                <div class="col-md-6">
                  <label class="form-label">Full Name</label>
                  <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                         value="{{ old('name', $user->name) }}" placeholder="Your full name">
                  @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                  <label class="form-label">Date of Birth</label>
                  <input type="text" name="dob" class="form-control @error('dob') is-invalid @enderror"
                         value="{{ old('dob', $user->dob) }}" placeholder="DD/MM/YYYY">
                  @error('dob')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
              </div>

              <div class="row mb-3">
                <div class="col-md-6">
                  <label class="form-label">Email <span class="text-muted small">(read-only)</span></label>
                  <input type="email" class="form-control bg-light" value="{{ $user->email }}" disabled>
                </div>
                <div class="col-md-6">
                  <label class="form-label">Mobile <span class="text-muted small">(read-only)</span></label>
                  <input type="text" class="form-control bg-light" value="{{ $user->mobile }}" disabled>
                </div>
              </div>

              <div class="mb-3">
                <label class="form-label">Profession / Designation</label>
                <input type="text" name="profession_designation"
                       class="form-control @error('profession_designation') is-invalid @enderror"
                       value="{{ old('profession_designation', $user->profession_designation) }}"
                       placeholder="e.g. Community Admin, President">
                @error('profession_designation')<div class="invalid-feedback">{{ $message }}</div>@enderror
              </div>

              <div class="mb-3">
                <label class="form-label">Address</label>
                <input type="text" name="address" class="form-control @error('address') is-invalid @enderror"
                       value="{{ old('address', $user->address) }}" placeholder="Your address">
                @error('address')<div class="invalid-feedback">{{ $message }}</div>@enderror
              </div>

              <div class="row mb-3">
                <div class="col-md-6">
                  <label class="form-label">Education</label>
                  <input type="text" name="education" class="form-control @error('education') is-invalid @enderror"
                         value="{{ old('education', $user->education) }}" placeholder="e.g. B.Tech, MBA">
                  @error('education')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                  <label class="form-label">Website</label>
                  <input type="url" name="website" class="form-control @error('website') is-invalid @enderror"
                         value="{{ old('website', $user->website) }}" placeholder="https://...">
                  @error('website')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
              </div>

              <div class="mb-3">
                <label class="form-label">Bio</label>
                <textarea name="bio" rows="3" class="form-control @error('bio') is-invalid @enderror"
                          placeholder="A short description about yourself...">{{ old('bio', $user->bio) }}</textarea>
                @error('bio')<div class="invalid-feedback">{{ $message }}</div>@enderror
              </div>

              <div class="mb-4">
                <label class="form-label">Profile Photo</label>
                <input type="file" name="profile_image" class="form-control @error('profile_image') is-invalid @enderror"
                       accept="image/jpeg,image/png,image/jpg,image/gif">
                @error('profile_image')<div class="invalid-feedback">{{ $message }}</div>@enderror
                @if($user->profile_image)
                  <div class="mt-2">
                    <img src="{{ asset('storage/' . $user->profile_image) }}" alt="Current"
                         class="rounded border" style="height:50px;width:50px;object-fit:cover;">
                    <small class="text-muted ms-2">Current photo — upload new to replace</small>
                  </div>
                @endif
              </div>

              <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                  <i class="fas fa-save me-2"></i>Save Changes
                </button>
                <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary">Cancel</a>
              </div>

            </form>
          </div>
        </div>
      </div>

    </div>
  </div>
</div>
</x-layouts.app>
