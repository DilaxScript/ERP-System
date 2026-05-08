<div class="card card-body table-wrapper table-responsive border-0 shadow">
    <h2 class="text-center text-muted mb-3">{{ \Carbon\Carbon::today()->format("l, F d, Y") }}</h2>
  <table class="table-hover table">
    <thead>
      <tr>
        <th class="border-gray-200">#</th>
        <th class="border-gray-200">Employee ID</th>
        <th class="border-gray-200">Full Name</th>
        <th class="border-gray-200">Date</th>
        <th class="border-gray-200">Login Time</th>
        <th class="border-gray-200">Logout Time</th>
        <th class="border-gray-200">Status</th>
        <th class="border-gray-200">Action</th>
      </tr>
    </thead>
    <tbody>

      @forelse ($users as $user)
        <tr>
          <td class="fw-bold">{{ $loop->iteration }}</td>
          <td><span class="fw-normal">#{{ $user->id }}</span></td>
          <td><span class="fw-normal">{{ $user->full_name }}</span></td>
          <td><span class="fw-normal">{{ optional($user->attendance?->date)->format('Y-m-d') ?? '-' }}</span></td>
          <td>
            <span class="fw-normal">{{ $user->attendance && $user->attendance->login_time ? $user->attendance->login_time->format('H:i:s') : '-' }}</span>
            @if($user->attendance && $user->attendance->login_method)
              <div><span class="badge bg-light text-dark border">{{ $user->attendance->login_method_text }}</span></div>
            @endif
          </td>
          <td>
            <span class="fw-normal">{{ $user->attendance && $user->attendance->logout_time ? $user->attendance->logout_time->format('H:i:s') : '-' }}</span>
            @if($user->attendance && $user->attendance->logout_method)
              <div><span class="badge bg-light text-dark border">{{ $user->attendance->logout_method_text }}</span></div>
            @endif
          </td>
          <td><span class="fw-normal">{!! $user->attendance ? "<span class='badge " . ($user->attendance->status_text === 'At Work' ? "bg-success" : ($user->attendance->status_text === 'Absent' ? "bg-danger" : ($user->attendance->status_text === 'Late' ? "bg-warning text-dark" : "bg-secondary"))) . "'>" . e($user->attendance->status_text) . '</span>' : "<span class='text-danger'>Null</span>" !!}</span></td>
          <td class="inline-flex">
            <button style="font-size:0.8rem; padding:0.1 rem 0.2" class="btn me-2 btn-sm btn-success" wire:click='manualLogin({{ $user->id }})'>Mark Login</button>
            <button style="font-size:0.8rem; padding:0.1 rem 0.2" class="btn me-2 btn-sm btn-secondary" wire:click='emergencyLogout({{ $user->id }})'>Emergency Logout</button>
            <button style="font-size:0.8rem; padding:0.1 rem 0.2" class="btn me-3 btn-sm btn-danger" wire:click='attendance({{ $user->id }},1)'>Absent</button>
            <button style="font-size:0.8rem; padding:0.1 rem 0.2" class="btn me-3 btn-sm btn-gray-200" wire:click='attendance({{ $user->id }},2)'>Late</button>
          </td>
        </tr>
      @empty
      @endforelse



    </tbody>
  </table>
  <div class="card-footer mt-3 border-0 px-3">
    {{ $users->links() }}
  </div>
</div>
