@extends('layouts.app')
@section('title', 'Facility Inventory | BloodLink')
@section('content')
<div class="soft-card p-4">
    <h1 class="h3 mb-2">Blood Availability — {{ $facility->facility_name }}</h1>
    <p class="text-secondary mb-4">
        Saved units are shown per blood type and component (Whole Blood, Platelets, Plasma).
        Enter <strong>additional</strong> units to add, then save.
    </p>

    <form method="POST" action="{{ route('facility.inventory.store') }}">
        @csrf
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead>
                <tr>
                    <th style="width: 12%">Blood Type</th>
                    @foreach($components as $component)
                        <th>{{ $component }}</th>
                    @endforeach
                </tr>
                </thead>
                <tbody>
                @foreach($bloodTypes as $type)
                    <tr>
                        <td><strong class="fs-5">{{ $type }}</strong></td>
                        @foreach($components as $component)
                            @php
                                $saved = (int) ($unitsByType[$type][$component] ?? 0);
                                $oldKey = "add_units.{$type}.{$component}";
                            @endphp
                            <td class="component-cell">
                                <div class="mb-2">
                                    <small class="text-secondary d-block">Saved</small>
                                    <span class="badge {{ $saved <= 0 ? 'text-bg-secondary' : ($saved <= 3 ? 'text-bg-warning' : 'text-bg-success') }}">
                                        {{ $saved }} unit{{ $saved === 1 ? '' : 's' }}
                                    </span>
                                </div>
                                <div>
                                    <small class="text-secondary d-block">Add</small>
                                    <input
                                        class="form-control form-control-sm @error($oldKey) is-invalid @enderror"
                                        type="number"
                                        name="add_units[{{ $type }}][{{ $component }}]"
                                        min="0"
                                        value="{{ old($oldKey) }}"
                                        placeholder="0"
                                    >
                                </div>
                            </td>
                        @endforeach
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
        @error('add_units')
            <div class="text-danger small mt-2">{{ $message }}</div>
        @enderror
        <button class="btn btn-danger btn-lg w-100 mt-4" type="submit">Save Availability</button>
    </form>
</div>
@endsection
