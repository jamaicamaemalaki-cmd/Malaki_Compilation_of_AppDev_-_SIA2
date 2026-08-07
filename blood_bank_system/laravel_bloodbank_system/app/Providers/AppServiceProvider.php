<?php

namespace App\Providers;

use App\Models\BloodRequest;
use App\Models\DonationRequest;
use App\Models\MedicalFacility;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        View::composer('*', function ($view) {
            $pendingDonorRequests = 0;
            $incomingBloodRequests = 0;

            if (session('role') === 'facility' && session('user_id')) {
                $facility = MedicalFacility::where('user_id', session('user_id'))->first();

                if ($facility) {
                    $pendingDonorRequests = DonationRequest::where('facility_name', $facility->facility_name)
                        ->where('status', 'pending')
                        ->count();

                    $incomingBloodRequests = BloodRequest::where('facility_name', $facility->facility_name)
                        ->where('status', 'pending')
                        ->where('requester_role', 'facility')
                        ->where('requester_id', '!=', session('user_id'))
                        ->count();
                }
            }

            $view->with([
                'pendingDonorRequests' => $pendingDonorRequests,
                'incomingBloodRequests' => $incomingBloodRequests,
            ]);
        });
    }
}
