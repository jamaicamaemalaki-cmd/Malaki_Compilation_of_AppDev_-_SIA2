<div class="table-responsive">
    <table class="table align-middle">
        <thead>
        <tr>
            <th>{{ ($showDestination ?? false) ? 'Request To' : 'Requester' }}</th>
            <th>Blood</th>
            <th>Units</th>
            <th>Urgency</th>
            <th>Status</th>
            @if($facilityActions ?? false)<th>Action</th>@endif
        </tr>
        </thead>
        <tbody>
        @forelse($requests as $request)
            <tr>
                <td>
                    @if($showDestination ?? false)
                        {{ $request->facility_name ?? 'Selected Facility' }}
                    @elseif($request->requester_role === 'facility')
                        {{ $request->requester->facility->facility_name ?? $request->requester->name ?? 'Medical Facility Request' }}
                    @else
                        {{ ucfirst($request->requester_role) }} Request
                    @endif
                </td>
                <td>{{ $request->blood_type }} {{ $request->component }}</td>
                <td>{{ $request->units }}</td>
                <td>{{ ucfirst($request->urgency) }}</td>
                <td><span class="badge text-bg-secondary">{{ ucfirst($request->status) }}</span></td>
                @if($facilityActions ?? false)
                    <td>
                        @if(in_array($request->status, ['rejected', 'released'], true))
                            <span class="text-secondary small">No action</span>
                        @else
                            <form class="d-flex gap-2" method="POST" action="{{ route('facility.addressed-requests.update', $request) }}">
                                @csrf @method('PATCH')
                                <select class="form-select form-select-sm" name="status">
                                    @if($request->status === 'approved')
                                        <option value="released">Released</option>
                                    @else
                                        <option value="approved">Approve</option>
                                        <option value="rejected">Reject</option>
                                    @endif
                                </select>
                                <button class="btn btn-sm btn-danger">Save</button>
                            </form>
                        @endif
                    </td>
                @endif
            </tr>
        @empty
            <tr><td colspan="{{ ($facilityActions ?? false) ? 6 : 5 }}" class="text-secondary">No requests yet.</td></tr>
        @endforelse
        </tbody>
    </table>
</div>
