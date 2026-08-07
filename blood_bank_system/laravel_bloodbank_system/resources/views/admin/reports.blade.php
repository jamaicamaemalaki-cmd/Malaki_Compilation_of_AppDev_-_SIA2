@extends('layouts.app')
@section('title', 'Reports | BloodLink')
@section('content')
<h1>Reports</h1>
<p class="lead text-secondary">Monitoring overview — donors, facilities, inventory, and donations.</p>

<div class="row g-4 mb-4">
    <div class="col-lg-6">
        <div class="soft-card p-4">
            <h2 class="h4 mb-3">User Registrations (Current Year)</h2>
            <canvas id="usersChart" height="200"></canvas>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="soft-card p-4">
            <h2 class="h4 mb-3">Blood Requests (Current Year)</h2>
            <canvas id="requestsChart" height="200"></canvas>
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-6"><div class="soft-card p-4"><h2 class="h4">Donors</h2><p class="stat">{{ $donors->count() }}</p></div></div>
    <div class="col-lg-6"><div class="soft-card p-4"><h2 class="h4">Facilities</h2><p class="stat">{{ $facilities->count() }}</p></div></div>
    <div class="col-lg-6">
        <div class="soft-card p-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h2 class="h4 mb-0">Inventory</h2>
                <a href="{{ route('availability') }}" class="btn btn-sm btn-outline-danger">View All</a>
            </div>
            @include('partials.inventory-list', ['inventories' => $inventory])
        </div>
    </div>
    <div class="col-lg-6"><div class="soft-card p-4"><h2 class="h4">Donations</h2>
        @forelse($donations as $donation)
            <div class="border-bottom py-2">{{ $donation->donor->user->name ?? 'Donor' }} donated {{ $donation->units }} {{ $donation->blood_type }} at {{ $donation->facility_name }}</div>
        @empty <p class="text-secondary">No donations yet.</p> @endforelse
    </div></div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];

    // Users Chart
    new Chart(document.getElementById('usersChart'), {
        type: 'line',
        data: {
            labels: months,
            datasets: [{
                label: 'New Users',
                data: @json($usersChartData),
                borderColor: '#dc3545',
                backgroundColor: 'rgba(220, 53, 69, 0.1)',
                fill: true,
                tension: 0.3
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { display: false } },
            scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } }
        }
    });

    // Requests Chart
    new Chart(document.getElementById('requestsChart'), {
        type: 'bar',
        data: {
            labels: months,
            datasets: [{
                label: 'Requests',
                data: @json($requestsChartData),
                backgroundColor: '#dc3545',
                borderRadius: 5
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { display: false } },
            scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } }
        }
    });
</script>
@endsection
