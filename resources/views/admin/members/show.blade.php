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
            <li class="breadcrumb-item"><a href="{{ route('admin.members.index') }}">Members</a></li>
            <li class="breadcrumb-item active">{{ $member->full_name }}</li>
          </ol>
        </nav>
        <h1 class="h3">Member Details</h1>
      </div>
      <a href="{{ route('admin.members.index') }}" class="btn btn-outline-secondary btn-sm"><i class="fas fa-arrow-left me-1"></i>Back</a>
    </div>

    <div class="row">

      {{-- Member Info --}}
      <div class="col-md-6 mb-4">
        <div class="card border-0 shadow">
          <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Personal Information</h5>
            @php $s = $member->user->status ?? 'pending'; @endphp
            <span class="badge bg-{{ $s==='accepted'?'success':($s==='rejected'?'danger':'warning') }} fs-6">{{ ucfirst($s) }}</span>
          </div>
          <div class="card-body">
            <table class="table table-borderless table-sm">
              <tr><th width="40%">Member ID</th><td>{{ $member->id }}</td></tr>
              <tr><th>Full Name</th><td class="fw-semibold">{{ $member->full_name }}</td></tr>
              <tr><th>Surname</th><td>{{ $member->surname ?? '—' }}</td></tr>
              <tr><th>Gender</th><td>{{ ucfirst($member->gender ?? '—') }}</td></tr>
              <tr><th>Date of Birth</th><td>{{ $member->date_of_birth ? \Carbon\Carbon::parse($member->date_of_birth)->format('d M Y') : '—' }}</td></tr>
              <tr><th>Marital Status</th><td>{{ ucfirst($member->marital_status ?? '—') }}</td></tr>
              <tr><th>Gotra</th><td>{{ $member->gotra ?? '—' }}</td></tr>
              <tr><th>Native Place</th><td>{{ $member->native_place ?? '—' }}</td></tr>
              <tr><th>Mobile</th><td>{{ $member->mobile_number ?? '—' }}</td></tr>
              <tr><th>Email</th><td>{{ $member->email ?? '—' }}</td></tr>
              <tr><th>Address</th><td>
                {{ $member->address_line_1 }}
                @if($member->address_line_2), {{ $member->address_line_2 }}@endif<br>
                {{ $member->city }}, {{ $member->state }} - {{ $member->pincode }}
              </td></tr>
              <tr><th>Submitted</th><td>{{ $member->created_at ? $member->created_at->format('d M Y H:i') : '—' }}</td></tr>
            </table>

            {{-- Status Update --}}
            <div class="border-top pt-3 mt-2">
              <h6>Update Verification Status</h6>
              <form method="POST" action="{{ route('admin.members.update-status', $member->id) }}" class="d-flex gap-2">
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

      {{-- User Account Info --}}
      <div class="col-md-6 mb-4">
        <div class="card border-0 shadow mb-4">
          <div class="card-header bg-white border-bottom"><h5 class="mb-0">User Account</h5></div>
          <div class="card-body">
            <table class="table table-borderless table-sm">
              <tr><th width="40%">User ID</th><td>{{ $member->user->id ?? '—' }}</td></tr>
              <tr><th>Name</th><td>{{ $member->user->name ?? '—' }}</td></tr>
              <tr><th>Email</th><td>{{ $member->user->email ?? '—' }}</td></tr>
              <tr><th>Mobile</th><td>{{ $member->user->mobile ?? '—' }}</td></tr>
              <tr><th>User Type</th><td>{{ ucfirst($member->user->user_type ?? '—') }}</td></tr>
              <tr><th>Community</th><td>{{ $member->user->community->community_name ?? '—' }}</td></tr>
            </table>
          </div>
        </div>

        {{-- Identity Documents --}}
        @if($member->identityDocuments && $member->identityDocuments->count())
        <div class="card border-0 shadow mb-4">
          <div class="card-header bg-white border-bottom"><h5 class="mb-0">Identity Documents</h5></div>
          <div class="card-body">
            @foreach($member->identityDocuments as $doc)
            <div class="mb-3">
              <p class="mb-1"><strong>Type:</strong> {{ $doc->document_type ?? '—' }}</p>
              @if($doc->document_path)
                <a href="{{ $doc->document_path }}" target="_blank">
                  <img src="{{ $doc->document_path }}" alt="Document" class="img-fluid rounded border" style="max-height:160px;">
                </a>
              @endif
            </div>
            @endforeach
          </div>
        </div>
        @endif
      </div>

    </div>

    {{-- Family Members --}}
    @if($member->familyMembers && $member->familyMembers->count())
    <div class="card border-0 shadow mb-4">
      <div class="card-header bg-white border-bottom">
        <h5 class="mb-0">Family Members <span class="badge bg-secondary">{{ $member->familyMembers->count() }}</span></h5>
      </div>
      <div class="table-responsive">
        <table class="table table-hover align-middle table-sm mb-0">
          <thead class="table-light">
            <tr>
              <th>#</th><th>Name</th><th>Relation</th><th>Mobile</th><th>Designation</th><th>Education</th>
            </tr>
          </thead>
          <tbody>
            @foreach($member->familyMembers as $i => $fm)
            <tr>
              <td>{{ $i + 1 }}</td>
              <td class="fw-semibold">{{ $fm->name ?? '—' }}</td>
              <td>{{ $fm->relation ?? '—' }}</td>
              <td>{{ $fm->mobile_number ?? '—' }}</td>
              <td>{{ $fm->designation ?? '—' }}</td>
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
</x-layouts.app>
