<div class="row g-3">
    <div class="col-md-3">
        <label class="form-label">Blood Type</label>
        <select class="form-select" name="blood_type" required>
            @foreach(['A+','A-','B+','B-','AB+','AB-','O+','O-'] as $type)<option value="{{ $type }}">{{ $type }}</option>@endforeach
        </select>
    </div>
    <div class="col-md-3">
        <label class="form-label">Component</label>
        <select class="form-select" name="component"><option>Whole Blood</option><option>Platelets</option><option>Plasma</option></select>
    </div>
    <div class="col-md-3">
        <label class="form-label">Units</label>
        <input class="form-control" name="units" type="number" min="1" value="1" required>
    </div>
    <div class="col-md-3">
        <label class="form-label">Urgency</label>
        <select class="form-select" name="urgency"><option value="low">Low</option><option value="medium">Medium</option><option value="high">High</option><option value="critical">Critical</option></select>
    </div>
    <div class="col-12">
        <label class="form-label">Reason</label>
        <textarea class="form-control" name="reason" rows="4"></textarea>
    </div>
</div>
