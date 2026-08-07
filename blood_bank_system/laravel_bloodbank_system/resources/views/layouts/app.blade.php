<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'BloodLink')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="{{ asset('css/bloodbank.css') }}" rel="stylesheet">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark py-3">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center gap-3 fw-bold fs-3" href="{{ route('home') }}">
            <span class="brand-mark"><i class="bi bi-droplet-fill"></i></span> BloodLink
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="mainNav">
            @php
                $isLoginScreen = ($on_login_page ?? false) || request()->routeIs('login');
            @endphp
            <div class="navbar-nav ms-auto align-items-lg-center gap-lg-2">
                @unless(request()->routeIs('home') && ! session('user_id'))
                    <a class="nav-link" href="{{ route('availability') }}">Availability</a>
                @endunless
                @if($isLoginScreen)
                    <a class="btn btn-light text-danger fw-bold" href="{{ route('register.choose') }}">Register</a>
                @endif
                @if(session('role') === 'donor')
                    <a class="nav-link" href="{{ route('donor.request') }}">Donate</a>
                @endif
                @if(session('role') === 'facility')
                    <a class="nav-link" href="{{ route('facility.request-blood') }}">Request Blood</a>
                    <a class="nav-link" href="{{ route('facility.requests') }}">
                        Facility Requests
                        @if(($incomingBloodRequests ?? 0) > 0)<span class="badge-notify">{{ $incomingBloodRequests }}</span>@endif
                    </a>
                    <a class="nav-link" href="{{ route('facility.donor-requests') }}">
                        Donor Requests
                        @if(($pendingDonorRequests ?? 0) > 0)<span class="badge-notify">{{ $pendingDonorRequests }}</span>@endif
                    </a>
                    <a class="nav-link" href="{{ route('facility.inventory') }}">Inventory</a>
                @endif
                @if(session('role') === 'admin')
                    <a class="nav-link" href="{{ route('admin.requests') }}">Monitor Requests</a>
                    <a class="nav-link" href="{{ route('admin.reports') }}">Reports</a>
                @endif
                @if(session('user_id') && ! $isLoginScreen)
                    <a class="nav-link" href="{{ route('dashboard') }}">Dashboard</a>
                    <form method="POST" action="{{ route('logout') }}">@csrf<button class="btn btn-light text-danger fw-bold">Logout</button></form>
                @elseif(! session('user_id') && ! $isLoginScreen)
                    <a class="nav-link" href="{{ route('login') }}">Login</a>
                    <a class="btn btn-light text-danger fw-bold" href="{{ route('register.choose') }}">Register</a>
                @endif
            </div>
        </div>
    </div>
</nav>

<main class="container py-4">
    @include('partials.alerts')
    @yield('content')
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    document.querySelectorAll('[data-toggle-password]').forEach((button) => {
        button.addEventListener('click', () => {
            const input = document.querySelector(button.dataset.togglePassword);
            input.type = input.type === 'password' ? 'text' : 'password';
            button.innerHTML = input.type === 'password' ? '<i class="bi bi-eye"></i>' : '<i class="bi bi-eye-slash"></i>';
        });
    });
</script>
</body>
</html>
