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
            <li class="breadcrumb-item"><a href="{{ route('admin.users.index') }}">Users</a></li>
            <li class="breadcrumb-item active">{{ $user->name ?? $user->email }}</li>
          </ol>
        </nav>
        <h1 class="h3">User Details</h1>
      </div>
      <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary btn-sm"><i class="fas fa-arrow-left me-1"></i>Back</a>
    </div>

    <div class="row">

      {{-- User Account --}}
      <div class="col-md-5 mb-4">
        <div class="card border-0 shadow">
          <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Account Info</h5>
            @php $s = $user->status ?? 'pending'; @endphp
            <span class="badge bg-{{ $s==='accepted'?'success':($s==='rejected'?'danger':'warning') }}">{{ ucfirst($s) }}</span>
          </div>
          <div class="card-body">
            {{-- Profile Image --}}
            <div class="text-center mb-3">
              @if($user->profile_image)
                <img src="{{ asset('storage/'.$user->profile_image) }}" class="rounded-circle border" style="width:80px;height:80px;object-fit:cover;" alt="profile">
              @else
                <div class="rounded-circle bg-primary d-inline-flex align-items-center justify-content-center text-white fw-bold" style="width:80px;height:80px;font-size:26px;">
                  {{ strtoupper(substr($user->name ?? 'U', 0, 1)) }}
                </div>
              @endif
            </div>
            <table class="table table-borderless table-sm">
              <tr><th width="40%">User ID</th><td>{{ $user->id }}</td></tr>
              <tr><th>Name</th><td class="fw-semibold">{{ $user->name ?? '—' }}</td></tr>
              <tr><th>Email</th><td>{{ $user->email ?? '—' }}</td></tr>
              <tr><th>Mobile</th><td>{{ $user->mobile ?? '—' }}</td></tr>
              <tr><th>User Type</th><td><span class="badge bg-{{ $user->user_type==='admin'?'danger':'primary' }}">{{ ucfirst($user->user_type ?? '—') }}</span></td></tr>
              <tr><th>Community</th><td>
                @if($user->community)
                  <a href="{{ route('admin.communities.show', $user->community_id) }}">{{ $user->community->community_name }}</a>
                @else —
                @endif
              </td></tr>
              <tr><th>DOB</th><td>{{ $user->dob ?? '—' }}</td></tr>
              <tr><th>Bio</th><td>{{ $user->bio ?? '—' }}</td></tr>
              <tr><th>Address</th><td>{{ $user->address ?? '—' }}</td></tr>
              <tr><th>Education</th><td>{{ $user->education ?? '—' }}</td></tr>
              <tr><th>Profession</th><td>{{ $user->profession_designation ?? '—' }}</td></tr>
              <tr><th>Website</th><td>{{ $user->website ?? '—' }}</td></tr>
              <tr><th>Registered</th><td>{{ $user->created_at ? $user->created_at->format('d M Y H:i') : '—' }}</td></tr>
            </table>

            {{-- Status Toggle --}}
            <div class="border-top pt-3 mt-2">
              <h6>Update Status</h6>
              <form method="POST" action="{{ route('admin.users.update-status', $user->id) }}" class="d-flex gap-2">
                @csrf
                <select name="status" class="form-select form-select-sm">
                  <option value="pending" {{ $s==='pending'?'selected':'' }}>Pending</option>
                  <option value="accepted" {{ $s==='accepted'?'selected':'' }}>Accepted</option>
                  <option value="rejected" {{ $s==='rejected'?'selected':'' }}>Rejected</option>
                </select>
                <button type="submit" class="btn btn-primary btn-sm">Update</button>
              </form>
            </div>
          </div>
        </div>
      </div>

      {{-- Member Info --}}
      <div class="col-md-7 mb-4">
        @if($user->member)
        <div class="card border-0 shadow mb-4">
          <div class="card-header bg-white border-bottom"><h5 class="mb-0">Member Verification</h5></div>
          <div class="card-body">
            <table class="table table-borderless table-sm">
              <tr><th width="40%">Full Name</th><td>{{ $user->member->full_name }}</td></tr>
              <tr><th>Surname / Gotra</th><td>{{ $user->member->surname }} / {{ $user->member->gotra }}</td></tr>
              <tr><th>Gender</th><td>{{ ucfirst($user->member->gender ?? '—') }}</td></tr>
              <tr><th>DOB</th><td>{{ $user->member->date_of_birth ? \Carbon\Carbon::parse($user->member->date_of_birth)->format('d M Y') : '—' }}</td></tr>
              <tr><th>Marital Status</th><td>{{ ucfirst($user->member->marital_status ?? '—') }}</td></tr>
              <tr><th>Native Place</th><td>{{ $user->member->native_place ?? '—' }}</td></tr>
              <tr><th>City / State</th><td>{{ $user->member->city }}, {{ $user->member->state }} - {{ $user->member->pincode }}</td></tr>
            </table>
            <a href="{{ route('admin.members.show', $user->member->id) }}" class="btn btn-sm btn-outline-primary">
              <i class="fas fa-id-card me-1"></i>Full Member Details
            </a>
          </div>
        </div>
        @else
        <div class="alert alert-light border">No member verification record submitted.</div>
        @endif

        {{-- Identity Documents --}}
        @if($user->member && $user->member->identityDocuments && $user->member->identityDocuments->count())
        <div class="card border-0 shadow mb-4">
          <div class="card-header bg-white border-bottom"><h5 class="mb-0">Identity Documents</h5></div>
          <div class="card-body">
            @foreach($user->member->identityDocuments as $doc)
            <div class="mb-3">
              <p class="mb-1"><strong>Type:</strong> {{ $doc->document_type ?? '—' }}</p>
              @if($doc->document_path)
                <a href="{{ $doc->document_path }}" target="_blank">
                  <img src="{{ $doc->document_path }}" alt="Document" class="img-fluid rounded border" style="max-height:140px;">
                </a>
              @endif
            </div>
            @endforeach
          </div>
        </div>
        @endif

        {{-- Family Members --}}
        @if($user->member && $user->member->familyMembers && $user->member->familyMembers->count())
        <div class="card border-0 shadow">
          <div class="card-header bg-white border-bottom">
            <h5 class="mb-0">Family Members <span class="badge bg-secondary">{{ $user->member->familyMembers->count() }}</span></h5>
          </div>
          <div class="table-responsive">
            <table class="table table-sm table-hover mb-0">
              <thead class="table-light"><tr><th>Name</th><th>Relation</th><th>Mobile</th><th>Education</th></tr></thead>
              <tbody>
                @foreach($user->member->familyMembers as $fm)
                <tr>
                  <td>{{ $fm->name }}</td>
                  <td>{{ $fm->relation }}</td>
                  <td>{{ $fm->mobile_number ?? '—' }}</td>
                  <td>{{ $fm->education ?? '—' }}</td>
                </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        </div>
        @endif
      </div>

    </div>
  </div>
</div>
</x-layouts.app>
