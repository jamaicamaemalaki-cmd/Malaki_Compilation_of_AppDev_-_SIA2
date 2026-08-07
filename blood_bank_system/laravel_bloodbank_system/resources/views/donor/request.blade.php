@extends('layouts.app')
@section('title', 'Request Donation | BloodLink')
@section('content')
<div class="soft-card p-4 p-md-5">
    <h1>Request Blood Donation Appointment</h1>
    <p class="text-secondary">Choose a Hinunangan facility. The facility will accept or reject and set the date and time range.</p>
    <form method="POST" action="{{ route('donor.request.store') }}">
        @csrf
        <div class="row g-3">
            @include('partials.facility-category-selects', [
                'facilities' => $facilities,
                'categoryLabel' => 'Facility Category',
                'facilityLabel' => 'Hinunangan Facility',
            ])
            <div class="col-md-6">
                <label class="form-label">Component</label>
                <select class="form-select" name="component"><option>Whole Blood</option><option>Platelets</option><option>Plasma</option></select>
            </div>
            <div class="col-md-6">
                <label class="form-label">Units</label>
                <input class="form-control" name="units" type="number" value="1" min="1" max="2">
            </div>
            <div class="col-12">
                <label class="form-label">Note</label>
                <textarea class="form-control" name="donor_note" rows="4"></textarea>
            </div>
        </div>
        <button class="btn btn-danger btn-lg w-100 mt-4">Submit Request</button>
    </form>
</div>
@endsection
