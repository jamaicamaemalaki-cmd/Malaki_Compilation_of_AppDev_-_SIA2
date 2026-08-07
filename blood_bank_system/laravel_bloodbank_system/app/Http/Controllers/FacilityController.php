<?php

namespace App\Http\Controllers;

use App\Models\BloodInventory;
use App\Models\BloodRequest;
use App\Models\Donation;
use App\Models\DonationRequest;
use App\Models\MedicalFacility;
use App\Services\BloodComponents;
use App\Services\BloodTypes;
use App\Services\FacilityOptions;
use Illuminate\Http\Request;

class FacilityController extends Controller
{
    public function requestBlood(Request $request)
    {
        $this->requireRole($request, 'facility');
        return view('facility.request-blood', ['facilities' => FacilityOptions::all()]);
    }

    public function storeBloodRequest(Request $request)
    {
        $this->requireRole($request, 'facility');
        $facility = $this->facility($request);

        $data = $request->validate([
            'facility_category' => ['required', 'in:Hospital,Rural Health Unit,Red Cross'],
            'facility_name' => ['required', 'in:'.implode(',', FacilityOptions::names())],
            'blood_type' => ['required', 'string', 'max:5'],
            'component' => ['required', 'string', 'max:60'],
            'units' => ['required', 'integer', 'min:1'],
            'urgency' => ['required', 'in:low,medium,high,critical'],
            'reason' => ['nullable', 'string', 'max:1200'],
        ]);

        BloodRequest::create([
            ...$data,
            'requester_id' => $request->session()->get('user_id'),
            'requester_role' => 'facility',
        ]);

        return redirect()->route('dashboard')->with('success', 'Blood request sent to '.$data['facility_name'].'.');
    }

    public function facilityRequests(Request $request)
    {
        $this->requireRole($request, 'facility');
        $facility = $this->facility($request);

        return view('facility.facility-requests', [
            'facility' => $facility,
            'requests' => BloodRequest::with('requester.facility')
                ->where('requester_role', 'facility')
                ->where('facility_name', $facility->facility_name)
                ->where('requester_id', '!=', $request->session()->get('user_id'))
                ->latest()
                ->get(),
        ]);
    }

    public function donorRequests(Request $request)
    {
        $this->requireRole($request, 'facility');
        $facility = $this->facility($request);

        $requests = DonationRequest::with('donor.user')
            ->where('facility_name', $facility->facility_name)
            ->latest()
            ->get();

        return view('facility.donor-requests', [
            'pendingRequests' => $requests->where('status', 'pending')->values(),
            'scheduledRequests' => $requests->whereIn('status', ['approved', 'completed'])->values(),
            'rejectedRequests' => $requests->where('status', 'rejected')->values(),
        ]);
    }

    public function scheduleDonor(Request $request, DonationRequest $donationRequest)
    {
        $this->requireRole($request, 'facility');
        $facility = $this->facility($request);
        abort_unless($donationRequest->facility_name === $facility->facility_name, 403);

        $data = $request->validate([
            'status' => ['required', 'in:approved,rejected,completed'],
            'scheduled_date' => ['nullable', 'date', 'required_if:status,approved'],
            'start_time' => ['nullable', 'date_format:H:i', 'required_if:status,approved'],
            'end_time' => ['nullable', 'date_format:H:i', 'required_if:status,approved'],
            'facility_note' => ['nullable', 'string', 'max:1000'],
        ]);

        if ($data['status'] === 'approved' && $data['start_time'] >= $data['end_time']) {
            return back()
                ->withErrors(['end_time' => 'End time must be after start time.'])
                ->withInput();
        }

        $donationRequest->update($data);

        if ($data['status'] === 'completed') {
            Donation::create([
                'donor_id' => $donationRequest->donor_id,
                'blood_type' => $donationRequest->blood_type,
                'component' => $donationRequest->component,
                'units' => $donationRequest->units,
                'donation_date' => now()->toDateString(),
                'facility_name' => $donationRequest->facility_name,
                'notes' => $data['facility_note'] ?? null,
            ]);
        }

        $message = match ($data['status']) {
            'approved' => 'Donor schedule saved and added to the scheduled list.',
            'rejected' => 'Donor request rejected.',
            default => 'Donation marked as completed.',
        };

        return redirect()
            ->route('facility.donor-requests')
            ->with('success', $message);
    }

    public function updateAddressedRequest(Request $request, BloodRequest $bloodRequest)
    {
        $this->requireRole($request, 'facility');
        $facility = $this->facility($request);

        abort_unless(
            $bloodRequest->requester_role === 'facility'
                && $bloodRequest->facility_name === $facility->facility_name,
            403
        );

        if (in_array($bloodRequest->status, ['rejected', 'released'], true)) {
            return back()->withErrors(['status' => 'This request is already final and cannot be changed.']);
        }

        $data = $request->validate([
            'status' => ['required', 'in:approved,rejected,released'],
            'admin_note' => ['nullable', 'string', 'max:1000'],
        ]);

        if ($data['status'] === 'released' && $bloodRequest->status !== 'approved') {
            return back()->withErrors(['status' => 'Request must be approved before it can be marked as released.']);
        }

        $bloodRequest->update($data);

        return redirect()
            ->route('facility.requests')
            ->with('success', 'Request updated.');
    }

    public function inventory(Request $request)
    {
        $this->requireRole($request, 'facility');
        $facility = $this->facility($request);

        return view('facility.inventory', [
            'facility' => $facility,
            'bloodTypes' => BloodTypes::all(),
            'components' => BloodComponents::all(),
            'unitsByType' => $this->unitsByTypeAndComponent($facility),
        ]);
    }

    public function storeInventory(Request $request)
    {
        $this->requireRole($request, 'facility');
        $facility = $this->facility($request);

        $data = $request->validate([
            'add_units' => ['required', 'array'],
            'add_units.*' => ['array'],
            'add_units.*.*' => ['nullable', 'integer', 'min:0'],
        ]);

        $addedTotal = 0;

        foreach (BloodTypes::all() as $type) {
            foreach (BloodComponents::all() as $component) {
                $toAdd = (int) ($data['add_units'][$type][$component] ?? 0);

                if ($toAdd <= 0) {
                    continue;
                }

                $inventory = BloodInventory::firstOrCreate(
                    [
                        'medical_facility_id' => $facility->id,
                        'blood_type' => $type,
                        'component' => $component,
                    ],
                    [
                        'facility_name' => $facility->facility_name,
                        'units_available' => 0,
                    ]
                );

                $inventory->increment('units_available', $toAdd);
                $addedTotal += $toAdd;
            }
        }

        if ($addedTotal === 0) {
            return back()
                ->withErrors(['add_units' => 'Enter at least one unit to add before saving.'])
                ->withInput();
        }

        BloodInventory::where('medical_facility_id', $facility->id)
            ->where(function ($query) {
                $query->whereNotIn('blood_type', BloodTypes::all())
                    ->orWhereNotIn('component', BloodComponents::all());
            })
            ->delete();

        return back()->with('success', "Availability saved. Added {$addedTotal} unit(s) to your inventory.");
    }

    private function unitsByTypeAndComponent(MedicalFacility $facility): array
    {
        $units = [];

        foreach (BloodTypes::all() as $type) {
            foreach (BloodComponents::all() as $component) {
                $units[$type][$component] = 0;
            }
        }

        foreach (BloodInventory::where('medical_facility_id', $facility->id)->get() as $record) {
            if (
                in_array($record->blood_type, BloodTypes::all(), true)
                && in_array($record->component, BloodComponents::all(), true)
            ) {
                $units[$record->blood_type][$record->component] += (int) $record->units_available;
            }
        }

        return $units;
    }

    private function facility(Request $request): MedicalFacility
    {
        return MedicalFacility::where('user_id', $request->session()->get('user_id'))->firstOrFail();
    }

    private function requireRole(Request $request, string $role): void
    {
        abort_unless($request->session()->get('role') === $role, 403);
    }
}
