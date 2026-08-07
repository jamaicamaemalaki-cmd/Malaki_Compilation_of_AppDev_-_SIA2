@extends('layouts.app')
@section('title', 'Register | BloodLink')
@section('content')
@php
    $title = ['donor' => 'Donor Registration', 'facility' => 'Medical Facility Registration', 'patient' => 'Patient Registration', 'admin' => 'Admin Registration'][$role];
@endphp
<div class="soft-card p-4 p-md-5">
    <div class="text-center mb-4">
        <i class="bi bi-droplet-fill fs-2 text-danger"></i>
        <h1>{{ $title }}</h1>
    </div>
    <form method="POST" action="{{ route('register.store', $role) }}">
        @csrf
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label">{{ $role === 'facility' ? 'Contact Person Name' : 'Full Name' }}</label>
                <input class="form-control" name="name" value="{{ old('name') }}" required>
            </div>
            <div class="col-md-6">
                <label class="form-label">Email</label>
                <input class="form-control" name="email" type="email" value="{{ old('email') }}" required>
            </div>
            <div class="col-md-6">
                <label class="form-label">Password</label>
                <div class="input-group">
                    <input id="registerPassword" class="form-control" name="password" type="password" required>
                    <button class="btn btn-outline-danger" type="button" data-toggle-password="#registerPassword"><i class="bi bi-eye"></i></button>
                </div>
            </div>
            <div class="col-md-6">
                <label class="form-label">Phone</label>
                <input class="form-control" name="phone" value="{{ old('phone') }}">
            </div>
            <div class="col-md-12">
                <label class="form-label">Address</label>
                <input class="form-control" name="address" value="{{ old('address') }}">
            </div>
        </div>

        @if($role === 'facility')
            <h2 class="h4 section-title mt-5">Medical Facility Details</h2>
            <div class="row g-3">
                @include('partials.facility-category-selects', [
                    'facilities' => $facilities,
                    'categoryLabel' => 'Facility Category',
                    'facilityLabel' => 'Hinunangan Facility',
                    'showEmptyCategory' => true,
                ])
                <div class="col-md-4">
                    <label class="form-label">License Number</label>
                    <input class="form-control" name="license_number" value="{{ old('license_number') }}">
                </div>
            </div>
        @endif

        @if($role === 'donor')
            <h2 class="h4 section-title mt-5">Donor Declaration</h2>
            <div class="soft-card p-3 mb-3">
                <p class="fw-bold">I understand that I should not donate blood if:</p>
                <ol class="mb-4">
                    <li>I consume illegal drugs.</li>
                    <li>I have HIV/AIDS, Hepatitis B or C, Syphilis, or other sexually transmitted infections.</li>
                    <li>I am currently sick or medically unfit to donate.</li>
                </ol>
                <p class="fw-bold">I understand that donation depends on facility screening and medical approval.</p>
            </div>
            <div class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Blood Type</label>
                    <select class="form-select" name="blood_type" required>
                        @foreach(['A+','A-','B+','B-','AB+','AB-','O+','O-'] as $type)
                            <option value="{{ $type }}">{{ $type }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Age</label>
                    <input class="form-control" name="age" type="number" min="18" max="65" value="{{ old('age') }}" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Gender</label>
                    <select class="form-select" name="gender" required>
                        <option>Female</option><option>Male</option><option>Prefer not to say</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Weight (kg)</label>
                    <input class="form-control" name="weight" type="number" step="0.01" min="40" value="{{ old('weight') }}">
                </div>
            </div>
            <div class="form-check mt-3">
                <input class="form-check-input" type="checkbox" name="declaration_confirmed" value="1" required>
                <label class="form-check-label">I confirm that I read and comply with the donor declaration.</label>
            </div>
        @endif

        <button class="btn btn-danger btn-lg w-100 mt-4">Create Account</button>
    </form>
</div>
@endsection
