@extends('layouts.app')
@section('title', 'Donor Requests | BloodLink')
@section('content')
<h1 class="mb-4">Donor Requests</h1>

<div class="soft-card p-4 mb-4">
    <h2 class="h4 mb-3">Pending Donor Requests</h2>
    @forelse($pendingRequests as $request)
        <div class="border-bottom py-4">
            <div class="row g-3 align-items-end">
                <div class="col-lg-4">
                    <h3 class="h5 mb-1">{{ $request->donor->user->name ?? 'Donor' }}</h3>
                    <div>{{ $request->blood_type }} {{ $request->component }} · {{ $request->units }} unit(s)</div>
                    @if($request->donor_note)
                        <small class="text-secondary d-block mt-1">{{ $request->donor_note }}</small>
                    @endif
                </div>
                <div class="col-lg-8">
                    <form class="row g-2" method="POST" action="{{ route('facility.donor-requests.update', $request) }}">
                        @csrf
                        @method('PATCH')
                        <div class="col-md-2">
                            <label class="form-label">Status</label>
                            <select class="form-select" name="status">
                                <option value="approved">Approve</option>
                                <option value="rejected">Reject</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Date</label>
                            <input class="form-control" name="scheduled_date" type="date">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Start</label>
                            <input class="form-control" name="start_time" type="time">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">End</label>
                            <input class="form-control" name="end_time" type="time">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Note</label>
                            <input class="form-control" name="facility_note" placeholder="Optional note">
                        </div>
                        <div class="col-12">
                            <button class="btn btn-danger">Save Schedule</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @empty
        <p class="text-secondary mb-0">No pending donor requests.</p>
    @endforelse
</div>

<div class="soft-card p-4">
    <h2 class="h4 mb-3">Scheduled Donors</h2>
    @forelse($scheduledRequests as $request)
        <div class="border-bottom py-3">
            <div class="row g-3 align-items-center">
                <div class="col-lg-5">
                    <strong>{{ $request->donor->user->name ?? 'Donor' }}</strong>
                    <div>{{ $request->blood_type }} {{ $request->component }} · {{ $request->units }} unit(s)</div>
                    @if($request->facility_note)
                        <small class="text-secondary">{{ $request->facility_note }}</small>
                    @endif
                </div>
                <div class="col-lg-4">
                    @if($request->scheduled_date)
                        <div class="fw-semibold">{{ $request->scheduled_date->format('M d, Y') }}</div>
                        <small class="text-secondary">
                            {{ $request->formattedStartTime() }} - {{ $request->formattedEndTime() }}
                        </small>
                    @else
                        <span class="text-secondary">No schedule set</span>
                    @endif
                </div>
                <div class="col-lg-3 text-lg-end">
                    <span class="badge {{ $request->status === 'completed' ? 'text-bg-success' : 'text-bg-primary' }}">
                        {{ ucfirst($request->status) }}
                    </span>
                    @if($request->status === 'approved')
                        <form class="d-inline-block ms-2" method="POST" action="{{ route('facility.donor-requests.update', $request) }}">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="status" value="completed">
                            <input type="hidden" name="scheduled_date" value="{{ $request->scheduled_date?->format('Y-m-d') }}">
                            <input type="hidden" name="start_time" value="{{ $request->formattedStartTime() }}">
                            <input type="hidden" name="end_time" value="{{ $request->formattedEndTime() }}">
                            <input type="hidden" name="facility_note" value="{{ $request->facility_note }}">
                            <button class="btn btn-sm btn-outline-success" type="submit">Mark Completed</button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    @empty
        <p class="text-secondary mb-0">No scheduled donors yet. Saved schedules will appear here.</p>
    @endforelse
</div>

@if($rejectedRequests->isNotEmpty())
    <div class="soft-card p-4 mt-4">
        <h2 class="h4 mb-3">Rejected Requests</h2>
        @foreach($rejectedRequests as $request)
            <div class="border-bottom py-3">
                <strong>{{ $request->donor->user->name ?? 'Donor' }}</strong>
                <div class="text-secondary">{{ $request->blood_type }} {{ $request->component }}</div>
            </div>
        @endforeach
    </div>
@endif
@endsection
