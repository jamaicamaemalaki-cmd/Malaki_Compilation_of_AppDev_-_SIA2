@extends('layouts.app')
@section('title', 'Monitor Blood Requests | BloodLink')
@section('content')
<div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
    <div>
        <h1>Monitor Blood Requests</h1>
        <p class="lead text-secondary mb-0">View-only overview. Facilities handle approve, reject, and release.</p>
    </div>
    <a class="btn btn-outline-danger" href="{{ route('dashboard') }}">Back to Dashboard</a>
</div>
<div class="soft-card p-4 dashboard-table-wrap">
    @include('partials.requests-table', ['requests' => $requests])
</div>
@endsection
