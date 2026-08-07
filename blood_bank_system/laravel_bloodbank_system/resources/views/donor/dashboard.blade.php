@extends('layouts.app')
@section('title', 'Donor Dashboard | BloodLink')
@section('content')
<div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
    <div><h1>Donor Dashboard</h1><p class="lead text-secondary">Donate blood and monitor your request status.</p></div>
    <a class="btn btn-danger" href="{{ route('donor.request') }}"><i class="bi bi-heart-pulse me-2"></i>Request Donation</a>
</div>
<div class="row g-4">
    <div class="col-lg-5">
        <div class="soft-card p-4">
            <h2 class="h4">Your Donation Requests</h2>
            @forelse($requests as $request)
                <div class="border-bottom py-3">
                    <strong>{{ $request->facility_name }}</strong>
                    <div>{{ ucfirst($request->status) }} - {{ $request->blood_type }} {{ $request->component }}</div>
                    @if($request->scheduled_date)
                        <small class="text-secondary">{{ $request->scheduled_date->format('M d, Y') }} · {{ $request->formattedStartTime() }} - {{ $request->formattedEndTime() }}</small>
                    @endif
                </div>
            @empty
                <p class="text-secondary">No donation requests yet.</p>
            @endforelse
        </div>
    </div>
    <div class="col-lg-7"><div class="soft-card p-4"><h2 class="h4">Facility Blood Availability</h2>@include('partials.inventory-list', ['inventories' => $inventories])</div></div>
</div>
@endsection
