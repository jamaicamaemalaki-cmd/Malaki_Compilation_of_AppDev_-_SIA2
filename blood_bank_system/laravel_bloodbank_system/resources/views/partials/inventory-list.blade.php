@forelse($inventories as $item)
    <div class="d-flex justify-content-between gap-3 border-bottom py-3">
        <div>
            <strong>{{ $item->blood_type }} {{ $item->component }}</strong>
            <div class="text-secondary">{{ $item->facility_name }}</div>
        </div>
        <span class="badge {{ $item->units_available <= 3 ? 'text-bg-warning' : 'text-bg-success' }} align-self-center">{{ $item->units_available }} units</span>
    </div>
@empty
    <p class="text-secondary mb-0">No records yet.</p>
@endforelse
