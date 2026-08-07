@extends('layouts.app')
@section('title', 'Facility Requests | BloodLink')
@section('content')
<div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
    <div>
        <h1>Facility Requests</h1>
        <p class="lead text-secondary mb-0">
            Blood requests from other facilities addressed to <strong>{{ $facility->facility_name }}</strong>.
        </p>
    </div>
    <a class="btn btn-outline-danger" href="{{ route('dashboard') }}">Back to Dashboard</a>
</div>

<div class="soft-card p-4">
    @include('partials.requests-table', [
        'requests' => $requests,
        'facilityActions' => true,
    ])
</div>
@endsection
