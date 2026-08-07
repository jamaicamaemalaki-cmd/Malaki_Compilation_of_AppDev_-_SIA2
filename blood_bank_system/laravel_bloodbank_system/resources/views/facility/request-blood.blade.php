@extends('layouts.app')
@section('title', 'Request Blood | BloodLink')
@section('content')
<div class="soft-card p-4 p-md-5">
    <h1>Request Blood</h1>
    <p class="text-secondary">Choose the source facility that will review and release the request.</p>
    <form method="POST" action="{{ route('facility.request-blood.store') }}">
        @csrf
        <div class="row g-3 mb-3">
            @include('partials.facility-category-selects', [
                'facilities' => $facilities,
                'categoryLabel' => 'Request From Category',
                'facilityLabel' => 'Request From Facility',
            ])
        </div>
        @include('partials.blood-request-fields')
        <button class="btn btn-danger btn-lg w-100 mt-4">Submit Request</button>
    </form>
</div>
@endsection
