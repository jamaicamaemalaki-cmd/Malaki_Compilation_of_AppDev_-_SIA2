@extends('layouts.app')
@section('title', 'Medical Facility Dashboard | BloodLink')
@section('content')
@php
    $pendingDonors = $donorRequests->where('status', 'pending');
    $scheduledDonors = $donorRequests->whereIn('status', ['approved', 'completed']);
@endphp

<div class="facility-dashboard">
    <header class="dashboard-hero soft-card">
        <div class="dashboard-hero__content">
            <div class="dashboard-hero__badge">
                <i class="bi bi-hospital"></i>
                <span>Medical Facility</span>
            </div>
            <h1 class="dashboard-hero__title">Facility Dashboard</h1>
            <p class="dashboard-hero__subtitle mb-0">{{ $facility->facility_name }}</p>
        </div>
        <div class="dashboard-hero__actions">
            <a class="btn btn-outline-danger" href="{{ route('facility.inventory') }}">
                <i class="bi bi-droplet me-2"></i>Update Availability
            </a>
            <a class="btn btn-danger" href="{{ route('facility.request-blood') }}">
                <i class="bi bi-plus-circle me-2"></i>Request Blood
            </a>
        </div>
    </header>

    <div class="row g-3 g-lg-4 dashboard-stats">
        <div class="col-6 col-lg-3">
            <div class="stat-card stat-card--primary">
                <div class="stat-card__icon"><i class="bi bi-send"></i></div>
                <div class="stat-card__body">
                    <span class="stat-card__label">Your Requests</span>
                    <span class="stat-card__value">{{ $bloodRequests->count() }}</span>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="stat-card stat-card--accent">
                <div class="stat-card__icon"><i class="bi bi-inbox"></i></div>
                <div class="stat-card__body">
                    <span class="stat-card__label">Facility Requests</span>
                    <span class="stat-card__value">{{ $incomingFacilityRequests->count() }}</span>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="stat-card stat-card--warning">
                <div class="stat-card__icon"><i class="bi bi-people"></i></div>
                <div class="stat-card__body">
                    <span class="stat-card__label">Pending Donors</span>
                    <span class="stat-card__value">{{ $pendingDonorRequests }}</span>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="stat-card stat-card--success">
                <div class="stat-card__icon"><i class="bi bi-box-seam"></i></div>
                <div class="stat-card__body">
                    <span class="stat-card__label">Inventory Items</span>
                    <span class="stat-card__value">{{ $inventories->count() }}</span>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-6">
            <section class="panel-card soft-card h-100">
                <div class="panel-card__header">
                    <div>
                        <h2 class="panel-card__title">Your Sent Blood Requests</h2>
                        <p class="panel-card__desc mb-0">Requests you sent to other facilities</p>
                    </div>
                    <a class="btn btn-sm btn-outline-danger" href="{{ route('facility.request-blood') }}">New Request</a>
                </div>
                <div class="panel-card__body">
                    @if($bloodRequests->isEmpty())
                        <div class="empty-state">
                            <i class="bi bi-inbox"></i>
                            <p>No blood requests sent yet.</p>
                        </div>
                    @else
                        <div class="dashboard-table-wrap">
                            @include('partials.requests-table', [
                                'requests' => $bloodRequests->take(5),
                                'showDestination' => true,
                            ])
                        </div>
                    @endif
                </div>
            </section>
        </div>

        <div class="col-lg-6">
            <section class="panel-card soft-card h-100">
                <div class="panel-card__header">
                    <div>
                        <h2 class="panel-card__title">Donor Appointments</h2>
                        <p class="panel-card__desc mb-0">Pending and scheduled donation requests</p>
                    </div>
                    <a class="btn btn-sm btn-danger" href="{{ route('facility.donor-requests') }}">Manage All</a>
                </div>
                <div class="panel-card__body">
                    <div class="list-section">
                        <h3 class="list-section__title">Pending</h3>
                        @forelse($pendingDonors->take(3) as $request)
                            <div class="list-item">
                                <div class="list-item__main">
                                    <strong>{{ $request->donor->user->name ?? 'Donor' }}</strong>
                                    <span class="text-secondary">{{ $request->blood_type }} · {{ $request->component }}</span>
                                </div>
                                <span class="badge badge-status badge-status--pending">Pending</span>
                            </div>
                        @empty
                            <p class="empty-state-inline">No pending donor requests.</p>
                        @endforelse
                    </div>

                    <div class="list-section mt-4">
                        <h3 class="list-section__title">Scheduled</h3>
                        @forelse($scheduledDonors->take(3) as $request)
                            <div class="list-item">
                                <div class="list-item__main">
                                    <strong>{{ $request->donor->user->name ?? 'Donor' }}</strong>
                                    <span class="text-secondary">
                                        {{ $request->blood_type }}
                                        @if($request->scheduled_date)
                                            · {{ $request->scheduled_date->format('M d, Y') }}
                                            @if($request->formattedStartTime())
                                                {{ $request->formattedStartTime() }}–{{ $request->formattedEndTime() }}
                                            @endif
                                        @endif
                                    </span>
                                </div>
                                <span class="badge badge-status badge-status--{{ $request->status === 'completed' ? 'completed' : 'approved' }}">
                                    {{ ucfirst($request->status) }}
                                </span>
                            </div>
                        @empty
                            <p class="empty-state-inline">No scheduled appointments yet.</p>
                        @endforelse
                    </div>
                </div>
            </section>
        </div>

        <div class="col-12">
            <section class="panel-card soft-card">
                <div class="panel-card__header">
                    <div>
                        <h2 class="panel-card__title">Facility Requests from Other Facilities</h2>
                        <p class="panel-card__desc mb-0">Incoming blood requests addressed to your facility</p>
                    </div>
                    <a class="btn btn-sm btn-danger" href="{{ route('facility.requests') }}">
                        View All
                        @if($incomingFacilityRequests->count() > 0)
                            <span class="badge bg-white text-danger ms-1">{{ $incomingFacilityRequests->count() }}</span>
                        @endif
                    </a>
                </div>
                <div class="panel-card__body">
                    @if($incomingFacilityRequests->isEmpty())
                        <div class="empty-state">
                            <i class="bi bi-building"></i>
                            <p>No incoming facility requests at the moment.</p>
                        </div>
                    @else
                        <div class="dashboard-table-wrap">
                            @include('partials.requests-table', [
                                'requests' => $incomingFacilityRequests->take(5),
                                'facilityActions' => true,
                            ])
                        </div>
                    @endif
                </div>
            </section>
        </div>
    </div>
</div>
@endsection
