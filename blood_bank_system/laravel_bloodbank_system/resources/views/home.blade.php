@extends('layouts.app')
@section('title', 'BloodLink')
@section('content')
<section class="row justify-content-center py-4">
    <div class="col-lg-8 text-center">
        <h1 class="display-4">Hinunangan Blood Bank Management System</h1>
        <p class="lead text-secondary">A Laravel and MySQL system for donors, patients, medical facilities, and administrators.</p>
        <div class="d-flex gap-2 flex-wrap justify-content-center mt-4">
            <a class="btn btn-danger btn-lg" href="{{ route('register.choose') }}"><i class="bi bi-person-plus me-2"></i>Register</a>
            <a class="btn btn-outline-danger btn-lg" href="{{ route('login') }}"><i class="bi bi-box-arrow-in-right me-2"></i>Login</a>
        </div>
    </div>
</section>
@endsection
