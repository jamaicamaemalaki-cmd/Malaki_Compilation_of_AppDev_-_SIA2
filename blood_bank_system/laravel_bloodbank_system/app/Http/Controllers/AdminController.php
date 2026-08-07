<?php

namespace App\Http\Controllers;

use App\Models\BloodInventory;
use App\Models\BloodRequest;
use App\Models\Donation;
use App\Models\Donor;
use App\Models\MedicalFacility;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    public function seedAdmin()
    {
        // Admin
        User::firstOrCreate(
            ['email' => 'admin@bloodlink.test'],
            [
                'name' => 'BloodLink Admin',
                'password' => Hash::make('admin123'),
                'role' => 'admin',
            ]
        );

        // Medical Facilities
        $facilities = [
            [
                'email' => 'hch@bloodlink.test',
                'name' => 'Hinunangan Community Hospital',
                'category' => 'Hospital',
            ],
            [
                'email' => 'ztlmh@bloodlink.test',
                'name' => 'Zenon T. Lagumbay Memorial Hospital',
                'category' => 'Hospital',
            ],
            [
                'email' => 'hrhu@bloodlink.test',
                'name' => 'Hinunangan Rural Health Unit',
                'category' => 'Rural Health Unit',
            ],
            [
                'email' => 'prc@bloodlink.test',
                'name' => 'Philippine Red Cross',
                'category' => 'Red Cross',
            ],
        ];

        foreach ($facilities as $f) {
            $user = User::firstOrCreate(
                ['email' => $f['email']],
                [
                    'name' => $f['name'],
                    'password' => Hash::make('facility123'),
                    'role' => 'facility',
                ]
            );

            MedicalFacility::firstOrCreate(
                ['user_id' => $user->id],
                [
                    'facility_category' => $f['category'],
                    'facility_name' => $f['name'],
                    'license_number' => 'FIXED-LICENSE-' . strtoupper(substr($f['email'], 0, 3)),
                ]
            );
        }

        return redirect()->route('login')->with('success', 'Admin and Facility accounts ready. Check your documentation for credentials.');
    }

    public function requests(Request $request)
    {
        $this->requireAdmin($request);

        return view('admin.requests', [
            'requests' => BloodRequest::with('requester.facility')->latest()->get(),
        ]);
    }

    public function reports(Request $request)
    {
        $this->requireAdmin($request);

        // Fetch monthly users count
        $usersMonthly = User::select(DB::raw('COUNT(*) as count'), DB::raw('MONTH(created_at) as month'))
            ->whereYear('created_at', date('Y'))
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->pluck('count', 'month')
            ->toArray();

        // Fetch monthly requests count
        $requestsMonthly = BloodRequest::select(DB::raw('COUNT(*) as count'), DB::raw('MONTH(created_at) as month'))
            ->whereYear('created_at', date('Y'))
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->pluck('count', 'month')
            ->toArray();

        // Fill missing months with 0
        $usersData = [];
        $requestsData = [];
        for ($i = 1; $i <= 12; $i++) {
            $usersData[] = $usersMonthly[$i] ?? 0;
            $requestsData[] = $requestsMonthly[$i] ?? 0;
        }

        return view('admin.reports', [
            'donors' => Donor::with('user')->get(),
            'facilities' => MedicalFacility::with('user')->get(),
            'inventory' => BloodInventory::orderBy('facility_name')->limit(5)->get(),
            'donations' => Donation::with('donor.user')->latest()->get(),
            'usersChartData' => $usersData,
            'requestsChartData' => $requestsData,
        ]);
    }

    private function requireAdmin(Request $request): void
    {
        abort_unless($request->session()->get('role') === 'admin', 403);
    }
}
