@extends('layouts.app')
@section('title', 'Choose Role | BloodLink')
@section('content')
<div class="container py-5">
    <div class="text-center mb-5">
        <h1 class="display-5 mb-3">Choose Registration Role</h1>
        <p class="lead text-secondary mx-auto" style="max-width: 600px;">Select the type of account that fits your needs to get started with the BloodLink network.</p>
    </div>
    
    <div class="row g-4 justify-content-center">
        <div class="col-lg-6 col-md-8">
            <a class="card-link h-100" href="{{ route('register.form', 'donor') }}">
                <div class="mb-4">
                    <div class="d-inline-flex p-4 rounded-circle bg-soft text-danger mb-2">
                        <i class="bi bi-heart-pulse display-4"></i>
                    </div>
                </div>
                <h2 class="h3 mb-3">Register as Donor</h2>
                <p class="text-secondary mb-0 fs-5">Join our network of life-savers. Register now to check your eligibility and start scheduling your blood donations at any Hinunangan facility.</p>
            </a>
        </div>
    </div>
</div>
@endsection
