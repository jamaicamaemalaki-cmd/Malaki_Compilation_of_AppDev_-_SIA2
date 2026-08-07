@extends('layouts.app')
@section('title', 'Login | BloodLink')
@section('content')
<div class="row justify-content-center">
    <div class="col-lg-6">
        <div class="soft-card p-4 p-md-5">
            <h1 class="mb-2">Login</h1>
            <p class="text-secondary mb-4">Use your registered email and password. Your dashboard opens based on your saved role.</p>
            <form method="POST" action="{{ route('login.store') }}">
                @csrf
                <label class="form-label">Email</label>
                <input class="form-control mb-3" name="email" type="email" value="{{ old('email') }}" required>
                <label class="form-label">Password</label>
                <div class="input-group mb-4">
                    <input id="loginPassword" class="form-control" name="password" type="password" required>
                    <button class="btn btn-outline-danger" type="button" data-toggle-password="#loginPassword"><i class="bi bi-eye"></i></button>
                </div>
                <button class="btn btn-danger w-100 btn-lg">Login</button>
            </form>
        </div>
    </div>
</div>
@endsection
