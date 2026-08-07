@php
    $categoryLabel = $categoryLabel ?? 'Facility Category';
    $facilityLabel = $facilityLabel ?? 'Facility';
    $categoryField = $categoryField ?? 'facility_category';
    $facilityField = $facilityField ?? 'facility_name';
    $oldCategory = old($categoryField, $oldCategory ?? '');
    $oldFacility = old($facilityField, $oldFacility ?? '');
@endphp
<div class="col-md-6">
    <label class="form-label">{{ $categoryLabel }}</label>
    <select class="form-select facility-category-select" name="{{ $categoryField }}" required>
        @if($showEmptyCategory ?? false)
            <option value="">Choose category</option>
        @endif
        @foreach($facilities as $category => $names)
            <option value="{{ $category }}" @selected($oldCategory === $category)>{{ $category }}</option>
        @endforeach
    </select>
</div>
<div class="col-md-6">
    <label class="form-label">{{ $facilityLabel }}</label>
    <select class="form-select facility-name-select" name="{{ $facilityField }}" required>
        <option value="">Choose facility</option>
        @foreach($facilities as $category => $names)
            @foreach($names as $name)
                <option
                    value="{{ $name }}"
                    data-category="{{ $category }}"
                    @selected($oldFacility === $name)
                >{{ $name }}</option>
            @endforeach
        @endforeach
    </select>
</div>

@once
<script>
    function initFacilityCategoryFilter(form) {
        const categorySelect = form.querySelector('.facility-category-select');
        const facilitySelect = form.querySelector('.facility-name-select');
        if (!categorySelect || !facilitySelect) return;

        const options = Array.from(facilitySelect.querySelectorAll('option[data-category]'));

        function filterFacilities() {
            const category = categorySelect.value;
            const currentValue = facilitySelect.value;

            facilitySelect.innerHTML = '<option value="">Choose facility</option>';

            options
                .filter((option) => option.dataset.category === category)
                .forEach((option) => facilitySelect.appendChild(option.cloneNode(true)));

            const stillValid = Array.from(facilitySelect.options).some(
                (option) => option.value === currentValue && option.value !== ''
            );
            facilitySelect.value = stillValid ? currentValue : '';
        }

        categorySelect.addEventListener('change', filterFacilities);
        filterFacilities();
    }

    document.querySelectorAll('form').forEach((form) => {
        if (form.querySelector('.facility-category-select')) {
            initFacilityCategoryFilter(form);
        }
    });
</script>
@endonce
