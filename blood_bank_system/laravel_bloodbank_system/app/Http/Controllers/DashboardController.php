<?php

namespace App\Http\Controllers;

use App\Models\BloodInventory;
use App\Models\BloodRequest;
use App\Models\Donation;
use App\Models\DonationRequest;
use App\Models\Donor;
use App\Models\MedicalFacility;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        if (! $request->session()->has('user_id')) {
            return redirect()->route('login');
        }

        return match ($request->session()->get('role')) {
            'admin' => $this->admin(),
            'donor' => $this->donor($request),
            'facility' => $this->facility($request),
            default => redirect()->route('login'),
        };
    }

    private function admin()
    {
        return view('admin.dashboard', [
            'users' => User::count(),
            'donors' => Donor::count(),
            'facilities' => MedicalFacility::count(),
            'inventoryUnits' => BloodInventory::sum('units_available'),
            'pendingRequests' => BloodRequest::where('status', 'pending')->count(),
            'requests' => BloodRequest::latest()->limit(8)->get(),
            'donations' => Donation::latest()->limit(8)->get(),
            'lowStocks' => BloodInventory::where('units_available', '<=', 3)->get(),
        ]);
    }

    private function donor(Request $request)
    {
        $donor = Donor::where('user_id', $request->session()->get('user_id'))->firstOrFail();

        return view('donor.dashboard', [
            'donor' => $donor,
            'requests' => DonationRequest::where('donor_id', $donor->id)->latest()->get(),
            'inventories' => BloodInventory::where('units_available', '>', 0)->orderBy('facility_name')->get(),
        ]);
    }

    private function facility(Request $request)
    {
        $facility = MedicalFacility::where('user_id', $request->session()->get('user_id'))->firstOrFail();

        return view('facility.dashboard', [
            'facility' => $facility,
            'bloodRequests' => BloodRequest::with('requester.facility')
                ->where('requester_id', $request->session()->get('user_id'))
                ->latest()
                ->get(),
            'incomingFacilityRequests' => BloodRequest::with('requester.facility')
                ->where('requester_role', 'facility')
                ->where('facility_name', $facility->facility_name)
                ->where('requester_id', '!=', $request->session()->get('user_id'))
                ->latest()
                ->get(),
            'donorRequests' => DonationRequest::with('donor.user')
                ->where('facility_name', $facility->facility_name)
                ->latest()
                ->get(),
            'pendingDonorRequests' => DonationRequest::where('facility_name', $facility->facility_name)->where('status', 'pending')->count(),
            'inventories' => BloodInventory::where('medical_facility_id', $facility->id)->latest()->get(),
        ]);
    }
}
