@extends('layouts.app')
@section('title', 'Availability | BloodLink')
@section('content')
<div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
    <div>
        <h1>Blood Availability</h1>
        <p class="lead text-secondary">Full availability posted by Hinunangan medical facilities.</p>
    </div>
    @if(session('role') === 'facility')
        <a class="btn btn-danger" href="{{ route('facility.inventory') }}"><i class="bi bi-droplet me-2"></i>Update Availability</a>
    @endif
</div>

<div class="soft-card p-4">
    <div class="row g-3 mb-4">
        <div class="col-md-6">
            <input id="availabilitySearch" class="form-control" placeholder="Search facility, blood type, or component">
        </div>
        <div class="col-md-3">
            <select id="bloodTypeFilter" class="form-select">
                <option value="">All blood types</option>
                @foreach(\App\Services\BloodTypes::all() as $type)
                    <option value="{{ $type }}">{{ $type }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table align-middle" id="availabilityTable">
            <thead>
            <tr>
                <th>Facility</th>
                <th>Blood Type</th>
                <th>Component</th>
                <th>Units</th>
                <th>Status</th>
            </tr>
            </thead>
            <tbody>
            @forelse($inventories as $item)
                <tr data-search="{{ strtolower($item->facility_name.' '.$item->blood_type.' '.$item->component) }}" data-type="{{ $item->blood_type }}">
                    <td>{{ $item->facility_name }}</td>
                    <td><strong>{{ $item->blood_type }}</strong></td>
                    <td>{{ $item->component }}</td>
                    <td>{{ $item->units_available }}</td>
                    <td>
                        <span class="badge {{ $item->units_available <= 3 ? 'text-bg-warning' : 'text-bg-success' }}">
                            {{ $item->units_available <= 3 ? 'Low Stock' : 'Available' }}
                        </span>
                    </td>
                </tr>
            @empty
                <tr><td colspan="5" class="text-secondary">No blood availability has been posted yet.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>

<script>
    const search = document.getElementById('availabilitySearch');
    const type = document.getElementById('bloodTypeFilter');
    const rows = document.querySelectorAll('#availabilityTable tbody tr[data-search]');

    function filterAvailability() {
        const text = search.value.toLowerCase();
        const selectedType = type.value;

        rows.forEach((row) => {
            const matchesText = row.dataset.search.includes(text);
            const matchesType = !selectedType || row.dataset.type === selectedType;
            row.style.display = matchesText && matchesType ? '' : 'none';
        });
    }

    search.addEventListener('input', filterAvailability);
    type.addEventListener('change', filterAvailability);
</script>
@endsection
