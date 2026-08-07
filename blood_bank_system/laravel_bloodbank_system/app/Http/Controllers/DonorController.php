<?php

namespace App\Http\Controllers;

use App\Models\DonationRequest;
use App\Models\Donor;
use App\Services\FacilityOptions;
use Illuminate\Http\Request;

class DonorController extends Controller
{
    public function create(Request $request)
    {
        $this->requireRole($request, 'donor');

        return view('donor.request', ['facilities' => FacilityOptions::all()]);
    }

    public function store(Request $request)
    {
        $this->requireRole($request, 'donor');

        $data = $request->validate([
            'facility_category' => ['required', 'in:Hospital,Rural Health Unit,Red Cross'],
            'facility_name' => ['required', 'in:'.implode(',', FacilityOptions::names())],
            'component' => ['required', 'string', 'max:60'],
            'units' => ['required', 'integer', 'min:1', 'max:2'],
            'donor_note' => ['nullable', 'string', 'max:1000'],
        ]);

        $donor = Donor::where('user_id', $request->session()->get('user_id'))->firstOrFail();

        DonationRequest::create([
            ...$data,
            'donor_id' => $donor->id,
            'blood_type' => $donor->blood_type,
        ]);

        return redirect()->route('dashboard')->with('success', 'Donation request submitted. The facility will set the schedule.');
    }

    private function requireRole(Request $request, string $role): void
    {
        abort_unless($request->session()->get('role') === $role, 403);
    }
}
